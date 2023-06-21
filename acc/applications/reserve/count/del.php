<?
if ($_GET["pass"] == "4308"){
$j_uid = $_GET["j_uid"];
require_once("../../includes/db.inc.php");

$del = mysql_query("DELETE FROM job WHERE j_uid = '$j_uid'");

if ($del == true){echo "ok";}else{echo "error ".mysql_error();}
}else{echo "Пароль не верен!";}
?>