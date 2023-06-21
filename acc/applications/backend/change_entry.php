<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
$uid = $_GET["job_uid"];
$act = $_GET["act"];
$changed_date = $_GET["changed_date"]." 01:01:01";
$changed_qty = $_GET["changed_qty"];


 if($act == "change_date")

 {
    $change_date = mysql_query("UPDATE job SET cur_time = '$changed_date' WHERE uid = '$uid'");

    if($change_date == TRUE){echo "OK";}else{echo "ошибка ".mysql_error();}
 }

 if($act == "change_qty")

 {
     if(is_numeric($changed_qty)){
    $change_qty = mysql_query("UPDATE job SET num_of_work = '$changed_qty' WHERE uid = '$uid'");
    }
    if($change_qty == TRUE){echo "OK";}else{echo "ошибка ".mysql_error();}
 }


?>