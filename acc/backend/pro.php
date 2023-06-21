
<?
require_once("../includes/db.inc.php");

$str = $_SERVER['QUERY_STRING'];
parse_str($str);

//фукнция формирует условия запроса в БД
function get_sql_vst($typ,$nm,$alias){
if($typ){
  $q = count($typ);
    foreach ($typ as $key => $value) {
    $i = $i + 1;
    if($i == $q){$or = '';}else{$or = ' OR ';}
    $v .= "$alias.$nm = '$value'$or";
}

$v = "($v)";

return $v;}
}

$doljnost_vst = get_sql_vst($doljnost, "doljnost","u");
$type_vst = get_sql_vst($type, "izd_type","a");
$job_vst = get_sql_vst($job_names, "job","j");
$app_type_vst = get_sql_vst($app_type, "app_type","a");


?>

<table border=1 cellspacing=0 style="text-align:center" class="analyt_pr_tbl">

<tr>
  <th>месяц</th>
  <th>начислено</th>
  <th>прочее</th>
  <th>сделка</th>
  <th>итого</th>
  <th>производительность</th>
  <th>абсолютная про-сть</th>
  <th>средн. цена за ед.</th>
</tr>

<?
//проставляем VIP не VIP галочку в каждой, где ранее не было проставлено   не было, по данным сайта. Вычисление ВИПов по другим признакам будет не совсем точным
$vips = mysql_query("SELECT art_id FROM plan_arts WHERE vip = '1'");
while($vip = mysql_fetch_array($vips)){
$art_id = $vip[art_id];
$update = mysql_query("UPDATE applications SET vip = '1' WHERE art_id = '$art_id'");
}

//проставляем коэфиципенты сложности в таблицу по заданным признакам
$update_k0 = mysql_query("UPDATE applications SET k = '1'");
$update_k1 = mysql_query("UPDATE applications SET k = '1.25' WHERE vip = '1' AND (hand_type <> '2' OR hand_type <> '3' OR hand_type <> '4' OR hand_type <> '5')");
$update_k2 = mysql_query("UPDATE applications SET k = '2' WHERE hand_type = '2' OR hand_type = '3' OR hand_type = '4' OR hand_type = '5'");
//коробки
$update_k3 = mysql_query("UPDATE applications SET k = '0.05' WHERE izd_type = '2'");
//премиум коробки
$update_k4 = mysql_query("UPDATE applications SET k = '20' WHERE izd_type = '23'");
//премиум коробки
$update_k5 = mysql_query("UPDATE applications SET k = '15' WHERE izd_type = '23'");
//конверты
$update_k6 = mysql_query("UPDATE applications SET k = '0.05' WHERE izd_type = '16'");
//коробки китпак
$update_k7 = mysql_query("UPDATE applications SET k = '0.1' WHERE izd_type = '11'");
//наклейки
$update_k8 = mysql_query("UPDATE applications SET k = '0.01' WHERE izd_type = '21'");

//средний пакет 30х40х13 имеет длину скотча 148см, по нему и будем мерять все остальные пакеты


//проставляем абсолютную производительность в applications
//получаем num_of_work в массив отдельным запросом
$num_of_work_q = "SELECT DATE_FORMAT(cur_time, '%m') AS month, SUM(j.num_of_work) AS num_of_work, a.k,
SUM(TRUNCATE((j.num_of_work*((a.izd_w * 2) + (a.izd_w * 0.7 * 2) + (a.podvorot + a.izd_v + a.izd_b / 2) * a.paper_num_list)/148)*a.k,0)) AS num_of_work_abs
FROM job AS j, applications AS a WHERE $job_vst AND $type_vst AND $app_type_vst AND a.num_ord = j.num_ord AND j.cur_time LIKE '$year_num_cap-%'  GROUP BY DATE_FORMAT(cur_time, '%m')";
//echo $num_of_work_q;
$num_of_work = mysql_query($num_of_work_q);
echo mysql_error();
while($nwk = mysql_fetch_array($num_of_work)){
 $nwk_arr[$nwk[month]][num_of_work] = $nwk[num_of_work];
 $nwk_arr[$nwk[month]][num_of_work_abs] = round($nwk[num_of_work_abs]);
}


//print_r($nwk_arr);
//объединяем с этим запросом
$rep_q = "SELECT r.year AS year, r.month AS month,  SUM(r.nachisleno) AS nachisleno, SUM(r.procee) AS procee, SUM(r.sdelka) AS sdelka FROM report2 AS r, users AS u WHERE r.year = '$year_num_cap' AND r.uid = u.job_id AND $doljnost_vst GROUP BY r.month";
$rep = mysql_query($rep_q);
//echo $rep_q;
echo mysql_error();
while ($r = mysql_fetch_assoc($rep)){
$year = $r[year];
$month = $r[month];

if($nachisleno == 1){$nachisleno_var = $r[nachisleno];}else{$nachisleno_var = "0";}
if($procee == 1){$procee_var  =  $r[procee];}else{$procee_var = "0";}
if($sdelka == 1){$sdelka_var  =  $r[sdelka];}else{$sdelka_var = "0";}

$itogo = round($nachisleno_var+$procee_var+$sdelka_var);
$done = $nwk_arr[$month][num_of_work];
if($done>0){
$abs_done = $nwk_arr[$month][num_of_work_abs];
$k_slojnost = round($abs_done/$done,2);

$done_itog = $done + $done_itog;
$abs_done_itog = $abs_done + $abs_done_itog;
$k_slojnost_itog = $k_slojnost + $k_slojnost_itog;

$cost_per_unit = round($itogo/$abs_done,2);
$cost_per_unit_itog = $cost_per_unit_itog+$cost_per_unit;
echo "<tr>
  <td>$month</td>
  <td>$nachisleno_var</td>
  <td>$procee_var</td>
  <td>$sdelka_var</td>
  <td>$itogo</td>
  <td>$done</td>
  <td>$abs_done ($k_slojnost)</td>
  <td>$cost_per_unit</td>
  </tr>";
}
$nachisleno_var="";
$procee_var="";
$sdelka_var="";
$itogo="";
$done="";
$abs_done="";
$k_slojnost="";
$cost_per_unit="";
}


$tek_year = date("Y");
if($tek_year == $year){$tek_mes_num = date("m");}else{$tek_mes_num = '12';}

$done_med = round($done_itog/$tek_mes_num);
$abs_done_med = round($abs_done_itog/$tek_mes_num);

$k_slojnost_med = round($k_slojnost_itog/$tek_mes_num,2);
$cost_per_unit_med = round($cost_per_unit_itog/$tek_mes_num,2);
echo "<tr style=font-weight:bold>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td>$done_med</td>
  <td>$abs_done_med ($k_slojnost_med)</td>
  <td>$cost_per_unit_med</td>
  </tr>";

?>
</table>
