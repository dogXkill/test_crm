<?php
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
$id=$_POST['id_acc'];
$sql="SELECT * FROM `users` WHERE `uid` = {$id}";

 $result  = mysql_query($sql);
        if (!$result) {
           echo '-';
            die();
        }
		$row = mysql_fetch_row($result);
		if (mysql_num_rows($result) <= 0){
			echo '-';//не найден
		}else{
		echo $row[5]." ".$row[6]." ".$row[7];//ФИО
		}