<?
ob_start();


require_once("/home/crmu660633/crm.upak.me/docs/acc/includes/db.inc.php");
require_once("/home/crmu660633/crm.upak.me/docs/acc/includes/auth.php");
require_once("/home/crmu660633/crm.upak.me/docs/acc/includes/lib.php");
require_once("/home/crmu660633/crm.upak.me/docs/acc/backend/send_notifications.php");


//$tpus = $user_type;		// тип пользовател€

$statistics_access = $user_access['statistics_access'];
if(!$statistics_access){$statistics_access = $_COOKIE['statistics_access'];}

if($statistics_access == 2 || $_GET["act"] !== "not_send_report"){


//if($user_id == '12' || $user_id == '11' || $user_id == '199' || $user_id == '332' || $_GET["act"] !== "not_send_report"){
?>

<div style="height:25px; text-align: right;"><span style="font-size:16px;color:red;cursor:pointer;font-weight:bold; position:relative;right: 10px;" onclick="svodka_hide()">закрыть!</span></div>

<?
function query_stat($start_date, $end_date, $descr, $sravn, $qst){
$qstat = "<table width=900 cellpadding=0 cellspacing=0 class=\"hightlight\">";

if($end_date){$zapros = "  BETWEEN '".$start_date." 00:00:01' AND '".$end_date."'";}
else{$zapros = " LIKE '".$start_date."%'";}

$stat = mysql_query("SELECT SUM(prdm_sum_acc), COUNT(*), SUM(prdm_opl), SUM(prdm_sum_acc)-SUM(prdm_opl) FROM queries WHERE deleted = 0 AND client_id <> 0 AND date_query".$zapros );

$stat = mysql_fetch_array($stat);
if($stat[0]){
$summa = round($stat[0]);
$sr_summa=round($summa/$stat[1]);
$shetov = round($stat[1]);
$dolg1 = round($stat[3]);
$rand = rand(1, 1000000);

//сравнение с аналогичным мес€цем прошлого года
if($sravn == "1"){
$prosl_year_month = date("Y-m",strtotime("-1 year"))."-01";
$prosl_year_month_day = date("Y-m-d",strtotime("-1 year"));
$sravn_zapros = "  BETWEEN '".$prosl_year_month." 00:00:01' AND '".$prosl_year_month_day." 23:59:59'";
$sravn_stat = mysql_query("SELECT typ_ord, COUNT(*), SUM(prdm_sum_acc) FROM `queries` WHERE deleted = 0 AND client_id <> 0 AND date_query ".$sravn_zapros." GROUP BY typ_ord");

while ( $r = mysql_fetch_array($sravn_stat) ) {
            $proshl_year[$r[0]] = $r[2];
            $proshl_sravn_summa = $proshl_sravn_summa+$r[2];
}

if($summa > 0 AND $proshl_sravn_summa > 0){
$sravn_total_percent = round(($summa/$proshl_sravn_summa-1)*100,2);

if($sravn_total_percent > 0){$sravn_total_txt = "<span class=sranv_rost>(".$sravn_total_percent."%)</span>";}else{$sravn_total_txt = "<span class=sranv_pad>(".$sravn_total_percent."%)</span>";}


}}

$qstat .= "<tr><td width=450 onclick=\"show_extra($rand)\" style=\"cursor:pointer\"><img src=\"/i/arrow_down.png\" id=arr1>$descr</td><td width=250><b>$shetov</b> счетов на сумму <b>$summa</b> р. $sravn_total_txt долг:$dolg1</td><td>средн€€ сумма: <b>$sr_summa</b> р. ($start_date - $end_date)</td></tr>";

//подробности по всего за€вок в текущем мес€це
$qstat .= "<tr id=extra_block_".$rand." style=\"display:none\"><td colspan=3>";




//типы заказов
$stat_types = mysql_query("SELECT typ_ord, COUNT(*), SUM(prdm_sum_acc) FROM queries WHERE deleted = 0 AND client_id <> 0 AND date_query ".$zapros." GROUP BY typ_ord");
$qstat .= "<table cellspacing=0 cellpadding=0 class=table_stat><th>“ип заказа</th><th> оличество</th><th>—умма</th><th>%</th><th>—редний заказ</th></tr>";
while($row = mysql_fetch_array($stat_types)){
if ($row[0] == "1"){$typ_ord = "под заказ";}
else if ($row[0] == "2"){$typ_ord = "магазин";}
else if ($row[0] == "3"){$typ_ord = "магазин с лого";}
else {$typ_ord = "не определено";}

$summ_typ_zakaz = round($row[2]);
$percent_typ_zakaz = round($summ_typ_zakaz/$summa*100);

if($sravn == "1" AND $summ_typ_zakaz > 0 AND $proshl_year[$row[0]] > 0 AND $summ_typ_zakaz !== $proshl_year[$row[0]]){
$sravn_percent = round(($summ_typ_zakaz/$proshl_year[$row[0]]-1)*100);

if($sravn_percent > 1){$sravn_txt = "<span class=sranv_rost>(+".$sravn_percent."%)</span>";}else{$sravn_txt = "<span class=sranv_pad>(-".$sravn_percent."%)</span>";}
}

$qstat .= "<tr><td align=center><b>".$typ_ord."</b></td><td align=center>".$row[1]."</td><td align=center>".$summ_typ_zakaz." $sravn_txt</td><td align=center>".$percent_typ_zakaz."%</td><td align=center>".round($summ_typ_zakaz/$row[1])."</td></tr>";

$sravn_percent = "";
$sravn_txt = "";
}
$qstat .= "</table><br>";


//формы оплаты
$stat_forms = mysql_query("SELECT form_of_payment, COUNT(*), SUM(prdm_sum_acc) FROM queries WHERE deleted = 0 AND client_id <> 0 AND date_query ".$zapros." GROUP BY form_of_payment");
$qstat .= "<table cellspacing=0 cellpadding=0 class=table_stat><tr><th>‘орма оплаты</th><th> оличество</th><th>—умма</th><th>%</th><th>—редний заказ</th></tr>";
while($row = mysql_fetch_array($stat_forms)){
if ($row[0] == "1"){$typ_form = "наличные";}
else if ($row[0] == "2"){$typ_form = "безнал";}
else if ($row[0] == "3"){$typ_form = "по квитанции";}
else if ($row[0] == "4"){$typ_form = "карта";}
else {$typ_form = "не определено";}
$summ_typ_opl = round($row[2]);
$percent_typ_opl = round($summ_typ_opl/$summa*100);
if($qst !== '0'){
$qstat .= "<tr><td align=center><b>".$typ_form."</b></td><td align=center>".$row[1]."</td><td align=center>".$summ_typ_opl."</td><td align=center>".$percent_typ_opl."%</td><td align=center>".round($summ_typ_opl/$row[1])."</td></tr>";
}}



}
$qstat .= "</table>";

$qstat .= "</td></tr></table>";

//return array($qstat, $summa);
return $qstat;
}

$segodnya = date("Y-m-d");
echo query_stat($segodnya, $segodnya." 23:59:59", "сегодн€", "", "1");



//вчера
$vchera = date("Y-m-d",strtotime("-1 day"));
echo query_stat($vchera, $vchera." 23:59:59", "вчера", "", "1");

//позавчера
$vchera = date("Y-m-d",strtotime("-2 day"));
echo query_stat($vchera, $vchera." 23:59:59", "позавчера", "", "1");

//поза поза вчера
$vchera = date("Y-m-d",strtotime("-3 day"));
echo query_stat($vchera, $vchera." 23:59:59", "3 дн€ назад", "", "1");


$tek_month = date("Y-m");
echo query_stat($tek_month, "", "всего за€вок в <u>текущем</u> мес€це", "1", "1");

$prosl_year_month = date("Y-m",strtotime("-1 year"))."-01";
$prosl_year_month_day = date("Y-m-d 23:59:59",strtotime("-1 year"));
echo query_stat($prosl_year_month, $prosl_year_month_day, "за€вок в <u>прошлом</u> году в <u>этом</u> мес€це и на <u>этот</u> день", "", "1");

$tek_date = date("Y-m-d");
$first_day_year = date("Y")."-01-01";
echo query_stat($first_day_year,$tek_date." 23:59:59", "всего за€вок в <u>этом</u> году", "", "1");

$tek_date = date("Y-m-d",strtotime("-1 year"));
$first_day_year = date("Y",strtotime("-1 year"))."-01-01";
echo query_stat($first_day_year,$tek_date." 23:59:59", "всего за€вок в <u>прошлом</u> году на этот день", "", "1");

//echo "$tek_date - $first_day_year";

$prosl_month = date("Y-m",strtotime("-1 month"))."-01";
$prosl_month_day = date("Y-m-d 23:59:59",strtotime("-1 month"));
echo query_stat($prosl_month, $prosl_month_day, "за€вок в <u>прошлом</u> мес€це на <u>этот</u> день", "", "1");

$prosl_month = date("Y-m",strtotime("-2 month"))."-01";
$prosl_month_day = date("Y-m-d 23:59:59",strtotime("-2 month"));
echo query_stat($prosl_month, $prosl_month_day, "за€вок в <u>позапрошлом</u> мес€це на <u>этот</u> день", "", "1");

$last_month = date("Y-m",strtotime("-1 month"));
echo query_stat($last_month, "", "<u>всего</u> за€вок <u>прошлом</u> мес€це", "", "1");

$last_month = date("Y-m",strtotime("-2 month"));
echo query_stat($last_month, "", "<u>всего</u> за€вок <u>позапрошлом</u> мес€це", "", "1");



$prosl_year_month = date("Y-m",strtotime("-1 year"));
echo query_stat($prosl_year_month, "", "<u>всего</u> за€вок в <u>прошлом</u> году в <u>этом</u> мес€це", "", "1");

//получаем массив с именами водителей
$driver_name = mysql_query("SELECT id, name FROM couriers");
$drivers_arr = array();
while($rows = mysql_fetch_row($driver_name)){
    $drivers_arr[$rows[0]] = $rows[1];
}


function logistika_info($date, $date_formatted, $descr){

$points_total = mysql_query("SELECT COUNT(*) FROM courier_tasks WHERE date LIKE '".$date."%'");
$points_total = mysql_fetch_array($points_total);
$log_info = "всего точек <u><b>".$descr."</b></u>: ".$points_total[0]."<br>";
if($points_total[0] > 0){
if($date == date("Y-m-d")){$hght = "style=\"border: 3px solid green;\"";}
$log_info .= "<table class=table_driver_stat $hght><tr><td align=center><b>»м€</b></td><td align=center><b>“очек</b></td><td align=center><b>ѕ ќ</b></td><td align=center><b>ќплата</b></td><td align=center><b>¬озврат</b></td></tr>";
$points_per_driver = mysql_query("SELECT courier_id, COUNT(*), SUM(cash_payment), SUM(opl_voditel) FROM courier_tasks WHERE courier_tasks.date = '$date' GROUP BY courier_id");
global $drivers_arr;
while($row = mysql_fetch_array($points_per_driver)){

 $minimalka = 1500;
 $opl_voditel = $row[3];
 if($opl_voditel < $minimalka){$opl_voditel = $minimalka;}
 $ostatok = $row[2]-$opl_voditel;
$log_info .= "<tr><td><a href=\"/acc/logistic/task_list.php?courier_id=".$row[0]."&date=".$date_formatted."\" target=_blank>".$drivers_arr[$row[0]]."</a></td><td align=center>".$row[1]."</td><td align=center>".$row[2]."</td><td align=center>".$opl_voditel."</td><td align=center>".$ostatok."</td></tr>";
$itog_tochek = $itog_tochek+$row[1];
$itog_pko = $itog_pko + $row[2];
$itog_opl_vod = $itog_opl_vod+$opl_voditel;
$itog_ostatok = $itog_ostatok+$ostatok;
}
$log_info .= "<tr><td><strong>»того:</strong></td><td align=center><strong>".$itog_tochek."</strong></td><td align=center><strong>".$itog_pko."</strong></td><td align=center><strong>".$itog_opl_vod."</strong></td><td align=center>".$itog_ostatok."</td></tr>";

$log_info .= "</table>";}
return $log_info;
}

$date = date("Y-m-d",strtotime("-2 day"));
$date_formatted = date("d-m-Y",strtotime("-2 day"));
echo logistika_info($date, $date_formatted, "позавчера");

$date = date("Y-m-d",strtotime("-1 day"));
$date_formatted = date("d-m-Y",strtotime("-1 day"));
echo logistika_info($date, $date_formatted, "вчера");

$date = date("Y-m-d");
$date_formatted = date("d-m-Y");
echo logistika_info($date, $date_formatted, "сегодн€");

$date = date("Y-m-d",strtotime("+1 day"));
$date_formatted = date("d-m-Y",strtotime("+1 day"));
echo logistika_info($date, $date_formatted, "завтра");

$date = date("Y-m-d",strtotime("+2 day"));
$date_formatted = date("d-m-Y",strtotime("+2 day"));
echo logistika_info($date, $date_formatted, "послезавтра");
?>
<br><br>
<?
$tek_month = date("m");
$tek_year = date("Y");
?>
<b>Cтатистика по менеджерам:</b>
<select id=month_num_managers style="width: 150px; height: 35px; font-size: 16px;">
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
<select id=year_num_managers style="width: 80px; height: 35px; font-size: 16px;">
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
<option value="2023" <?if($tek_year=="2023"){echo " selected";}?>>2023</option>
<option value="2024" <?if($tek_year=="2024"){echo " selected";}?>>2024</option>
</select> <input type=submit value=">>" onclick="get_managers_stat()" style="width: 50px; height: 35px; font-size: 16px;">

<span id=managers_stat_span></span>
<br><br>
<b>ѕроверка логистики с </b>
<input type="date" id="date_from_logistic" style="width: 130px; height: 35px; font-size: 16px;" name="date_from_logistic" value="<?=date("Y-m-d",strtotime("-10 day"))?>"/>
по <input type="date" id="date_to_logistic" style="width: 130px; height: 35px; font-size: 16px;" name="date_to_logistic" value="<?=date("Y-m-d");?>"/>
<input type="button" value=">>" onclick="check_logistic()" style="width: 50px; height: 35px; font-size: 16px;"><br>
<span id="check_logistic_span"></span>
<span style="top:0px;right:0px;width:450px;height:55px;display:none;background-color:white;border:1px #3399CC solid;position:fixed;font-size:12px;" id="update_courier_status_span">OK</span>
<?
$tek_year = date("Y");
$tek_month = date("m");
?><br>

<br><br>

<? //долги по активным счетам за заданный срок



$pr_date = date("Y-m-d",strtotime("-3 month"));


function get_debt($from_date){
$tek_date = date("Y-m-d 23:59:59");
//переводим дату в нужный формат, под ссылку
$href_date = strtotime($from_date);
$href_date = date("d.m.Y", $href_date);

$vse_dolgi = mysql_query("SELECT SUM(prdm_sum_acc) - SUM(prdm_opl) FROM queries WHERE deleted = 0 AND client_id <> 0 AND date_query BETWEEN  '".$from_date." 00:00:00' AND '$tek_date'");
$vse_dolgi = mysql_fetch_array($vse_dolgi);
$vse_dolgi = round($vse_dolgi[0]);
echo "долги клиентов с <b>$href_date</b> <a href=\"http://crm.upak.me/acc/query/?from=$href_date&debt=1\" target=_blank><b>$vse_dolgi</b></a></a><br>";
//echo "SELECT SUM(prdm_sum_acc) - SUM(prdm_opl) FROM queries WHERE deleted <> 1 AND date_query BETWEEN  '".$from_date." 00:00:00' AND '$tek_date' AND deleted <> '1'<br>";

}


get_debt(date("Y-m-d",strtotime("-14 days")));
get_debt(date("Y-m-d",strtotime("-1 month")));
get_debt(date("Y-m-d",strtotime("-3 month")));

?>

<div style="cursor:move;height:25px; text-align: right;"><span style="font-size:16px;color:red;cursor:pointer;font-weight:bold; position:relative;right: 10px;" onclick="svodka_hide()">закрыть!</span></div>
 <br><br> <br><br> <br><br> 
</td></tr>
</table>


<?
 }
 //отправл€ем ежедневно сводку через хостинг
if($_GET["act"] !== "not_send_report"){

$vrem_txt = " (по сост. на ".date("H:i:s").")";


$segodnya = date("Y-m-d");
$tek_month = date("Y-m");

$tema = "ќтчет за ".$segodnya.$vrem_txt;


$bod = query_stat($segodnya, $segodnya." 23:59:59", "сегодн€", "", "0").query_stat($tek_month, "", "всего за€вок в <u>текущем</u> мес€це", "1", "0").logistika_info(date("Y-m-d"), date("d-m-Y"), "сегодн€");

send_mail($tema, $bod, '');


}

ob_end_flush(); ?>