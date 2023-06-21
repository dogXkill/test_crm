<?php
//require_once 'sms.ru.php';
require_once("/home/crmu660633/test.upak.me/docs/acc/query/backend/phpmailer/sms.ru.php");
class Emails{
	private $mail;
	private $flag;
	private $login="z1676914790660";
	private $pass="269856";
	private $sender="paketoff.ru";
	private $debug=1;//1-yes,0-no

	function __construct($status=0) {
		$this->debug=$status;
	}
	public function send_mail($tip,$to, $sub, $msg) {
		$fromMail = 'notification@upak.me';
			//$fromName = 'paketoff.ru';
			
			$fromName='=?Windows-1251?B?' . base64_encode($sub) . '?=';
			$sub= '=?Windows-1251?B?' . base64_encode($sub) . '?=';
			//$sub= mb_convert_encoding($sub, "utf-8","windows-1251");
			//$sub=iconv ('utf-8', 'windows-1251', $sub); 
			$date = date(DATE_RFC2822);
			$messageId=sprintf("<%s.%s@%s>",
									base_convert(microtime(), 10, 36),
									base_convert(bin2hex(openssl_random_pseudo_bytes(8)), 16, 36),
									'paketoff.ru');
		$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= "Content-type: text/html; charset=Windows-1251". "\r\n";
			$headers .= "From: ". $fromName ." <". $fromMail ."> \r\n";
			$headers .= "Date: ". $date ." \r\n";
			$headers .= "Message-ID: ". $messageId ." \r\n";
		if ($tip==1 || $this->debug==1){
			$to="tutaev9@yandex.ru,pavel@moskvin.ru";
			//$to="tutaev9@yandex.ru";
		}else if ($tip==2 && $this->debug==0){
			//$to=$to;//на время отключл.
		}
		if ($this->debug==0){
			//$msg.="<br>[debug] <br> FromName:".mb_detect_encoding($fromName)." \r\n SUB:".mb_detect_encoding($sub)." \r\n MSG:".mb_detect_encoding($msg);
			return mail($to, $sub, $msg, $headers);
		}else{
			return 1;//для тестов
		}
	}
	public function download_file($file, $name) {
		$fd = fopen($file, 'r') or die("не удалось открыть файл");
		$str="";
		while(!feof($fd))
		{
			$str.=htmlentities(fgets($fd));
			//echo $str;
		}
		return $str;
	}
	
