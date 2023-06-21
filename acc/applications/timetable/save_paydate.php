<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");


$year = $_GET["year"];
$month = $_GET["month"];
$pay = $_GET["pay"];
$paydate = $_GET["paydate"];



$id=$year.$month."pay".$pay;

$update = mysql_query("INSERT INTO report (`id`, `year`, `month`, pay, paydate) VALUES('$id', '$year', '$month', '$pay', '$paydate') ON DUPLICATE KEY UPDATE id='$id', year='$year', month='$month', pay='$pay', paydate='$paydate'");

echo mysql_error();

if($update=="true"){echo "ok";}
else
{echo mysql_error();}

?>