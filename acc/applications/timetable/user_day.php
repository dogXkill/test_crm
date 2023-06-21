<?php
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");

$name = '';
$result = [];
$userId = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
$day = isset($_GET['day']) ? $_GET['day'] : null;
$month = isset($_GET['month']) ? $_GET['month'] : null;
if ($month<=9){
	if(stristr($month, '0') === FALSE) {
	$month='0'.$month;
	}
}

$year = isset($_GET['year']) ? $_GET['year'] : null;
$id=$year."-".$month."-".$userId;
if ($userId) {
		$sql="SELECT pay{$day},pay{$day}date FROM report2 WHERE uid = '{$userId}' AND id='{$id}' AND year='{$year}' AND month='{$month}' ";
        $result  = mysql_query($sql);
        if (!$result) {
            echo json_encode(['status' => 'error', 'error' => mysql_error()]);
            die();
        }else{
			$row = mysql_fetch_row($result);
		}
		$dats=explode("-",$row[1]);
		$row[1]=$dats[2].".".$dats[1].".".$dats[0];
		
		$id_report=$year."-".$month."-".$userId;
		$sql1="SELECT comment,tip_pay,id_user FROM report_day_comment WHERE id_uid = '{$userId}' AND id_report='{$id_report}' AND day='{$day}'  ";
		//echo $sql;
        $result1  = mysql_query($sql1);
		$tip_load=0;
		if (mysql_num_rows($result1) >= 1){
			$tip_load=1;
		}
        if ($result1) {
				$row1 = mysql_fetch_row($result1);
				$result = ['status' => 'success','uid'=>$userId, 'amount' => $row[0], 'date' => $row[1], 'comment' => $row1[0],'tip_pay'=>$row1[1],'id_acc'=>$row1[2],'sql1'=>$sql,'tip_load'=>$tip_load];
        }else{
			$result = ['status' => 'error', 'error' => mysql_error()];
		}
}
//print_r($result);
echo json_encode($result);
die();
