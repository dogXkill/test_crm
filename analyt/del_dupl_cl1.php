<html>

<head>
  <title>Удаление дубликатов</title>

</head>

<body>

<?
$act = $_GET["act"];
require_once("../acc/includes/db.inc.php");
?>

<a href="index.php">В главное меню</a> | <a href="?act=clean_dupl">Очистить колонку dupl</a> | <a href="?act=search_dupl">Поиск дубликатов</a> | <a href="?act=dupl_queries">Проставить номера клиентов в табл queries</a>

<?

if($act == "clean_dupl"){ $clean = mysql_query("UPDATE clients SET dupl = '0'"); if($clean == true){echo "<b>Колонка dupl очищена!</b>";} }



if($act == "search_dupl"){
$cnt = "0";

$q = "SELECT uid, short, name, legal_address, postal_address, deliv_address, inn,  cont_tel, firm_tel, email, ogrn
FROM clients WHERE 1 ORDER BY uid DESC LIMIT 0,20000";
$get = mysql_query($q);



while($g =  mysql_fetch_assoc($get)){

$uid = $g[uid];
$short = $g[short];
$name = mysql_real_escape_string($g[name]);
$legal_address = mysql_real_escape_string($g[legal_address]);
$deliv_address = mysql_real_escape_string($g[deliv_address]);
$inn = $g[inn];
$cont_tel = mysql_real_escape_string($g[cont_tel]);
$firm_tel = mysql_real_escape_string($g[firm_tel]);
$email = $g[email];

$update = mysql_query("UPDATE clients SET dupl = '$uid' WHERE uid = '$uid'");

if($update == true){$cnt = $cnt + 1;}

$q2 = "SELECT uid, short, name, legal_address, postal_address, deliv_address, inn, cont_tel, firm_tel, email, ogrn, dupl
FROM clients
WHERE
(email = '$email' AND CHARACTER_LENGTH(email) > '9')

OR

(((name = '$name' AND CHARACTER_LENGTH(name) > '3') OR (short = '$short' AND CHARACTER_LENGTH(short) > '3') ) AND ( (legal_address = '$legal_address' AND CHARACTER_LENGTH(legal_address) > '15') OR (postal_address = '$postal_address' AND CHARACTER_LENGTH(postal_address) > '15') OR (deliv_address = '$deliv_address' AND CHARACTER_LENGTH(deliv_address) > '15')))


OR

((inn = '$inn' AND CHARACTER_LENGTH(inn) > '9') AND ((name = '$name' AND CHARACTER_LENGTH(name) > '3') OR (short = '$short' AND CHARACTER_LENGTH(short) > '3')))

OR (ogrn = '$ogrn' AND CHARACTER_LENGTH(ogrn) > '13')
OR (firm_tel = '$firm_tel' AND CHARACTER_LENGTH(firm_tel) > '11')
OR (cont_tel = '$cont_tel' AND CHARACTER_LENGTH(cont_tel) > '11')
AND dupl = '0' AND uid <> '$uid' ORDER BY uid DESC";
$get2 = mysql_query($q2);
echo mysql_error();

while($g2 =  mysql_fetch_assoc($get2)){
$uid2 = $g2[uid];
$update1 = mysql_query("UPDATE clients SET dupl = '$uid' WHERE uid = '$uid2'");
//$ids .= ",$uid2";
if($update1 == true){$cnt1 = $cnt1 + 1;}
}
$uid = "";
$ui2 = "";
}

echo "исправлено <b>$cnt</b> записей, у <b>$cnt1</b> записей найдены 1 или более дубликата<br>";
echo mysql_error();
}

 if ($act == "dupl_queries"){
$cnt = "0";
$select = mysql_query("SELECT uid, client_id FROM queries");
while($d =  mysql_fetch_assoc($select)){
$client_id = $d[client_id];
$uid = $d[uid];
$sel = mysql_fetch_assoc(mysql_query("SELECT dupl FROM clients WHERE uid = '$client_id' LIMIT 0,1"));
$dupl = $sel[dupl];
$upd = mysql_query("UPDATE queries SET dupl = '$dupl' WHERE uid = '$uid'");
if($upd == true){$cnt = $cnt + 1;}

}
echo "исправлено <b>$cnt</b> записей<br>";

 }
?>
</body>
</html>
