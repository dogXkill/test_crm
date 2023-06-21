<?php
require_once("/home/crmu660633/test.upak.me/docs/acc/query/backend/phpmailer/email_class.php");
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/db.inc.php");//поменять путь на боевом
//
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
$status_email=$row[0];
$sql="SELECT val FROM `options` WHERE `name`='delivery_status'";
$result = mysql_query($sql);
$row = mysql_fetch_row($result);
$delivery_status=$row[0];
//
if ($delivery_status==1){
$emails=new Emails(1);

//echo $emails->email->From;
$mas['title']=$title_email_ch;
		$mas['body']=$body_email_ch;
		$mas['За сколько часов смотреть']=$delivery_hours;
		$emails->vivod($mas);
		
$date = new DateTime();
$start_date=$date->format('Y-m-d');
$date->add(new DateInterval("PT{$delivery_hours}H"));//получим дату до которой вытаскиваем заказы
echo $date->format('Y-m-d') . "</br>";
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
  t.courier_id,
  COALESCE(c.name, 'Удален') AS courier,
  COALESCE(CONCAT(u.surname, ' ', u.name, ' ', u.father), 'Удален') AS `user`,
  DATE_FORMAT(t.date, '%d-%m-%Y') AS date,
  t.text,
  q.uid AS query_id,
  t.address,
  t.contact_name,
  t.contact_phone,
  c.auto_number,
  c.passport2,
  c.phone,
  cl.email,
  q.deliveri_notif_sent
FROM
  courier_tasks AS t
  LEFT JOIN couriers AS c ON t.courier_id = c.id
  LEFT JOIN users AS u ON t.user_id = u.uid
  LEFT JOIN queries AS q ON t.id = q.courier_task_id
  LEFT JOIN clients AS cl ON cl.uid = q.client_id
WHERE
  t.done = 0 AND date>='{$start_date}' AND date<='{$end_data}' AND ({$sql_1}) AND q.deliv_id=2 AND q.deliveri_notif_sent=0
ORDER BY
  t.date DESC
  LIMIT 0, $lim
  ";
  echo $sql."</br>";
if($result = mysql_query($sql))
{
	if (mysql_num_rows($result)>=1){

  while($row = mysql_fetch_assoc($result))
  {
    $row["courier"] = $row["courier"];

	$courier_task_id = $row["id"];
	$sql2="SELECT uid AS query_id,deliveri_notif_sent FROM queries WHERE courier_task_id = '$courier_task_id' ";
	//echo $sql2."</br>";
	$get_order_info = mysql_query($sql2);

	$ord_row = mysql_fetch_array($get_order_info);

	$row["query_id"] = $ord_row["query_id"];

	//pre_vid($row);
	$courier_task[]=$row;
  }
  $col_res=mysql_num_rows($result);
  $emails->log_save("SQL запрос вернул :{$col_res}");
  mysql_free_result($result);
}
}
else{
	$emails->log_save("SQL запрос вернул ошибку");
	//mysql_free_result($result);
	exit();
}
$mas_setting=$emails->check_debug();
if ($mas_setting[1]==1){
	echo "SQL запрос вернул : ".$col_res;
	echo "<h3>{$mas_setting[0]}</h3>";
	//echo $sql;
	$mas['top']="Шаблон";
	$mas['title']=$title_email_ch;
		$mas['body']=$body_email_ch;
		$mas['За сколько часов смотреть']=$delivery_hours;
		//$emails->vivod($mas);
		echo "-------------------------------</br>";
}
//
$mas=array();
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
		
		$email_client=$value['email'];
		$mas=array();
		$mas['title']=$title_email;
		$mas['body']=$body_email;
		$mas['client_email']=$email_client;
		
		if (filter_var($email_client, FILTER_VALIDATE_EMAIL) !== false)
		{
			$emails->log_save("tip:1|{$title_email}|{$email_client}");
			$result_email=$emails->send_mail(1,$email_client,$title_email,$body_email);
			if ($result_email==1){
				$sql="UPDATE `queries` SET `deliveri_notif_sent`=1 WHERE `uid`={$number_order}";
				//$result = mysql_query($sql);//снять комментарий для обновления состояния
				$mas['status']=true;
				$emails->log_save("Отправлено успешно.({$email_client}) - OK");
				echo $emails->send_sms("79017922782","{$body_email}");
			}else{
				$emails->log_save('Email - error('.$email_client.')</br>');
				$mas['status']=false;
			}
		}else
		{
		  //echo 'Email - error('.$email.')</br>';
		  $emails->log_save('Email - error('.$email_client.')</br>');
		}
		$emails->vivod($mas);
	}
}
}else{
	echo "Доставка уведомления отключены";
}
//
?>