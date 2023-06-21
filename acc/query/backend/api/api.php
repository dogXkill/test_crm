<?php

require_once("/home/crmu660633/test.upak.me/docs/acc/includes/db.inc.php");

if (isset($_GET['num_sklad'])){
	//если пришло num_sklad ,берем uid и изменяем зн-ие
	$g_id=$_GET['uid'];
	$num_sklad=$_GET['num_sklad'];
	$sql_update_num="UPDATE `plan_arts` SET `num_sklad` = '{$num_sklad}' WHERE `plan_arts`.`uid` = {$g_id};";
	$res=mysql_query($sql_update_num);
	
	echo $res;
}
?>