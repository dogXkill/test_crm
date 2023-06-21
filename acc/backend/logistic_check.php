<?
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
if($user_id == '12' || $user_id == '11'){
$date_from_logistic = $_GET["date_from_logistic"];
$date_to_logistic = $_GET["date_to_logistic"];
$q = "SELECT SUM(a.cash_payment) AS cash_payment, SUM(a.opl_voditel) AS opl_voditel, SUM(a.cash_payment-a.opl_voditel) AS back, COUNT(*) AS tochek, b.name, b.id, date, Year(date) AS year, Month(date) AS month, Day(date) AS day FROM courier_tasks AS a, couriers AS b WHERE a.courier_id=b.id AND date BETWEEN '$date_from_logistic 00:00:00' AND '$date_to_logistic 23:59:59' GROUP BY b.id, Year(date),Month(date),Day(date) ORDER BY date DESC";
//echo "<br>".$q;
$log = mysql_query($q);
echo mysql_error();
echo "<table cellpadding=2 cellspacing=0 class=table_stat><tr><td align=center><b>Дата</b></td><td align=center><b>Имя водителя</b></td><td align=center><b>Сумма чеков</b></td><td align=center><b>Точек</b></td><td align=center><b>Вознагр. водителя</b></td><td align=center><b>Возврат</b></td><td align=center><b>Проверено</b></td><td align=center><b>Комент</b></td></tr>";
while($p = mysql_fetch_array($log)){
$year = $p[year];
$month = $p[month];
if($month < 10 && strlen ($month) == 1){$month = "0".$month;}
$day = $p[day];
$id=$p[id];
$name = $p[name];
$cash_payment = $p[cash_payment];
$cash_payment_total = $cash_payment_total+$cash_payment;
$tochek = $p[tochek];
$tochek_total = $tochek_total+$tochek;
$opl_voditel = $p[opl_voditel];
$opl_voditel_total = $opl_voditel_total + $opl_voditel;
$back = $p[back];
$back_total = $back_total + $back;
$idl = $year."-".$month."-".$day."-".$id;

$q = "SELECT * FROM courier_check WHERE id = '$idl'";
//echo $q;
$check = mysql_query($q);
if($check == true){
$check = mysql_fetch_array($check);}

$checked = $check[1];
if($checked == "1"){$checked_txt = "checked";}else{$checked_txt = "";}
$comment = $check[2];
echo "<tr><td><b>$day</b>.$month</td><td><a href=\"/acc/logistic/task_list.php?courier_id=$id&date=$day-$month-$year\" target=\"_blank\">$name</a></td><td>$cash_payment</td><td>$tochek</td><td>$opl_voditel</td><td>$back</td><td><input type=\"checkbox\" $checked_txt id=\"$idl-checked\" onchange=\"update_courier_check('$idl')\"/></td><td><input type=\"text\" value=\"$comment\" id=\"$idl-comment\" style=\"width:200px;\" onchange=\"update_courier_check('$idl')\"/></td></tr>";
}
echo "<tr><td></td><td></td><td>$cash_payment_total</td><td>$tochek_total</td><td>$opl_voditel_total</td><td>$back_total</td><td></td><td></td></tr>";

echo "</table>";



}
?>

