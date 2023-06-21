<?php

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");

$name = '';
$result = [];
$userId = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : null;
$month = isset($_GET['month']) ? $_GET['month'] : null;
$year = isset($_GET['year']) ? $_GET['year'] : null;
if ($userId) {
    if ($action === 'take_amount') {
        // ��������� ����������� ����� �������
        $result  = mysql_query("SELECT SUM(amount) FROM user_other WHERE user_id = {$userId}  AND fine_month={$month} AND fine_year={$year}");
        if (!$result) {
            echo json_encode(['status' => 'error', 'error' => mysql_error()]);
            die();
        }
        $row = mysql_fetch_row($result);
        $result = ['status' => 'success', 'amount' => $row[0]];
    } else {
        $query = "SELECT id, reason, amount,user_id_added,date_add FROM user_other WHERE user_id = {$userId}  AND fine_month={$month} AND fine_year={$year}";
		//echo $query;
        $resource = mysql_query($query);
		if (mysql_num_rows($resource) > 0){
        while ($row = mysql_fetch_array($resource, MYSQL_ASSOC)) {
            
			//$i=count($result);
			$user_id_added=$row['user_id_added'];
			$dat_tmp=explode(" ",$row['date_add']);//0- дата ,1-время
			$dat_tmp1=explode("-",$dat_tmp[0]);
			$row['date_add']=$dat_tmp1[2].".".$dat_tmp1[1].".".$dat_tmp1[0]." ".$dat_tmp[1];
			
			 $query1 = "SELECT uid, surname, name,father FROM users WHERE uid  = {$user_id_added}";
			 //echo $query1;
				$resource1 = mysql_query($query1);
				while ($row1 = mysql_fetch_array($resource1, MYSQL_ASSOC)) {
					//$result[$i-1]['name'] = $row1['surname']." ".$row1['name']." ".$row1['father'];
					//$row['name']='test';//$row1['surname']." ".$row1['name']." ".$row1['father']
					$name=$row1['surname']." ".mb_substr($row1['name'],0,1).". ".mb_substr($row1['father'],0,1).".";
					
					$row['name']=iconv('windows-1251//IGNORE', 'UTF-8//IGNORE', $name);
				}
			$result[] = $row;
        }
		
		}
    }
}
//print_r($result);
echo json_encode($result);
die();
