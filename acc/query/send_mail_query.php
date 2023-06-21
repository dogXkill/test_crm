<?
require_once("../includes/db.inc.php");


	$query_id = @$_GET['id'];
	if($query_id) {
	
	
		// Запуск скрипта отправки письма с новым запросом счета
		//----------------------------------------------------------------------------
		
		$bod = '<html><body onLoad="document.fff.submit();">'."\r\n";
		$bod .= '<form name="fff" action="http://printfolio.ru/query/send_mail_cmd.php" method="post">'."\r\n";
//		$bod .= '<form name="fff" action="send_mail_cmd.php" method="post">'."\r\n";

		
		// чтение нового запроса
		$query = "SELECT * FROM queries WHERE uid=".$query_id;
		$res = mysql_query($query);
		$r_qr = mysql_fetch_array($res);
		
		
		$query = "SELECT * FROM clients WHERE uid=".$r_qr['client_id'];
		$res = mysql_query($query);
		$r_req = mysql_fetch_array($res);
		
		
		
		$bod .= '<input name="user_id" type="hidden" value="'.$r_qr['user_id'].'" />'."\r\n";
		
		
		// предмет - сумма счета
		$bod .= '<input name="predm_summ_acc" type="hidden" value="'.htmlspecialchars($r_qr['prdm_sum_acc'],ENT_QUOTES).'" />'."\r\n";
		// предмет - оплата
		$bod .= '<input name="predm_opl_summ" type="hidden" value="'.htmlspecialchars($r_qr['prdm_opl'],ENT_QUOTES).'" />'."\r\n";
		// предмет - долг
		$bod .= '<input name="predm_dolg" type="hidden" value="'.htmlspecialchars($r_qr['prdm_dolg'],ENT_QUOTES).'" />'."\r\n";
		
		
		// подрядчики - общая себистоимость
		$bod .= '<input name="podr_sebist" type="hidden" value="'.htmlspecialchars($r_qr['podr_sebist'],ENT_QUOTES).'" />'."\r\n";
		// подрядчики - суммарная оплата
		$bod .= '<input name="podr_opl_summ" type="hidden" value="'.htmlspecialchars($r_qr['podr_opl'],ENT_QUOTES).'" />'."\r\n";
		// подрядчики - суммарный долг
		$bod .= '<input name="podr_dolg_summ" type="hidden" value="'.htmlspecialchars($r_qr['podr_dolg']	,ENT_QUOTES).'" />'."\r\n";


		// примечание
		if(trim($r_qr['note']))	
			$bod .= '<input name="adition" type="hidden" value="'.htmlspecialchars($r_qr['note'],ENT_QUOTES).'" />'."\r\n";



		// предмет - номер счета
		$acc_num = trim($r_qr['prdm_num_acc']);
			
		$acc_num = ( $acc_num === 'none' ) ? 'не нужен' : $acc_num;

		if($acc_num)
			$bod .= '<input name="acc_number" type="hidden" value="'.htmlspecialchars($acc_num,ENT_QUOTES).'" />'."\r\n";

		
		
		// чтение полного имени менеджера
		$query = "SELECT surname,name,father FROM users WHERE uid=".$r_qr['user_id'];
		$res = mysql_query($query);
		$r = mysql_fetch_array($res);
		$full_name = $r['surname'].' '.$r['name'].' '.$r['father'];


		// полное название клиента
		$bod .= '<input name="client_name" type="hidden" value="'.htmlspecialchars($r_req['name'],ENT_QUOTES).'" />'."\r\n";
		// короткое название клиента
		$bod .= '<input name="short_name" type="hidden" value="'.htmlspecialchars($r_req['short'],ENT_QUOTES).'" />'."\r\n";
		// полное имя менеджера, запросившего счет
		$bod .= '<input name="full_name" type="hidden" value="'.htmlspecialchars($full_name,ENT_QUOTES).'" />'."\r\n";

		
		
		
		// список предмета счета
		$query = "SELECT * FROM obj_accounts WHERE query_id=".$query_id;
		$res = mysql_query($query);
		while($r_prdm = mysql_fetch_array($res)) {
			// наименование продукта
			$bod .= '<input name="name_prdm[]" type="hidden" value="'.htmlspecialchars($r_prdm['name'],ENT_QUOTES).'" />'."\r\n";
			// количество
			$bod .= '<input name="num_prdm[]" type="hidden" value="'.htmlspecialchars($r_prdm['num'],ENT_QUOTES).'" />'."\r\n";
			// стоимость
			$bod .= '<input name="price_prdm[]" type="hidden" value="'.htmlspecialchars($r_prdm['price'],ENT_QUOTES).'" />'."\r\n";
		}
		
		
		// список предмета счета
		$query = "SELECT * FROM contractors_list WHERE query_id=".$query_id;
		$res_podr = mysql_query($query);
		
		
		while($r_podr = mysql_fetch_array($res_podr)) {
//		$arr_podr_send = array(); // массив имен подрядчиков для отправки на майл
		
			if($r_podr['contr_id'] == 0)
				$podr_name = 'не определен';
			else {	
				$query = sprintf("SELECT name FROM contractors WHERE uid=%d",$r_podr['contr_id']);
				$res_contr = mysql_query($query);
				$r_contr = mysql_fetch_array($res_contr);
				$podr_name = $r_contr['name'];
			}

			
			// наименование продукта
			$bod .= '<input name="name_podr[]" type="hidden" value="'.htmlspecialchars($r_podr['name'],ENT_QUOTES).'" />'."\r\n";
			// нименование подрядчика
			$bod .= '<input name="podr_podr[]" type="hidden" value="'.htmlspecialchars($podr_name,ENT_QUOTES).'" />'."\r\n";
			// стоимость
			$bod .= '<input name="price_podr[]" type="hidden" value="'.htmlspecialchars($r_podr['price'],ENT_QUOTES).'" />'."\r\n";
			// количество
			$bod .= '<input name="num_podr[]" type="hidden" value="'.htmlspecialchars($r_podr['num'],ENT_QUOTES).'" />'."\r\n";
			// номер счета
			$bod .= '<input name="numacc_podr[]" type="hidden" value="'.htmlspecialchars($r_podr['acc_number'],ENT_QUOTES).'" />'."\r\n";
			// сумма оплаты
			$bod .= '<input name="summ_podr[]" type="hidden" value="'.htmlspecialchars($r_podr['opl'],ENT_QUOTES).'" />'."\r\n";
			// сумма долга
			$bod .= '<input name="dolg_podr[]" type="hidden" value="'.htmlspecialchars($r_podr['debt'],ENT_QUOTES).'" />'."\r\n";


		}
		
		
		
		// РЕКВИЗИТЫ
		
		// юридический адрес
		$bod .= '<input name="leg_add" type="hidden" value="'.htmlspecialchars($r_req['legal_address'],ENT_QUOTES).'" />'."\r\n";
		// фактический адрес
		$bod .= '<input name="post_add" type="hidden" value="'.htmlspecialchars($r_req['postal_address'],ENT_QUOTES).'" />'."\r\n";
		// ИНН
		$bod .= '<input name="inn" type="hidden" value="'.htmlspecialchars($r_req['inn'],ENT_QUOTES).'" />'."\r\n";
		// КПП
		$bod .= '<input name="kpp" type="hidden" value="'.htmlspecialchars($r_req['kpp'],ENT_QUOTES).'" />'."\r\n";
		
		
		
		// КОНТАКТЫ
		
		// контактное лицо
		$bod .= '<input name="cont_pers" type="hidden" value="'.htmlspecialchars($r_req['cont_pers'],ENT_QUOTES).'" />'."\r\n";
		// контактный телефон
		$bod .= '<input name="cont_tel" type="hidden" value="'.htmlspecialchars($r_req['cont_tel'],ENT_QUOTES).'" />'."\r\n";



		
//		$bod .= '<input name="compl_cost" type="hidden" value="'.htmlspecialchars($_POST['compl_cost'],ENT_QUOTES).'" />'."\r\n";
			
//		$bod .= '<input name="sum" type="hidden" value="'.htmlspecialchars($_POST['summ_acc'],ENT_QUOTES).'" />'."\r\n";
//		$bod .= '<input name="cost" type="hidden" value="'.htmlspecialchars($_POST['cost'],ENT_QUOTES).'" />'."\r\n";

		// адрес страницы для возврата после отправки на майлы	
		$bod .= '<input name="back_adr" type="hidden" value="http://'.$_SERVER['HTTP_HOST'].'/acc/query/" />'."\r\n";
		
		
		// список майлов для отправки
		$query = "SELECT * FROM mail ORDER BY uid";
		$res = mysql_query($query);
		while($r = mysql_fetch_array($res)) { 
			$bod .= '<input name="mail_arr[]" type="hidden" value="'.htmlspecialchars($r['email'],ENT_QUOTES).'" />'."\r\n";
		}
		
		
		$bod .= '</form></body></html>'."\r\n";
		echo $bod;
		
		
		$pereadr = 'add';



	}
//	if(!$pereadr)
//		header("Location: /acc/query/");


?>


