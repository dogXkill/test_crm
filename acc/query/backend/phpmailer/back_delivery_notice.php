<?php
//error_reporting(E_STRICT | E_ALL);
//echo $_SERVER['DOCUMENT_ROOT'];
  /*----------------------------------/
 /---------SYSTEM--------------------/
/----------------------------------*/
function log_save($text){
	$log = date('Y-m-d H:i:s') . ' - '.$text;
	file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
}
//log_save("Обращение к файлу - ".date("d.m.Y H:i")."\r\n");
function pre_vid($mas){
	echo "<pre>";
	print_r($mas);
	echo "</pre>";
}
function vivod($mas){
	echo "<div style='border:1px solid black;padding:5px;margin-top:5px;'>";
	foreach ($mas as $k=>$value){
		echo "<b>{$k}</b> {$value}</br>";
	}
	echo "</div>";
}
date_default_timezone_set('Etc/UTC');
require_once("/home/crmu660633/test.upak.me/docs/acc/query/backend/phpmailer/PHPMailerAutoload.php");//поменять путь на боевом
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/db.inc.php");//поменять путь на боевом

  /*----------------------------------/
 /---------обращение за option-------/
/----------------------------------*/
//title_email
$sql="SELECT val FROM `options` WHERE `name`='title_email_delivery_notice'";
$result = mysql_query($sql);
$row = mysql_fetch_row($result);
$title_email_ch=$row[0];
//body_email
$sql="SELECT val FROM `options` WHERE `name`='body_email_delivery_notice'";
$result = mysql_query($sql);
$row = mysql_fetch_row($result);
$body_email_ch=$row[0];
//за какое время смотреть
$sql="SELECT val FROM `options` WHERE `name`='delivery_hours'";
$result = mysql_query($sql);
$row = mysql_fetch_row($result);
$delivery_hours=$row[0];
//email setting
//настройка почты
$mail = new PHPMailer;//класс для отправки email
//отладка
//$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->setLanguage('en', '/home/crmu660633/test.upak.me/docs/acc/query/backend/phpmailer/language/');
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
//----
/*echo "|---------------------------------|</br>
|---------ШАБЛОН&nbsp;----------|</br>
|---------------------------------|</br>";*/
//
//echo "<b>TITLE:</b>{$title_email_ch}</br><b>BODY:</b>{$body_email_ch}</br><b>За сколько часов смотреть:</b>{$delivery_hours}</br>";
$mas['title']=$title_email_ch;
		$mas['body']=$body_email_ch;
		$mas['За сколько часов смотреть']=$delivery_hours;
		//vivod($mas);
  /*----------------------------------/
 /---------Обработка заказов---------/
/----------------------------------*/
$date = new DateTime();
$start_date=$date->format('Y-m-d');
$date->add(new DateInterval("PT{$delivery_hours}H"));//получим дату до которой вытаскиваем заказы
//echo $date->format('Y-m-d') . "</br>";
$end_data=$date->format('Y-m-d');

$lim="20";
//массив id для исключения из выборки(ОФИС/СДЭК и прочие)
$mas_courier_id_minus=array(
35,38
);
$sql_1="courier_id !={$mas_courier_id_minus[0]}";
$courier_task=array();
foreach ($mas_courier_id_minus as $value){
	$sql_1.=" AND courier_id !={$value}";
}
$sql = "
SELECT
  t.id,
  t.cash_payment,
  t.opl_voditel,
  t.courier_id,
  COALESCE(c.name, 'Удален') AS courier,
  COALESCE(CONCAT(u.surname, ' ', u.name, ' ', u.father), 'Удален') AS `user`,
  DATE_FORMAT(t.date, '%d-%m-%Y') AS date,
  t.text,
  q.uid AS query_id,
  t.address,
  t.contact_name,
  t.contact_phone,
  t.comment,
  t.first_point,
  c.auto_number,
  c.passport2,
  c.phone,
  cl.email,
  q.deliveri_notif_sent,
  ROUND((792 - t.map_x) / 4 - 8) AS map_x,
  ROUND((784 - t.map_y) / 4 - 7) AS map_y
FROM
  courier_tasks AS t
  LEFT JOIN couriers AS c ON t.courier_id = c.id
  LEFT JOIN users AS u ON t.user_id = u.uid
  LEFT JOIN queries AS q ON t.id = q.courier_task_id
  LEFT JOIN clients AS cl ON cl.uid = q.client_id
WHERE
  t.done = 0 AND date>='{$start_date}' AND date<='{$end_data}' AND ({$sql_1}) AND q.deliveri_notif_sent=0
ORDER BY
  t.date DESC
  LIMIT 0, $lim
  ";
 //echo $sql;
