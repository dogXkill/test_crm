<?php

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
$day_uid=$_POST['datas'][0];
$day_id_report=$_POST['datas'][1];
$razb=explode("-",$day_id_report);
$year=$razb[0];
$month=$razb[1];
if ($month<=9){
	if(stristr($month, '0') === FALSE) {
	$month='0'.$month;
	}
}
$day_id_report=$year."-".$month."-".$razb[2];
$day=$_POST['datas'][2];
$day_summa=$_POST['datas'][3];
$day_date=$_POST['datas'][4];
$razb1=explode(".",$day_date);
//print_r($razb1);
$day_date=$razb1[2]."-".$razb1[1]."-".$razb1[0];
$day_sp_pay=$_POST['datas'][5];
$comment_day=$_POST['datas'][6];
$userIdAdded = intval($user_access['uid']);
//Обновляем/создаем сумму и дату в report
	$sql="SELECT pay{$day},pay{$day}date FROM report2 WHERE uid = {$day_uid} AND id='{$day_id_report}' AND year='{$year}' AND month='{$month}';";
	$result  = mysql_query($sql);
        if (mysql_num_rows($result) <= 0){
			//создаем
			$res['save']=1;
		}else{
			//изменяем
			$res['save']=0;
			$sql="UPDATE `report2` SET `pay{$day}` = '{$day_summa}',`pay{$day}date` = '{$day_date}' WHERE `report2`.`id` = '{$day_id_report}';";
			$result  = mysql_query($sql);
				if (!$result) {
					echo json_encode(['status' => 'error', 'error' => mysql_error()]);
					die();
				}
		}
//обновляем/создаем комментарий и тип оплаты
$sql="SELECT * FROM report_day_comment WHERE id_report = '{$day_id_report}' AND id_uid='{$day_uid}' AND day='{$day}' ";
	$result  = mysql_query($sql);
        if (mysql_num_rows($result) <= 0){
			//создаем
		$sql="INSERT INTO `report_day_comment` (`id`, `id_report`, `id_uid`, `day`, `tip_pay`, `comment`, `id_user`) VALUES ('', '{$day_id_report}', '{$day_uid}', '{$day}', '{$day_sp_pay}', '{$comment_day}','{$userIdAdded}');";
			$result  = mysql_query($sql);
				if (!$result) {
					echo json_encode(['status_comment_cre' => 'error', 'error' => mysql_error(),'sql'=>$sql]);
					die();
				}
		}else{
			//изменяем
			$sql="UPDATE `report_day_comment` SET `comment` = '{$comment_day}' , `tip_pay` = {$day_sp_pay}  WHERE `report_day_comment`.`id_report` = '{$day_id_report}' AND `id_uid` = '{$day_uid}' AND `day` = '{$day}' ;";
			$result  = mysql_query($sql);
				if (!$result) {
					echo json_encode(['status_comment_upd' => 'error', 'error' => mysql_error()]);
					die();
				}
		}
echo json_encode($res);
	
die();
?>