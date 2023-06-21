<?
require_once("../includes/db.inc.php");

$uid = $_GET["uid"];
$ignore = $_GET["ignore"];

if(is_numeric($uid)){

 $q = mysql_query("UPDATE queries SET ignoreerror = '$ignore' WHERE uid = '$uid'");
 if($q == TRUE){echo "ok";}else{echo "error ".mysql_error();}
}


 ?>