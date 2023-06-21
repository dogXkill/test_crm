  <?
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");


$tek_year = date("Y");
$tek_month = date("m");
if($user_id == '12' || $user_id == '11' || $user_id == '199'){
?>


<?
function get_tek_rate($job){
  if($job == '14'){$tek_rate = '0.5';}
  if($job == '15'){$tek_rate = '0.17';}
  if($job == '16'){$tek_rate = '0.17';}
return $tek_rate;
}

function get_month_name($month){$months = array("01"=>"€нварь", "02"=>"февраль", "03"=>"март", "04"=>"апрель", "05"=>"май", "06"=>"июнь", "07"=>"июль", "08"=>"август", "09"=>"сент€брь", "10"=>"окт€брь", "11"=>"но€брь", "12"=>"декабрь");
return $months[$month];
}

function pokazateli($tek_mon){


$get_job_q = "SELECT * FROM job WHERE cur_time LIKE '".$tek_mon."-%'";

$get_job = mysql_query($get_job_q);
while($j =  mysql_fetch_assoc($get_job)){
$num_ords[] .= $j[num_ord];
$uid = $j[uid];
$job[$uid][num_ord] = $j[num_ord];
$job[$uid][job] = $j[job];
$job[$uid][num_of_work] = $j[num_of_work];
$job[$uid][nadomn] = $j[nadomn];
$job[$uid][order_price] = $j[order_price];
}
if (is_array($num_ords)){$num_ords = array_unique($num_ords);
$num_ords = implode(",",$num_ords);

$get_apps_q = "SELECT num_ord, izd_type, rate_1, rate_2, rate_3, rate_4, rate_5, rate_6, rate_7, rate_8, rate_9, rate_10, rate_11, rate_12, rate_13, rate_14, rate_15, rate_16, rate_17, rate_18, rate_19, rate_20, rate_21, rate_22, rate_23, rate_24, rate_25, rate_26, rate_27, rate_28 FROM applications WHERE num_ord IN($num_ords)";
$get_apps = mysql_query($get_apps_q);
echo mysql_error();

while ($a = mysql_fetch_assoc($get_apps)) {
 $apps[$a[num_ord]] = $a;
}
//echo mysql_num_rows($get_job);
if(mysql_num_rows($get_job)!==0)
//считаем сделку
foreach ($job as $val) {
$tek_rate = $apps[$val[num_ord]]["rate_".$val[job]];

if($tek_rate == ""){$tek_rate = get_tek_rate($val[job]);}

$tek_rate = round($tek_rate,2);

$sdelka = round(($val[num_of_work] * $tek_rate + $sdelka));

if($val[job] == '1'){$laminated = $val[num_of_work]+$laminated;}
if($val[job] == '3'){$tisn = $val[num_of_work]+$tisn;}
if($val[job] == '3'){$virubleno = $val[num_of_work]+$virubleno;}
if($val[job] == '4'){$sobrano = $val[num_of_work]+$sobrano;}
if($val[job] == '11'){
  $packed = $val[num_of_work]+$packed;
  if($apps[$val[num_ord]][izd_type] == 4){$packed_pacs = $val[num_of_work]+$packed_pacs;}
}}

$tek_rate = "";
}






?>
<h2>ќсновные показатели за <?echo $tek_mon;?></h2>
<table class=pokazateli_tbl border=1>
  <tr>
    <td>Ќачислено по часам</td>
    <td></td>
  </tr>
  <tr>
    <td>Ќачислено сделка</td>
    <td><?=$sdelka;?></td>
  </tr>
  <tr>
    <td>Ќачислено итого</td>
    <td></td>
  </tr>
  <tr>
    <td>ќтламинировано (листов)</td>
    <td><?=$laminated;?></td>
  </tr>
  <tr>
    <td>ќттиснено (ударов)</td>
    <td><?=$tisn;?></td>
  </tr>
  <tr>
    <td>¬ырублено (ударов)</td>
    <td><?=$virubleno;?></td>
  </tr>
  <tr>
    <td>—обрано (шт)</td>
    <td><?=$sobrano;?></td>
  </tr>
  <tr>
    <td>”паковано изделий всего (шт)</td>
    <td><?=$packed;?></td>
  </tr>
  <tr>
    <td>”паковано пакетов (шт)</td>
    <td><?=$packed_pacs;?></td>
  </tr>
</table>
<?
  }
$tek_mon = date('Y-m');
$proshl_mon = strtotime("$tek_mon -1 month");
$proshl_mon = date('Y-m', $proshl_mon);

pokazateli($tek_mon);
pokazateli($proshl_mon);?>


<h2>—татистика производства</h2>
<form id="form_pro" name="form_pro">
<?
//получить доступные должности, где производилось хот€ бы 1 начисление за последние 5 лет в массив
$doljnost_q = "SELECT u.doljnost, d.name FROM report2 AS r, users AS u, doljnost AS d WHERE year > 2014 AND (r.procee > 0 OR r.nachisleno > 0 OR r.sdelka > 0) AND u.job_id = r.uid AND u.doljnost = d.id  AND u.proizv = 1 GROUP BY doljnost ORDER BY name ASC";
$doljnost = mysql_query($doljnost_q);
echo mysql_error();
while($d =  mysql_fetch_assoc($doljnost)){
$doljnost_html .= "<input type=checkbox checked=checked name=doljnost[] id=doljnost_$d[doljnost] value=$d[doljnost]> <label for=doljnost_$d[doljnost]>$d[name]</label><br>";
}

//получить типы изделий, где есть хот€ бы 1 добавленна€ работа за посл 5 лет в массив
$types_q = "SELECT a.izd_type, t.type FROM job AS j, applications AS a, types AS t WHERE j.num_ord = a.num_ord AND t.tid = a.izd_type AND j.cur_time > '2014-01-01 00:00:00' GROUP BY a.izd_type";
$types = mysql_query($types_q);
while($t =  mysql_fetch_assoc($types)){
$types_html .= "<input type=checkbox checked=checked name=type[] id=type_$t[izd_type] value=$t[izd_type]> <label for=type_$t[izd_type]>$t[type]</label><br>";
}


//получить этапы, где есть хот€ бы 1 добавленна€ работа за посл 5 лет
$job_names_q = "SELECT j.job, jn.name FROM job AS j, job_names AS jn WHERE j.job = jn.id AND j.cur_time > '2014-01-01 00:00:00' GROUP BY j.job";
$job_names = mysql_query($job_names_q);
while($j =  mysql_fetch_assoc($job_names)){
$job_names_html .= "<input type=checkbox name=job_names[] id=job_names_$j[job] value=$j[job]> <label for=job_names_$j[job]>$j[name]</label><br>";
}
?>
<table>
<tr>
<td style="vertical-align: top;">
<select id="year_num_cap" name="year_num_cap">
<option value="">год</option>
<option value="2015">2015</option>
<option value="2016">2016</option>
<option value="2017">2017</option>
<option value="2018">2018</option>
<option value="2019">2019</option>
<option value="2020">2020</option>
<option value="2021">2021</option>
<option value="2022">2022</option>
<option value="2023">2023</option>
<option value="2024">2024</option>
<option value="2025">2025</option>
</select>
<br>
<b>тип издели€:</b><br>
<input type="checkbox" checked="checked" name="app_type[]" id="ser" value="2"> <label for="ser">серийное</label> <br>
<input type="checkbox" checked="checked" name="app_type[]" id="zakaz" value="1"> <label for="zakaz">заказное</label><br>

<b>учитывать:</b><br>
<input type="checkbox" checked="checked" name="nachisleno" id="nachisleno" value="1"> <label for="nachisleno">начислени€ «ѕ</label> <br>
<input type="checkbox" checked="checked" name="sdelka" id="sdelka" value="1"> <label for="sdelka">сделка</label><br>
<input type="checkbox" checked="checked" name="procee" id="procee" value="1"> <label for="procee">прочее</label><br>
<b>по следующим типам изделий:</b><br>
<?=$types_html;?>
<b>по следующим должност€м:</b><br><?=$doljnost_html;?></td>

<td style="vertical-align: top;">
<b>по следующим этапам:</b><br>
<?=$job_names_html;?>
<button onclick="form_pro_report();return false;">—формировать отчет!</button></td>
</tr>

</table>

</form>

 <span id="proizv_analyt_span"></span>



<h2>Ёффективность сотрудников</h2>
<table>
  <tr>
    <td>период исследовани€ (последние ’ мес€цев): </td>
  <td><select id=period style="width:150px;">
<option value="24">24 мес.</option>
<option value="12" selected>12 мес.</option>
<option value="6">6 мес.</option>
<option value="3">3 мес.</option>
<option value="1">1 мес.</option>
</select></td>
  </tr>
  <tr>
    <td>типы сотрудников: </td>
    <td><select id=doljnost style="width:150px;">
<option value="8">менеджеры</option>
<option value="16">мастер на станках</option>
<option value="17">упаковщик</option>
<option value="15" selected>мастер-сборщик</option>
<option value="12">учетчик</option>
<option value="33">мастер допработ</option>
</select></td>
  </tr>





  <tr>
    <td>јнализируемый мес€ц:</td>
    <td><select id=month_num_eff>
<option value="01" <?if($tek_month=="01"){echo " selected";}?>>€нварь</option>
<option value="02" <?if($tek_month=="02"){echo " selected";}?>>февраль</option>
<option value="03" <?if($tek_month=="03"){echo " selected";}?>>март</option>
<option value="04" <?if($tek_month=="04"){echo " selected";}?>>апрель</option>
<option value="05" <?if($tek_month=="05"){echo " selected";}?>>май</option>
<option value="06" <?if($tek_month=="06"){echo " selected";}?>>июнь</option>
<option value="07" <?if($tek_month=="07"){echo " selected";}?>>июль</option>
<option value="08" <?if($tek_month=="08"){echo " selected";}?>>август</option>
<option value="09" <?if($tek_month=="09"){echo " selected";}?>>сент€брь</option>
<option value="10" <?if($tek_month=="10"){echo " selected";}?>>окт€брь</option>
<option value="11" <?if($tek_month=="11"){echo " selected";}?>>но€брь</option>
<option value="12" <?if($tek_month=="12"){echo " selected";}?>>декабрь</option>
</select>
<select id=year_num_eff>
<option value="2010" <?if($tek_year=="2010"){echo " selected";}?>>2010</option>
<option value="2011" <?if($tek_year=="2011"){echo " selected";}?>>2011</option>
<option value="2012" <?if($tek_year=="2012"){echo " selected";}?>>2012</option>
<option value="2013" <?if($tek_year=="2013"){echo " selected";}?>>2013</option>
<option value="2014" <?if($tek_year=="2014"){echo " selected";}?>>2014</option>
<option value="2015" <?if($tek_year=="2015"){echo " selected";}?>>2015</option>
<option value="2016" <?if($tek_year=="2016"){echo " selected";}?>>2016</option>
<option value="2017" <?if($tek_year=="2017"){echo " selected";}?>>2017</option>
<option value="2018" <?if($tek_year=="2018"){echo " selected";}?>>2018</option>
<option value="2019" <?if($tek_year=="2019"){echo " selected";}?>>2019</option>
<option value="2020" <?if($tek_year=="2020"){echo " selected";}?>>2020</option>
<option value="2021" <?if($tek_year=="2021"){echo " selected";}?>>2021</option>
<option value="2022" <?if($tek_year=="2022"){echo " selected";}?>>2022</option>
</select></td>
  </tr>
  <tr>
    <td></td>
    <td><input type=submit value=">>>" onclick="get_sotr_effectivity()"> </td>
  </tr>
</table>

 <span id="sotr_effectivity_span"></span>


   <script>
//tek_year = <?=$tek_year;?>;

$("#year_num_cap [value='<?=$tek_year;?>']").attr("selected", "");
$("#job_names_4").attr("checked", "");

</script>

 <? } ?>