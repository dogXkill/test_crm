<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");

$shop_site = 'https://www.paketoff.ru';         // адрес пакетофф
//$shop_site = 'http://paketoff.lc';         // адрес пакетофф

$shop_id = 0;       // ид заказа в магазине пакетофф
if (isset($_REQUEST['shop_id']) && is_numeric($_REQUEST['shop_id'])) {
    $shop_id = $_REQUEST['shop_id'];
}
$query_id = $_GET['show'];

$auth = false;

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");
require_once("../includes/hint.inc.php");
include_once '../../amo/amocrm.php';//подключаем амо
if ($user_access['order_access'] == '0' || empty($user_access['order_access'])) {
  header('Location: /');
}

if (!$auth) {
    header("Location: /");
    exit;
}

$pereadr = '';

$op = 'new';        // по умолч тип операции новый запрос

$arr_prdm_list = array();
$arr_podr_list = array();

// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;

// форматирование дробного числа до 2 чисел после запятой
function form_num($v)
{
    $v = preg_replace('/\,/', '.', '' . $v);
    $v = number_format($v, 2, '.', '');
    $v = preg_replace('/\.00/', '', $v);
    $v = preg_replace('/-0/', '0', $v);
    return $v;
}

//--------------------------- изменеие менеджера редактируемого запроса ------------------------
if (isset($_POST['switch_manag'])) {
    $query = sprintf("UPDATE queries SET user_id='%d' WHERE uid=%d LIMIT 1", $_POST['sel_manag'], $query_id);
    mysql_query($query);
	//отправляем в amo 
	
	//ловим amo_crm_id ,если не пустой 
	$querys = sprintf("SELECT * FROM queries WHERE uid=%d", $query_id);
    $res_qrs = mysql_query($querys);
    $r_qrs = mysql_fetch_array($res_qrs);
	if ($r_qrs['amo_crm_id']!='' && $r_qrs['amo_crm_id']!=0){
		$datetime = new DateTime();
		$id_crm_sdelka=$r_qrs['amo_crm_id'];
		$querys1 = "SELECT * FROM users WHERE uid=" . $_POST['sel_manag'];
		
		$res_qr1 = mysql_query($querys1);
		$r_qr1 = mysql_fetch_array($res_qr1);
		$user_amo_id=$r_qr1['amo_id'];
		if ($user_amo_id!=NULL){
		//echo $user_amo_id."|".$id_crm_sdelka;
		$amoCrm=new AmoCrm();
		$amoCrm->login_amo();
		
		
		$update['update']=array(
			array(
			'id'=>$id_crm_sdelka,
			'responsible_user_id'=>intval($user_amo_id),
			 'updated_at'=>$datetime->getTimestamp(),
				 )
		);
		$res_update=$amoCrm->add_sdelki_amo($update);
		}
	}
	
}

$max_booking_days_q = "SELECT val FROM options WHERE name='max_booking_days'";
$max_booking_days = mysql_fetch_array(mysql_query($max_booking_days_q));
$max_booking_days = $max_booking_days[0];

$umolch_booking_days_q = "SELECT val FROM options WHERE name='umolch_booking_days'";
$umolch_booking_days = mysql_fetch_array(mysql_query($umolch_booking_days_q));
$umolch_booking_days = $umolch_booking_days[0];

// --------------Чтение данных пользователя ----------------------------------
$query = "SELECT * FROM users WHERE uid=" . $user_id;
$res = mysql_query($query);
$r = mysql_fetch_array($res);
$full_name = @$r['surname'] . ' ' . @$r['name'] . ' ' . @$r['father'];

