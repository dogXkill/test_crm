<?php
//d06sArh1FUmNZQgimH3TxFHLT42VVBybZha3k8SC0iJs8dVhQQ2EWIKQ2iGdoTn8
//transit@upak.me
#Массив с параметрами, которые нужно передать методом POST к API системы
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
			'USER_LOGIN' => $login, #Ваш логин (электронная почта)
			'USER_HASH' => $key, #Хэш для доступа к API (смотрите в профиле пользователя)
		);
		//$subdomain = 'upakme'; #Наш аккаунт - поддомен
		#Формируем ссылку для запроса
		$link = 'https://' . $subdomain . '.amocrm.ru/private/api/auth.php?type=json';
		/* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Вы также
		можете
		использовать и кроссплатформенную программу cURL, если вы не программируете на PHP. */
		$curl = curl_init(); #Сохраняем дескриптор сеанса cURL
		#Устанавливаем необходимые опции для сеанса cURL
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
		$out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
		curl_close($curl); #Завершаем сеанс cURL
		/* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
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
			#Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
			if ($code != 200 && $code != 204) {
				throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
			}

		} catch (Exception $E) {
			die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
		}
		/*
		Данные получаем в формате JSON, поэтому, для получения читаемых данных,
		нам придётся перевести ответ в формат, понятный PHP
		 */
		$Response = json_decode($out, true);
		$Response = $Response['response'];
		if (isset($Response['auth'])) #Флаг авторизации доступен в свойстве "auth"
		{
			//echo 'ok';
			//авторизация прошла
			//print_r($Response);
			//
			return 'Авторизация прошла успешно';
		}

		return 'Авторизация не удалась';
	}
	public static function account_check(){
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		///api/v2/account
		#Формируем ссылку для запроса
		$link = 'https://' . $subdomain . '.amocrm.ru/api/v2/account?with=pipelines,';

		/*
		Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP).
		Вы также можете использовать и кроссплатформенную программу cURL, если вы не программируете на PHP.
		*/

		$curl = curl_init(); #Сохраняем дескриптор сеанса cURL
		#Устанавливаем необходимые опции для сеанса cURL
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
		curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		$out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		/*
		Теперь мы можем обработать ответ, полученный от сервера.
		Это пример. Вы можете обработать данные своим способом.
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

		try { #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
			if ($code != 200 && $code != 204) {
				throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
			}
		} catch (Exception $E) {
			die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
		}

		/*
		Данные получаем в формате JSON, поэтому, для получения читаемых данных,
		нам придётся перевести ответ в формат, понятный PHP
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
		//8 800 555 35 35 - 11 знаков,обрезаем первый
		if (strlen($phone)==11){
			$phone=substr($phone, -10);
			$phone="+7".$phone;
			$mas_result['phone']=$phone;
		}else if (strlen($phone)>11){//если больше 11 символов +7 800 555 35 35 (12) 
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
		//echo "<b>Проверка клиента по телефону($phone)</b></br>";
		//функция для проверки клиента по email/telefon
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		#Формируем ссылку для запроса
		//$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/list?query='.$phone;
		$link='https://'.$subdomain.'.amocrm.ru/api/v2/contacts/?query='.$phone;
		//$link='https://'.$subdomain.'.amocrm.ru/api/v4/contacts?query='.$phone;
		$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
		#Устанавливаем необходимые опции для сеанса cURL
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
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
		  #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
		  if($code!=200 && $code!=204)
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		}
		catch(Exception $E)
		{
		  die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
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
		//echo "<b>Проверка клиента по телефону($phone)</b></br>";
		//функция для проверки клиента по email/telefon
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		#Формируем ссылку для запроса
		//$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/list?query='.$phone;
		$link='https://'.$subdomain.'.amocrm.ru/api/v2/contacts/?id='.$ids;
		//$link='https://'.$subdomain.'.amocrm.ru/api/v4/contacts?query='.$phone;
		$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
		#Устанавливаем необходимые опции для сеанса cURL
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
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
		  #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
		  if($code!=200 && $code!=204)
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		}
		catch(Exception $E)
		{
		  die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
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
	public static function load_sdelki_amo($zap=null){//в тесте
	//echo "<b>SDELKA($zap)</b></br>";
	//$zap=78005553535;
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		//$subdomain=$subdomain; #Наш аккаунт - поддомен
		#Формируем ссылку для запроса
		if ($zap==null){
		//$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads';
			$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads/';
		}else{
			$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads?query='.$zap;
			//echo $link;
		}
		//$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads';
		$curl=curl_init();
		/* Устанавливаем необходимые опции для сеанса cURL */
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		/* Вы также можете передать дополнительный HTTP-заголовок IF-MODIFIED-SINCE, в котором указывается дата в формате D, d M Y
		H:i:s. При
		передаче этого заголовка будут возвращены сделки, изменённые позже этой даты. */
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('IF-MODIFIED-SINCE: Mon, 01 Aug 2013 07:07:23'));
		/* Выполняем запрос к серверу. */
		$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
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
		  #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
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
		  die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
		  //return $E->getMessage();
		}
		
		/**
		 * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
		 * нам придётся перевести ответ в формат, понятный PHP
		 */
		 
		
		//print_r($Response);
		
		
		//$Response=$Response['response'];
		//print_r($Response);
		/*foreach($Response as $value){
			$value['id'];
		}*/
		
		//echo "test:".$out;
		
		
	}
	public static function load_sdelki_amo_id($zap=null){//в тесте
	//echo "<b>SDELKA($zap)</b></br>";
	//$zap=78005553535;
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		//$subdomain=$subdomain; #Наш аккаунт - поддомен
		#Формируем ссылку для запроса
		if ($zap==null){
		//$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads';
			$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads/';
		}else{
			$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads?id='.$zap;
			//echo $link;
		}
		//$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads';
		$curl=curl_init();
		/* Устанавливаем необходимые опции для сеанса cURL */
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		/* Вы также можете передать дополнительный HTTP-заголовок IF-MODIFIED-SINCE, в котором указывается дата в формате D, d M Y
		H:i:s. При
		передаче этого заголовка будут возвращены сделки, изменённые позже этой даты. */
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('IF-MODIFIED-SINCE: Mon, 01 Aug 2013 07:07:23'));
		/* Выполняем запрос к серверу. */
		$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
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
		  #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
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
		  die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
		  //return $E->getMessage();
		}
		
		/**
		 * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
		 * нам придётся перевести ответ в формат, понятный PHP
		 */
		 
		
		//print_r($Response);
		
		
		//$Response=$Response['response'];
		//print_r($Response);
		/*foreach($Response as $value){
			$value['id'];
		}*/
		
		//echo "test:".$out;
		
		
	}
	public static function load_sdelki_amoContats($zap=null){//в тесте
	$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		//$subdomain=$subdomain; #Наш аккаунт - поддомен
		#Формируем ссылку для запроса
		if ($zap==null){
		//$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads';
			$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads/';
		}else{
			$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads?id='.$zap;
			echo $link;
		}
		//$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads';
		$curl=curl_init();
		/* Устанавливаем необходимые опции для сеанса cURL */
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
		curl_setopt($curl,CURLOPT_URL,$link);
		curl_setopt($curl,CURLOPT_HEADER,false);
		curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
		/* Вы также можете передать дополнительный HTTP-заголовок IF-MODIFIED-SINCE, в котором указывается дата в формате D, d M Y
		H:i:s. При
		передаче этого заголовка будут возвращены сделки, изменённые позже этой даты. */
		curl_setopt($curl,CURLOPT_HTTPHEADER,array('IF-MODIFIED-SINCE: Mon, 01 Aug 2013 07:07:23'));
		/* Выполняем запрос к серверу. */
		$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
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
		  #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
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
		  die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
		  //return $E->getMessage();
		}
	}
	public static function add_sdelki_amo_full($leads){
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads';
		/* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
		работе с этой
		библиотекой Вы можете прочитать в мануале. */
		$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
		#Устанавливаем необходимые опции для сеанса cURL
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
		$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
		//var_dump($out);
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
		/* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
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
		  #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
		 if($code!=200 && $code!=204) {
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		  }
		  
		}
		catch(Exception $E)
		{
		  die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
		}
	}
	public static function add_sdelki_amo($leads){
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		$link='https://'.$subdomain.'.amocrm.ru/api/v2/leads';
		/* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
		работе с этой
		библиотекой Вы можете прочитать в мануале. */
		$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
		#Устанавливаем необходимые опции для сеанса cURL
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
		$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
		//var_dump($out);
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
		/* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
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
		  #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
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
		  die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
		}
	}
	
	public static function add_client_amo($data=null){
		$subdomain = self::SUBDOMAIN_AMO;
		$login = self::LOGIN_AMO;
		$key = self::HASK_KEY_AMO;
		$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/set';
		/* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
		работе с этой
		библиотекой Вы можете прочитать в мануале. */
		$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
		#Устанавливаем необходимые опции для сеанса cURL
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
		 
		$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
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
		  #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
		 if($code!=200 && $code!=204) {
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		  }
		}
		catch(Exception $E)
		{
		  die('СОЗДАНИЕ Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
		}
		/*
		 Данные получаем в формате JSON, поэтому, для получения читаемых данных,
		 нам придётся перевести ответ в формат, понятный PHP
		 */
		$Response=json_decode($out,true);
		$Response=$Response['_embedded']['items'];
		$output='ID добавленных контактов:'.PHP_EOL;
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
		/* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
		работе с этой
		библиотекой Вы можете прочитать в мануале. */
		$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
		#Устанавливаем необходимые опции для сеанса cURL
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
		$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
		$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
		/* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
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
		  #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
		 if($code!=200 && $code!=204) {
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
		  }
		}
		catch(Exception $E)
		{
		  //die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
		}
		/*
		 Данные получаем в формате JSON, поэтому, для получения читаемых данных,
		 нам придётся перевести ответ в формат, понятный PHP
		 */
		$Response=json_decode($out,true);
		$Response=$Response['_embedded']['items'];
		//$output='ID добавленных контактов:'.PHP_EOL;
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
			/* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
			работе с этой
			библиотекой Вы можете прочитать в мануале. */
			$curl = curl_init(); #Сохраняем дескриптор сеанса cURL
			#Устанавливаем необходимые опции для сеанса cURL
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
			$out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
			$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			/* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
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
				#Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
				if ($code != 200 && $code != 204) {
					//throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
				}

			} catch (Exception $E) {
				die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
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
printLink('api/v4/leads/custom_fields', 'Список utm меток', $subdomain);
printLink('api/v4/users', 'Список пользователей', $subdomain);
printLink('api/v4/contacts/custom_fields', 'Список полей контакта', $subdomain);
*/
?>