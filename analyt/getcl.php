 <html>

<head>
  <title>ABC отчет по клиентам</title>


</head>

<body>
<a href="index.php">¬ главное меню</a> | <a href="?act=do">¬ыгрузить клиентов</a>

<?
/*

- убрать ≈виных
- убрать свежих
- убрать дубликаты

название, контакт, телефон, количество заказов, дата последнего заказа, тип последнего заказа, содержание последнего заказа, id менеджера */
$act = $_GET["act"];

require_once("../acc/includes/db.inc.php");
if ($act == "do"){

 //date_format(q.date_query, '%d.%m.%Y')

$q = "SELECT c.uid, c.short, c.cont_pers, c.cont_tel, c.firm_tel, round(q.prdm_sum_acc, 0) FROM clients AS c, queries AS q WHERE q.date_query > '2012-01-01 00:00:00' AND q.prdm_sum_acc > 1000 AND c.uid = q.client_id GROUP BY c.uid ORDER BY q.date_query DESC LIMIT 0,30000";
$get = mysql_query($q);
echo mysql_error();
$fp = fopen('clients.csv', 'w');

while($g =  mysql_fetch_assoc($get)){

$uid = $g[uid];

//количество заказов
$num_orders = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM queries WHERE client_id = '$uid'"));
$g[] .= $num_orders[0];

//последний заказ
$last_order = mysql_fetch_array(mysql_query("SELECT date_format(date_query, '%d.%m.%Y'), typ_ord, uid FROM queries WHERE date_query > '2012-01-01 00:00:00' AND client_id = '$uid' ORDER BY date_query DESC LIMIT 0,1"));
$g[] .= $last_order[0];
$g[] .= $last_order[1];

$query_id = $last_order[2];


//содержание посл заказа   3 наибольших позиции
$last_order_info = mysql_query("SELECT name FROM obj_accounts WHERE query_id = '$query_id' ORDER BY num DESC LIMIT 0,3");

while($l = mysql_fetch_array($last_order_info)){
$last_ord_in .= $l[0].", ";
}
//echo $last_ord_in."<br>";
$g[] .= $last_ord_in;

fputcsv($fp, $g, ';');

$last_ord_in = "";
}

/*foreach ($g as $val) {

} */
echo mysql_error();
fclose($fp);

?>

 <a href="clients.csv">скачать файл</a>
               <? } ?>
                </body>

</html>