// --------------Если открыто в режиме редактирования- просмотра--------------
if (isset($query_id) && is_numeric($query_id)) {
    $op = 'edit';

    $query = sprintf("SELECT * FROM queries WHERE uid=%d", $query_id);
    $res_qr = mysql_query($query);
    $r_qr = mysql_fetch_array($res_qr);

    $ed_us_id = $r_qr['user_id'];
    $corsina_order_uid = $r_qr["corsina_order_uid"];
    $corsina_order_num = $r_qr["corsina_order_num"];
    $client_id = $r_qr["client_id"];
    $client_type = $r_qr["client_type"];
    $short = $r_qr['short'];
    $name = $r_qr['name'];
    $typ_ord = $r_qr['typ_ord'];
    $form_of_payment = $r_qr['form_of_payment'];
    $uniq_id = $r_qr["uniq_id"];
    $deliv_id = $r_qr['deliv_id'];
    $cont_tel = $r_qr['cont_tel'];
    $booking_till = $r_qr['booking_till'];
    $shipped = $r_qr['shipped'];
    $skidka = $r_qr['skidka'];
    $note = $r_qr['note'];
	$amo_crm_id=$r_qr['amo_crm_id'];


    $query = sprintf("SELECT * FROM obj_accounts WHERE query_id=%d", $query_id);
    $res_qr = mysql_query($query);
    $arr_prdm_list = array();
    while ($r_prdm = mysql_fetch_array($res_qr))
        $arr_prdm_list[] = $r_prdm;

    $query = sprintf("SELECT * FROM contractors_list WHERE query_id=%d", $query_id);
    $res_qr = mysql_query($query);
    $arr_podr_list = array();
    while ($r_podr = mysql_fetch_array($res_qr))
        $arr_podr_list[] = $r_podr;

    $query = "SELECT * FROM users WHERE uid=" . $ed_us_id;
    $res = mysql_query($query);
    $r = mysql_fetch_array($res);
    $full_name = @$r['surname'] . ' ' . @$r['name'] . ' ' . @$r['father'];
}
// ---------------------------------------------------------------------------
if ($op=='new' && isset($_POST['firm_tel'])){
	//echo "Создание";
	
}else{
	/*
	$querys = sprintf("SELECT * FROM queries WHERE uid=%d", $query_id);
    $res_qrs = mysql_query($querys);
    $r_qrs = mysql_fetch_array($res_qrs);
	if ($r_qrs['amo_crm_id']!='' && $r_qrs['amo_crm_id']!=0){
		$datetime = new DateTime();
		$id_crm_sdelka=$r_qrs['amo_crm_id'];
	$amoCrm=new AmoCrm();
		$amoCrm->login_amo();
	//проверяем конт.данные в заявке
		//firm_tel
		//cont_tel
		//email
		$mas_zn=$amoCrm->valid($_POST['firm_tel'],$_POST['email']);
		$dop_zn=$amoCrm->valid($_POST['cont_tel']);
		//ловим сделку по id 
		$res1=$amoCrm->load_sdelki_amoContats($id_crm_sdelka);
		$mas_new_email=array();
		$mas_new_phone=array();
		//echo $res1;
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
		print_r($mas_new_email);
		print_r($mas_new_phone);
		//echo "test".$res1;
		//echo "<pre>";
		//print_r($res1);
		//echo "</pre>";
	}
	*/
		//смотрим какие телефоны там есть
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251"/>
    <title>Printfolio intranet v.2</title>
    <link href="../style.css?cache=<?=rand(1,10000000);?>" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" media="all" href="../includes/js/calendar/calendar-blue.css" title="Aqua"/>
    <!--<link rel="stylesheet" href="../../assets/libs/font-awesome-4.7.0/css/font-awesome.min.css" type="text/css" media="all">-->
	<link rel="stylesheet" type="text/css" href="../includes/fonts/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="../../assets/libs/push/overhang.css">
</head>
<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script src="../includes/lib/JsHttpRequest/JsHttpRequest.js"></script>
<script src="../../assets/libs/push/overhang.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
<script type="text/javascript" src="../includes/js/mask.js"></script>


<script language="JavaScript" type="text/javascript">
    var user_id =    <?=$user_id?>;					// ид пользователя
    var tpacc =    <?=$tpacc?>;				// тип пользователя
    var reqsl_user_id = <?=(($tpacc) ? 0 : $user_id)?>;	// ид пользователя для списка клиентов
    var ed_us_id = <?=((@$ed_us_id) ? $ed_us_id : $user_id)?>;	// ид пользователя запроса

    // новый запрос или редактирование
    var edit = <?=($op == 'edit') ? $query_id : "'new'"?>;
    var op = '<?=$op;?>';
    var curr_date = '<?=date("d.m.Y")?>';			// текущая дата в формате '01.05.2007'
    var user_full_name = '<?=$full_name?>';
    var req_fl_hd = 0;
    var shop_id = <?=$shop_id?>;    // ид заказа в пакетофф
    var client_id = '<?=$client_id;?>';
    var user_email = '<?=$user_email?>';

    //-->

</script>

<script src="../includes/js/query_send.js?cache=<?=rand(1,10000000);?>"></script>
<script language="JavaScript" type="text/javascript">






    // Загрузка данных из магазина пакетофф
    function load_shop_order(id) {
		console.log('load_order');
        ajrun(
            'http://test.paketoff.ru/modules/admin/shop/backend/shop.php',
            {
                id: id,										// id
                user: user_full_name
            },

            function (a, b) {	  // обработка результатов

                $('#debug').html(b);
                // реквизиты
                $('#client_short').val(a['acc']['full_name']);   // короткое название
                $('#client_type[value="'+a["acc"]["type"]+'"]').prop('checked', true);   // тип клиента
                $('#client').val(a['acc']['full_name']);         // Полнное юридическое наименование
                // если счет
                $('#post_add').val(a['acc']['post']);          // почтовый адрес
                $('#deliv_add').val(a['acc']['addr_deliv']);     // Фактический
                $('#form_of_payment').val(a['acc']['payment_id']); // Форма оплаты
                $('#deliv_id').val(a['acc']['deliv_id']); // тип доставки
                $('#cont_pers').val(a['acc']['cont_pers']);    // Контактное лицо
                $('#uniq_id').val(a['acc']['uniq_id']);    // Уникальный номер заказа для ссылки прямой
                if (a['acc']['tp'] == 1) {   // если счет
                    $('#inn').val(a['acc']['inn']);                // ИНН
                    $('#kpp').val(a['acc']['kpp']);                // КПП
                    $('#okpo').val(a['acc']['okpo']);                // ОКПО
                    $('#rs').val(a['acc']['rs']);                  // р/с
                    $('#bank').val(a['acc']['bank']);              // открыт в банке
                    $('#bik').val(a['acc']['bik']);                // БИК


                } else {  // если квитанция
                    $('#cont_pers').val(a['acc']['full_name']);    // Контактное лицо
                }
                $('#comment').val(a['acc']['comment']);
                $('#firm_tel').val(a['acc']['phone']);           // Телефон
                $('#email').val(a['acc']['email']);              // E-Mail
                $('#cont_tel').val(a['acc']['phone']);           // Контактный телефон

                //$("#typ_ord").attr('checked', 'checked')
                $("#typ_ord option[value='2']").attr("selected", "selected");

                <?
                if($corsina_order_uid == "") {
                ?>
                $('#corsina_order_span').html("<a href=\"https://www.paketoff.ru/order/print?num=" + a['acc']['uniq_id'] + "\" target=_blank><i class=\"fa fa-cart-plus\" style=\"font-size: 14px; color: #666; margin-right: 5px;\"></i>" + a['acc']['num'] + "</a>"); // Ссылка на номер счета в примечании
                $('#corsina_order_uid').val(a['acc']['uid']);
                $('#corsina_order_num').val(a['acc']['num']);        // Номер счета в примечании
                <?
                }
                ?>
                var nnsn = 0;
                if (a['goods'].length > 0) {
                    for (var i = 0; i < a['goods'].length; i++) {

                        var art_id = a['goods'][i]['art_id'];

                        //if (!art_id){art_id=""; space="";}else{space=", "}
                        price_our = "";
                        var r_price_our = '';

                        if (!a['goods'][i]['price_our'] || a['goods'][i]['price_our'] == "0" && art_id) {
                            price_our = a['goods'][i]['opt'] * 0.8
                            price_our = price_our.toFixed(2);
                        } else {
                            price_our = a['goods'][i]['price_our'];
                        }

                        if (art_id == "d" && a['acc']['deliv_id'] == "3") {
                            price_our = "1500";
                        }

                        if (art_id == "n") {
                            price_our = ""
                        }

                        if (!a['goods'][i]['r_price_our'] || a['goods'][i]['r_price_our'] == "0" && art_id) {
                            r_price_our = a['goods'][i]['price'] * 0.3;
                            r_price_our = r_price_our.toFixed(2);
                            if (art_id == "d") {
                                r_price_our = '';
                            }
                            if (art_id == "n") {
                                r_price_our = ""
                            }
                        } else {
                            r_price_our = a['goods'][i]['r_price_our'];
                        }

                        if (a['goods'][i]['price_print'] > 0) {
                            write_feld_predm(1, 'n', 'Нанесение ' + a['goods'][i]['print_logo'], a['goods'][i]['print_count'], a['goods'][i]['price_print'] / a['goods'][i]['print_count'], 99, 0);
                            write_feld_podr2(1, 126, 'Нанесение ' + a['goods'][i]['print_logo'], a['goods'][i]['print_count'], 0, 0);
                            $("#typ_ord option[value='3']").attr("selected", "selected");
                            nnsn = 1;
                        } else {
                            if (art_id == "d" && ((a['acc']['deliv_id'] == '15' && a['acc']['dellin_city_id'] == '200555') || (a['acc']['deliv_id'] == '5' && a['acc']['sdek_city_id'] == '44')) && (a['acc']['goods_summ_itog'] >= 15000 || nnsn == 1)) {

                                write_feld_podr2(i, 126, a['goods'][i]['title'], a['goods'][i]['num'], price_our, "");
                            } else {
                                write_feld_predm(i, art_id, art_id + ' ' + a['goods'][i]['title'], a['goods'][i]['num'], a['goods'][i]['price'], 99, r_price_our);
                                write_feld_podr2(i, 126, a['goods'][i]['title'], a['goods'][i]['num'], price_our, "");
                            }
                        }



                        kalk_summ_predm();
                        var zero = "";

                    }
                } else {
                    write_feld_predm(0, 0, 0, 0, 0);
                }



                write_feld_podr2(0, 0, 'Доставка', 0, 0, 0, 0, 1);

                kalk_summ_predm();

                //$('#debug').html(b);
            },
            'load_intra_order'
        );
    }

    // Отметить в пакетофф заказ как выполненный в интранете и перейти в список заказов
    function set_shop_order_on(id) {
        ajrun(
            '<?=$shop_site?>/modules/admin/shop/backend/shop.php',
            {
                id: id,										// id
                user: '<?=$full_name?>'
            },
            function (a, b) {	  // обработка результатов
            console.log('set_intra_order');
              //  setTimeout("document.location.href='read_mail_base.php'", 1000);       // разкомментировать после правок!!!!!
            },
            'set_intra_order'
        );
    }

 function change_order_type(){
    var typ_ord = $('#typ_ord').val();
    var booking_span = $('#booking_span');
    var table_vendor_head = $('.table_vendor_head');
    var vendor_info = $('.vendor_info');

    // тип заказа "Под заказ"
    if (typ_ord === "1") {
      // скрытие поля "бронь"
      booking_span.hide();
      // скрытие заголовка таблицы и блока с артикулом
      table_vendor_head.hide();
      vendor_info.hide();
    } else {
      // показ бронь
      booking_span.show();
      // показ артикул
      table_vendor_head.show();
      vendor_info.show();
    }
 }
    //-->
</script>

<body onload="init_page();">
<script type="text/javascript" src="../includes/js/wz_tooltip.js?cache=<?=rand(1,10000000);?>"></script>
<? require_once("../templates/top.php");
$name_curr_page = 'query_list';
require_once("../templates/main_menu.php");

?>

<table width=1100 border=0 cellpadding="0" cellspacing="0" align=center bgcolor="#F6F6F6">
    <tr>
        <td valign="top" align="center">
            <table border="0" cellspacing="5" cellpadding="0">
                <tr>
                    <td width="160">
                        <a href="query_send.php" class="sublink"><img src="../../i/invoice_sm.png" width="24" height="24" alt="" align="absmiddle"></a>
                        <a href="query_send.php" class="sublink">запросить счет</a>
                    </td>
                    <td width="160" valign=bottom>
                        <a href="/acc/stat/stat_shop.php?type=shop_history" class="sublink"><img src="../../i/statistics.png" width="24" height="24" alt="" align="absmiddle"></a>
                        <a href="/acc/stat/stat_shop.php?type=shop_history" class="sublink">статистика магазин</a>
                    </td>
                    <?
                    if ($user_access['sprav_access'] == '1') {
                      ?>
                      <td width="160">
                          <a href="clients_list.php" class="sublink"><img src="../../i/clients.png" width="24" height="24" alt="" align="absmiddle"></a>
                          <a href="clients_list.php" class="sublink">клиенты</a>
                      </td>
                      <td width="160">
                          <a href="contractors_list.php" class="sublink"><img src="../../i/vendor.png" width="24" height="24" alt="" align="absmiddle"></a>
                          <a href="contractors_list.php" class="sublink">поставщики</a>
                      </td>
                      <?
                    }
                    ?>

                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td align="center"><h3>Заявка на счет <?php if ($amo_crm_id){?>
												   <a href='https://upakme.amocrm.ru/leads/detail/<?php echo $amo_crm_id;?>' target="_blank" class="goToAmo"><i class="fa fa-external-link"></i></a>
												   <?}?></h3></td>
    </tr>
    <tr>
        <td align="center">
            <? if ($auth) { ?>
                <table border="0" width=100% cellspacing="0" cellpadding="0" align="center">
                    <form action="" method=post name="f_send" id="f_send">

                        <input name="user_id" type="hidden" value="<?= $user ?>"/>
                        <input name="edit_id" type="hidden" value="<?= ($op == 'edit') ? $query_id : 0; ?>"/>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2" align="right">
                                <table width="850" border="0" cellspacing="0" cellpadding="0" align="right">
                                    <tr>
                                        <td width="250" align="right">Менеджер проекта:</td>
                                        <td width="500" align="left">

                                                <strong><?=$full_name ?></strong>
												
                                                <? if ($tpacc && ($op == 'edit')) { ?>
                                                <a href="#" onclick="ShowDivManager(); return false;"
                                                   onmouseover="Tip('Изменить менеджера проекта')"><img width="15" height="18" src="/acc/i/edit3.gif"/></a>
                                            <? }else{
												//если новый,то ищем эту сделку на amo и меняем там отвественного
											}												?>
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <span id=date_query_span></span>
                                        </td>

                                    </tr>
                                </table>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="tab_tit_td">Выбор клиента</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table border="0" cellspacing="0" cellpadding="3" align="right" width=100%>
                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td class="tab_first_col" width=250>Короткое название: <span
                                                    class="err">*</span><input type="hidden" id=dubl_clients value="" /></td>
                                        <td class="tab_two_col">
                                            <input name="client_short" id="client_short" autocomplete="off"
                                                   onchange="this.value=replace_str_cl_sh(this.value)" type=text
                                                   class="frm_wdfull" onKeyUp="doLoad('search','', '', '')"
                                                   value="<?=$short;?>" maxlength="50"
                                                   onmouseover="Tip('Короткое название клиента,<br>без ковычек и \'ООО\' !')"/>
												   
                                            <div style="font-size: 11px; margin-bottom: 5px;">По мере ввода,
                                                существующие клиенты будут появляться в списке
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <!--Тип клиента -->
                                        <td class="tab_first_col" width=250>Тип: <span class="err">*</span></td>
                                        <td class="tab_two_col">
                                            <label>
                                                <input name="client_type" id="client_type" autocomplete="off"
                                                       type="radio" value="physical"
                                                    <?= isset($client_type) && $client_type === 'physical' ? ' checked' : '' ?> />
                                                Физическое лицо
                                            </label>
                                            <br>
                                            <label class='client_type_me'>
                                                <input name="client_type" id="client_type" autocomplete="off"
                                                       type="radio" value="legal"
                                                    <?= isset($client_type) && $client_type === 'legal' ? ' checked' : '' ?> />
                                                Юридическое лицо
                                            </label>
                                        </td>
                                    </tr>
                                    <tr <?if($op == 'edit'){echo "style='display:none; readonly';";} ?>>
                                        <td class="tab_first_col">Список клиентов:&nbsp;&nbsp;</td>
                                        <td class="tab_two_col">

                                            <select name="client_list" size="<?= (($tpacc) ? 10 : 10) ?>"
                                                    style="width:700px" id="client_list"
                                                    onclick="doLoad('get_req', this.value, '', '')"
                                                    onmouseover="Tip('Весь список коротких названий клиентов')">
                                                <option value="0"<?= ($op != 'edit') ? ' selected' : '' ?>
                                                        style="background-color:#E2E2E2">другой...
                                                </option>
                                                <?
                                                $i = 0;
                                                $optgr = -1;    // ид тек клиента
                                                $fl_optgr = 0;    // после открытия 1го тега optgroup=1


                                                while (false) {
                                                $sel = '';
                                                if ($op == 'edit') {
                                                    if ($r_cl['uid'] == $client_id)
                                                        $sel = ' selected';
                                                }
                                                $gr_name = '';    // фам пользователя (группа селекта)
                                                if ($r_cl['user_id'] != $optgr) {    // клиенты след пользователя
                                                    $optgr = $r_cl['user_id'];        // запомнить польз
                                                    $gr_name = $r_cl['surname'];    // фам пользователя
                                                }
                                                if ($gr_name) {    /* открытие группы */ ?>
                                                <? if ($fl_optgr) { /* закрыть предыдущую гр если открыта */ ?></optgroup><? }
                                            $fl_optgr = 1;    /* флаг - пред группа открыта */ ?>
                                                <optgroup label="<?= $gr_name ?>">
                                                    <? } ?>
                                                    <option value="<?= $r_cl['uid'] ?>"<?= $sel ?>><?= $r_cl['short'] ?></option>
                                                    <? $i++;
                                                    } ?>
                                            </select>
                                        </td>
                                    </tr>

                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="tab_tit_td">Реквизиты</td>
                        </tr>
                        <tr>
                            <td colspan="2">


                                <table width=100% border="0" cellspacing="0" cellpadding="3" id="tab_req" align="right"
                                       style="display:block">
                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td class="tab_first_col">Полное наименование: <span class="err">*</span></td>
                                        <td class="tab_two_col">
                                            <input onmouseover="Tip('Полное название клиента')"
                                                   onchange="this.value=replace_str(this.value)" name=client type=text
                                                   class="frm_wdfull" id="client"
                                                   value="<?=htmlspecialchars($name) ?>" maxlength="255" autocomplete="off"/>
                                        </td>
                                    </tr>

                                 <tr>
                                        <td class="tab_first_col">Фактический/почтовый адрес:</td>
                                        <td class="tab_two_col">
                                            <input onmouseover="Tip('Фактический/почтовый адрес')"
                                                   onchange="this.value=replace_str(this.value)" name="post_add"
                                                   id="post_add" type=text class="frm_wdfull" value="" maxlength="255" autocomplete="off"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tab_first_col">Адрес доставки:</td>
                                        <td class="tab_two_col">
                                            <input onmouseover="Tip('Адрес доставки')"
                                                   onchange="this.value=replace_str(this.value)" name="deliv_add"
                                                   id="deliv_add" type=text class="frm_wdfull" value=""
                                                   maxlength="255" autocomplete="off"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tab_first_col">ИНН:</td>
                                        <td class="tab_two_col">
                                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                <tr>
                                                    <td width="160" align="left"><input onmouseover="Tip('ИНН')"
                                                                                        onchange="this.value=replace_str(this.value);uniqFld('inn')"
                                                                                        name=inn id="inn" type=text
                                                                                        class="frm_req" value=""
                                                                                        maxlength="250" autocomplete="off"/></td>
                                                    <td>
                                                        <table border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td>КПП:&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                                <td><input onmouseover="Tip('КПП')"
                                                                           onchange="this.value=replace_str(this.value)"
                                                                           name=kpp id="kpp" type=text class="frm_req"
                                                                           value="" maxlength="250" autocomplete="off"/></td>


                                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;ОКПО:&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                                <td><input onmouseover="Tip('ОКПО')"
                                                                           onchange="this.value=replace_str(this.value)"
                                                                           name=okpo id="okpo" type=text class="frm_req"
                                                                           value="" maxlength="250" autocomplete="off"/></td>

                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tab_first_col">р/с:</td>
                                        <td class="tab_two_col">
                                            <table border="0" cellspacing="0" align="left" cellpadding="0" width="550">
                                                <tr>
                                                    <td align="left"><input onmouseover="Tip('р/с')"
                                                                            onchange="this.value=replace_str(this.value)"
                                                                            name=rs id="rs" type=text class="frm_rs"
                                                                            value="" maxlength="20" autocomplete="off"/></td>
                                                    </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tab_first_col">БИК:</td>
                                        <td class="tab_two_col">
                                            <table border="0" cellspacing="0" align="left" cellpadding="0" width="100%">
                                                <tr>
                                                    <td align="left"><input
                                                                onchange="this.value=replace_str(this.value)" name=bik
                                                                id="bik" type=text class="frm_rs value="" maxlength="15" autocomplete="off"/>
                                                    </td>
                                                    <td width="120" style="white-space:nowrap;" align="right">открыт в
                                                        банке:&nbsp;&nbsp;&nbsp;
                                                    </td>
                                                    <td><input onmouseover="Tip('Наименование банка')" class="frm_rs"
                                                               onchange="this.value=replace_str(this.value)" name=bank
                                                               id="bank" type=text value="" maxlength="255" autocomplete="off"/></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="tab_first_col">Телефон клиента:</td>
                                        <td class="tab_two_col">

                                            <input onmouseover="Tip('Телефон')" name=firm_tel id="firm_tel"
                                                   type=text class="frm_fld" onchange="standartize_phone('firm_tel');uniqFld('firm_tel')" value="" maxlength="255"/>
                                            E-Mail: <input onmouseover="Tip('E-Mail')"
                                                           onchange="this.value=replace_str(this.value);uniqFld('email')" name=email
                                                           id="email" type=text class="frm_fld" value=""
                                                           maxlength="255" autocomplete="off"/>
											<label>разрешить отправку уведомлений <input type='checkbox' id='status_check_email' name=status_check_email checked onmouseover="Tip('Разрешена ли отправка уведомлений')"></label>


                                        </td>
                                    </tr>
									

                                    <tr>
                                        <td class="tab_first_col">Комментарий для водителя:</td>
                                        <td class="tab_two_col"><input onmouseover="Tip('Комментарий для водителя')"
                                                                       onchange="this.value=replace_str(this.value)"
                                                                       name="comment" id="comment" type=text
                                                                       class="frm_wdfull" value="" maxlength="255" autocomplete="off"/>
                                        </td>
                                    </tr>
									<tr>
										<td class='tab_first_col'>Сфера деятельности клиента</td>
										<td class="tab_two_col">
										<?
										$query = "SELECT id,name FROM sfera_deyatel";
										$res = mysql_query($query);
										while ($r = mysql_fetch_array($res)) { 
												$mas_sfer_dey[$r['id']]=$r['name'];
										}
										//функция перемешивания массива
										function shuffle_assoc($array)
										{
												$shuffled_array = array();
												$shuffled_keys = array_keys($array);
												shuffle($shuffled_keys);
												foreach ( $shuffled_keys AS $shuffled_key ) {
													$shuffled_array[  $shuffled_key  ] = $array[  $shuffled_key  ];
												} 
												return $shuffled_array;
										}
										$mas_sfer_dey=shuffle_assoc($mas_sfer_dey);
										//print_r($mas_sfer_dey);
										?>
											<select id='sfera_dei' onmouseover="Tip('Сфера деятельности клиента')" style='font-size:18px;height:25px;'>
											<option value="0">Выберите</option>
											<?php foreach($mas_sfer_dey as $k=> $value){?>
											<option value="<?= $k ?>">&nbsp;&nbsp;<?= $value ?></option>
											<?php }?>
											</select>
										</td>
									</tr>

                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="tab_tit_td">Контактная информация для водителя</td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <table align="right" border="0" width=100% cellspacing="0" cellpadding="3" id="tab_cont"
                                       style="display:block">

                                    <tr>
                                        <td></td>
                                        <td align="center">Контактное лицо: <span class="err">*</span></td>
                                        <td align="center">Контактный телефон: <span class="err">*</span></td>
                                    </tr>
                                    <tr>
                                        <td>


                                            <select name="typ_ord" id="typ_ord" class=selecty1 style="width:150px" onchange="change_order_type()" autocomplete="off">
                                                <option value="">тип заказа</option>
                                                <option value="1" <?= (($op == 'new') ? '' : (($typ_ord == 1) ? 'selected' : '')) ?>>
                                                    под заказ
                                                </option>
                                                <option value="2" <?= (($op == 'new') ? '' : (($typ_ord == 2) ? 'selected' : '')) ?>>
                                                    магазин
                                                </option>
                                                <option value="3" <?= (($op == 'new') ? '' : (($typ_ord == 3) ? 'selected' : '')) ?>>
                                                    магазин с лого
                                                </option>
                                            </select>


                                            <span id=corsina_order_span></span>
                                            &nbsp;&nbsp;
                                            <? if ($corsina_order_uid) {
                                                echo '<i class="fa fa-cart-plus" style="font-size: 14px; color: #666; margin-right: 5px;"> <a href=https://www.paketoff.ru/order/print?num=' . $uniq_id . ' target=_blank> '.$corsina_order_num.'</a>';
                                            } ?>
                                            <input type=hidden id="uniq_id" name="uniq_id" value="" autocomplete="off"/>
                                            <input type=hidden id="corsina_order_num" name="corsina_order_num" value="" autocomplete="off"/>
                                            <input type=hidden id="corsina_order_uid" name="corsina_order_uid" value="" autocomplete="off"/>


                                            <select name="form_of_payment" id="form_of_payment" size="1" class=selecty1 autocomplete="off">
                                                <option value="0" <?= (($op == 'new') ? (($form_of_payment == 0) ? 'selected="selected"' : '') : '') ?>>
                                                    оплата
                                                </option>
                                                <option value="1" <? if ($form_of_payment == "1") echo "selected"; ?>>
                                                    наличные
                                                </option>
                                                <option value="2" <? if ($form_of_payment == "2") echo "selected"; ?>>
                                                    безнал
                                                </option>
                                                <option value="3" <? if ($form_of_payment == "3") echo "selected"; ?>>
                                                    безнал по реквизитам
                                                </option>
                                                <option value="4" <? if ($form_of_payment == "4") echo "selected"; ?>>по
                                                    карте
                                                </option>
                                            </select>



                                            <select name="deliv_id" id="deliv_id" size="1" class=selecty1 style="width:150px" autocomplete="off">
                                            <option value="">доставка</option>
                                            <option value="1" <? if ($deliv_id == "1") echo "selected"; ?>>самовывоз</option>
                                            <option value="2" <? if ($deliv_id == "2") echo "selected"; ?>>по Мск</option>
                                            <option value="8" <? if ($deliv_id == "8") echo "selected"; ?>>до ТК</option>
                                            <option value="3" <? if ($deliv_id == "3") echo "selected"; ?>>срочная (достависта)</option>
                                            <option value="5" <? if ($deliv_id == "5") echo "selected"; ?>>СДЭК</option>
                                            <option value="15" <? if ($deliv_id == "15") echo "selected"; ?>>Дел. линии</option>
                                            </select>


                                            <br/>

                                        </td>
                                        <td align=center>
                                            <input name=cont_pers id="cont_pers" type=text class="frm_cont_lic"
                                                   onchange="this.value=replace_str(this.value)"
                                                   value="<?=$cont_pers;?>" maxlength="50" autocomplete="off"></td>
                                        <td align="left"><input
                                                    onmouseover="Tip('Контактный телефон, только цифры')"
                                                    name=cont_tel id="cont_tel" type=text  onchange="standartize_phone('cont_tel');uniqFld('cont_tel')" class="frm_cont_tel"
                                                    value="<?=$cont_tel;?>" maxlength="50" autocomplete="off"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="tab_tit_td">Предмет счета</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table border="0" cellspacing="0" cellpadding="3" align="right" width="100%">
                                    <tr>
                                        <td class="table_vendor_head" width="300"<?= $typ_ord === '1' ? ' style="display: none"' : ''?>>#&nbsp;<span id=art_title>Артикул</span></td>
                                        <td align="center" width="400">Наименование</td>
                                        <td style="width: 100px;">Количество</td>
                                        <td style="width: 155px;">Цена за ед.</td>
                                        <td>Сумма</td>
                                        <td>&nbsp;</td>

                                    </tr>


                                    <tr>
                                        <td colspan="6" id="tddivs">
                                            <? for ($i = 0; $i >= 19; $i++) {
                                                ?>
                                                <div id='pr_feld<?= $i ?>' style="display:none;"></div>
                                            <? } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">
                                            <table border="0" cellspacing="0" cellpadding="4" align="right">

                                                <tr>
                                                    <td rowspan=2 width=350>
                                                    <span id="booking_span">
                                                      Бронь до (включительно): <input type="date" min="<?=date('Y-m-d');?>" max="<?=date('Y-m-d', strtotime("+$max_booking_days days"));?>" style="width: 150px; height: 30px; font-size:18px;" value="<?
                                                      if($op == 'new')
                                                      {
                                                      echo date('Y-m-d', strtotime("+$umolch_booking_days days"));
                                                      }else{
                                                      if($booking_till) {echo $booking_till;}}?>" style="width:150px" id="booking_till" name="booking_till">

                                                      <input type="checkbox" id="shipped" <?if($shipped == '1'){echo "checked";}?> onchange="change_shipped_status('<?=$query_id?>')" <?if($user_access['shipped_edit'] == 0){echo "disabled";}?>/> <label for="shipped" style="cursor:pointer">накладная проведена</label>

                                                    </span>
													
                                                    </td>
                                                    <td rowspan=2 width=350>
                                                      <? if ($skidka) {echo "Скидка <b>уже</b> дана в размере";}else{echo "Дать скидку:";} ?>
                                                        <input type="text" <? if ($skidka) {echo "disabled"; } ?> value="<?=$skidka;?>" maxlength=2
                                                               style="width: 50px; height: 30px; font-size:18px;"
                                                               id="skidka" name="skidka"/>%
                                                        <input type="button" <? if ($skidka) {echo "disabled";} ?> style="width: 50px; height: 30px; font-size:18px;" value=">>>" onclick="give_discount()"/>
                                                    </td>

                                                    <td align="center">
                                                        <strong>Сумма счета:</strong></td>
                                                    <td align="center"><strong>Оплачено:</strong></td>
                                                    <td align="center"><strong>Долг:</strong></td>
                                                </tr>
                                                <tr>

                                                    <td align="center"><strong>
                                                            <div id="summ_val">0</div>
                                                        </strong></td>


                                                    <td align="center">
                                                        <strong>
                                                            <div id="predm_opl">0</div>
                                                        </strong></td>
                                                    <td align="center"><strong>
                                                            <div id="predm_dolg">0</div>
                                                        </strong></td>

                                                </tr>

                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="tab_tit_td">Смета по данному заказу (себестоимость)</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <table border="0" cellspacing="0" cellpadding="0" align="right" width=100%>

                                    <tr>
                                        <td colspan="7" style="padding-bottom:5px;">

                                            <!-- *******************    ТАБЛИЦА ПОДРЯДЧИКОВ 1 НАЧАЛО     ******************** //-->

                                            <table border="0" cellpadding="3" cellspacing="0" width=100%>
                                                <tr>
                                                    <td align="left" width=20>#</td>
                                                    <td align="left" width=200>Подрядчик</td>
                                                    <td align="center" width=400>Наименование</td>
                                                    <td align="left" width=90>Количество</td>
                                                    <td align="left" width=120>Цена</td>
                                                    <td align="left" width=120>Себестоимость</td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            </table>

                                            <!-- *******************    ТАБЛИЦА ПОДРЯДЧИКОВ 1 КОНЕЦ     ******************** //-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" id="tddivs2">

                                            <? for ($i = 100; $i <= 19; $i++) { ?>

                                                <div id='pd_feld<?= $i ?>' style="display:none;"></div>

                                            <? } ?>    </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7">
                                            <table border="0" align="right" cellpadding="0" cellspacing="0"
                                                   class="tab_podr_title" width=500>

                                                <tr>
                                                    <td align="center"><strong>
                                                           Общая себестоимость: <div id="summ_val_predm">0</div>
                                                        </strong>

                                                    </td>
                                                    <td align="center"></td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                       <?if($op !== 'edit'){?>
                        <tr>
                            <td colspan="2">

                                <table border="0" cellspacing="0" cellpadding="0" width=100%>

                                    <tr>
                                        <td align="left">Примечание:</td>
                                    </tr>
                                    <tr>
                                        <td align="left">
                                            <textarea onchange="this.value=replace_str(this.value)" class="frm_note"
                                                      rows="4" name="adition"
                                                      id="adition"><?=$note;?></textarea></td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                        <?}?>
                        <?
                        $btnAccesss = false;
                        switch ($user_access['order_access_edit']) {
                          case '2':
                            $btnAccesss = true;
                            break;

                          case '1':
                            $q = "SELECT user_id FROM queries WHERE uid = " . $query_id;
                            $queryResult = mysql_query($q);
                            if ($queryResult) {
                                $r = mysql_fetch_assoc($queryResult);
                            }
                            if (!isset($query_id) || (isset($query_id) && (isset($r['user_id']) && $r['user_id'] == $_COOKIE['uid']))) {
                              $btnAccesss = true;
                            }
                            break;
                        }
                          ?>
                          <tr>
                            <?
                            if ($btnAccesss == true) {
                              ?>
                              <td align="center" colspan=2>
                                  <? if (($op == 'edit') || ($op == 'new')) { ?>
                                      <input class="frm_butt_send" name="butt_send" id="butt_send" type=button
                                             value="Сохранить!" onClick="check();"> 
                                  <? } ?>
                                </td>
                              <?
                            }
                            ?>


                          </tr>
                          <tr>
                              <td align="center" colspan=2>

                               <?

                               if (isset($query_id) && is_numeric($query_id)) {

                              $apps_q = "SELECT uid, tiraz, izd_w, izd_v, izd_b FROM applications WHERE zakaz_id = '$query_id'";

                              $apps = mysql_query($apps_q);
                                    while ($app =  mysql_fetch_assoc($apps)) {
                                         $uid   = $app["uid"];
                                         $tiraz = $app["tiraz"];
                                         $izd_w = $app["izd_w"];
                                         $izd_v = $app["izd_v"];
                                         $izd_b = $app["izd_b"];

                                          echo  "<a href=/acc/applications/edit.php?uid=$uid target=_blank>заявка на $tiraz шт. размер $izd_w x $izd_v x $izd_b</a><br>";
                                    }
                                    }
                              ?>
                              </td>
                          </tr>

                    </form>
                </table>

            <? } ?>    </td>
    </tr>
</table>
</td></tr></table>




<!-- ******************** СЛОЙ ИЗМЕНЕНИЯ МЕНЕДЖЕРА ПРОЕКТА  <<<< *****************  //-->


<!--<div id="div_manag" style="background-color:#FFFFFF; position:absolute; top:400px; left:300px; width:155px; padding:5px; border:1px #0099CC solid; display:none;">-->
<div id="div_manag" style="background-color:#FFFFFF; position:absolute; top:45%; left:45%; width:155px; padding:5px; border:1px #0099CC solid; display:none;">
    <table border="0" cellspacing="0" cellpadding="0" align="center" width="1">

        <form name="ff_manag" action="?show=<?=$query_id?>" method="post">
            <tr>
                <td align="center">
                    <?
                    $query = "SELECT uid,surname,name FROM users WHERE archive!='1' AND user_department = '3' ORDER BY surname";
                    $res = mysql_query($query);
                    ?>
                    <select id="sel_manag" name="sel_manag" size="8" class="sel_query_man_switch">
                        <? while ($r = mysql_fetch_array($res)) { ?>
                            <option value="<?= $r['uid'] ?>">&nbsp;&nbsp;<?= $r['surname'] ?> <?= $r['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <input class="frm_podr_opl_butt" name="switch_manag" type="submit" value="ОК"/>
                    <input name="" class="frm_podr_opl_butt" type="button" value="Отмена"
                           onclick="document.getElementById('div_manag').style.display = 'none';return false;"/>
                </td>
            </tr>
        </form>
    </table>
</div>


<!--  >>>>>>******************** СЛОЙ ИЗМЕНЕНИЯ МЕНЕДЖЕРА ПРОЕКТА  *****************  //-->
<div class='wrap'>
<div class='modal_select' >
	<input type='hidden' id='izd_sdelka_red_id' value="0">
	<p id="zn_pole" style="    margin: 0px;
    border-bottom: 1px solid black;
    font-size: 13px; display: block;cursor: move;    width: calc(100% - 20px);"></p>
	<h3 style="    display: block;font-size: 14px;margin: 5px 0px 0px 0px;">Тип изделия </h3>
	
	<img src="../../i/del.gif" width="20" align="right" height="20" alt="" style="cursor:pointer;    position: absolute;right: 5px;top: 5px;" onclick="$(this).parents('.wrap').hide();">
	
	<p style='margin: 5px 0px 5px 0px;'><select id="select_izd" style='width:200px;'>
	<?php 
	$types_izd = "SELECT tid, type FROM types ";
	echo "<option class='' value='0'>Выберите тип изделия</option>"; 
                              $typesIzd = mysql_query($types_izd);
                                    while ($typeizd_row =  mysql_fetch_assoc($typesIzd)) {
										echo "<option class='' value='{$typeizd_row[tid]}'>{$typeizd_row[type]}</option>"; 
									}
	?>
	</select></p>
	<input type='hidden' id='sdelka_red_id' value="0">
	<h3 style="cursor: move;    display: inline;font-size: 14px;margin:0px;">Тип сделки</h3>
	<p style='margin: 5px 0px 5px 0px;'><select id="select_sdelka" style='width:200px;'>
	<option class='' value='0'>Выберите тип сделки</option>
	<option value='1'>Наше производство </option>
	<option value="2">Перезаказ</option>
	<option value="3">Доставка</option>
	</select></p>
	<!--<button id='btn_select_izd'>Изменить</button>-->
</div>
</div>
<!--
<div class='wrap'>
<div class='modal_select' >
<input type='hidden' id='sdelka_red_id' value="0">
	<h3 style="cursor: move;    display: inline;">Тип сделки</h3>
	<img src="../../i/del.gif" width="20" align="right" height="20" alt="" style="cursor:pointer" onclick="$(this).parents('.wrap').hide();">
	<p><select id="select_sdelka">
	<option class='' value='0'>Выберите тип сделки</option>
	<option value='1'>наше производство </option>
	<option value="2">перезаказ</option>
	</select></p>
	<button id='btn_select_sdelka'>Изменить</button>
</div>
</div>-->
<div class='modal'>
	<div class='modal_amo_crm' style='width: 350px;    border: 1px solid rgb(145, 140, 140);padding-bottom: 0px;background: #e9e9e9;'>
	<input type='hidden' id='izd_sdelka_red_id_amo' value="0">
	
	<h3 style="    display: block;font-size: 18px;margin: 5px 0px 0px 0px;cursor:move">Поиск/Создание сделки</h3>
	<p id="zn_pole_amo" style="    margin: 0px;
    /*border-bottom: 1px solid black;*/
    font-size: 13px; display: block;cursor: move;    width: calc(100% - 20px);"></p>
	<img src="../../i/del.gif" width="20" align="right" height="20" alt="" style="cursor:pointer;    position: absolute;right: 5px;top: 5px;" onclick="$(this).parents('.modal').hide();">
	<div style='background:white;    margin-left: -10px;margin-right: -10px;    padding: 10px;margin-top:3px;    border-radius: 0px 0px 15px 15px;'>
	<p style='margin-top: 2px;font-size:16px;'>Введите номер заявки вручную:&nbsp;</p>
	<p><input type='text' id='search_sdelka_amo' style=' font-size: 18px;'>&nbsp;<button onclick="izm_sdelka_amo();" style='width:105px;height:28px;font-size: 18px;float:right;'>Привязать</button></p>
	<p><span style='font-size:16px;'>Или создать новую сделку</span> <button onclick="new_sdelka_amo();" style=' font-size: 18px;float:right;width:105px;height:28px;'>Создать</button></p>
	</div>
	</div>
</div>
<div class="preloader">
  <div class="preloader__row">
    <div class="preloader__item"></div>
    <div class="preloader__item"></div>
  </div>
</div>
</body>
</html>