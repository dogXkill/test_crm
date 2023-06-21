<html>

<head>
  <title>ABC отчет по клиентам</title>


</head>

<body>
<a href="index.php">¬ главное меню</a> | <a href="?act=do">—генерить ABC отчет по клиентам</a>
<?


/*
название клиента
количество заказов за выбранный период
оборот
средний заказ
маржа обща€


*/
$act = $_GET["act"];

require_once("../acc/includes/db.inc.php");
if ($act == "do"){


$date_from = "2015-01-01 00:00:00";
 //date_format(q.date_query, '%d.%m.%Y')

$q = "SELECT c.uid, c.short, c.dupl AS dupl, c.sphere, c.sphere_other FROM clients AS c, queries AS q WHERE q.date_query > '$date_from' AND q.typ_ord = '2' AND c.uid = q.client_id GROUP BY c.dupl ORDER BY c.dupl DESC LIMIT 0,1000000";
$get = mysql_query($q);
echo mysql_error();
$fp = fopen('clients_abc.csv', 'w');

while($g =  mysql_fetch_assoc($get)){

$uid = $g[uid];
$dupl = $g[dupl];
$short = $g[short];

//$g[] .= $short;

//количество
$num_ord = "0";
$vyr_total = "0";
$vyr_t = "0";
$num_orders = mysql_query("SELECT uid FROM clients WHERE dupl = '$dupl'");

while($n_o =  mysql_fetch_assoc($num_orders)){
$tek_uid = $n_o["uid"];
//количество заказов
$num = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM queries WHERE client_id = '$tek_uid' AND date_query > '$date_from' AND typ_ord = '2'"));
$num_ord = $num_ord + $num[0];


//выручка
$vur1 = mysql_fetch_assoc(mysql_query("SELECT ROUND(SUM(prdm_sum_acc)) AS sum FROM queries WHERE client_id = '$tek_uid' AND date_query > '$date_from' AND typ_ord = '2'"));
$vyr_t = $vyr_t + $vur1[sum];

if($last_order == ""){
//последний заказ
$ls = "SELECT date_format(date_query, '%d.%m.%Y') AS last_order FROM queries WHERE dupl = '$dupl' AND date_query > '$date_from' AND typ_ord = '2' ORDER BY date_query DESC LIMIT 0,1";
$last_order = mysql_fetch_assoc(mysql_query($ls));
$last_order = $last_order[last_order];
//echo "$ls<br>";
}
}

$g[] .= $num_ord;
$g[] .= $vyr_t;
$g[] .= $last_order;
fputcsv($fp, $g, ';');
$last_order = "";
}

echo mysql_error();
fclose($fp);
?>
 <br><br><a href="clients_abc.csv">скачать файл</a>

 <? }
?>
 </body>

</html>
