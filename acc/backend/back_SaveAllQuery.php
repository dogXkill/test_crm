<?

// Сохранение всего запроса на счет в базе
require_once "../includes/lib/JsHttpRequest/JsHttpRequest.php";
require_once("../includes/db.inc.php");
require_once("send_notifications.php");
include_once '../../amo/amocrm.php';
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

$clientTypes = [
    'physical' => 'Физ. лицо',
    'legal' => 'Юр. лицо'
];


// Блок динамической передачи данных в java script
//----------------------------------------------------------------------
$JsHttpRequest = new JsHttpRequest("windows-1251");

// переводит дату из формата '23.05.1997' в '1997-05-23' для корректного сохранения в базе
function date_switch($val) {
    $a = explode('.',$val);
    $str =@$a[2].'-'.@$a[1].'-'.@$a[0];
    return $str;
}



$new_query_id = 0;						// ид нового сохраненного запроса

$a = $_REQUEST['arr'];				// получение массива всех значений
$b = $_REQUEST['arr'];
extract($a, EXTR_PREFIX_SAME, "wddx");

$user_id = $a['usid'];

//получаем type менеджера
$query_men = "SELECT type FROM users WHERE uid=$user_id";
$res_men = mysql_query($query_men);

if($r_men = mysql_fetch_array($res_men)) {
  $type_user = $r_men['type'];
}

$client_lst = $a['client_lst'];

    // ----------------- КЛИЕНТ, РЕКВИЗИТЫ И КОНТАКТНАЯ ИНФОРМАЦИЯ ------------------

    if($client_lst == 0 or $client_lst == "") {		// в списке клиентов данного клиента нет, создаем нового
            $q = ("INSERT INTO clients(user_id,short,name,postal_address,deliv_address,inn,kpp,okpo,comment,cont_pers,cont_tel,rs_acc,bank,bik,firm_tel,email) VALUES ('$ed_us_id', '$client_sh', '$client_full', '$post_add', '$deliv_add', '$inn', '$kpp', '$okpo', '$comment', '$cont_pers', '$cont_tel', '$rs', '$bank', '$bik', '$firm_tel', '$email')");
            mysql_query($q);

     $client_lst = mysql_insert_id();		// ид нового клиента

    }
    else {
    // ид клиента выбрано из списка , просто обновляем информацию
    $q = "UPDATE clients SET short='$client_sh', name='$client_full', postal_address='$post_add', deliv_address='$deliv_add', inn='$inn', kpp='$kpp', okpo='$okpo', comment='$comment', cont_pers='$cont_pers', cont_tel='$cont_tel', rs_acc='$rs', bank='$bank', bik='$bik', firm_tel='$firm_tel', email='$email' WHERE uid='$client_lst'";
    mysql_query($q);

    }

 echo mysql_error();



    //---------------------------------------------------------------------------------

    $tpacc = $a['tpacc'];

// ################################################################################
// ############################  НОВЫЙ ЗАПРОС  ####################################
// ################################################################################

