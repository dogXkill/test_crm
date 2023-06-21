<!DOCTYPE HTML>

<html>

<head>
  <title>Долги</title>
</head>

<body>
<?
require_once("../includes/db.inc.php");

$tek_date = date("Y-m-d h:i:s");
$pr_date = date("Y-m-d",strtotime("-3 month"));


if ($_GET["act"] == "dolgi_act"){
$dolgi = mysql_query("SELECT queries.prdm_sum_acc, queries.prdm_opl, queries.prdm_dolg, queries.client_id,  queries.date_query, clients.short, queries.uid
FROM queries, clients
WHERE  `date_query`
BETWEEN  '".$pr_date." 00:00:00'
AND  '$tek_date'
AND prdm_opl >  '0'
AND prdm_opl < ( prdm_sum_acc -100 )
AND queries.prdm_dolg > 100
AND queries.client_id = clients.uid");

$title = "Долги клиентов по заявкам выставленным за последние 3 месяца, где клиент совершил предоплату";
}

if ($_GET["act"] == "dolgi_act_pr"){
$dolgi = mysql_query("SELECT queries.prdm_sum_acc, queries.prdm_opl, queries.prdm_dolg, queries.client_id, queries.date_query, clients.short, queries.uid
FROM queries, clients
WHERE  `date_query`
BETWEEN  '".$pr_date." 00:00:00'
AND  '$tek_date'
AND prdm_sum_acc > prdm_opl
AND queries.client_id = clients.uid ORDER BY  queries.date_query DESC");

$title = "Долги клиентов по <u>всем</u> заявкам выставленным за последние 3 месяца";
}

echo "<h3>".$title."</h3>";

print "<table border=1 cellpadding=2 cellspacing=0 style=\"border:#0099CC solid 1px; font-family:arial\"><tr>
<td><b>Дата</b></td>
<td><b>Название клиента</b></td>
<td><b>Сумма счета</b></td>
<td><b>Оплачено</b></td>
<td><b>Долг</b></td></tr>";

while($row = mysql_fetch_array($dolgi)){
$dolg = $row[0]-$row[1];
$total_prdm_sum_acc = round($row[0])+$total_prdm_sum_acc;
$total_prdm_opl = round($row[1])+$total_prdm_opl;
$total_prdm_dolg = round($dolg)+$total_prdm_dolg;

echo "<tr><td>".$row[4]."</td><td><a href=\"query_send.php?show=".$row[6]."\" target=\"blank\">".$row[5]."</a></td><td align=center>".round($row[0])."</td><td align=center>".round($row[1])."</td><td align=center>".round($dolg)."</td></tr>";
}
echo "<tr><td><strong>Итого</strong></td><td></td><td align=center>".$total_prdm_sum_acc."</td><td align=center>".$total_prdm_opl."</td><td align=center>".$total_prdm_dolg."</td></tr>";

echo "</table>";
echo $total;
?>
</body>

</html>