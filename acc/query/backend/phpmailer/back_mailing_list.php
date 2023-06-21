<?php
//error_reporting(E_STRICT | E_ALL);
//echo $_SERVER['DOCUMENT_ROOT'];
date_default_timezone_set('Etc/UTC');
function log_save($text){
	$log = date('Y-m-d H:i:s') . ' - '.$text;
file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);

}
log_save("Обращение к файлу - ".date("d.m.Y H:i")."\r\n");

require_once("/home/crmu660633/test.upak.me/docs/acc/query/backend/phpmailer/PHPMailerAutoload.php");//поменять путь на боевом
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/db.inc.php");//поменять путь на боевом
//обращение за option
$sql="SELECT val FROM `options` WHERE `name`='title_email_booking_expires'";
$result = mysql_query($sql);
$row = mysql_fetch_row($result);
$title_email=$row[0];
$sql="SELECT val FROM `options` WHERE `name`='body_email_booking_expires'";
$result = mysql_query($sql);
$row = mysql_fetch_row($result);
$body_email=$row[0];
//настройка почты
$mail = new PHPMailer;//класс для отправки email
//отладка
//$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->setLanguage('en', '/home/crmu660633/crm.upak.me/docs/acc/query/backend/phpmailer/language/');
//настройки
$mail->CharSet = 'Windows-1251';
$mail->isHTML();
$mail->Host = 'ssl://smtp.rambler.ru';
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'ssl';  
$mail->Timeout = 20;
//$mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent, reduces SMTP overhead
$mail->Port = 465;
$mail->Username = 'tutaev9@lenta.ru';
$mail->Password = 'Slavok1998';
$mail->setFrom('tutaev9@lenta.ru', $title_email);
//тут будет подключение для телефон. рассылок

//
//сам запрос на поиск кому отправить 
$ysl='typ_ord=2 AND (form_of_payment=2 OR form_of_payment=3 OR form_of_payment=4) AND shipped=0 AND prdm_num_acc =0 AND (courier_task_id=NULL OR courier_task_id=0) AND deleted=0 AND booking_till=DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY) AND booking_till<>"0000-00-00" AND booking_notif_sent=0';
$sql="SELECT qu.uid,qu.uniq_id,qu.booking_till,qu.client_id,qu.typ_ord,qu.form_of_payment,qu.shipped,qu.prdm_num_acc,qu.courier_task_id,qu.deleted,qu.booking_till,qu.booking_notif_sent,cl.email FROM queries qu LEFT JOIN clients cl ON cl.uid=client_id WHERE {$ysl}";
//$result = mysql_query("SELECT qu.uid,qu.uniq_id,qu.booking_till,qu.client_id,qu.typ_ord,qu.form_of_payment,qu.shipped,qu.prdm_num_acc,qu.courier_task_id,qu.deleted,qu.booking_till,qu.booking_notif_sent,cl.email FROM queries qu LEFT JOIN clients cl ON cl.uid=client_id WHERE {$ysl}");
$result=mysql_query($sql);
//echo $sql;

log_save("SQL запрос вернул : ".mysql_num_rows($result));
//обработка результата и отправка уведомлений
while ($row = 	mysql_fetch_assoc($result)) {
	//echo "<pre>";
	//print_r($row);
	//echo "</pre>";
	$email=$row['email'];
	$uniq_id=$row['uniq_id'];
	$uid=$row['uid'];
		if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
		{
			$mail->Subject =$title_email;
			$body=str_replace("{UNIQ_ID}",$uniq_id,$body_email);
			//echo $body;
			//$mail->Body    = "Содержание сообщения";
			$mail->MsgHTML($body);
			
			echo $email."</br>";
			$mail->addAddress($email);//меняем на адрес с записи $email и имя , $title_email
			//$mail->addAddress("tutaev9@yandex.ru");
			//$mail->addAddress("slavok22222@mail.ru");
			//$mail->addAddress("slavok600@gmail.com");
			//$mail->addAddress("pavel@moskvin.ru"); 
			//msgHTML also sets AltBody, but if you want a custom one, set it afterwards
			//$mail->Body = "<i>Mail body in HTML</i>";
			$mail->AltBody = $body;
			//echo "</br>$title_email </br> $body</br>";
			if (!$mail->send()) {
				//уведомление не отправлено
				//echo 'error...'.$mail->ErrorInfo;
				log_save($mail->ErrorInfo);
			}else{
				//изменяем booking_notif_sent =1
				$sql="UPDATE `queries` SET `booking_notif_sent`=1 WHERE `uid`={$uid}";
				//$result = mysql_query($sql);//снять комментарий для обновления состояния
				//echo $sql;
				//echo "Отправлено и изменено";
				log_save("Отправлено успешно.");
			}
			
			
		}
		else
		{
		 echo 'Email - error('.$email.')</br>';
		  log_save('Email - error('.$email.')</br>');
		}
		//echo $row['uid']."</br>";
		// Clear all addresses and attachments for next loop
		$mail->clearAddresses();
		$mail->clearAttachments();
	
}
/*
for ($i=0;$i<2;$i++){
	$mail->addAddress("tutaev9@yandex.ru");
	$mail->Subject = 'Тест';                         // тема письма
	// html текст письма
	$mail->msgHTML("<html><body>
					<h1>Здравствуйте!</h1>
					<p>Это тестовое письмо.</p>
					</html></body>");
	// Отправляем
	if ($mail->send()) {
	  echo 'Письмо отправлено!';
	} else {
	  echo 'Ошибка: ' . $mail->ErrorInfo;
	}  
}
*/