if($result = mysql_query($sql))
{
  $num = 2;

  while($row = mysql_fetch_assoc($result))
  {
    $row["opl_voditel"] = $row["opl_voditel"];
    $row["cash_payment"] = $row["cash_payment"];
    $row["courier"] = $row["courier"];
    $row["user"] = $row["user"];
    $row["text"] = $row["text"];
    $row["contact_name"] = $row["contact_name"];
    $row["contact_phone"] = $row["contact_phone"];
    $row["address"] = $row["address"];
    $row["comment"] = nl2br($row["comment"]);
    $row["first_point"] = $row["first_point"];

	//$query_id = $_GET["query_id"];

	$courier_task_id = $row["id"];
	$sql2="SELECT uid AS query_id, prdm_sum_acc AS prdm_sum_acc, form_of_payment AS form_of_payment, prdm_dolg AS prdm_dolg,deliveri_notif_sent FROM queries WHERE courier_task_id = '$courier_task_id' ";
	//echo $sql2."</br>";
	$get_order_info = mysql_query($sql2);

	$ord_row = mysql_fetch_array($get_order_info);

	$row["form_of_payment"] = $ord_row["form_of_payment"];
	$row["prdm_sum_acc"] = $ord_row["prdm_sum_acc"];
	$row["prdm_dolg"] = $ord_row["prdm_dolg"];
	$row["query_id"] = $ord_row["query_id"];

    if($num == 2)
    {
      $row["num"] = 1;
      $num = 1;
    }
    else
    {
      $row["num"] = 2;
      $num = 2;
    }
	//pre_vid($row);
	$courier_task[]=$row;
  }
  log_save("SQL запрос вернул : ".mysql_num_rows($result));
  mysql_free_result($result);
}

  /*----------------------------------/
 /---------создание шаблона----------/
/----------------------------------*/
/*echo "</br>|---------------------------------|</br>
|---------ВЫВОД-------------|</br>
|---------------------------------|</br></br>";*/
//pre_vid($courier_task);
if (count($courier_task)>=1){
	foreach ($courier_task as $value){
		//pre_vid($value);
		
		$number_order=$value['query_id'];
		$title_email=$title_email_ch;
		$body_email=$body_email_ch;
		$title_email=str_replace("{ORDER_NUM}",$number_order,$title_email);
		
		$date_delivery=$value['date'];
		$phone_driver=$value['phone'];
		$car_model=$value['auto_number'];
		$name_driver=$value['courier'];
		$model_car=$value['passport2'];
		$body_email=str_replace("{ORDER_NUM}",$number_order,$body_email);
		$body_email=str_replace("{DATE_DELIVERY}",$date_delivery,$body_email);
		$body_email=str_replace("{PHONE_DRIVER}",$phone_driver,$body_email);
		if (!empty($model_car)){$body_email=str_replace("{MODEL_CAR}",$model_car,$body_email);}else{$body_email=str_replace("{MODEL_CAR}","",$body_email);}
		$body_email=str_replace("{NUMBER_CAR}",$car_model,$body_email);
		$body_email=str_replace("{NAME_DRIVER}",$name_driver,$body_email);
		
		//echo $title_email."</br>";
		//echo $body_email."</br>";
		$email_client=$value['email'];
		$mas=array();
		$mas['title']=$title_email;
		$mas['body']=$body_email;
		$mas['client_email']=$email_client;
		//vivod($mas);
		if (filter_var($email_client, FILTER_VALIDATE_EMAIL) !== false)
		{
			//$mail->setFrom('tutaev9@lenta.ru', $title_email);
			$mail->Subject =$title_email;
			$mail->clearAddresses();
			$mail->addAddress("tutaev9@yandex.ru");
			//$mail->addAddress("pavel@moskvin.ru"); 
			$mail->Body = "<i>Mail body in HTML</i>";
			$mail->AltBody = $body_email;
			$mail->MsgHTML($body_email);
			/*
			if (!$mail->send()) {
				//уведомление не отправлено
				//echo 'error...'.$mail->ErrorInfo;
				log_save($mail->ErrorInfo);
			}else{
				//изменяем booking_notif_sent =1
				$sql="UPDATE `queries` SET `deliveri_notif_sent`=1 WHERE `uid`={$number_order}";
				//$result = mysql_query($sql);//снять комментарий для обновления состояния
				//echo $sql;
				//echo "Отправлено и изменено";
				log_save("Отправлено успешно.");
			}*/
			try {
				$mail->send();
				//echo "Message has been sent successfully";
			} catch (Exception $e) {
				echo "Mailer Error: " . $mail->ErrorInfo;
				print_r(error_get_last());
			}
		}else
		{
		  //echo 'Email - error('.$email.')</br>';
		  log_save('Email - error('.$email_client.')</br>');
		}
		$mail->clearAddresses();
		$mail->clearAttachments();
		//echo "Email Client:{$email_client}</br>";
	}
}