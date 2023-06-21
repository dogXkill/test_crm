<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");

$uid = $_GET["uid"];
$year = $_GET["year"];
$month = $_GET["month"];
$work_time = $_GET["work_time"];
$oklad = $_GET["oklad"];
$socoklad = $_GET["socoklad"];
$sdelka = $_GET["sdelka"];
$procee = $_GET["procee"];
$pay1 = $_GET["pay1"];
$pay2 = $_GET["pay2"];
$pay3 = $_GET["pay3"];
$pay4 = $_GET["pay4"];
$pay5 = $_GET["pay5"];
$pay6 = $_GET["pay6"];
//ордера
$pay7 = $_GET["pay7"];

$pay1date = $_GET["pay1date"];
$pay2date = $_GET["pay2date"];
$pay3date = $_GET["pay3date"];
$pay4date = $_GET["pay4date"];
$pay5date = $_GET["pay5date"];
$pay6date = $_GET["pay6date"];
$pay7date = $_GET["pay7date"];
//$working_days = $_GET["working_days"];


$id=$year."-".$month."-".$uid;

$update = mysql_query("INSERT INTO report2 (`id`, `uid`, `year`, `month`, `work_time`, `oklad`, `socoklad`, `sdelka`, `procee`, `pay1`, `pay1date`, `pay2`, `pay2date`, `pay3`,`pay3date`, `pay4`, `pay4date`, `pay5`, `pay5date`, `pay6`, `pay6date`, `pay7`, `pay7date`) VALUES('$id', '$uid', '$year', '$month', '$work_time', '$oklad', '$socoklad', '$sdelka', '$procee', '$pay1', '$pay1date', '$pay2', '$pay2date', '$pay3', '$pay3date', '$pay4', '$pay4date', '$pay5', '$pay6date', '$pay6', '$pay6date', '$pay7', '$pay7date') ON DUPLICATE KEY UPDATE year='$year', month='$month', work_time='$work_time', oklad='$oklad', socoklad='$socoklad', sdelka='$sdelka', procee='$procee', pay1='$pay1', pay2='$pay2', pay3='$pay3', pay4='$pay4', pay5='$pay5', pay6='$pay6', pay7='$pay7',  pay1date='$pay1date', pay2date='$pay2date', pay3date='$pay3date', pay4date='$pay4date', pay5date='$pay5date', pay6date='$pay6date', pay7date='$pay7date'");

echo mysql_error();

if($update=="true"){echo "ok".mysql_error();}
else
{echo mysql_error();}

?>