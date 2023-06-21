<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
$year = $_GET["year"];
$month = $_GET["month"];
$day = $_GET["day"];
$hours = $_GET["hours"];
$uid = $_GET["uid"];

//$id=$year."-".$month."-".$day."-".$uid;

$check_exist = mysql_query("SELECT * FROM timetable WHERE year = '$year' AND month = '$month' AND day = '$day' AND uid = '$uid'");
$num_rows = mysql_num_rows($check_exist);

if($num_rows == "1"){
$update = mysql_query("UPDATE timetable SET hours = '$hours' WHERE year = '$year' AND month = '$month' AND day = '$day' AND uid = '$uid'");
if($update=="true"){echo "ok";}
}
else if($num_rows == "0"){
$insert = mysql_query("INSERT INTO timetable (uid,year,month,day,hours) VALUES('$uid','$year','$month','$day','$hours')");
if($insert=="true"){echo "ok";}
}
else {echo "SELECT * FROM timetable WHERE year = '$year' AND month = '$month' AND day = '$day' AND uid = '$uid' ошибка: запись с такими же параметрами присутствует больше 1 раза".$num_rows;}
echo mysql_error();
/*
$id=$year."-".$month."-".$day."-".$uid;

$update = mysql_query("INSERT INTO timetable (id,uid,year,month,day,hours) VALUES('$id','$uid','$year','$month','$day','$hours') ON DUPLICATE KEY UPDATE uid='$uid',year='$year',month='$month',day='$day',hours='$hours'");

echo mysql_error();

if($update=="true"){echo $id."ok";}
else
{echo mysql_error();}
*/
?>
