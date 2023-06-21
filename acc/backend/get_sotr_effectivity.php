<?
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
if($user_id == '12' || $user_id == '11' || $user_id == '199'){

$period = $_GET[period]+1;
$period_r = $_GET[period];
$doljnost = $_GET[doljnost];

//echo $period." ".$doljnost;

$year_num_eff = $_GET[year_num_eff];
$month_num_eff = $_GET[month_num_eff];

$tek_month = $year_num_eff."-".$month_num_eff;

$start_date = strtotime("$tek_month -$period month");
$start_date = date('Y-m', $start_date);

if($tek_month > date('Y-m')){echo "<br><div class=error>Выбранная месяц позже текщего. Выберите месяц/год, который раньше.</div>";}else{


if($tek_month == date('Y-m')){echo "<br><div class=warn>Вы анализируете текущий месяц. Для корректного анализа, все ЗП должны быть выплачены по ведомости, проставлены все часы, все сдельные оплаты и колнка прочее - заполнены.</div>";}

//$end_date = strtotime("$tek_month -1 month");
//$end_date = date('Y-m', $tek_month);


if ($month_num_eff == "01"){$month_text = "январь";}
if ($month_num_eff == "02"){$month_text = "февраль";}
if ($month_num_eff == "03"){$month_text = "март";}
if ($month_num_eff == "04"){$month_text = "апрель";}
if ($month_num_eff == "05"){$month_text = "май";}
if ($month_num_eff == "06"){$month_text = "июнь";}
if ($month_num_eff == "07"){$month_text = "июль";}
if ($month_num_eff == "08"){$month_text = "август";}
if ($month_num_eff == "09"){$month_text = "сентябрь";}
if ($month_num_eff == "10"){$month_text = "октябрь";}
if ($month_num_eff == "11"){$month_text = "ноябрь";}
if ($month_num_eff == "12"){$month_text = "декабрь";}


//для начала приводим состояние колонки дата в порядок
$data = mysql_query("UPDATE report2 SET date = concat_ws('-', year,month,'00')");
echo mysql_error();

$users_q = "SELECT job_id, name, surname FROM users WHERE doljnost = '$doljnost' AND nadomn <> '1' AND archive <> '1'";
$get_users = mysql_query($users_q);

echo mysql_error();
while($u =  mysql_fetch_assoc($get_users)){
$users[$u[job_id]] = $u[name]." ".$u[surname];
$uids[] .= $u[job_id];
}

$uids = implode(",",$uids);


echo "<h2>Месяц анализа <u>$month_text $year_num_eff</u></h2>период анализа с $start_date по $tek_month (не включительно) (выборка за последние $period_r мес.)";


echo mysql_error();
echo "<table cellpadding=2 cellspacing=0 class=stat_tbl border=1><tr><th>Имя</th><th>Всего выплачено</th><th>Сделка</th><th>Прочее</th><th>Окладная часть</th><th>Средний --> Текущий коэф.</th></tr>";

//получаем данные по текущему месяцу на конкретного сотрудника
$get_tek_stat_q = "SELECT uid,
(sdelka+procee)/(pay1+pay2+pay3+pay4+pay5+pay6+pay7-sdelka-procee) AS k
FROM report2
WHERE  '$tek_month' = date_format(date, '%Y-%m')
AND uid IN ($uids) GROUP BY uid ORDER BY k DESC";

//echo $get_tek_stat_q;

$get_tek_stat = mysql_query($get_tek_stat_q);

while($t =  mysql_fetch_assoc($get_tek_stat)){
$tek_m[$t[uid]] = round($t[k],2);
}
echo "<pre>";
//print_r($tek_m);
echo "</pre>";


$get_stat_q = "SELECT uid,
SUM(pay1+pay2+pay3+pay4+pay5+pay6+pay7) AS vyplata,
SUM(sdelka) AS sdelka,
SUM(procee) AS procee,
(SUM(sdelka)+SUM(procee))/(SUM(pay1+pay2+pay3+pay4+pay5+pay6+pay7)-SUM(sdelka)-SUM(procee)) AS k
FROM report2
WHERE date_format(date, '%Y-%m') < '$tek_month'
AND '$start_date' < date_format(date, '%Y-%m')
AND uid IN ($uids) GROUP BY uid ORDER BY k DESC";

//echo $get_stat_q;

$get_stat = mysql_query($get_stat_q);

while($s =  mysql_fetch_assoc($get_stat)){

$job_id = $s["uid"];
$vyplata = $s["vyplata"];
$sdelka = $s["sdelka"];
$procee = $s["procee"];
$oklad = $vyplata - $sdelka - $procee;
@$k = round(($sdelka+$procee)/$oklad,2);

$k_proc = round(($tek_m[$job_id]/$k-1)*100);
if($k_proc>0){$k_proc = "+".$k_proc; $sotr_eff_color = "green";}
else if ($k_proc==0){$k_proc = "";}
else{$sotr_eff_color = "red";}
$k_text = "$k --> <b>$tek_m[$job_id]</b> <sup style=\"color:$sotr_eff_color\"><small>$k_proc</small></sup>";



echo "<tr><td>$users[$job_id] ($job_id)</td><td>$vyplata</td><td>$sdelka</td><td>$procee</td><td>$oklad</td><td>$k_text</td></tr>";


}

echo "</table>";

}

}
?>