	public function send_email_dop($tip,$to,$sub,$msg,$file1,$name_file,$name_client){
		//
			// Кому.
			if ($tip==1 || $this->debug==1){
				$to="tutaev9@yandex.ru";
			}
			$name_client = mb_convert_encoding($name_client, "windows-1251", "utf-8");
		$to = '=?Windows-1251?B?' . base64_encode($name_client) . '?= <'.$to.'>';
		 
		// От кого.
		$from = '=?Windows-1251?B?' . base64_encode($sub) . '?= <notification@upak.me>';
		$name_file='=?Windows-1251?B?' . base64_encode($name_file) . '?=';//mb_convert_encoding($name_file, "utf-8", "windows-1251");
		$subject = '=?Windows-1251?B?' . base64_encode($sub) . '?=';//mb_convert_encoding($sub, "utf-8", "windows-1251");
		$body    =$msg;
		 
		// Массив с файлами.
		$files = array(
			$file1
		);
		 
		$boundary = md5(uniqid(time()));
		 
		// Формирование заголовка письма.
		$headers = array(
			'Content-Type: multipart/mixed; boundary="' . $boundary . '"',
			'Content-Transfer-Encoding: 7bit',
			'MIME-Version: 1.0',
			'From: ' . $from,
			'Date: ' . date('r')
		);
		 
		// Формирование текста письма.
		$message = array(
			'--' . $boundary,
			'Content-Type: text/html; charset=Windows-1251',
			'Content-Transfer-Encoding: base64',
			'',
			chunk_split(base64_encode($body))
		);
		 
		// Формирование файлов.
		foreach ($files as $row) {
			if (is_file($row)) {
				$name = basename($row);
				$fp   = fopen($row, 'rb');
				$file = fread($fp, filesize($row));
				fclose($fp);
		 
				$message[] =  '';
				$message[] =  '--' . $boundary;
				$message[] = 'Content-Type: application/octet-stream; name="' . $name_file . '"';
				$message[] = 'Content-Transfer-Encoding: base64';
				$message[] = 'Content-Disposition: attachment; filename="' . $name_file . '"';
				$message[] = '';
				$message[] = chunk_split(base64_encode($file));
			}
		}
		 
		$message[] = '';
		$message[] = "--" . $boundary . '--';
		 
		$headers = implode("\r\n", $headers);
		$message = implode("\r\n", $message);
		 
		// Отправка.
		//echo $message;
		echo mail($to, $subject, $message, $headers);
		//unlink($file1);
		//
	}
	public function send_email_dop_ot($tip,$ot,$to,$sub,$msg,$file1,$name_file,$name_client){
		//
			// Кому.
			if ($tip==1 || $this->debug==1){
				$to="tutaev9@yandex.ru";
			}
			$name_client = mb_convert_encoding($name_client, "windows-1251", "utf-8");
		$to = '=?Windows-1251?B?' . base64_encode($name_client) . '?= <'.$to.'>';
		 
		// От кого.
		$from = '=?Windows-1251?B?' . base64_encode($sub) . '?= <'.$ot.'>';
		$name_file='=?Windows-1251?B?' . base64_encode($name_file) . '?=';//mb_convert_encoding($name_file, "utf-8", "windows-1251");
		$subject = '=?Windows-1251?B?' . base64_encode($sub) . '?=';//mb_convert_encoding($sub, "utf-8", "windows-1251");
		$body    =$msg;
		 
		// Массив с файлами.
		$files = array(
			$file1
		);
		 
		$boundary = md5(uniqid(time()));
		 
		// Формирование заголовка письма.
		$headers = array(
			'Content-Type: multipart/mixed; boundary="' . $boundary . '"',
			'Content-Transfer-Encoding: 7bit',
			'MIME-Version: 1.0',
			'From: ' . $from,
			'Date: ' . date('r')
		);
		 
		// Формирование текста письма.
		$message = array(
			'--' . $boundary,
			'Content-Type: text/html; charset=Windows-1251',
			'Content-Transfer-Encoding: base64',
			'',
			chunk_split(base64_encode($body))
		);
		 
		// Формирование файлов.
		foreach ($files as $row) {
			if (is_file($row)) {
				$name = basename($row);
				$fp   = fopen($row, 'rb');
				$file = fread($fp, filesize($row));
				fclose($fp);
		 
				$message[] =  '';
				$message[] =  '--' . $boundary;
				$message[] = 'Content-Type: application/octet-stream; name="' . $name_file . '"';
				$message[] = 'Content-Transfer-Encoding: base64';
				$message[] = 'Content-Disposition: attachment; filename="' . $name_file . '"';
				$message[] = '';
				$message[] = chunk_split(base64_encode($file));
			}
		}
		 
		$message[] = '';
		$message[] = "--" . $boundary . '--';
		 
		$headers = implode("\r\n", $headers);
		$message = implode("\r\n", $message);
		 
		// Отправка.
		//echo $message;
		echo mail($to, $subject, $message, $headers);
		//unlink($file1);
		//
	}
	public function send_sms($phone,$text){
		
		$smsru = new SMSRU('F45E0421-2A4E-C91F-91E3-896DB1BC9FC1'); 
		$data = new stdClass();
		$data->to = $phone;
		
		$data->test = 1;//в тестовом режиме(фактически не отправляет) 
		$data->text = $text;
		 $data->from = 'SUPPORT';
		//https://api.iqsms.ru/messages/v2/send/?phone=%2B71234567890&text=test
		//+71234567890
		//
		
			$sms = $smsru->send_one($data); // Отправка сообщения и возврат данных в переменную
			
			if ($sms->status == "OK") { // Запрос выполнен успешно
				echo "Сообщение отправлено успешно. ";
				echo "ID сообщения: $sms->sms_id. ";
				echo "Ваш новый баланс: $sms->balance";
			} else {
				echo "Сообщение не отправлено. ";
				echo "Код ошибки: $sms->status_code. ";
				//echo "Текст ошибки: $sms->status_text.";
				echo iconv('utf-8//IGNORE', 'windows-1251//IGNORE', $sms->status_text);
			}
		//
	}
	public function log_save($text){
		if ($this->debug==0){//записывать только в бою
			$log = date('Y-m-d H:i:s') . ' - '.$text;
			file_put_contents('/home/crmu660633/test.upak.me/docs/acc/query/backend/phpmailer/log.txt', $log . PHP_EOL, FILE_APPEND);
		}else{
			$log = date('Y-m-d H:i:s') . ' - '.$text." [DEBUG]";
			file_put_contents('/home/crmu660633/test.upak.me/docs/acc/query/backend/phpmailer/log.txt', $log . PHP_EOL, FILE_APPEND);
		}
	}
	public function pre_vid($mas){
		if ($this->debug==1){//выводим только в режиме отладки
			echo "<pre>";
			print_r($mas);
			echo "</pre>";
		}
	}
	public function vivod($mas){
		echo "<div style='border:1px solid black;padding:5px;margin-top:5px;'>";
		foreach ($mas as $k=>$value){
			echo "<b>{$k}</b> {$value}</br>";
		}
		echo "</div>";
	}
	public function debug_ec(){
		return $this->debug;
	}
	public function check_debug(){
		if ($this->debug==1){$mas=array("Сообщения не будут отправляться (Тестовый режим)",1); return $mas;}else{$mas=array("Активный режим",0);return $mas;}
		return json_encode("error");
	}
	public function save_file($path,$url){
		$login=$_COOKIE['user_des'];
		$pass=$_COOKIE['pass_des'];
		$login_aut=$_COOKIE['login_autocomplete'];
		$host=$_SERVER['HTTP_HOST'];
		$fp = fopen($path, 'w');
		$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				//
				foreach (getallheaders() as $name => $value) {
    //echo "$name: $value</br>";
					if ($name=='Authorization'){$key_bas=$value;}
}
				//
				$headers = array(
				   "Authorization: {$key_bas}",
				   "Host:{$host}",
				   "Cookie: user_des=$login; pass_des=$pass; login_autocomplete=$login_aut; auto_redirect=0;",// PHPSESSID=b4478480412700787ddccea1b5edd8c4
				   
				   
				);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				//for debug only!
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_FILE, $fp);

				$resp = curl_exec($curl);
				//echo $resp;
				curl_close($curl);
				$fp = fopen($path, 'r');
				for ($i = 0; $i < 4; $i++) {
					//echo fgets($fp);
					//echo '</br>';
				}
				fclose($fp);
	}
	public function load_file($url){
		$login=$_COOKIE['user_des'];
		$pass=$_COOKIE['pass_des'];
		$login_aut=$_COOKIE['login_autocomplete'];
		$host=$_SERVER['HTTP_HOST'];
		//
		$headers = array(
				   "Authorization: Basic YWRtaW46MTIzNDU2",
				   "Host:{$host}",
				   "Cookie: user_des=$login; pass_des=$pass; login_autocomplete=$login_aut; auto_redirect=0;",// PHPSESSID=b4478480412700787ddccea1b5edd8c4
				   "Content-Type: application/pdf",
					"Content-Disposition: attachment; filename='downloaded.pdf'",
				   
				);
		//
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		$html = curl_exec ($ch);
		//$responseInfo = curl_getinfo($ch);
		curl_close ($ch);
		$html=mb_convert_encoding($html, "utf-8", "windows-1251");
		echo $html;
		
		return $html;
	}
	
	
}

?>