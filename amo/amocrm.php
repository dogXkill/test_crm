<?php
//d06sArh1FUmNZQgimH3TxFHLT42VVBybZha3k8SC0iJs8dVhQQ2EWIKQ2iGdoTn8
//transit@upak.me
#������ � �����������, ������� ����� �������� ������� POST � API �������
//header('Content-Type: application/json; charset=utf-8');
//error_reporting(E_ALL);
class AmoCrm{
	const LOGIN_AMO='transit@upak.me';
	const HASK_KEY_AMO='a292ed23a7a76e6d99787babbf6b9d71025e0d97';
	const SUBDOMAIN_AMO='upakme';
	
	public static function login_amo(){
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		$user = array(
			'USER_LOGIN' => $login, #��� ����� (����������� �����)
			'USER_HASH' => $key, #��� ��� ������� � API (�������� � ������� ������������)
		);
		//$subdomain = 'upakme'; #��� ������� - ��������
		#��������� ������ ��� �������
		$link = 'https://' . $subdomain . '.amocrm.ru/private/api/auth.php?type=json';
		/* ��� ���������� ������������ ������ � �������. ������������� ����������� cURL (������������ � ������� PHP). �� �����
		������
		������������ � ������������������ ��������� cURL, ���� �� �� �������������� �� PHP. */
		$curl = curl_init(); #��������� ���������� ������ cURL
		#������������� ����������� ����� ��� ������ cURL
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($user));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_COOKIEFILE, dirname
			(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl, CURLOPT_COOKIEJAR, dirname
			(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		$out = curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE); #������� HTTP-��� ������ �������
		curl_close($curl); #��������� ����� cURL
		/* ������ �� ����� ���������� �����, ���������� �� �������. ��� ������. �� ������ ���������� ������ ����� ��������. */
		$code = (int) $code;
		$errors = array(
			301 => 'Moved permanently',
			400 => 'Bad request',
			401 => 'Unauthorized',
			403 => 'Forbidden',
			404 => 'Not found',
			500 => 'Internal server error',
			502 => 'Bad gateway',
			503 => 'Service unavailable',
		);
		try
		{
			#���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
			if ($code != 200 && $code != 204) {
				throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
			}

		} catch (Exception $E) {
			die('������: ' . $E->getMessage() . PHP_EOL . '��� ������: ' . $E->getCode());
		}
		/*
		������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
		��� ������� ��������� ����� � ������, �������� PHP
		 */
		$Response = json_decode($out, true);
		$Response = $Response['response'];
		if (isset($Response['auth'])) #���� ����������� �������� � �������� "auth"
		{
			//echo 'ok';
			//����������� ������
			//print_r($Response);
			//
			return '����������� ������ �������';
		}

		return '����������� �� �������';
	}
	public static function account_check(){
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		///api/v2/account
		#��������� ������ ��� �������
		$link = 'https://' . $subdomain . '.amocrm.ru/api/v2/account?with=pipelines,';

		/*
		��� ���������� ������������ ������ � �������. ������������� ����������� cURL (������������ � ������� PHP).
		�� ����� ������ ������������ � ������������������ ��������� cURL, ���� �� �� �������������� �� PHP.
		*/

		$curl = curl_init(); #��������� ���������� ������ cURL
		#������������� ����������� ����� ��� ������ cURL
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		$out = curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		/*
		������ �� ����� ���������� �����, ���������� �� �������.
		��� ������. �� ������ ���������� ������ ����� ��������.
		*/
		$code = (int) $code;
		$errors = array(
			301 => 'Moved permanently',
			400 => 'Bad request',
			401 => 'Unauthorized',
			403 => 'Forbidden',
			404 => 'Not found',
			500 => 'Internal server error',
			502 => 'Bad gateway',
			503 => 'Service unavailable'
		);

		try { #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
			if ($code != 200 && $code != 204) {
				throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
			}
		} catch (Exception $E) {
			die('������: ' . $E->getMessage() . PHP_EOL . '��� ������: ' . $E->getCode());
		}

		/*
		������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
		��� ������� ��������� ����� � ������, �������� PHP
		*/
		$Response = json_decode($out, true);
		//echo "<pre>";
		//print_r($out);
		//echo "</pre>";
		
	}
	public static function valid($phone=null,$email=null){
		$mas_result=array();
		//phone
		$phone=str_replace(' ','',$phone);
		$phone=str_replace('-','',$phone);
		//echo $phone;
		//8 800 555 35 35 - 11 ������,�������� ������
		if (strlen($phone)==11){
			$phone=substr($phone, -10);
			$phone="+7".$phone;
			$mas_result['phone']=$phone;
		}else if (strlen($phone)>11){//���� ������ 11 �������� +7 800 555 35 35 (12) 
			$phone=substr($phone, -10);
			$phone="+7".$phone;
			$mas_result['phone']=$phone;
		}else{$mas_result['phone']=null;}
		//email
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$mas_result['email']=$email;
		}else{
			$mas_result['email']=null;
		}
		return $mas_result;
	}
	/*public static function check_sdelka_amo($phone=null,$email=null){
		
	}*/
	public static function check_client_amo($phone=null,$email=null){
		//echo "<b>�������� ������� �� ��������($phone)</b></br>";
		//������� ��� �������� ������� �� email/telefon
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		#��������� ������ ��� �������
		//$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/list?query='.$phone;
		$link='https://'.$subdomain.'.amocrm.ru/api/v2/contacts/?query='.$phone;
		//$link='https://'.$subdomain.'.amocrm.ru/api/v4/contacts?query='.$phone;
		$curl=curl_init(); #��������� ���������� ������ cURL
		#������������� ����������� ����� ��� ������ cURL
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		$out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
		curl_close($curl);
		$code=(int)$code;
		//echo $code;
		$errors=array(
		  301=>'Moved permanently',
		  400=>'Bad request',
		  401=>'Unauthorized',
		  403=>'Forbidden',
		  404=>'Not found',
		  500=>'Internal server error',
		  502=>'Bad gateway',
		  503=>'Service unavailable'
		);
		try
		{
		  #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
		  if($code!=200 && $code!=204)
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		}
		catch(Exception $E)
		{
		  die('������: '.$E->getMessage().PHP_EOL.'��� ������: '.$E->getCode());
		}
		$Response=json_decode($out,true);
		//print_r($Response);
		//$Response=$Response['response'];
		if ($Response){
			//echo "<pre>";
			//print_r($Response);
			//echo "</pre>";
			return $Response['_embedded']['items'][0]['id'];
		}else{return false;}
		
	}
	public static function check_client_amo_info($ids){
		//echo "<b>�������� ������� �� ��������($phone)</b></br>";
		//������� ��� �������� ������� �� email/telefon
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		#��������� ������ ��� �������
		//$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/list?query='.$phone;
		$link='https://'.$subdomain.'.amocrm.ru/api/v2/contacts/?id='.$ids;
		//$link='https://'.$subdomain.'.amocrm.ru/api/v4/contacts?query='.$phone;
		$curl=curl_init(); #��������� ���������� ������ cURL
		#������������� ����������� ����� ��� ������ cURL
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		$out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
		curl_close($curl);
		$code=(int)$code;
		//echo $code;
		$errors=array(
		  301=>'Moved permanently',
		  400=>'Bad request',
		  401=>'Unauthorized',
		  403=>'Forbidden',
		  404=>'Not found',
		  500=>'Internal server error',
		  502=>'Bad gateway',
		  503=>'Service unavailable'
		);
		try
		{
		  #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
		  if($code!=200 && $code!=204)
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		}
		catch(Exception $E)
		{
		  die('������: '.$E->getMessage().PHP_EOL.'��� ������: '.$E->getCode());
		}
		$Response=json_decode($out,true);
		//print_r($Response);
		//$Response=$Response['response'];
		if ($Response){
			//echo "<pre>";
			//print_r($Response);
			//echo "</pre>";
			return $Response['_embedded']['items'][0]['custom_fields'];
		}else{return false;}
	}
	public static function load_sdelki_amo($zap=null){//� �����
	//echo "<b>SDELKA($zap)</b></br>";
	//$zap=78005553535;
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		//$subdomain=$subdomain; #��� ������� - ��������
		#��������� ������ ��� �������
		if ($zap==null){
		//$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads';
			$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads/';
		}else{
			$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads?query='.$zap;
			//echo $link;
		}
		//$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads';
		$curl=curl_init();
		/* ������������� ����������� ����� ��� ������ cURL */
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		/* �� ����� ������ �������� �������������� HTTP-��������� IF-MODIFIED-SINCE, � ������� ����������� ���� � ������� D, d M Y
		H:i:s. ���
		�������� ����� ��������� ����� ���������� ������, ��������� ����� ���� ����. */
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('IF-MODIFIED-SINCE: Mon, 01 Aug 2013 07:07:23'));
		/* ��������� ������ � �������. */
		$out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
		curl_close($curl);
		$code=(int)$code;
		//echo $code;
		$errors=array(
		  301=>'Moved permanently',
		  400=>'Bad request',
		  401=>'Unauthorized',
		  403=>'Forbidden',
		  404=>'Not found',
		  500=>'Internal server error',
		  502=>'Bad gateway',
		  503=>'Service unavailable'
		);
		
		try
		{
		  #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
		  if($code!=200 && $code!=204)
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		  $Response=json_decode($out,true);
		  //echo "<pre>";
			//print_r($Response);
		  //echo $code;
		 //echo "</pre>";
		 
		  return $Response['_embedded']['items'];
		}
		catch(Exception $E)
		{
		  die('������: '.$E->getMessage().PHP_EOL.'��� ������: '.$E->getCode());
		  //return $E->getMessage();
		}
		
		/**
		 * ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
		 * ��� ������� ��������� ����� � ������, �������� PHP
		 */
		 
		
		//print_r($Response);
		
		
		//$Response=$Response['response'];
		//print_r($Response);
		/*foreach($Response as $value){
			$value['id'];
		}*/
		
		//echo "test:".$out;
		
		
	}
	public static function load_sdelki_amo_id($zap=null){//� �����
	//echo "<b>SDELKA($zap)</b></br>";
	//$zap=78005553535;
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		//$subdomain=$subdomain; #��� ������� - ��������
		#��������� ������ ��� �������
		if ($zap==null){
		//$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads';
			$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads/';
		}else{
			$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads?id='.$zap;
			//echo $link;
		}
		//$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads';
		$curl=curl_init();
		/* ������������� ����������� ����� ��� ������ cURL */
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		/* �� ����� ������ �������� �������������� HTTP-��������� IF-MODIFIED-SINCE, � ������� ����������� ���� � ������� D, d M Y
		H:i:s. ���
		�������� ����� ��������� ����� ���������� ������, ��������� ����� ���� ����. */
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('IF-MODIFIED-SINCE: Mon, 01 Aug 2013 07:07:23'));
		/* ��������� ������ � �������. */
		$out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
		curl_close($curl);
		$code=(int)$code;
		//echo $code;
		$errors=array(
		  301=>'Moved permanently',
		  400=>'Bad request',
		  401=>'Unauthorized',
		  403=>'Forbidden',
		  404=>'Not found',
		  500=>'Internal server error',
		  502=>'Bad gateway',
		  503=>'Service unavailable'
		);
		
		try
		{
		  #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
		  if($code!=200 && $code!=204)
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		  $Response=json_decode($out,true);
		  //echo "<pre>";
			//print_r($Response);
		  //echo $code;
		 //echo "</pre>";
		 
		  return $Response['_embedded']['items'];
		}
		catch(Exception $E)
		{
		  die('������: '.$E->getMessage().PHP_EOL.'��� ������: '.$E->getCode());
		  //return $E->getMessage();
		}
		
		/**
		 * ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
		 * ��� ������� ��������� ����� � ������, �������� PHP
		 */
		 
		
		//print_r($Response);
		
		
		//$Response=$Response['response'];
		//print_r($Response);
		/*foreach($Response as $value){
			$value['id'];
		}*/
		
		//echo "test:".$out;
		
		
	}
	public static function load_sdelki_amoContats($zap=null){//� �����
	$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		//$subdomain=$subdomain; #��� ������� - ��������
		#��������� ������ ��� �������
		if ($zap==null){
		//$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads';
			$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads/';
		}else{
			$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads?id='.$zap;
			echo $link;
		}
		//$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads';
		$curl=curl_init();
		/* ������������� ����������� ����� ��� ������ cURL */
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		/* �� ����� ������ �������� �������������� HTTP-��������� IF-MODIFIED-SINCE, � ������� ����������� ���� � ������� D, d M Y
		H:i:s. ���
		�������� ����� ��������� ����� ���������� ������, ��������� ����� ���� ����. */
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('IF-MODIFIED-SINCE: Mon, 01 Aug 2013 07:07:23'));
		/* ��������� ������ � �������. */
		$out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
		curl_close($curl);
		$code=(int)$code;
		//echo $code;
		$errors=array(
		  301=>'Moved permanently',
		  400=>'Bad request',
		  401=>'Unauthorized',
		  403=>'Forbidden',
		  404=>'Not found',
		  500=>'Internal server error',
		  502=>'Bad gateway',
		  503=>'Service unavailable'
		);
		
		try
		{
		  #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
		  if($code!=200 && $code!=204)
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		  $Response=json_decode($out,true);
		  //echo "<pre>";
		 // print_r($Response);
		  //echo $code;
		 //echo "</pre>";
		 
		  return $Response['_embedded']['items'][0]['main_contact']['id'];
		}
		catch(Exception $E)
		{
		  die('������: '.$E->getMessage().PHP_EOL.'��� ������: '.$E->getCode());
		  //return $E->getMessage();
		}
	}
	public static function add_sdelki_amo_full($leads){
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads';
		/* ��� ���������� ������������ ������ � �������. ������������� ����������� cURL (������������ � ������� PHP). ��������� �
		������ � ����
		����������� �� ������ ��������� � �������. */
		$curl=curl_init(); #��������� ���������� ������ cURL
		#������������� ����������� ����� ��� ������ cURL
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
		curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($leads));
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		$out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
		//var_dump($out);
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
		/* ������ �� ����� ���������� �����, ���������� �� �������. ��� ������. �� ������ ���������� ������ ����� ��������. */
		$code=(int)$code;
		$errors=array(
		  301=>'Moved permanently',
		  400=>'Bad request',
		  401=>'Unauthorized',
		  403=>'Forbidden',
		  404=>'Not found',
		  500=>'Internal server error',
		  502=>'Bad gateway',
		  503=>'Service unavailable'
		);
		try
		{
			$Response=json_decode($out,true);
		  //print_r($Response);
		//$Response=$Response['_embedded']['items'][0]['id'];
		return $Response;
		  #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
		 if($code!=200 && $code!=204) {
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		  }
		  
		}
		catch(Exception $E)
		{
		  die('������: '.$E->getMessage().PHP_EOL.'��� ������: '.$E->getCode());
		}
	}
	public static function add_sdelki_amo($leads){
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads';
		/* ��� ���������� ������������ ������ � �������. ������������� ����������� cURL (������������ � ������� PHP). ��������� �
		������ � ����
		����������� �� ������ ��������� � �������. */
		$curl=curl_init(); #��������� ���������� ������ cURL
		#������������� ����������� ����� ��� ������ cURL
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
		curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($leads));
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		$out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
		//var_dump($out);
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
		/* ������ �� ����� ���������� �����, ���������� �� �������. ��� ������. �� ������ ���������� ������ ����� ��������. */
		$code=(int)$code;
		$errors=array(
		  301=>'Moved permanently',
		  400=>'Bad request',
		  401=>'Unauthorized',
		  403=>'Forbidden',
		  404=>'Not found',
		  500=>'Internal server error',
		  502=>'Bad gateway',
		  503=>'Service unavailable'
		);
		try
		{
		  #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
		 if($code!=200 && $code!=204) {
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		  }
		  $Response=json_decode($out,true);
		  //print_r($Response);
		$Response=$Response['_embedded']['items'][0]['id'];
		return $Response;
		}
		catch(Exception $E)
		{
		  die('������: '.$E->getMessage().PHP_EOL.'��� ������: '.$E->getCode());
		}
	}
	
	public static function add_client_amo($data=null){
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/set';
		/* ��� ���������� ������������ ������ � �������. ������������� ����������� cURL (������������ � ������� PHP). ��������� �
		������ � ����
		����������� �� ������ ��������� � �������. */
		$curl=curl_init(); #��������� ���������� ������ cURL
		#������������� ����������� ����� ��� ������ cURL
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
		curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($data));
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		 
		$out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
		$errors=array(
		  301=>'Moved permanently',
		  400=>'Bad request',
		  401=>'Unauthorized',
		  403=>'Forbidden',
		  404=>'Not found',
		  500=>'Internal server error',
		  502=>'Bad gateway',
		  503=>'Service unavailable'
		);
		try
		{
		  #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
		 if($code!=200 && $code!=204) {
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		  }
		}
		catch(Exception $E)
		{
		  die('�������� ������: '.$E->getMessage().PHP_EOL.'��� ������: '.$E->getCode());
		}
		/*
		 ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
		 ��� ������� ��������� ����� � ������, �������� PHP
		 */
		$Response=json_decode($out,true);
		$Response=$Response['_embedded']['items'];
		$output='ID ����������� ���������:'.PHP_EOL;
		foreach($Response as $v)
		 if(is_array($v))
		   $output.=$v['id'].PHP_EOL;
		return $output;
	}
	public static function add_client_amo1($data=null){
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		$link='https://'.$subdomain.'.amocrm.ru/api/v2/contacts';
		/* ��� ���������� ������������ ������ � �������. ������������� ����������� cURL (������������ � ������� PHP). ��������� �
		������ � ����
		����������� �� ������ ��������� � �������. */
		$curl=curl_init(); #��������� ���������� ������ cURL
		#������������� ����������� ����� ��� ������ cURL
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
		curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($data));
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		$out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
		/* ������ �� ����� ���������� �����, ���������� �� �������. ��� ������. �� ������ ���������� ������ ����� ��������. */
		$code=(int)$code;
		$errors=array(
		  301=>'Moved permanently',
		  400=>'Bad request',
		  401=>'Unauthorized',
		  403=>'Forbidden',
		  404=>'Not found',
		  500=>'Internal server error',
		  502=>'Bad gateway',
		  503=>'Service unavailable'
		);
		try
		{
		  #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
		 if($code!=200 && $code!=204) {
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		  }
		}
		catch(Exception $E)
		{
		  //die('������: '.$E->getMessage().PHP_EOL.'��� ������: '.$E->getCode());
		}
		/*
		 ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
		 ��� ������� ��������� ����� � ������, �������� PHP
		 */
		$Response=json_decode($out,true);
		$Response=$Response['_embedded']['items'];
		//$output='ID ����������� ���������:'.PHP_EOL;
		foreach($Response as $v)
		 if(is_array($v))
		   $output.=$v['id'].PHP_EOL;
		return $output;
			}
		public static function add_note($data){
			$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
			//
			$link = 'https://' . $subdomain . '.amocrm.ru/api/v2/notes';
			/* ��� ���������� ������������ ������ � �������. ������������� ����������� cURL (������������ � ������� PHP). ��������� �
			������ � ����
			����������� �� ������ ��������� � �������. */
			$curl = curl_init(); #��������� ���������� ������ cURL
			#������������� ����������� ����� ��� ������ cURL
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
			curl_setopt($curl, CURLOPT_URL, $link);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
			curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			$out = curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
			$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			/* ������ �� ����� ���������� �����, ���������� �� �������. ��� ������. �� ������ ���������� ������ ����� ��������. */
			$code = (int) $code;
			$errors = array(
				301 => 'Moved permanently',
				400 => 'Bad request',
				401 => 'Unauthorized',
				403 => 'Forbidden',
				404 => 'Not found',
				500 => 'Internal server error',
				502 => 'Bad gateway',
				503 => 'Service unavailable',
			);
			try
			{
				#���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
				if ($code != 200 && $code != 204) {
					//throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
				}

			} catch (Exception $E) {
				die('������: ' . $E->getMessage() . PHP_EOL . '��� ������: ' . $E->getCode());
			}
			//
		}
}
		
		function printLink($method, $title, $subdomain) {
			echo '<br>';
			echo "<a href='https://$subdomain.amocrm.ru/$method' target='_blank'>$title</a>";
			echo '<br>';
}
/*
$subdomain='upakme';
printLink('api/v4/leads/custom_fields', '������ utm �����', $subdomain);
printLink('api/v4/users', '������ �������������', $subdomain);
printLink('api/v4/contacts/custom_fields', '������ ����� ��������', $subdomain);
*/
?>