<?php
//отправка
require_once("/home/crmu660633/test.upak.me/docs/acc/query/backend/phpmailer/email_class.php");
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/db.inc.php");//поменять путь на боевом
//
$emails=new Emails(1);//1-режим отладки,0-обычный

	$to=$_POST['to'];
	$tip=1;
	$sub=$_POST['tip'];
	$uid=$_POST['uid'];
	$domen="http://test.upak.me";
	switch ($sub){
		case "pdf":
		$sub="Счет по заказу {$uid}";
		$url = $domen."acc/backend/invoice_pdf.php?qid={$uid}";
		$format='pdf';
		$d="chet";
	break;
	case "pdf1":
		$sub="Накладная по заказу  {$uid}";
		$url = $domen."<a href=/acc/backend/waybill_pdf.php?qid={$uid}";
		$format='pdf';
		$d="naklad";
	break;
	case "word":
		$sub="Договор по заказу {$uid}";
		$url = $domen."/acc/files/load_word.php?qid={$uid }";
		$format='word';
		$d="dogovor";
	break;
	}
	$files=date("Y-m-d H:i:s");
	//$url = $_POST['url'];
	$path = "/home/crmu660633/test.upak.me/docs/engine/mails/temp/".$files."_{$d}.".$format;
	$fp = fopen($path, 'w');
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	$data = curl_exec($ch);
	curl_close($ch);
	fclose($fp);
	$msg="";
	$mas['tip']=$tip;
	$mas['to']=$to;
	$mas['sub']=$sub;
	$mas['msg']=$msg;
	$emails->pre_vid($mas);
	//$emails->send_email_dop($tip,$to,$sub,$msg,$path);
?>