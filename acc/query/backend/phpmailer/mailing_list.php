<?php
//require 'email_class.php';
require_once("/home/crmu660633/test.upak.me/docs/acc/query/backend/phpmailer/email_class.php");
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/db.inc.php");//помен€ть путь на боевом
//
$emails=new Emails(1);//1-режим отладки,0-обычный
//обращение за option
$sql="SELECT val FROM `options` WHERE `name`='title_email_booking_expires'";
$result = mysql_query($sql);
$row = mysql_fetch_row($result);
$title_email_ch=$row[0];
$sql="SELECT val FROM `options` WHERE `name`='body_email_booking_expires'";
$result = mysql_query($sql);
$row = mysql_fetch_row($result);
$body_email=$row[0];
$sql="SELECT val FROM `options` WHERE `name`='email_booking_expires_status'";
$result = mysql_query($sql);
$row = mysql_fetch_row($result);
$status_email=$row[0];
$sql="SELECT val FROM `options` WHERE `name`='email_booking_expires_hours'";
$result = mysql_query($sql);
$row = mysql_fetch_row($result);
$email_booking_expires_hours=$row[0];

//
if ($status_email==1){
//
$date = new DateTime();
$start_date=$date->format('Y-m-d');
$date->add(new DateInterval("PT{$email_booking_expires_hours}H"));//получим дату до которой вытаскиваем заказы
//echo $date->format('Y-m-d') . "</br>";
$end_data=$date->format('Y-m-d H:i:s');
echo $start_date." - ".$end_data;
//сам запрос на поиск кому отправить 
//booking_till>=DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY) старый вариант
$ysl='typ_ord=2  AND cl_em.status=1 AND shipped=0 AND prdm_opl<=0 AND prdm_num_acc =0 AND (courier_task_id=NULL OR courier_task_id=0) AND deleted=0 AND `booking_till` LIKE "'.$start_date.'%" AND booking_till<="'.$end_data.'" AND booking_till<>"0000-00-00" AND booking_notif_sent=0';
$left_ysl="LEFT JOIN clients cl ON cl.uid=client_id";
$left_ysl.=" LEFT JOIN client_email_status cl_em ON cl_em.client_id=qu.client_id";
$sql="SELECT qu.uid,qu.uniq_id,qu.booking_till,qu.client_id,cl_em.status,qu.typ_ord,qu.form_of_payment,qu.shipped,qu.prdm_num_acc,qu.courier_task_id,qu.deleted,qu.booking_till,qu.booking_notif_sent,cl.email FROM queries qu {$left_ysl} WHERE {$ysl}";
//$result = mysql_query("SELECT qu.uid,qu.uniq_id,qu.booking_till,qu.client_id,qu.typ_ord,qu.form_of_payment,qu.shipped,qu.prdm_num_acc,qu.courier_task_id,qu.deleted,qu.booking_till,qu.booking_notif_sent,cl.email FROM queries qu LEFT JOIN clients cl ON cl.uid=client_id WHERE {$ysl}");
echo $sql;
$result=mysql_query($sql);
$emails->log_save("SQL запрос вернул : ".mysql_num_rows($result));
//echo $emails->sms_list_sender();
$mas_setting=$emails->check_debug();

if ($mas_setting[1]==1){
	echo "<h3>{$mas_setting[0]}</h3>";
	echo $sql;
	$mas['top']="Ўаблон";
	$mas['title']=$title_email_ch;
		$mas['body']=$body_email;
		$mas['«а сколько часов смотреть']=$email_booking_expires_hours;
		$emails->vivod($mas);
		echo "-------------------------------</br>";
}
//$emails->pre_vid($mas);
//echo $emails->debug_ec();
while ($row = 	mysql_fetch_assoc($result)) {
	//echo "<pre>";
	//print_r($row);
	//echo "</pre>";
	$email=$row['email'];
	$uniq_id=$row['uniq_id'];
	$uid=$row['uid'];
	$date_order=explode("-",$row['booking_till']);
	$date_order=$date_order[2]."-".$date_order[1]."-".$date_order[0];
	$mas=array();
		if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
		{
			$body=str_replace("{UNIQ_ID}",$uniq_id,$body_email);
			$body=str_replace("{DATE}",$date_order,$body);
			$title_email=str_replace("{DATE}",$date_order,$title_email_ch);
			$mas['title']=$title_email;
			$mas['body']=$body;
			$mas['client_email']=$email;
				$emails->log_save("tip:1|{$title_email}|{$email}");
				
				$result_email=$emails->send_mail(1,$email,$title_email,$body);
				if ($result_email==1){
					$sql="UPDATE `queries` SET `booking_notif_sent`=1 WHERE `uid`={$uid}";
					//$result = mysql_query($sql);//сн€ть комментарий дл€ обновлени€ состо€ни€
					$mas['status']=true;
					$emails->log_save("ќтправлено успешно.({$email}) - OK");
					echo $emails->send_sms("79017922782","{$body}");
				}else{
					$emails->log_save('Email - error('.$email.')</br>');
					$mas['status']=false;
				}
			
		}else{
			echo 'Email - error некоректна€ почта</br>';
			echo "<b>ѕочта:</b> {$email} (если пусто,значит еЄ нет)</br>";
			$emails->log_save('Email - error('.$email.')</br>');
		}
		$emails->vivod($mas);
}
}else{
	echo "Email уведомлени€ отключены";
}
?>