<?

$month = array(
	'января',
	"февраля",
	"марта",
	"апреля",
	"мая",
	"июня",
	"июля",
	"августа",
	"сентября",
	"октября",
	"ноября",
	"декабря"
	);
	
function repl($str)	{
	return htmlspecialchars(stripslashes(stripslashes($str)),ENT_QUOTES);
}
// Функция отправки почты
function send_mail($to, $sub, $msg) {

//$headers="Content-type: text/html; charset=\"windows-1251\"";
	$headers= "From: COMCAD \r\n" ;
	$headers.="Content-type: text/html; charset=\"windows-1251\"";
	return mail($to, $sub, $msg, $headers);
		
}

	// ид пользователя
	$user_id = $_POST['user_id'];
	// дата запроса
	$dat = date("j ").$month[date("n")-1].date(" Y").'г. '.date("G:i");
	
	// реквизиты
	$client = repl($_POST['client_name']);
	$leg_add = (isset($_POST['leg_add']) && trim($_POST['leg_add'])) ? repl($_POST['leg_add']) : '';
	$post_add = (isset($_POST['post_add']) && trim($_POST['post_add'])) ? repl($_POST['post_add']) : '';
	$inn = (isset($_POST['inn']) && trim($_POST['inn'])) ? repl($_POST['inn']) : '';
	$kpp = (isset($_POST['kpp']) && trim($_POST['kpp'])) ? repl($_POST['kpp']) : '';

	// массив для предмета счета
	$arr_name_prdm = $_POST['name_prdm'];
	$arr_num_prdm = $_POST['num_prdm'];
	$arr_price_prdm = $_POST['price_prdm'];
	
	// получение масивов для подрядчиков
	$arr_name_podr		= $_POST['name_podr'];
	$arr_podr_podr		= $_POST['podr_podr'];
	$arr_price_podr		= $_POST['price_podr'];
	$arr_num_podr			= $_POST['num_podr'];
	$arr_numacc_podr	=	$_POST['numacc_podr'];
	$arr_opl_podr			= $_POST['summ_podr'];
	$arr_dolg_podr		= $_POST['dolg_podr'];
	
	// форматирование предмета счета
	for($i=0;$i<count($arr_name_prdm);$i++) {
		$arr_name_prdm[$i]	=	repl($arr_name_prdm[$i]);
		$arr_num_prdm[$i]		=	repl($arr_num_prdm[$i]);
		$arr_price_prdm[$i]	=	repl($arr_price_prdm[$i]);
	}

	// форматирование подрядчиков
	for($i=0;$i<count($arr_name_podr);$i++) {
		$arr_name_podr[$i]		=	repl($arr_name_podr[$i]);
		$arr_podr_podr[$i]		=	repl($arr_podr_podr[$i]);
		$arr_price_podr[$i]		=	repl($arr_price_podr[$i]);
		$arr_num_podr[$i]			=	repl($arr_num_podr[$i]);
		$arr_numacc_podr[$i]	=	repl($arr_numacc_podr[$i]);
		$arr_opl_podr[$i]			=	(trim($arr_opl_podr[$i])) ? repl($arr_opl_podr[$i]) : '---';
		$arr_dolg_podr[$i]		=	repl($arr_dolg_podr[$i]);
	}
	
	
	$sum = (trim($_POST['predm_summ_acc'])) ? repl($_POST['predm_summ_acc']) : '0';
	$opl = (isset($_POST['predm_opl_summ']) && trim($_POST['predm_opl_summ'])) ? repl($_POST['predm_opl_summ']) : '';
	$prdm_dolg = (isset($_POST['predm_dolg']) && trim($_POST['predm_dolg'])) ? repl($_POST['predm_dolg']) : '';
	
	$acc_number = (isset($_POST['acc_number']) && trim($_POST['acc_number'])) ? $_POST['acc_number'] : '';
	$podr_sebist = ((trim($_POST['podr_sebist']))) ? repl($_POST['podr_sebist']) : '0';
	$podr_opl_summ = ((trim($_POST['podr_opl_summ']))) ? repl($_POST['podr_opl_summ']) : '0';
	
	$cont_pers = repl($_POST['cont_pers']);
	$cont_tel = repl($_POST['cont_tel']);
	$adition = (isset($_POST['adition']) && trim($_POST['adition'])) ? repl($_POST['adition']) : '';
	
	

		
	$bod = '<html><head><META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251" />';
	$bod .= '<style type="text/css"><!--
				body, table, td { font-family:Tahoma, Verdana, Arial;
					font-size:11px;	color:#333333; }
				body { padding:0px;	padding-left:10px;
					margin:5px;	background-color:#FFFFFF; }
				.title_mail { font-size:14px;	font-weight:bold;	color:#006699;
					padding:10px; text-align:center; border-bottom:2px #006699 solid; }
				.first_col { width:250px;	text-align:right;	color:#006600;
				 	vertical-align:top; border-bottom:1px #006699 solid; font-weight:bold; }
				.two_col { width:250px; padding-left:20px; vertical-align:top;	border-bottom:1px #006699 solid; }
				.title_col { text-align:center;	color:#006600;
				 	vertical-align:top; font-weight:bold; }
				.first_col2 { white-space: nowrap;width:500px;	text-align:center;	color:#006600; font-weight:bold;
				 	vertical-align:top; border:0; font-weight:bold;border-bottom:1px #006699 solid; }
				.col1_sub_tit { width:220px; text-align:right; color:#999999; padding-bottom:5px;  }
				.col2_sub_tit { width:120px; text-align:center; color:#999999; padding-bottom:5px;}
				.col3_sub_tit { width:130px; text-align:center; color:#999999; padding-bottom:5px;}
				.col1_sub { width:220px; text-align:right; color:#000000;padding-bottom:5px; }
				.col2_sub { width:120px; text-align:center; color:#000000;padding-bottom:5px; }
				.col3_sub { width:130px; text-align:center; color:#000000;padding-bottom:5px; }
				.col_sub_tit { color:#000000; }
				.comment { color:#999999; text-align:center;}
		--></style></head>
	<body>';
	
	$bod .= 
	'<table border="0" cellspacing="3" cellpadding="5" width="700">
		<tr><td colspan="2" class="title_mail">Запрос на счет</td>
		</tr><tr>'.
		'<td class="first_col">Название клиента:</td>
		 <td class="two_col">'.
		 $client.'</tr>';
	
	if($leg_add) 
		$bod .= '<tr><td class="first_col">Юридический адрес:</td><td class="two_col">'.$leg_add.'</td></tr>';

	if($post_add)	
		$bod .= '<tr><td class="first_col">Фактический/почтовый адрес:</td><td class="two_col">'.$post_add.'</td></tr>';
	
	if($inn)
		$bod .= '<tr><td class="first_col">ИНН:</td><td class="two_col">'.$inn.'</td></tr>';
		
	if($kpp)
		$bod .= '<tr><td class="first_col">КПП:</td><td class="two_col">'.$kpp.'</td></tr>';
		
	$bod .= '<tr><td colspan="2" >&nbsp;</td></tr>
	<tr><td colspan="2" class="title_col">Предмет счета:</td></tr>
		<tr><td colspan="2" class="first_col2">
		<table class="sub_tab" cellpadding="0" cellspacing="0" width="650">
		<tr><td class="first_col2">Наименование</td>
		<td class="first_col2">Количество</td>
		<td class="first_col2">Стоимость (руб)</td></tr>';
	
	// Список предмета счета					
	for($i=0;$i<count($arr_name_prdm );$i++) {
		$bod .= '<tr><td align="center" height="20">'.$arr_name_prdm[$i].'</td>'.
		'<td align="center" height="20">'.$arr_num_prdm[$i].'</td>'.
		'<td align="center" height="20">'.$arr_price_prdm[$i].'</td></tr>';
	}
	$bod .= '<tr><td colspan="3">&nbsp;</td></tr></table></td></tr>';
	
	// предмет - номер счета
	if($acc_number) {
		$bod .= '<tr><td class="first_col">Номер счета:</td>'.
		'<td class="two_col">'.$acc_number.'</td></tr><tr>';
	}
	
	// Сумма счета
	$bod .= '<tr><td class="first_col">Сумма счета:</td>'.
	'<td class="two_col">'.$sum.'</td></tr><tr>';
	
	if($opl) 	// Оплачено
		$bod .= '<tr><td class="first_col">Оплачено:</td>'.
		'<td class="two_col">'.$opl.'</td></tr>';
		
	if($prdm_dolg) 	// Оплачено
		$bod .= '<tr><td class="first_col">Долг:</td>'.
		'<td class="two_col">'.$prdm_dolg.'</td></tr>';
	
	// Наименования подрядчиков
	$bod .= '<tr><td colspan="2" >&nbsp;</td></tr>
	<tr><td colspan="2" class="title_col">Наименования подрядчиков:</td></tr><tr>
		<td colspan="2" class="first_col2">
		<table class="sub_tab" cellpadding="0" cellspacing="0" width="700">
		<tr><td class="first_col2">Подрядчик</td>
		<td class="first_col2">Наименование</td>
		<td class="first_col2">Количество</td>
		<td class="first_col2">Стоимость(руб)</td>
		<td class="first_col2">Номер счета</td>
		<td class="first_col2">Оплачено</td>
		<td class="first_col2">Долг</td></tr>
		';
		
		
	for($i=0;$i<count($arr_name_podr);$i++) {
		$bod .= '<tr><td align="center" height="20">'.$arr_name_podr[$i].'</td>'.
		'<td align="center" height="20">'.$arr_podr_podr[$i].'</td>'.
		'<td align="center" height="20">'.$arr_num_podr[$i].'</td>'.
		'<td align="center" height="20">'.$arr_price_podr[$i].'</td>'.
		'<td align="center" height="20">'.$arr_numacc_podr[$i].'</td>'.
		'<td align="center" height="20">'.$arr_opl_podr[$i].'</td>'.
		'<td align="center" height="20">'.$arr_dolg_podr[$i].'</td></tr>';
	}
	$bod .= '<tr><td colspan="7">&nbsp;</td></tr></table></td></tr>'.
	
	
	// Общая себестоимость
	'<tr><td class="first_col">Общая себестоимость:</td>
		<td class="two_col">'.$podr_sebist.'</td></tr>'.
		
	// подрядчики - суммарная оплата
	'<tr><td class="first_col">Суммарная оплата:</td>
		<td class="two_col">'.$podr_opl_summ.'</td></tr>';

		
//	if($acc_number) 		// Номер счета
//		$bod .= '<tr><td class="first_col">Номер счета:</td>
//			<td class="two_col">'.$acc_number.'</td></tr>';
			
	
	if($adition)			// Примечание
		$bod .= '<tr><td colspain="2">&nbsp;</td></tr>
			<tr><td class="first_col">Примечание:</td>
			<td class="two_col">'.$adition.'</td></tr>
			<tr><td colspain="2">&nbsp;</td></tr>';
			

	$bod .= '<tr><td colspan="2" class="comment">
		<strong>Дата: &nbsp;&nbsp;</strong>'.$dat.'<br />
		<strong>Запросил: &nbsp;&nbsp;</strong>'.repl($_POST['full_name']).'</td></tr>
		</table></body></html>';

	
	$mail_arr = $_POST['mail_arr'];
	
//	echo $bod;
//	exit;
	
//	print_r($mail_arr);
	
//	if(send_mail('vlad@printfolio.ru', 'Запрос на счет, '.$dat, $bod))
//		echo 'send';
//	else 
//		echo 'error';
//	exit;


	
	for($i=0;$i<count($mail_arr);$i++) {
		if($i%10==0){
			sleep(1);
		}
//		echo stripslashes($_POST['short_name']).'-';
		send_mail( $mail_arr[$i], stripslashes($_POST['short_name']).' ( '.stripslashes($_POST['client_name']).' ) , '.stripslashes($_POST['full_name']), $bod );
	}
	
//	exit;
	$back_adr = $_POST['back_adr'];
	header("Location: ".$back_adr);

?>