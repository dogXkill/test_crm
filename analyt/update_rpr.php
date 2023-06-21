<!DOCTYPE HTML>

<html>

<head>
  <title>Untitled</title>
</head>

<body>
  <form action="" method="get">
<? $query_id = $_GET["query_id"];  ?>
Проставить с/с в записях начиная со следующего номера заявки: <input type="text" value="<?=$query_id;?>" name="query_id" id="query_id"/>  <input type="submit" value="Проставить с/с" />
  </form>
<?

if($query_id){
require_once("../acc/includes/db.inc.php");

function get_all_types($key_clm, $tbl_clmns,$tbl_name,$where){
//если делаем выборку из всех полей, то подставляем звездочку иначе бьем перечен колонок на массив
if($tbl_clmns == "*"){$q = "SELECT * FROM $tbl_name $where";}else{$q = "SELECT $key_clm, $tbl_clmns FROM $tbl_name $where"; $tbl_clmns = explode(",", $tbl_clmns);}
$get = mysql_query($q);
while($g =  mysql_fetch_assoc($get)){
//ключ таблицы
$id = $g[$key_clm];
//если все колонки, то пишем построково, если не все, то пишем только названия колонок
if($tbl_clmns == "*"){$arr[$id] = $g;}else{foreach($tbl_clmns as $clm){if($clm !== "")$arr[$id][$clm] = $g[$clm];}}
}
//print_r ($arr);
return $arr;
}

$r_price_our_ar = get_all_types("art_id", "r_price_our, price","plan_arts","");
 echo "<pre>";
//print_r($r_price_our_ar);
 echo "</pre>";
//echo $r_price_our_ar[531][r_price_our]."<br>";

$q = "SELECT art_num, uid, price FROM obj_accounts WHERE art_num <> ''  AND query_id > '$query_id' ORDER BY query_id ASC";
$q = mysql_query($q);

//echo $r_price_our_ar[531][r_price_our]."<br>";

while($r = mysql_fetch_array($q)){
$r_price_our = $r_price_our_ar[$r[art_num]][r_price_our];
$price = $r[price];
$uid = $r[uid]; 
if($r_price_our == "0"){$r_price_our = $price*0.5;}

///удалить после исправления ошибки
if($r["art_num"] == 'd'){
$upd1 = "UPDATE obj_accounts SET r_price_our = '$price' WHERE uid = $uid";
echo "OK";
mysql_query($upd1);
}else{

$upd = "UPDATE obj_accounts SET r_price_our = '$r_price_our' WHERE uid = $uid";
mysql_query($upd);
$price = "";
}

echo $upd." ($price)<br>";
echo mysql_error();
//echo "<br>$uid $r[art_num] $price --> $r_price_our<br>";
}}
?>
</body>

</html>