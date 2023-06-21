<?php
//поиск штампов по номеру и возвращает массив id штампов
require_once("../../includes/db.inc.php");
$mas_num_stamps=trim($_POST['num_stamps']);
if (strpos($mas_num_stamps, ",") != false) {//более 1
	$mas_num=explode(",",$mas_num_stamps);
	$result_id='';
	$result_num='';
	$error='';
	foreach ($mas_num as $value){
			$q = "SELECT * FROM stamps WHERE number = '$value'";
			$r = mysql_query ($q);
			$arr = mysql_fetch_assoc($r);
			if (empty($arr['id'])) {
				if ($error==''){$error.="{$value}";}else{$error.=",{$value}";}
			}else{
			if ($result_id==''){
					$result_num=$value;
					$result_id=$arr['id'];
				}else{
					$result_num.= ",".$value;
					$result_id.= ",".$arr['id'];
				}
			}
	}
	
}else{
		$q = "SELECT * FROM stamps WHERE number = '$mas_num_stamps'";
		$r = mysql_query ($q);
		$arr = mysql_fetch_assoc($r);
		if (empty($arr['id'])) {
			$result_id=0;
			$result_num=0;
			if ($error==''){$error.="{$value}";}else{$error.=",{$value}";}
		}else{
			$result_id=$arr['id'];
			$result_num=$mas_num_stamps;
		}
	
	
}
$mas['id']=$result_id;
$mas['num']=$result_num;
//if ($error!=''){$error='Штамп(ы)'.$error.' не найден';}
$mas['error']=$error;
echo json_encode($mas);
?>