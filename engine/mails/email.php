<?php
//отправка
$auth = false;
require_once("/home/crmu660633/test.upak.me/docs/acc/query/backend/phpmailer/email_class.php");
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/db.inc.php");//поменять путь на боевом
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/auth.php");
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/lib.php");
require_once("/home/crmu660633/test.upak.me/docs/engine/init.php");
if ($user_access['order_access'] == '0' || empty($user_access['order_access'])) {
    header('Location: /');
}
//
$user_id = intval($user_access['uid']);//для поиска почты с которой отправлять
$sql="SELECT * FROM `users` WHERE `uid` = {$user_id}";
$res=mysql_query($sql);
$row = mysql_fetch_row($res);
//print_r($row);
$email_ot=$row[10];
if ($email_ot=='' || $email_ot==null){$email_ot='notification@upak.me';}
$emails=new Emails(0);//1-режим отладки,0-обычный
$sql="SELECT val FROM `options` WHERE `name`='email_doki'";
$result = mysql_query($sql);
$row = mysql_fetch_row($result);
$msg=$row[0];
	$to=$_POST['to'];
	$tip=0;
	$sub=$_POST['tip'];
	$uid=$_POST['uid'];
	$domen="http://test.upak.me";
	$name_client=$_POST['name_client'];
	$host=$_SERVER['host_name'];
	switch ($sub){
		case "pdf":
		$sub="Счет по заказу {$uid}";
		$name_file="Счет-по-заказу-{$uid}";
		$url = $domen."/acc/backend/invoice_pdf.php?qid={$uid}";
		$format='pdf';
		$d="chet";
	break;
	case "pdf1":
		$sub="Накладная по заказу  {$uid}";
		$name_file="Накладная-по-заказу-{$uid}";
		$url = $domen."/acc/backend/waybill_pdf.php?qid={$uid}";
		$format='pdf';
		$d="naklad";
	break;
	case "word":
		$sub="Договор по заказу {$uid}";
		$name_file="Договор-по-заказу-{$uid}";
		$url = $domen."/acc/files/load_word.php?qid={$uid }";
		$format='docx';
		$d="dogovor";
	break;
	}
	$files=date("Y-m-d");
	$files1=date("H:i:s");
	//$url = $_POST['url'];
	$path = "/home/crmu660633/test.upak.me/docs/docs/temp/".$files."-{$files1}-{$d}.".$format; 
	//echo file_get_contents("{$domen}/acc/backend/waybill_pdf.php?qid={$uid}");
	//$emails->pre_vid($_COOKIE);
	//$fp = fopen($path, 'w');
	//$ch = curl_init($url);
	//print_r($ch);
	//curl_setopt($ch, CURLOPT_FILE, $fp);
	//$data = curl_exec($ch);
	
	//curl_close($ch);
	//fclose($fp);
	//test
	$login=$_COOKIE['user_des'];
	$pass=$_COOKIE['pass_des'];
	$login_aut=$_COOKIE['login_autocomplete'];
	//$emails->pre_vid($_SERVER);
	$emails->save_file($path,$url);
	//
	//$msg="тест";
	
	$mas['tip']=$tip;
	$mas['to']=$to;
	$mas['sub']=$sub;
	$mas['msg']=$msg;
	$mas['url']=$url;
	$mas['name_client']=$name_client;
	$mas['path']="/home/crmu660633/test.upak.me/docs/docs/temp/".$files."-{$files1}-{$d}.".$format;
	$emails->pre_vid($mas);
	$path1="/home/crmu660633/test.upak.me/docs/docs/temp/".$files."-{$files1}-{$d}.".$format;
	$path="/home/crmu660633/test.upak.me/docs/docs/temp/";
	$filename=$files."-{$files1}-{$d}.".$format;
	//$path1=$mas['path'];
	$path=$domen."/docs/temp/".$files."-{$files1}-{$d}.".$format; 
	//echo $path1;
	if ( file_exists($path1)){
	$file = fopen($path1, "rb"); //Открываем файл
		  //$text = fread($file, filesize($path)); //Считываем весь файл
		  //echo $text;
		$result=$emails->send_email_dop_ot($tip,$email_ot,$to,$sub,$msg,$path1,$name_file.".".$format,$name_client);
		echo $result;
		$replyto="";
		$from_name="t";
		//$emails-->send_email_dop($tip,$filename, $path, $to, $from_mail, $from_name, $replyto, $sub, $msg)
	}else{echo 0;}
	
				
?>