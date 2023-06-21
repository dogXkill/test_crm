<?
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
$statistics_access = $user_access['statistics_access'];


if($statistics_access == 2){

$year_num = $_GET[year_num_managers];
$month_num = $_GET[month_num_managers];


//объявляем массив с типами заказов

$typ_ord_txt[1] = "заказ";
$typ_ord_txt[2] = "магазин";
$typ_ord_txt[3] = "магазин с лого";
$typ_ord_txt[4] = "перезаказ";
//массив с перечнем айдишек типов заказа
$typ_ord = array('1', '2', '3');



$procent[1] = "0.1";
$procent[2] .= "0.06";
$procent[3] .= "0.1";
$procent[4] .= "0.1";



//получаем текущий план

function get_plan_info($year_num,$month_num){
$plans = mysql_query("SELECT user_id, summ, prem, summ2, prem2 FROM plan_users WHERE year = '$year_num' AND month = '$month_num'");
while($p =  mysql_fetch_array($plans)){
$id = $p["user_id"];
$plan[$id][summ] .= $p["summ"];
$plan[$id][prem] .= $p["prem"];
$plan[$id][summ2] .= $p["summ2"];
$plan[$id][prem2] .= $p["prem2"];
}
return $plan;
}
$plan = get_plan_info($year_num,$month_num);
if ($month_num == "01"){$month_text = "январь";}
if ($month_num == "02"){$month_text = "февраль";}
if ($month_num == "03"){$month_text = "март";}
if ($month_num == "04"){$month_text = "апрель";}
if ($month_num == "05"){$month_text = "май";}
if ($month_num == "06"){$month_text = "июнь";}
if ($month_num == "07"){$month_text = "июль";}
if ($month_num == "08"){$month_text = "август";}
if ($month_num == "09"){$month_text = "сентябрь";}
if ($month_num == "10"){$month_text = "октябрь";}
if ($month_num == "11"){$month_text = "ноябрь";}
if ($month_num == "12"){$month_text = "декабрь";}
echo "<br><b>$month_text $year_num</b><br>";

echo "<table cellpadding=2 cellspacing=0 class=table_stat><tr><td align=center><b>Имя</b></td><td align=center><b>Сумма счетов</b></td><td align=center><b>Оплаты</b></td><td align=center><b>План</b></td><td align=center><b>М</b></td><td align=center><b>%М</b></td><td align=center><b>Прогноз %</b></td><td align=center><b>Тип заявки</b></td><td align=center><b>Количество</b></td><td align=center><b>Отмены</b></td></tr>";





$users_q = "SELECT u.uid, u.name, u.surname, p.summ FROM users AS u, plan_users AS p WHERE (u.user_department = '6' OR u.user_department = '3' OR u.user_department = '1' OR u.user_department = '2' OR u.user_department = '9') AND ((u.archive = 1 AND p.year = '$year_num' AND p.month = '$month_num') OR u.archive = 0) AND p.user_id = u.uid GROUP BY u.uid";
//echo $users_q;
$users = mysql_query($users_q);
while($u = mysql_fetch_array($users)){
$uid = $u[uid];
$name = $u[name];
$surname = $u[surname];

foreach ($typ_ord as $tp_ord){
/*
$qu = "SELECT SUM(ROUND(prdm_sum_acc)) AS prdm_sum_acc, SUM(ROUND(prdm_sum_acc - podr_sebist)) AS marja, SUM(ROUND(prdm_opl)) AS prdm_opl, typ_ord AS typ_ord, COUNT(*) AS num FROM queries WHERE deleted <> 1 AND date_query LIKE '$year_num-$month_num-%' AND user_id = '$uid' AND typ_ord='$tp_ord'";
 */
$vst =  "date_query LIKE '$year_num-$month_num-%' AND user_id = '$uid' AND typ_ord='$tp_ord'";
$qu = "SELECT q1.prdm_sum_acc, SUM(ROUND(q1.prdm_sum_acc - q1.podr_sebist)) AS marja, SUM(ROUND(q1.prdm_opl)) AS prdm_opl, q1.typ_ord, q1.num, q2.otmen AS otmen, q2.summa_otmen FROM
(SELECT date_query, SUM(ROUND(prdm_sum_acc)) AS prdm_sum_acc, SUM(podr_sebist) AS podr_sebist, SUM(ROUND(prdm_sum_acc - podr_sebist)) AS marja, SUM(ROUND(prdm_opl)) AS prdm_opl, COUNT(*) AS num, typ_ord, user_id FROM queries WHERE deleted = 0 AND client_id <> 0 AND ".$vst.")q1,
(SELECT COUNT(*) AS otmen, SUM(ROUND(prdm_sum_acc)) AS summa_otmen FROM queries WHERE deleted <> '0' AND ".$vst.")q2";

 // echo $qu;

//if($uid == '138'){echo $qu."<br>";}
//echo mysql_error();
$qu = mysql_query($qu);
$q = mysql_fetch_array($qu);
$tek_plan = $plan[$uid][summ];
$prdm_sum_acc = $q[prdm_sum_acc];
$prdm_opl = $q[prdm_opl];
$marja = $q[marja];
if($prdm_sum_acc>0){$marja_proc = round($marja/$prdm_sum_acc*100)."%";}

//перевод месяца и года в целое число

$year_num_c = $year_num * 1;
$month_num_c = $month_num * 1;

if($year_num_c > 2020 and $month_num_c > 6){
//определяем вознаграждение % манагеров после 14.07.2021
if($tp_ord == "1"){
//под заказ
$procent_man = round($marja * 0.1);
}
if($tp_ord == "2"){
//магазин
$procent_man = round($prdm_sum_acc * 0.01);
}
if($tp_ord == "3"){
//магазин с лого
$procent_man = round($prdm_sum_acc * 0.025);
}

}
else{
$procent_man = round($marja * $procent[$tp_ord]);
 }



$num = $q[num];

$otmen = $q[otmen];
$summa_otmen = $q[summa_otmen];
if($otmen > 0){$otmen_txt = "$otmen шт ($summa_otmen р.)";}


$prdm_sum_acc_total = $prdm_sum_acc_total + $prdm_sum_acc;
$prdm_opl_total = $prdm_opl_total + $prdm_opl;
if($prdm_sum_acc_total > 0 and $tek_plan > 0){
$proc_oplaty_total = round($prdm_opl_total * 100 / $tek_plan,1);
$proc_oplaty_total_txt = "($proc_oplaty_total%)";
$proc_acc_total = round($prdm_sum_acc_total * 100 / $tek_plan,1);
$proc_acc_total_txt = "($proc_acc_total%)";
}else{$proc_oplaty_total_txt=""; $proc_acc_total_txt = "";}
$marja_total = $marja_total + $marja;
$num_total = $num_total + $num;
$procent_total = $procent_total + $procent_man;
$otmen_total = $otmen_total + $otmen;
$summa_otmen_total = $summa_otmen_total + $summa_otmen;
if($otmen_total > 0){$otmen_total_txt = "$otmen_total шт ($summa_otmen_total р.)";}

//добавить количество и сумму отмен
//сразу расчет премий
echo "<tr><td>$surname $name</td><td>$prdm_sum_acc </td><td>$prdm_opl</td><td></td><td>$marja</td><td>$marja_proc</td><td>$procent_man</td><td>$typ_ord_txt[$tp_ord]</td><td>$num</td><td>$otmen_txt</td></tr>";

$prdm_sum_acc = "";
$prdm_opl = "";
$marja = "";
$marja_proc = "";
$procent_man = "";
$num = "";
$otmen  = "";
$summa_otmen = "";
$otmen_txt = "";
}

echo "<tr class=poditog><td>подитог</td><td>$prdm_sum_acc_total $proc_acc_total_tx $proc_acc_total_txt</td><td>$prdm_opl_total $proc_oplaty_total_txt</td><td><span id=\"pl_$month_num$year_num$uid\">план: $tek_plan</span><span onclick=\"change_plan('$uid', '$month_num', '$year_num', 'get_form')\"  style=\"cursor:pointer\"><img src=\"../../i/editbut.png\"></span></td><td>$marja_total</td><td></td><td>$procent_total</td><td></td><td>$num_total</td><td>$otmen_total_txt</td></tr>";
$tek_plan_itog = $tek_plan_itog + $tek_plan;
$prdm_sum_acc_itog = $prdm_sum_acc_total + $prdm_sum_acc_itog;
$prdm_opl_itog = $prdm_opl_total + $prdm_opl_itog;
if($prdm_sum_acc_itog > 0 and $tek_plan_itog > 0){
$proc_prdm_opl_itog = round($prdm_opl_itog * 100 / $tek_plan_itog,1);
$proc_oplaty_itog_txt = "($proc_prdm_opl_itog%)";
$proc_acc_itog = round($prdm_sum_acc_itog * 100 / $tek_plan_itog,1);
$proc_acc_itog_txt = "($proc_acc_itog%)";
}else{$proc_oplaty_itog_txt=""; $proc_acc_itog_txt = ""; }
$marja_itog = $marja_total + $marja_itog;
$num_itog = $num_total + $num_itog;
$procent_itog = $procent_total + $procent_itog;
$otmen_itog = $otmen_itog + $otmen_total;
$summa_otmen_itog = $summa_otmen_itog + $summa_otmen_total;
if($otmen_itog > 0){$otmen_itog_txt = "$otmen_itog шт ($summa_otmen_itog р.)";}

$prdm_sum_acc_total = "";
$prdm_opl_total = "";
$marja_total = "";
$num_total = "";
$procent_total = "";
$otmen_total = "";
$summa_otmen_total = "";
$otmen_total_txt = "";
}


echo "<tr class=poditog><td>ИТОГ</td><td>$prdm_sum_acc_itog $proc_acc_itog_txt</td><td>$prdm_opl_itog $proc_oplaty_itog_txt</td><td>$tek_plan_itog</td><td>$marja_itog</td><td></td><td>$procent_itog</td><td></td><td>$num_itog</td><td></td></tr>";



echo "</table>";


}else{echo "Доступ запрещен!";}


?>