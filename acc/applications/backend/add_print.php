<?
require_once("../../includes/db.inc.php");
$uid = $_GET["uid"];
$query = "SELECT printed FROM applications WHERE uid = '$uid'";
$res = mysql_query($query);
$r = mysql_fetch_array($res);
$printed_old = $r["printed"];
$printed_new = $printed_old + 1;
$update =  "UPDATE applications SET printed='$printed_new' WHERE uid = '$uid'";
mysql_query($update);
echo "<span style=\"font-family: arial; fint-size: 12;\">распечатан: <b>".$printed_new."</b></span>";


?>