<?
if($_GET["code"] == "fdsfds8fu883832ije99089fs"){
include_once($_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php');

$art_id = $_GET["art_id"];
//echo $art_id;
$get_uid = mysql_query("SELECT title, col_in_pack FROM shop_goods WHERE art_id = '$art_id'");
$get_uid = mysql_fetch_array($get_uid);
echo mysql_error();

echo $get_uid['title'].";".$get_uid['col_in_pack'];


}
 ?>