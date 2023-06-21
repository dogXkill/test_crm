<?php
//
$auth = false;

require_once("../../../includes/db.inc.php");
require_once("../../../includes/auth.php");
require_once("../../../includes/lib.php");
$id=$_POST['id'];
$izd_type=$_POST['izd_type'];
$app_statuses = array("не принята", "заявка принята", "в работе", "требует внимания", "выполнена");
if ($id!=null && $id!=0){
	//application stamp_num(char)
	$query="SELECT *  FROM `applications` WHERE `izd_type` ={$izd_type} AND `stamp_num` = '{$id}'";
	//echo $query;
	$result=mysql_query($query);
	if (mysql_num_rows($result)>=1){
		while ($row=mysql_fetch_array($result)){
			//print_r($row);
			$app_status=$row['app_status'];
			$status=$app_statuses[$app_status];
			if ($row['title']){
				$title=$row['title'];
			}else{
				$title=$row['ClientName'];
			}
		echo "<p class='open_applist' data-id-list='{$row['uid']}'>".$row['num_ord']."&nbsp; {$title}  &nbsp; {$status} &nbsp; <i class='fa-solid fa-share' style='color:black'></i></p>";
		}
	}else{echo "Заявок не найдено";}
}else{echo "";}
?>