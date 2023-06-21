<?
if ($_GET["code"] == "fdsfds8fu883832ije99089fs"){
$uid = $_GET["uid"];
$num_sotr = $_GET["num_sotr"];
$num_ord = $_GET["num_ord"];
$job = $_GET["job"];
$num_of_work = $_GET["num_of_work"];
require_once("../../includes/db.inc.php");

$edit = mysql_query("UPDATE `job` SET `num_sotr`='$num_sotr',`num_ord`='$num_ord',`job`='$job',`num_of_work`='$num_of_work' WHERE `uid` = '$uid'");
//echo "UPDATE `job` SET `num_sotr`='$num_sotr',`num_ord`='$num_ord',`job`='$job',`num_of_work`='$num_of_work' WHERE `uid` = '$uid'";
if ($edit == true){echo "ok";}else{echo "error";echo mysql_error();}

}
?>