if($a['edit'] == 'new') {

// ################################################################################
// #####################  запись в базу письма для отправки  ######################


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


$bod = '<html><head><META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251" />';

$bod .= '<style type="text/css"><!--
body,table,td {font-family: Arial, Helvetica, sans-serif;font-size: 11px;color:#000000;}
body {padding:0px;padding-left:10px;margin:5px;background-color:#FFFFFF;}
.tab_main {width:800px;border:2px #FFFFFF solid;background-color:#EEEEEE;}
.td_title {background-color:#A9DFA8;height:25px;text-align:center;font-weight:bold;font-size:12px;vertical-align:middle;}
.td_title2 {background-color:#CBEEC8;height:18px;text-align:center;font-weight:bold;font-size:12px;vertical-align:middle;}
.td_left {text-align:right;width:50%;border-bottom:2px #FFFFFF solid;font-weight:bold;height:18px;}
.td_right {text-align:left;width:50%;border-bottom:2px #FFFFFF solid;padding-left:20px;height:18px;}
.td_empty {background-color:#FFFFFF;}
.tab2 {width:100%;}
.tab2_td1 {border-bottom:2px #FFFFFF solid;border-right:2px #FFFFFF solid;width:60%;text-align:center;height:18px;}
.tab2_td2 {border-bottom:2px #FFFFFF solid;border-right:2px #FFFFFF solid;width:20%;text-align:center;height:18px;}
.tab2_td3 {border-bottom:2px #FFFFFF solid;border-right:2px #FFFFFF solid;width:20%;text-align:center;height:18px;}
.tab2_tdspace {height:70px;text-align:right;vertical-align:middle;background-color:#FFFFFF;}
.tab2_sub {width:400px;background-color:#EEEEEE;}
.tab2_sub_title {background-color:#CBEEC8;height:18px;text-align:center;font-weight:bold;font-size:11px;vertical-align:middle;border-right:1px #FFFFFF solid;	border-bottom:1px #FFFFFF solid;width:25%;}
.tab2_sub_td {text-align:center;width:25%;border-right:1px #FFFFFF solid;}
.tab3_td1 {border-bottom:2px #FFFFFF solid;border-right:2px #FFFFFF solid;width:25%;text-align:center;height:18px;}
.tab3_td2 {border-bottom:2px #FFFFFF solid;border-right:2px #FFFFFF solid;width:10%;text-align:center;height:18px;}
.tab3_td_nr {border-bottom:2px #FFFFFF solid;border-right:2px #FFFFFF solid;text-align:center;height:18px;}
.tab3_sub {width:600px;background-color:#EEEEEE;}
.tab3_sub_title {background-color:#CBEEC8;height:18px;	text-align:center;font-weight:bold;font-size:11px;vertical-align:middle;border-right:1px #FFFFFF solid;	border-bottom:1px #FFFFFF solid;width:36%;}
.tab3_sub_td {text-align:center;width:32%;border-right:1px #FFFFFF solid;}
--></style></head><body>';

$dat = date("j ").$month[date("n")-1].date(" Y").' г. '.date("G:i");

$bod .= '<table class="tab_main" cellpadding="0" cellspacing="0" align="center">
  <tr><td colspan="2" class="td_title">запрос на счет '.$dat.', '.$a['user_full_name'].'</td></tr>';

if ($a['client_type']) {
    $bod .= '<tr>
    <td class="td_left">Тип:</td>
    <td class="td_right">' . $clientTypes[$a['client_type']] . '</td>
  </tr>';
}

if ($a['client_full']) {
    $bod .= '<tr>
    <td class="td_left">Полное юридическое наименование:</td>
    <td class="td_right">' . $a['client_full'] . '</td>
  </tr>';
}

if($a['post_add'])
    $bod .= '<tr>
    <td class="td_left">Фактический/почтовый адрес:</td>
    <td class="td_right">'.$a['post_add'].'</td>
  </tr>';

  if($a['deliv_add'])
    $bod .= '<tr>
    <td class="td_left">Адрес доставки:</td>
    <td class="td_right">'.$a['deliv_add'].'</td>
  </tr>';

if($a['inn'])
    $bod .= '<tr>
    <td class="td_left">ИНН:</td>
    <td class="td_right">'.$a['inn'].'</td>
  </tr>';

if($a['kpp'])
    $bod .= '<tr>
    <td class="td_left">КПП:</td>
    <td class="td_right">'.$a['kpp'].'</td>
  </tr>';

if($a['okpo'])
    $bod .= '<tr>
    <td class="td_left">ОКПО:</td>
    <td class="td_right">'.$a['okpo'].'</td>
  </tr>';

if(trim($a['rs']))
    $bod .= '<tr>
    <td class="td_left">р/с:</td>
    <td class="td_right">'.$a['rs'].'</td>
  </tr>';

if(trim($a['bank']))
    $bod .= '<tr>
    <td class="td_left">открыт в банке:</td>
    <td class="td_right">'.$a['bank'].'</td>
  </tr>';

if(trim($a['bik']))
    $bod .= '<tr>
    <td class="td_left">БИК:</td>
    <td class="td_right">'.$a['bik'].'</td>
  </tr>';

if(trim($a['firm_tel']))
    $bod .= '<tr>
    <td class="td_left">Телефон компании:</td>
    <td class="td_right">'.$a['firm_tel'].'</td>
  </tr>';

if(trim($a['email']))
    $bod .= '<tr>
    <td class="td_left">E-Mail:</td>
    <td class="td_right">'.$a['email'].'</td>
  </tr>';


if(trim($a['cont_pers']))
    $bod .= '<tr>
    <td class="td_left">Контактное лицо:</td>
    <td class="td_right">'.$a['cont_pers'].'</td>
  </tr>';

if(trim($a['cont_tel']))
    $bod .= '<tr>
    <td class="td_left">Контактный телефон:</td>
    <td class="td_right">'.$a['cont_tel'].'</td>
  </tr>';


if(trim($a['typ_ord'])){

if($a['typ_ord'] == "1"){$typ_ord = "под заказ";}
if($a['typ_ord'] == "2"){$typ_ord = "магазин";}
if($a['typ_ord'] == "3"){$typ_ord = "магазин с лого";}

    $bod .= '<tr>
    <td class="td_left">Тип заказа:</td>
    <td class="td_right">'.$typ_ord.'</td>
  </tr>';
  }

if(trim($a['corsina_order_uid']))
    $bod .= '<tr>
    <td class="td_left">Ссылка на заказ в интернет магазине:</td>
    <td class="td_right"><a href=http://www.paketoff.ru/admin/shop/orders/view/?id='.$a['corsina_order_uid'].' target=_blank>'.$a['corsina_order_num'].'</a></td>
  </tr>';

if(trim($a['form_of_payment']))
if($a['form_of_payment'] == "1"){$form_of_payment = "наличные";}
if($a['form_of_payment'] == "2"){$form_of_payment = "безнал";}
if($a['form_of_payment'] == "3"){$form_of_payment = "безнал по реквизитам";}
if($a['form_of_payment'] == "4"){$form_of_payment = "по карте";}
    $bod .= '<tr>
    <td class="td_left">Форма оплаты:</td>
    <td class="td_right">'.$form_of_payment.'</td>
  </tr>';

if(trim($a['deliv_id'])){
if($a['deliv_id'] == ""){$deliv_type = "не указано";}
if($a['deliv_id'] == "1"){$deliv_type = "самовывоз";}
if($a['deliv_id'] == "2"){$deliv_type = "Доставка по Мск";}
if($a['deliv_id'] == "8"){$deliv_type = "Доставка до ТК";}
if($a['deliv_id'] == "3"){$deliv_type = "Срочная";}
if($a['deliv_id'] == "5"){$deliv_type = "СДЭК";}
if($a['deliv_id'] == "15"){$deliv_type = "Деловые линии";}
    $bod .= '<tr>
    <td class="td_left">Доставка:</td>
    <td class="td_right">'.$deliv_type.'</td>
  </tr>';}

$bod .= '<tr>
    <td colspan="2" class="td_empty">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">

    <table class="tab2" cellpadding="0" cellspacing="0" >
      <tr>
        <td colspan="4" class="td_title2">Предмет счета</td>
      </tr>
      <tr>
        <td class="tab2_td1"><strong>Наименование</strong></td>
        <td class="tab2_td2"><strong>Количество</strong></td>
        <td class="tab2_td3"><strong>Цена за ед.</strong></td>
        <td class="tab2_td3"><strong>Итого</strong></td>
      </tr>';



for($i=0;$i<count($a['predmet']['list']);$i++) {
    $itog = $a['predmet']['list'][$i]['num'] * $a['predmet']['list'][$i]['price'];
    $bod .= '<tr>
        <td class="tab2_td1">'.$a['predmet']['list'][$i]['name'].'</td>
        <td class="tab2_td2">'.$a['predmet']['list'][$i]['num'].'</td>
        <td class="tab2_td3">'.$a['predmet']['list'][$i]['price'].'</td>
        <td class="tab2_td3">'.$itog.'</td>
      </tr>';
}

$bod .= '<tr>
<td colspan="4" class="tab2_tdspace">
<table class="tab2_sub" cellpadding="0" cellspacing="0" align="right">
<tr>';


$bod .= '<td class="tab2_sub_title">Сумма счета</td>
<td class="tab2_sub_title">Оплачено</td>
<td class="tab2_sub_title">Долг</td>
</tr><tr>';


$bod .= '<td class="tab2_sub_td">'.$a['predmet']['summ_acc'].' скидка: '.$a['predmet']['skidka'].'%</td>
<td class="tab2_sub_td">'.$a['predmet']['opl']['summ'].'</td>
<td class="tab2_sub_td">'.$a['predmet']['dolg'].'</td></tr>';


$bod .= '</table></td></tr></table></td>
</tr><tr><td colspan="2">
<table class="tab2" cellpadding="0" cellspacing="0">
<tr><td colspan="6" class="td_title2">Наименование подрядчиков</td></tr>
<tr>
<td class="tab3_td1"><strong>Подрядчик</strong></td>
<td class="tab3_td1"><strong>Наименование</strong></td>
<td class="tab3_td2"><strong>Количество</strong></td>
<td class="tab3_td2"><strong>Стоимость</strong></td>
<td class="tab3_td2"><strong>Оплачено</strong></td>
<td class="tab3_td2"><strong>Долг</strong></td>
</tr>';

for($i=0;$i<count($a['podr']['list']);$i++) {
    if(!trim($a['podr']['list'][$i]['num_acc']))
        $a['podr']['list'][$i]['num_acc'] = '-';
    $bod .= '<tr>
        <td class="tab3_td_nr">'.$a['podr']['list'][$i]['podr_name'].'</td>
        <td class="tab3_td_nr">'.$a['podr']['list'][$i]['name'].'</td>
        <td class="tab3_td_nr">'.$a['podr']['list'][$i]['num'].'</td>
        <td class="tab3_td_nr">'.$a['podr']['list'][$i]['price'].'</td>
        <td class="tab3_td_nr">'.$a['podr']['opl'][$i]['summ'].'</td>
        <td class="tab3_td_nr">'.$a['podr']['list'][$i]['dolg'].'</td>
      </tr>';
}

$bod .= '<tr><td class="tab2_tdspace" colspan="6">
        <table class="tab2_sub" cellpadding="0" cellspacing="0" align="right">
            <tr>
              <td class="tab3_sub_title">Общая себестоимость</td>
              <td class="tab3_sub_title">Оплачено (сумм.)</td>
              <td class="tab3_sub_title">Долг (сумм.):</td>
            </tr>
            <tr>
              <td class="tab3_sub_td">'.$a['podr']['sebist'].'</td>
              <td class="tab3_sub_td">'.$a['podr']['opl_summ'].'</td>
              <td class="tab3_sub_td">'.$a['podr']['dolg_summ'].'</td>
            </tr>
        </table></td></tr></table></td></tr>';

if(trim($a['note']))
    $bod .= '<tr><td class="td_left">Примечание:</td>
    <td class="td_right">'.$a['note'].'</td></tr>';

$bod .= '</table></body></html>';

$tema = $a['client_sh'].' ( '.$a['client_full'].' ) , '.$a['user_full_name'].' - заявка на счет ';


 if($a['uniq_id'] == ''){
     $uniq_id = $uniq_id = md5(uniqid(rand(), 1));
 } else {$uniq_id = $a['uniq_id'];}

	// ################################################################################
// #################  обработка AMOCRM для связи с заказом  #######################
// ################################################################################
$amoCrm=new AmoCrm();
$amoCrm->login_amo();
$mas_zn=$amoCrm->valid($a['firm_tel'],$a['email']);


//,a['email']
$res=$amoCrm->load_sdelki_amo($mas_zn['phone']);//поиск по телефону

$res1=$amoCrm->load_sdelki_amo($mas_zn['email']);//поиск по email

//перебираем массивы (если они не пустые и не равны 1 значению) на поиск актуального заказа
$status_ok=142;//успешно
	$status_error=143;//не реализовано
	$statud_dop=30754849;//заказ
	$mas_isk_search=array(142,143,30754849);
echo $res."|".$res1;

if ($res!=null && count($res)>1){//найдено
	$dat_max=0;//максимальная дата 
	
	$temp_id=0;
	foreach ($res as $value){
		//if ($value['status_id']!=$status_ok && $value['status_id']!=$status_error && $value['status_id']){
			if (in_array($value['status_id'],$mas_isk_search)===false){
			if ($value['created_at']>$dat_max){$dat_max=$value['created_at'];$temp_id=$value['id'];}
		}
	}
	$res=$temp_id;
}else if (count($res)==1 && (in_array($res[0]['status_id'],$mas_isk_search)===false)){
//if (count($res)==1 && ($res[0]['status_id']!=$status_ok && $res[0]['status_id']!=$status_error)){
	$res=$res[0]['id'];
}else{
	//заказа нету
	$res=null;
}
if ($res1!=null && count($res1)>1){
	$dat_max1=0;//максимальная дата 
	$temp_id=0;
	foreach ($res1 as $value){
		//if ($value['status_id']!=$status_ok && $value['status_id']!=$status_error){
		if (in_array($value['status_id'],$mas_isk_search)===false){
			if ($value['created_at']>$dat_max1){$dat_max1=$value['created_at'];$temp_id=$value['id'];}
		}
	}
	$res1=$temp_id;
}else if (count($res1)==1 && (in_array($res1[0]['status_id'],$mas_isk_search)===false)){
	$res1=$res[0]['id'];
}else{
	//заказа нету
	$res1=null;
}
if ($res!=null && $res1!=null){
	//заполненные
	if ($res==$res1){$amo_id=$res;}//одинаковые
	else{
		//они разные
		if ($dat_max1>$dat_max){$amo_id=$res1;}else{$amo_id=$res;}
	}
}else{
//минимум 1 res равен null
	if ($res1!=null){
		$amo_id=$res1;
	}else if ($res!=null){
		$amo_id=$res;
	}else{
		$amo_id=null;
	}
}
//добавление контактов (которых не было) amo_id
if ($amo_id!=null){
	$id_crm_sdelka=$amo_id;
	$mas_zn=$amoCrm->valid($a['firm_tel'],$a['email']);
	$dop_zn=$amoCrm->valid($a['cont_tel']);
	$res1=$amoCrm->load_sdelki_amoContats($id_crm_sdelka);
	$mas_new_email=array();
	$mas_new_phone=array();
	if ($res1!=null && $res1!=0){
			$res2=$amoCrm->check_client_amo_info($res1);
			echo "<pre>";
			print_r($res2);
			echo "</pre>";
			foreach ($res2 as $value){
				if ($value['code']=='EMAIL'){
					foreach ($value['values'] as $value_dop){
						if ($value_dop['value']!=$mas_zn['email']){
							$mas_new_email[]=$mas_zn['email'];
						}
					}
				}else if ($value['code']=='PHONE'){
					foreach ($value['values'] as $value_dop){
						$phones=$amoCrm->valid($value_dop['value']);
						//print_r($phones);
						echo $phones['phone']."/".$mas_zn['phone']."</br>";
						if ($phones['phone']!=$mas_zn['phone']){
							$mas_new_phone[]=$mas_zn['phone'];
						}else if ($value_dop['value']!=$dop_zn['phone']){
							$mas_new_phone[]=$dop_zn['phone'];
						}
					}
				}
			}
		}
	// изменения менеджера в сделке на amo
	/*отправляем запрос в users на поиск amo_crm_id 
	если есть,то изменяем в сделке отвеств.*/

	//$a['user_id'] - для
	$amo_users=mysql_query("SELECT * FROM `users` WHERE `uid` = {$a['user_id']} ");
	$amo_users_row = mysql_fetch_array($amo_users);
	if ($amo_users_row['amo_id']!='' && $amo_users_row['amo_id']!=0){
		//не пустое
		$user_amo_id=$amo_users_row['amo_id'];
		$datetime = new DateTime();
		$update['update']=array(
			array(
			'id'=>$amo_id,
			'responsible_user_id'=>intval($user_amo_id),
			 'updated_at'=>$datetime->getTimestamp(),
			 'status_id'=>intval(30754849)
				 )
		);
		$res_update=$amoCrm->add_sdelki_amo($update);
		
	}
}

/*----------------------------*/

    // --------------------- ЗАПРОС ------------------
    $query = "INSERT INTO queries(client_id,user_id,";
    $vals = $client_lst.','.$a['user_id'].',';

    // тип клиента
    $query 		.= 'client_type,';
    $vals 		.= "'{$a['client_type']}',";

    if($tpacc && $a['predmet']['skidka']) {
        $query 	.= 'skidka,';
        $vals 	.= "'".$a['predmet']['skidka']."',";
    }
    $query 		.= 'prdm_sum_acc,';										// предмет - сумма счета
    $vals 		.= "'".$a['predmet']['summ_acc']."',";

    if($tpacc) {														// предмет - оплата, долг
        $query 	.= 'prdm_opl,prdm_dolg,';
        $vals 	.= "'".$a['predmet']['opl']['summ']."','".$a['predmet']['dolg']."',";
    }

    $query		.= 'podr_sebist,';										// подрядчики - общая себистоимость
    $vals 		.= "'".$a['podr']['sebist']."',";

    if($tpacc) {														// подрядчики - оплата, долг
        $query 	.= 'podr_opl,podr_dolg,';
        $vals 	.= "'".$a['podr']['opl_summ']."','".$a['podr']['dolg_summ']."',";
    }

    $query 		.= 'note,date_query,booking_till';									// примечание, дата запроса
    $vals 		.= "'".$a['note']."', NOW(),'".$a['booking_till']."'";
	

    $query 		.= ',prj_ready,typ_ord,form_of_payment,deliv_id,corsina_order_uid,corsina_order_num,uniq_id';									// тип заказа, форма оплаты
    $vals 		.= ",'0', ".$a['typ_ord'].", ".$a['form_of_payment'].", ".$a['deliv_id'].", '".$a['corsina_order_uid']."', '".$a['corsina_order_num']."', '".$uniq_id."'";
	
	if ($amo_id!=null){
		$query.=',amo_crm_id';
		$vals.=',"'.$amo_id.'"';
	}

    $query .= ") VALUES(".$vals.")";


    mysql_query($query);
	
   // echo $query." ".mysql_error();

    $new_query_id = mysql_insert_id();
	//создание записи в client_email_status
	//client_lst
	$status_push=$a['status_check_email'];
	
	$data1 = mysql_query("SELECT * FROM `client_email_status` WHERE  `client_id` ='{$client_lst}'");
	if (mysql_num_rows($data1) != 0) {
		//обновляем 
		$sql="UPDATE `client_email_status` SET `status` = '{$status_push}' WHERE `client_id` = '{$client_lst}';";
	}else{	
		$sql="INSERT INTO `client_email_status` (`id`, `client_id`, `status`) VALUES (NULL, '{$client_lst}', '{$status_push}');";
	}
	$result  = mysql_query($sql);
	
	//запись о сфере деятельности
	$sfera_dey=$a['sfera_dei'];
	$data1 = mysql_query("SELECT * FROM `client_sfera` WHERE  `id_client` ={$client_lst}");
	if (mysql_num_rows($data1) != 0) {
		//обновляем 
		$sql="UPDATE `client_sfera` SET `id_sfera` = {$sfera_dey} WHERE `id_client` = {$client_lst};";
	}else{	
		$sql="INSERT INTO `client_sfera` (`id_client`, `id_sfera`) VALUES ( {$client_lst}, {$sfera_dey});";
	}
	$result  = mysql_query($sql);
	
    // --------------------- СПИСОК ПРЕДМЕТА СЧЕТА ------------------

    for($i=0;$i<count($a['predmet']['list']);$i++) {

        if ($a['predmet']['list'][$i]['art_num'] == 'd') {
            $a['predmet']['list'][$i]['price_our'] = $a['predmet']['list'][$i]['price'];
        }

        $query = sprintf("INSERT INTO obj_accounts(query_id,art_num,nn,name,num,price,r_price_our,tip_izd,tip_sdelki) VALUES(%d,'%s','%s','%s','%s','%s', '%s','%s','%s')",$new_query_id, $a['predmet']['list'][$i]['art_num'],($i+1), $a['predmet']['list'][$i]['name'], $a['predmet']['list'][$i]['num'], $a['predmet']['list'][$i]['price'],$a['predmet']['list'][$i]['price_our'],$a['predmet']['list'][$i]['tip_izd'],$a['predmet']['list'][$i]['tip_sdelki']);

                mysql_query($query);
    }




    // ------------------ СПИСОК ПОДРЯДЧИКОВ --------------------------


    for($i=0;$i<count($a['podr']['list']);$i++) {
        $query = sprintf("INSERT INTO contractors_list(query_id,nn,contr_id,name,price,num,opl,debt)  VALUES(%d,%d,%d,'%s','%s','%s','%s','%s')",
        $new_query_id,
        ($i+1),
        $a['podr']['list'][$i]['podr'],
        $a['podr']['list'][$i]['name'],
        $a['podr']['list'][$i]['price'],
        $a['podr']['list'][$i]['num'],
        $a['podr']['list'][$i]['num_acc'],
        $a['podr']['opl'][$i]['summ'],
        $a['podr']['list'][$i]['dolg']);

                mysql_query($query);

        $new_contr_id = mysql_insert_id();


    }



        // форматирование предмета счета
        $acc_num = trim($a['predmet']['acc_number']);
        if( ($acc_num == 'нет') || ($acc_num == 'no') || ($acc_num == '-') )
            $acc_num = 'none';
        else
            $acc_num = intval($acc_num);

        $acc_num = ( $acc_num === 'none' ) ? 'не нужен' : $acc_num;




$tema = stripslashes($tema);
$bod  = stripslashes($bod);  // тело письма


send_mail($tema, $bod, $user_id);


// возврат значений в родительский скрипт
$GLOBALS['_RESULT'] = array(
    "id"	=> $new_query_id,
	"amo_id"=>$amo_id,
    "e"	=> mysql_error(),
	"id_qu"=>$new_query_id
);
//echo $new_query_id;

}

// ################################################################################
// ########################  РЕДАКТИРОВАНИЕ ЗАПРОСА  ##############################
// ################################################################################

else {

        // -----------------обработка номера счета -----------------------
   //		if($tpacc) {		// админ, бухгалтер
   //       $acc_num = trim($a['predmet']['acc_number']);



        // -------------------- таблица - queries --------------------
        $query = "UPDATE queries SET client_id='$client_lst', ";


        // тип клиента
        $query 		.= "client_type='{$a['client_type']}', ";

       	// -----------------обработка номера накладной если админы, то номер накладной не слетает при обновлении заказа
  // 	if($type_user !== 'sup' && $type_user !== 'acc' && $type_user !== 'adm') {		// админ, бухгалтер
  //              $query .= "prdm_num_acc='',";
  //		}
            // предмет - сумма счета
        $query .= "prdm_sum_acc='".$a['predmet']['summ_acc']."', ";
        $query .= "skidka='".$a['predmet']['skidka']."', ";
        // оплата, долг
       // $query .= ($tpacc) ? sprintf("prdm_opl='%s',prdm_dolg='%s',", $a['predmet']['opl']['summ'], $a['predmet']['dolg']) : '';

        $query .= "podr_sebist='".$a['podr']['sebist']."',";	// подрядчики - общая себистоимость

        // подрядчики - оплата, долг
      //  $query .= ($tpacc) ? sprintf("podr_opl='%s',podr_dolg='%s',", $a['podr']['opl_summ'], $a['podr']['dolg_summ']) : '';

    //    $query .= "note='".$a['note']."'";

      //	if($tpacc) {
      //      $query .= "ready='0'";
      //  }

    $query .= "typ_ord='".$a['typ_ord']."',";
    $query .= "form_of_payment='".$a['form_of_payment']."',";
    $query .= "deliv_id='".$a['deliv_id']."',";
    $query .= "booking_till='".$a['booking_till']."'";
    $query .= " WHERE uid=".$a['edit'];



        mysql_query($query);
       // echo $query;
        echo mysql_error();

//	-------------------	СПИСОК ПРЕДМЕТА СЧЕТА	------------------

        $query = "DELETE FROM obj_accounts WHERE query_id=".$a['edit'];
        mysql_query($query);



        for($i=0;$i<count($a['predmet']['list']);$i++) {
            if ($a['predmet']['list'][$i]['art_num'] == 'd') {
                $a['predmet']['list'][$i]['price_our'] = $a['predmet']['list'][$i]['price'];
            }
			
            $query = sprintf("INSERT INTO obj_accounts(query_id,nn,art_num, name,num,price, r_price_our,tip_izd,tip_sdelki) VALUES(%d,'%s','%s','%s','%s','%s','%s','%s','%s')",$a['edit'],($i+1), $a['predmet']['list'][$i]['art_num'], $a['predmet']['list'][$i]['name'], $a['predmet']['list'][$i]['num'], $a['predmet']['list'][$i]['price'], $a['predmet']['list'][$i]['price_our'], $a['predmet']['list'][$i]['tip_izd'], $a['predmet']['list'][$i]['tip_sdelki']);
			echo $query;
           mysql_query($query);
        }

//	-------------------	СПИСОК ПОДРЯДЧИКОВ	------------------

        $arr_contr_id 		= array();
        $arr_contr_numsp 	= array();	// массив количества оплат, до сохранения

        // чтения ид списка подрядчиков для удаления полей оплат
        $query = "SELECT uid FROM contractors_list WHERE query_id=".$a['edit']." ORDER BY nn";
        $res = mysql_query($query);
        while($r = mysql_fetch_array($res)) {
            $arr_contr_id[] = $r['uid'];
            $query = "SELECT uid FROM payment_podr WHERE contr_id=".$r['uid'];
            $res2 = mysql_query($query);
            $arr_contr_numsp[] = mysql_num_rows($res2);
        }


        $query = "DELETE FROM contractors_list WHERE query_id=".$a['edit'];
        mysql_query($query);



    for($i=0;$i<count($a['podr']['list']);$i++) {


        $query = sprintf("INSERT INTO contractors_list(query_id,nn,contr_id,name,price,num,acc_number,opl,debt)  VALUES(%d,%d,%d,'%s','%s','%s','%s','%s','%s')",
        $a['edit'],
        ($i+1),
        $a['podr']['list'][$i]['podr'],
        $a['podr']['list'][$i]['name'],
        $a['podr']['list'][$i]['price'],
        $a['podr']['list'][$i]['num'],
        $a['podr']['list'][$i]['num_acc'],
        $a['podr']['opl'][$i]['summ'],
        $a['podr']['list'][$i]['dolg']);

        mysql_query($query);
        $new_contr_id = mysql_insert_id();





}
$status_push=$a['status_check_email'];
	  $data1 = mysql_query("SELECT * FROM `client_email_status` WHERE  `client_id` ='{$client_lst}'");
	if (mysql_num_rows($data1) != 0) {
		//обновляем 
		$sql="UPDATE `client_email_status` SET `status` = '{$status_push}' WHERE `client_id` = '{$client_lst}';";
	}else{	
		$sql="INSERT INTO `client_email_status` (`id`, `client_id`, `status`) VALUES (NULL, '{$client_lst}', '{$status_push}');";
	}
	$result  = mysql_query($sql);
	
//запись о сфере деятельности
	$sfera_dey=$a['sfera_dei'];
	$data1 = mysql_query("SELECT * FROM `client_sfera` WHERE  `id_client` ={$client_lst}");
	if (mysql_num_rows($data1) != 0) {
		//обновляем 
		$sql="UPDATE `client_sfera` SET `id_sfera` = {$sfera_dey} WHERE `id_client` = {$client_lst};";
	}else{	
		$sql="INSERT INTO `client_sfera` (`id_client`, `id_sfera`) VALUES ({$client_lst}, {$sfera_dey});";
	}
	$result  = mysql_query($sql);
//проверяем amocrm(если не заполнен - заполняем,иначе не трогаем)
 $data1 = mysql_query("SELECT * FROM `queries` WHERE uid=".$a['edit']);
	if (mysql_num_rows($data1) != 0) {
		$mas_to_amo = mysql_fetch_array($data1);
		$amo_id=$mas_to_amo['amo_crm_id'];
		if ($amo_id=="" || $amo_id==NULL){
			//пустой,пробуем заполнить
			// ################################################################################
			// #################  обработка AMOCRM для связи с заказом  #######################
			// ################################################################################
			$amoCrm=new AmoCrm();
			$amoCrm->login_amo();
			$mas_zn=$amoCrm->valid($a['firm_tel'],$a['email']);

			//,a['email']
			$res=$amoCrm->load_sdelki_amo($mas_zn['phone']);//поиск по телефону
			//print_r($res);
			$res1=$amoCrm->load_sdelki_amo($mas_zn['email']);//поиск по email
			//перебираем массивы (если они не пустые и не равны 1 значению) на поиск актуального заказа
$status_ok=142;
	$status_error=143;
	$mas_isk_search=array(142,143,30754849);
	//print_r($mas_isk_search);
	//print_r($res); 
			if ($res!=null && count($res)>1){
				$dat_max=0;//максимальная дата 
				$temp_id=0;
				foreach ($res as $value){
					//if ($value['status_id']!=$status_ok && $value['status_id']!=$status_error){
						echo in_array($value['status_id'],$mas_isk_search);
					if (in_array($value['status_id'],$mas_isk_search)===false){
						if ($value['created_at']>$dat_max){$dat_max=$value['created_at'];$temp_id=$value['id'];}
					}
				}
				$res=$temp_id;
			}else if (count($res)==1 && (in_array($res[0]['status_id'],$mas_isk_search)===false)){
			//if (count($res)==1  && ($res[0]['status_id']!=$status_ok && $res[0]['status_id']!=$status_error)){
				$res=$res[0]['id'];
			}else{
				//заказа нету
				$res=null;
			}
			if ($res1!=null && count($res1)>1){
				$dat_max1=0;//максимальная дата 
				$temp_id=0;
				foreach ($res1 as $value){
					//if ($value['status_id']!=$status_ok && $value['status_id']!=$status_error){
						if (in_array($value['status_id'],$mas_isk_search)===false){
						if ($value['created_at']>$dat_max1){$dat_max1=$value['created_at'];$temp_id=$value['id'];}
					}
				}
				$res1=$temp_id;
			}else if (count($res1)==1 && (in_array($res1[0]['status_id'],$mas_isk_search)===false)){
			//if (count($res1)==1  && ($res1[0]['status_id']!=$status_ok && $res1[0]['status_id']!=$status_error)){
				$res1=$res[0]['id'];
			}else{
				//заказа нету
				$res1=null;
			}
			if ($res!=null && $res1!=null){
				//заполненные
				if ($res==$res1){$amo_id=$res;}//одинаковые
				else{
					//они разные
					if ($dat_max1>$dat_max){$amo_id=$res1;}else{$amo_id=$res;}
				}
			}else{
			//минимум 1 res равен null
				if ($res1!=null){
					$amo_id=$res1;
				}else if ($res!=null){
					$amo_id=$res;
				}else{
					$amo_id=null;
				}
			}
			
			//
		}
	}else{
		$amo_id=null;
	}
	if ($amo_id!=null){
		$uid=$a['edit'];
		$sql="UPDATE `queries` SET `amo_crm_id`='{$amo_id}' WHERE uid={$uid}";
		$result  = mysql_query($sql);
	}
	if ($amo_id!=null){
	$id_crm_sdelka=$amo_id;
	if (!$amoCrm){
		$amoCrm=new AmoCrm();
			$amoCrm->login_amo();
	}
	$mas_zn=$amoCrm->valid($a['firm_tel'],$a['email']);
	$dop_zn=$amoCrm->valid($a['cont_tel']);
	$res1=$amoCrm->load_sdelki_amoContats($id_crm_sdelka);
	$mas_new_email=array();
	$mas_new_phone=array();
	if ($res1!=null && $res1!=0){
			$ic_contact=$amoCrm->check_client_amo($mas_zn['phone']);
			$res2=$amoCrm->check_client_amo_info($res1);
			//echo "<pre>";
			//print_r($res2);
			//echo "</pre>";
			foreach ($res2 as $value){
				if ($value['code']=='EMAIL'){
					foreach ($value['values'] as $value_dop){
						$email=$amoCrm->valid($value_dop['value']);
						$mas_new_email[]=$email['email'];
					}
				}else if ($value['code']=='PHONE'){
					foreach ($value['values'] as $value_dop){
						$phones=$amoCrm->valid($value_dop['value']);
						$mas_new_phone[]=$phones['phone'];
					}
				}
			}
			foreach ($res2 as $value){
				if ($value['code']=='EMAIL'){
					foreach ($value['values'] as $value_dop){
						$email=$amoCrm->valid($value_dop['value']);
						if ($email['email']!=$mas_zn['email']){
							if (!in_array($mas_zn['email'],$mas_new_email)){
							$mas_new_email[]=$mas_zn['email'];
							}
						}
					}
				}else if ($value['code']=='PHONE'){
					foreach ($value['values'] as $value_dop){
						$phones=$amoCrm->valid($value_dop['value']);
						//print_r($phones);
						//echo $phones['phone']."/".$mas_zn['phone']."</br>";
						if ($phones['phone']!=$mas_zn['phone']){
							//echo $phones['phone']."/".$mas_zn['phone']."</br>";
							if (!in_array($mas_zn['phone'],$mas_new_phone)){
								$mas_new_phone[]=$mas_zn['phone'];
							}
						}
						if ($phones['phone']!=$dop_zn['phone']){
							//echo $phones['phone']."/".$dop_zn['phone']."</br>";
							if (!in_array($dop_zn['phone'],$mas_new_phone)){
							$mas_new_phone[]=$dop_zn['phone'];
							}
						}
					}
					
				}
			}
			//
					if (count($mas_new_phone)>0){
						//добавляем к контакту телефоны
						//меняем массив на нужный формат
						foreach ($mas_new_phone as $keys => $value){
							$mas_phone[]=$value;
							//$mas_phone[$keys]['enum']='WORK';
							$arrays_new[]=array(
											  'value'=>$value,
											  'enum'=>'WORK'
											);
						}
					}
					if (count($mas_new_email)>0){
						//добавляем к контакту телефоны
						foreach ($mas_new_email as $keys => $value){
							$mas_email[]=$value;
							//$mas_email[$keys]['enum']='WORK';
							$arrays_new1[]=array(
											  'value'=>$value,
											  'enum'=>'WORK'
											);
							
						}
					}
					//получаем 
					$datetime = new DateTime();
					if (count($mas_phone)>0){
						
						$update['update']=array(
								array(
								'id'=>$ic_contact,
								'updated_at'=>$datetime->getTimestamp(),
									  'custom_fields'=>array(
									  array(
										  'id'=>284889,
										  'values'=>
											$arrays_new1
										  //array()
										),
										array(
										  'id'=>284887,
										  'values'=>
											$arrays_new
										  //array()
										)
										
									  )
									 )
								);
							//print_r($update);
							$zn_contant=$amoCrm->add_client_amo1($update);
							//echo "ZM:".$zn_contant;
					}
			//
		}
}
$amo_users=mysql_query("SELECT * FROM `users` WHERE `uid` = {$a['user_id']} ");
	$amo_users_row = mysql_fetch_array($amo_users);
	if ($amo_users_row['amo_id']!='' && $amo_users_row['amo_id']!=0){
		//не пустое
		$user_amo_id=$amo_users_row['amo_id'];
		$datetime = new DateTime();
		$update['update']=array(
			array(
			'id'=>$amo_id,
			'responsible_user_id'=>intval($user_amo_id),
			 'updated_at'=>$datetime->getTimestamp()
				 )
		);
		//$res_update=$amoCrm->add_sdelki_amo($update);
		
	}else{
		//не нашли пользователя с таким id
		$datetime = new DateTime();
		$update['update']=array(
			array(
			'id'=>$amo_id,
			'responsible_user_id'=>intval('3939454'),
			 'updated_at'=>$datetime->getTimestamp()
				 )
		);
		//$res_update=$amoCrm->add_sdelki_amo($update);
	}
 // возврат значений в родительский скрипт
$GLOBALS['_RESULT'] = array(
    "id"	=> 'edit',
	"amo_id"=>$amo_id,
	"id_qu"=>$a['edit']
);
   }
?>