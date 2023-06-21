<?php
require_once("../includes/db.inc.php");
$art_id = $_GET["art_num"];
$g = mysql_query("SELECT uid, title, sklad, booked, col_in_pack, price, price_our, r_price_our, retail, retail_price FROM plan_arts WHERE art_id = '$art_id'");
$g = mysql_fetch_array($g);
echo mysql_error();
if ($g['uid']){
	echo $g['uid']."*;*".$g['title']."*;*".$g['sklad']."*;*".$g['booked']."*;*".$g['col_in_pack']."*;*".$g['price']."*;*".$g['price_our']."*;*".$g['r_price_our']."*;*".$g['retail']."*;*".$g['retail_price'];
} else {
	echo "no";
}

?>
