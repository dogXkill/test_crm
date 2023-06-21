<?php
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
$id_report=$_POST['id_report'];
$uid=$_POST['uid'];
$day=$_POST['day'];
$sql="DELETE FROM `report_day_comment` WHERE `report_day_comment`.`id_uid` = '{$uid}' AND `id_report`='{$id_report}' AND `report_day_comment`.`day`='{$day}'";
$result  = mysql_query($sql);
        if (!$result) {
            echo json_encode(['status' => 'error', 'error' => mysql_error()]);
            die();
        }else{
			$sql="UPDATE `report2` SET `pay{$day}` = '0' ,`pay{$day}date` = '0'  WHERE `report2`.`id` = '{$id_report}';";
			$result  = mysql_query($sql);
			echo json_encode(['status' => 'ok']);
            die();
		}
?>