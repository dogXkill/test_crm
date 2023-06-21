<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

ob_start();

$auth = false;

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");

// ----- перейти на главную если доступ запрещен ---------
if(!$auth) {
	header("Location: /");
	exit;
}

// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;


// форматирование дробного числа до 2 чисел после запятой
function form_num($v) {
	$v = preg_replace('/\,/', '.', ''.$v);
	$v = number_format($v,2, '.', '');
	$v = preg_replace('/\.00/', '', $v);
	$v = preg_replace('/-0/', '0', $v);
	return $v;
}


// массив переменных передаваемых через GET
$fltr_s = array(
	'nm_acc',
	'num_prdm',
	'summ_prdm',
	'opl',
	'debt',
	'num_acc_prdm',
	'client',
	'podr',
	'spis',
);

// массив соответствующих полей в базе
$fltr_t = array(
	'prdm_num_acc',
	'num',
	'price',
	'opl',
	'debt',
	'acc_number',
	'client_id',
	'podr',
	'debt_spis',
);




// ЧТЕНИЕ ФИЛЬТРОВ ИЗ COKIES В ОДИН МАССИВ
$arr_filtr = array();

	for($i=0;$i<count($fltr_s);$i++) {
			$arr_filtr[$fltr_s[$i]][0] 			= @$_COOKIE['fltr_cl_'.$fltr_s[$i].'_case'];
			$arr_filtr[$fltr_s[$i]][1] 			= @$_COOKIE['fltr_cl_'.$fltr_s[$i].'_val'];
	}


// ОЧИСТКА ВСЕХ ФИЛЬТРОВ
if(@$_GET['filtr'] == 'clear') {
	for($i=0;$i<count($fltr_s);$i++) {
		setcookie('fltr_cl_'.$fltr_s[$i].'_case');
		setcookie('fltr_cl_'.$fltr_s[$i].'_val');
		$arr_filtr[$fltr_s[$i]][0] = '';
		$arr_filtr[$fltr_s[$i]][1] ='';
	}
}



// если был добавлен фильтр по цифре
if(isset($_GET['filtr']) && !empty($_GET['filtr'])) {

	$fltr = $_GET['filtr'];
	$fltr_case = @$_GET['case'];
	$fltr_val = @$_GET['val'];

	setcookie(('fltr_cl_'.$fltr.'_case'), $fltr_case, 0);
	setcookie(('fltr_cl_'.$fltr.'_val'), $fltr_val, 0);
	$arr_filtr[$fltr][0] = $fltr_case;
	$arr_filtr[$fltr][1] = $fltr_val;
}


$query = "SELECT a.uid as cl_id, a.query_id as qr_id, a.contr_id, a.name as contr_name, a.price as contr_price, a.num as contr_num, a.acc_number, a.opl as contr_opl, a.debt as contr_dolg, a.debt_spis, b.prdm_num_acc,c.short, c.name as full_name, c.legal_address , c.postal_address, c.inn, c.kpp, c.cont_pers, c.cont_tel, d.name as podr_name,d.cont_pers as podr_cont_pers, d.cont_tel as podr_cont_tel, d.email as podr_email,e.surname, e.name as us_name, e.father FROM contractors_list as a LEFT JOIN contractors as d ON a.contr_id=d.uid,queries as b LEFT JOIN users as e ON b.user_id=e.uid, clients as c WHERE a.query_id=b.uid AND b.client_id=c.uid ";

// фильтр - количество
if( trim($arr_filtr['num_prdm'][0]) )
	$query .= " AND a.num".$arr_filtr['num_prdm'][0].$arr_filtr['num_prdm'][1];

// фильтр - сумма
if( trim($arr_filtr['summ_prdm'][0]) )
	$query .= " AND a.price".$arr_filtr['summ_prdm'][0].$arr_filtr['summ_prdm'][1];

// фильтр - оплата
if( trim($arr_filtr['opl'][0]) )
	$query .= " AND a.opl".$arr_filtr['opl'][0].$arr_filtr['opl'][1];

// фильтр - долг
if( trim($arr_filtr['debt'][0]) )
	$query .= " AND a.debt".$arr_filtr['debt'][0].$arr_filtr['debt'][1];

// фильтр - номер счета
if( trim($arr_filtr['num_acc_prdm'][0]) )
	$query .= " AND a.acc_number".$arr_filtr['num_acc_prdm'][0].$arr_filtr['num_acc_prdm'][1];

// фильтр по поставщику
if( (trim($arr_filtr['podr'][0])) && (intval($arr_filtr['podr'][1]) != 0) )
	$query .= " AND a.contr_id=".$arr_filtr['podr'][1];

// фильтр по списанию долга
if( trim($arr_filtr['spis'][0]) )
	$query .= " AND a.debt_spis=".$arr_filtr['spis'][1];



// фильтр - номер счета предмета
if( trim($arr_filtr['nm_acc'][0]) )
	$query .= " AND b.prdm_num_acc".$arr_filtr['nm_acc'][0].$arr_filtr['nm_acc'][1];

// фильтр по клиенту
if( (trim($arr_filtr['client'][0])) && (intval($arr_filtr['client'][1]) != 0) )
	$query .= " AND b.client_id=".$arr_filtr['client'][1];
/*
*/
//$query .= "AND a.query_id=c.uid AND c.client_id=d.uid ";

$query .= " AND (b.type='0' OR b.type='2') AND b.ready='1' ";

//echo $query;
$res_qr = mysql_query($query);
echo mysql_error();
/*
while($r = mysql_fetch_array($res_qr,MYSQL_ASSOC)) {

echo '<pre>';
print_r($r);
echo '</pre><br><br>';

}

exit;
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.1</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" media="all" href="../includes/js/calendar/calendar-blue.css" title="Aqua" />
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<?
require_once("../includes/auth_stat_table.php");
?>



<script src="../includes/lib/JsHttpRequest/JsHttpRequest.js"></script>
<script type="text/javascript" src="../includes/js/calendar/calendar.js"></script>
<script type="text/javascript" src="../includes/js/calendar/calendar-ru_win_.js"></script>
<script type="text/javascript" src="../includes/js/calendar/calendar-setup.js"></script>
<script src="stat_table_clients.js"></script>


<script language="JavaScript" type="text/javascript">
<!--

var tpacc = <?=$tpacc?>;
var curr_date = '<?=date("d.m.Y")?>';		// текущая дата в формате '01.05.2007'
var curr_filtr = '';

//-->
</script>


<body>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>

<table width="750" align="center">
<tr>
<td>
<br>
<?
$tit = 'Работа с таблицами / Поставщики';
$name_curr_page = 'clients';
require_once("../templates/main_menu_stat.php");?>
<table width="700" cellpadding="5" cellspacing="0" bgcolor="#F6F6F6" align="center">
	<tr>
		<td align="center" class="title_razd"><?=@$tit?></td>
	</tr>
	<tr>
		<td align="center"><a href="?filtr=clear">Очистить фильтры</a></td>
	</tr>
	<tr>
	  <td valign="top">



			<table align="center" class="stat_table_main">
				<tr>
				  <td align="center"><img src="../i/pix.gif" width="50" height="1"></td>
				  <td align="center"><img src="../i/pix.gif" width="100" height="1"></td>
				  <td align="center"><img src="../i/pix.gif" width="100" height="1"></td>
				  <td align="center"><img src="../i/pix.gif" width="150" height="1"></td>
				  <td align="center"><img src="../i/pix.gif" width="60" height="1"></td>
				  <td align="center"><img src="../i/pix.gif" width="1" height="1"></td>
				  <td align="center"><img src="../i/pix.gif" width="1" height="1"></td>
				  <td align="center"><img src="../i/pix.gif" width="80" height="1"></td>
				  <td align="center"><img src="../i/pix.gif" width="1" height="1"></td>
				  <td align="center"><img src="../i/pix.gif" width="60" height="1"></td>
				</tr>
				<tr class="stat_tr_title">
					<td class="stat_td_title">
						<a href="#" onclick="ShowFiltrNum('nm_acc'); return false;"><img width="10" height="6" onmouseover="Tip('Фильтр по номеру счета')" src="../i/filter.gif"></a>&nbsp;№					</td>
					<td class="stat_td_title">
						<a href="#" onclick="ShowFiltrNum('client'); return false;"><img width="10" height="6" onmouseover="Tip('Фильтр по наименованию клиента')" src="../i/filter.gif"></a>&nbsp;Клиент				</td>
					<td class="stat_td_title">
						<a href="#" onclick="ShowFiltrNum('podr'); return false;"><img width="10" height="6" onmouseover="Tip('Фильтр по наименованию подрядчика')" src="../i/filter.gif"></a>&nbsp;Подрядчик</td>
					<td class="stat_td_title">Предмет</td>
					<td class="stat_td_title">
						<a href="#" onclick="ShowFiltrNum('num_prdm'); return false;"><img width="10" height="6" onmouseover="Tip('Фильтр по количеству')" src="../i/filter.gif"></a>&nbsp;Количество
					</td>
					<td class="stat_td_title">
						<a href="#" onclick="ShowFiltrNum('summ_prdm'); return false;"><img width="10" height="6" onmouseover="Tip('Фильтр по сумме')" src="../i/filter.gif"></a>&nbsp;Сумма
					</td>
					<td class="stat_td_title">
						<a href="#"  onclick="ShowFiltrNum('opl'); return false;"><img width="10" height="6" onmouseover="Tip('Фильтр по сумме оплаты')" src="../i/filter.gif"></a>&nbsp;Оплачено
					</td>
					<td class="stat_td_title">
						<a href="#" onclick="ShowFiltrNum('debt'); return false;"><img width="10" height="6" onmouseover="Tip('Фильтр по размеру долга')" src="../i/filter.gif"></a>&nbsp;<span>Долг</span>					</td>
					<td class="stat_td_title">
						<a href="#" onclick="ShowFiltrNum('spis'); return false;"><img width="10" height="6" onmouseover="Tip('Фильтр по статусу долга')" src="../i/filter.gif"></a>&nbsp;Долг списан</td>
					<td class="stat_td_title">
						<a href="#"  onclick="ShowFiltrNum('num_acc_prdm'); return false;"><img width="10" height="6" onmouseover="Tip('Фильтр по номеру счета')" src="../i/filter.gif"></a>
							&nbsp;Номер счета
					</td>
				</tr>
				<?
				$num_itg 			= 0;			// итоговое количество
				$summ_itg 		= 0;			// итоговая сумма
				$opl_itg 			= 0;			// итоговая оплата
				$dolg_itg 		= 0;			// итоговый долг

				while(@$r_qr = mysql_fetch_array($res_qr)) {
					$foff = ($r_qr['debt_spis']) ? true : false;

				?>
				<tr align="center" class="stat_tr_norm">
					<?
					// Подсказка для номера счета - менеджер
					$alt_user = htmlspecialchars($r_qr['surname'].' '.$r_qr['us_name'].' '.$r_qr['father']);
					$alt_user = ' onmouseover="Tip(\''.$alt_user.'\', TITLE, \'Счет запросил\')" ';

					$acc_prdm_val = $r_qr['prdm_num_acc'];
					if( ($acc_prdm_val == 'none') || ($acc_prdm_val == '') )
						$acc_prdm_val = '-';
					?>
				  <td class="stat_td_norm" <?=$alt_user?>><?=$acc_prdm_val?></td>

					<?
					// Подсказка для клиента
					$alt_client = '<table><tr><td align=right>Полное наименование:&nbsp;&nbsp;</td><td align=left><strong>'.$r_qr['full_name'].'</strong></td></tr>';
					$alt_client .= (trim($r_qr['legal_address'])) ? '<tr><td align=right>Юридический адрес:&nbsp;&nbsp;</td><td align=left><strong>'.$r_qr['legal_address'].'</strong></td></tr>' : '';
					$alt_client .= (trim($r_qr['postal_address'])) ? '<tr><td align=right>Фактический адрес:&nbsp;&nbsp;</td><td align=left><strong>'.$r_qr['postal_address'].'</strong></td></tr>' : '';
					$alt_client .= (trim($r_qr['inn'])) ? '<tr><td align=right>ИНН:&nbsp;&nbsp;</td><td align=left><strong>'.$r_qr['inn'].'</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>' : '';
					$alt_client .= (trim($r_qr['kpp'])) ? '<tr><td align=right>КПП:&nbsp;&nbsp;</td><td align=left><strong>'.$r_qr['kpp'].'</strong><br></td></tr>' : '';
					$alt_client .= (trim($r_qr['cont_pers'])) ? '<tr><td align=right>Контактное лицо:&nbsp;&nbsp;</td><td align=left><strong>'.$r_qr['cont_pers'].'</strong><br></td></tr>' : '';
					$alt_client .= (trim($r_qr['cont_tel'])) ? '<tr><td align=right>Контактный телефон:&nbsp;&nbsp;</td><td align=left><strong>'.$r_qr['cont_tel'].'</strong><br></td></tr>' : '';
					$alt_client .= '</table>';

					$alt_client = htmlspecialchars($alt_client);
					$alt_client = ' onmouseover="Tip(\''.$alt_client.'\', TITLE, \'Подробно\')" '
					?>
				  <td class="stat_td_norm" <?=$alt_client?>><a target="_blank" href="/acc/query/query_send.php?show=<?=$r_qr['qr_id']?>"><?=$r_qr['short']?></a></td>

					<?
					// Подсказка для поставщика
					$t1 = trim($r_qr['podr_cont_pers']);	// контактное лицо
					$t2 = trim($r_qr['podr_cont_tel']);		// контактный телефон
					$t3 = trim($r_qr['podr_email']);			// мэйл

					$alt_contr = '';

					if($t1 || $t2 || $t3) {
							$alt_contr = '<table>';
							$alt_contr .= ($t1) ? '<tr><td align=right>Контактное лицо:&nbsp;&nbsp;</td>'.
							'<td align=left><strong>'.$t1.'</strong></td></tr>' : '';
							$alt_contr .= ($t2) ? '<tr><td align=right>Контактный тел.:&nbsp;&nbsp;</td>'.
							'<td align=left><strong>'.$t2.'</strong></td></tr>' : '';
							$alt_contr .= ($t3) ? '<tr><td align=right>E-Mail:&nbsp;&nbsp;</td>'.
							'<td align=left><strong>'.$t3.'</strong></td></tr>' : '';
							$alt_contr .= '</table>';
							$alt_contr = htmlspecialchars($alt_contr);
							$alt_contr = ' onmouseover="Tip(\''.$alt_contr.'\', TITLE, \'Подробно\')" ';
					}

					if(!trim($r_qr['podr_name'])) {
						$r_qr['podr_name'] = '???';
						$alt_contr = ' onmouseover="Tip(\'Не определен\')" ';
					}
					?>
				  <td class="stat_td_norm" <?=$alt_contr?>><a target="_blank" href="/acc/query/contractors_list.php?edit=<?=$r_qr['contr_id']?>"><?=$r_qr['podr_name']?></a></td>
					<?
						// предмет
						$predm = htmlspecialchars($r_qr['contr_name']);

						// подсказка для предмета
						$alt_predm = ($predm) ? ' onmouseover="Tip(\''.$predm.'\')" ' : '';

					?>
				  <td class="stat_td_edit" <?=$alt_predm?>><input onkeyup="enableSaveButt()" onchange="setValTab(<?=$r_qr['cl_id']?>,'name', this.value);" class="contr_inp_predmet" name="" type="text" value="<?=$predm?>" /></td>
					<? if(!$foff) $num_itg += $r_qr['contr_num']; ?>
				  <td class="stat_td_edit"><input onkeyup="enableSaveButt()" onchange="setValTab(<?=$r_qr['cl_id']?>,'num', this.value);"  class="contr_inp_num" name="" type="text" value="<?=$r_qr['contr_num']?>" /></td>
					<? if(!$foff) $summ_itg += $r_qr['contr_num'] * strtr($r_qr['contr_price'],',','.'); ?>
				  <td class="stat_td_norm"><?=form_num($r_qr['contr_num'] * strtr($r_qr['contr_price'],',','.'))?></td>

					<?
					//ПОЛЯ ОПЛАТЫ

					$compl_cost = form_num(trim($r_qr['contr_opl']));
					$alt_compl_cost = '<div id="opl_div_'.$r_qr['cl_id'].'">';
					if($compl_cost && ($compl_cost != '0'))
							$alt_compl_cost .= '<a href="#" class=stat_yes_alt><strong>'.$compl_cost.'</strong></a>';
					else
						$alt_compl_cost .= '<a href="#" class=stat_no><strong>---<strong></a>';
					$alt_compl_cost .= '<div>';

					if(!$foff) $opl_itg += $compl_cost;
					?>

				  <td class="stat_td_n_opl"  onmouseover="Tip('Редактировать список платежей');" onclick="LoadCostList(<?=$r_qr['cl_id']?>);return false;"><?=$alt_compl_cost?></td>
					<?
					// ДОЛГ

					$dolg = ($r_qr['contr_num'] * strtr($r_qr['contr_price'],',','.')) - $compl_cost;
					if(!$foff)
						$dolg_itg += $dolg;
					?>
				  <td class="stat_td_n_dolg" style="backround-color:#EEEEEE;"><?=form_num($dolg)?></td>
				  <td class="stat_td_norm"><input  name="" type="checkbox" class="contr_chk_dolg" onchange="setValTab(<?=$r_qr['cl_id']?>,'debt_spis', (0+this.checked));" onclick="enableSaveButt()" value="1" <?=($foff) ? 'checked="checked"' : ''?> /></td>
				  <td class="stat_td_edit"><input onkeyup="enableSaveButt()" onchange="setValTab(<?=$r_qr['cl_id']?>,'acc_number', this.value);" class="contr_inp_price" name="" type="text" value="<?=@$r_qr['acc_number']?>" /></td>
				 </tr>
				 <? } ?>
				 <tr align="center" class="stat_tr_norm">
					<td class="stat_td_itog" colspan="4" align="center"><strong>ИТОГО</strong></td>
					<td class="stat_td_itog" align="center"><?=$num_itg?></td>
					<td class="stat_td_itog" align="center"><?=form_num($summ_itg)?></td>
					<td class="stat_td_itog" align="center"><?=form_num($opl_itg)?></td>
					<td class="stat_td_itog" align="center"><?=form_num($dolg_itg)?></td>
					<td colspan="2" class="stat_td_itog" align="center">&nbsp;</td>

				 	<td colspan="6"></td>
				 </tr>
			</table>
		</td>
	  </tr>
	<tr>
	  <td valign="top" align="center"><input onclick="SaveTabAllData()" id="SaveButt" disabled="disabled" type="button" value="Сохранить изменения" /></td>
	  </tr>
</table>

</td>
</tr>
</table>



<!-- ******************** СЛОЙ ФИЛЬТРА ПО ЦИФРЕ  <<<< *****************  //-->


<div onMouseUp="end_drag()" onMouseMove="dragIt(this,event)" id="div_fltr_num" style="background-color:#FFFFFF; position:absolute; top:400px; left:300px; width:160px; padding:10px; border:1px #0099CC solid; display:none;">
<table border="0" cellspacing="0" cellpadding="0" align="center" width="1">

	<tr>
		<td style="cursor:move;" onMouseDown="start_drag(document.getElementById('div_fltr_num'),event)" colspan="2" align="center" valign="top"><strong><div id="div_fltr_num_tit"></div></strong></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td align="center">Условие</td>
	  <td align="center">Значение</td>
	</tr>
	<form name="ff_fltr_num" action="" method="get">
	<tr>
		<td align="center">

		<input id="inp_fltr_num_name" name="" type="hidden" value="" />

		<select id="sel_fltr_num_case" name="" class="stat_fltr_num_sel">
	    <option value="">&nbsp;</option>
	    <option value="=">равно</option>
	    <option value="<>">не равно</option>
	    <option value="<">меньше</option>
	    <option value=">">больше</option>
	  </select>

		</td>
	  <td align="center"><input id="inp_fltr_num_val" onkeyup="this.value=replace_price(this.value)" class="stat_fltr_num_inp" name="" type="text" /></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<input class="frm_podr_opl_butt" name="" type="button" value="ОК" onclick="return SetFiltrNum();" /><input name="" class="frm_podr_opl_butt" type="button" value="Отмена" onclick="document.getElementById('div_fltr_num').style.display = 'none';return false;" />
		</td>
	</tr>
	</form>
</table>
</div>



<!--  >>>>>>******************** СЛОЙ ФИЛЬТРА ПО ЦИФРЕ  *****************  //-->





<!-- ******************** СЛОЙ ФИЛЬТРА ПО КЛИЕНТУ  <<<< *****************  //-->


<div onMouseUp="end_drag()" onMouseMove="dragIt(this,event)" id="div_fltr_client" style="background-color:#FFFFFF; position:absolute; top:400px; left:300px; width:255px; padding:5px; border:1px #0099CC solid; display:none;">
<table border="0" cellspacing="0" cellpadding="0" align="center" width="1">
	<tr>
		<td style="cursor:move;" onMouseDown="start_drag(document.getElementById('div_fltr_client'),event)" width="100%">&nbsp;</td>
	</tr>
	<form name="ff_fltr_client" action="" method="get">
	<tr>
		<td align="center">
				<?
				$query = "SELECT a.*, b.surname FROM clients as a, users as b WHERE a.del=0 AND a.user_id=b.uid ORDER BY a.user_id,a.name";
//				$query = "SELECT uid,short FROM clients ORDER BY short";
				$res_cl = mysql_query($query);
				?>
					<select name="" size="10" class="stat_fltr_client_sel" id="sel_filtr_client" >
						<option value="0">&nbsp;&nbsp;------</option>
						<?
						$i=0;
						$optgr = -1;	// ид тек клиента
						$fl_optgr = 0;	// после открытия 1го тега optgroup=1

						while($r_cl = mysql_fetch_array($res_cl)) {

							$sel='';
							$gr_name = '';	// фам пользователя (группа селекта)

							if($r_cl['user_id'] != $optgr) {	// клиенты след пользователя
								$optgr = $r_cl['user_id'];		// запомнить польз
								$gr_name = $r_cl['surname'];	// фам пользователя
							}

							if($gr_name) { 	/* открытие группы */ ?>
							<? if($fl_optgr) { /* закрыть предыдущую гр если открыта */ ?></optgroup><? }
								$fl_optgr = 1;	/* флаг - пред группа открыта */ ?>
							<optgroup label="<?=$gr_name?>" >
							<? } ?>

							<option value="<?=$r_cl['uid']?>"<?=$sel?>><?=$r_cl['short']?></option>

							<? $i++; } ?>
					</select>


		</td>
	</tr>
	<tr>
		<td align="center">
			<input class="frm_podr_opl_butt" name="" type="button" value="ОК" onclick="return SetFiltrClient();" /><input name="" class="frm_podr_opl_butt" type="button" value="Отмена" onclick="document.getElementById('div_fltr_client').style.display = 'none';return false;" />
		</td>
	</tr>
	</form>
</table>
</div>



<!--  >>>>>>******************** СЛОЙ ФИЛЬТРА ПО КЛИЕНТУ  *****************  //-->




<!-- ******************** СЛОЙ ФИЛЬТРА ПО ПОСТАВЩИКУ  <<<< *****************  //-->


<div onMouseUp="end_drag()" onMouseMove="dragIt(this,event)" id="div_fltr_podr" style="background-color:#FFFFFF; position:absolute; top:400px; left:300px; width:255px; padding:5px; border:1px #0099CC solid; display:none;">
<table border="0" cellspacing="0" cellpadding="0" align="center" width="1">
	<tr>
		<td style="cursor:move;" onMouseDown="start_drag(document.getElementById('div_fltr_podr'),event)" width="100%">&nbsp;</td>
	</tr>

	<form name="ff_fltr_podr" action="" method="get">
	<tr>
		<td align="center">
				<?
				$query = "SELECT uid,name FROM contractors ORDER BY name";
				$res = mysql_query($query);
				?>
				<select id="sel_filtr_podr" name="" size="8" class="stat_fltr_client_sel">
						<option value="0">&nbsp;&nbsp;------</option>
					<? while($r = mysql_fetch_array($res)) { ?>
						<option value="<?=$r['uid']?>">&nbsp;&nbsp;<?=$r['name']?></option>
					<? } ?>
				</select>
		</td>
	</tr>
	<tr>
		<td align="center">
			<input class="frm_podr_opl_butt" name="" type="button" value="ОК" onclick="return SetFiltrPodr();" /><input name="" class="frm_podr_opl_butt" type="button" value="Отмена" onclick="document.getElementById('div_fltr_podr').style.display = 'none';return false;" />
		</td>
	</tr>
	</form>
</table>
</div>



<!--  >>>>>>******************** СЛОЙ ФИЛЬТРА ПО ПОСТАВЩИКУ  *****************  //-->





<!-- ******************** СЛОЙ ФИЛЬТРА ДОЛГ СПИСАН  <<<< *****************  //-->


<div onMouseUp="end_drag()" onMouseMove="dragIt(this,event)" id="div_fltr_spis" style="background-color:#FFFFFF; position:absolute; top:400px; left:300px; width:60px; padding:5px; border:1px #0099CC solid; display:none;">
<table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
	<tr>
		<td style="cursor:move;" onMouseDown="start_drag(document.getElementById('div_fltr_spis'),event)" align="right" valign="top"><a onmouseover="Tip('Закрыть')" onclick="document.getElementById('div_fltr_spis').style.display = 'none';return false;" style="font-weight:bold; color:#FF0000;" href="#">X</a></td>
	</tr>

	<form name="ff_fltr_spis" action="" method="get">
	<tr>
		<td align="center">
			<a href="#" onclick="SetFiltrSpis(0);return false;">нет</a><br>
			<a href="#" onclick="SetFiltrSpis(1);return false;">да</a><br>
		</td>
	</tr>
	</form>
</table>
</div>



<!--  >>>>>>******************** СЛОЙ ФИЛЬТРА ДОЛГ СПИСАН  *****************  //-->



<!-- ******************** СЛОЙ РЕДАКТИРОВАНИЯ ОПЛАТ <<<< *****************  //-->


<div onMouseUp="end_drag()" onMouseMove="dragIt(this,event)" id="div_podr_opl" style="background-color:#FFFFFF; position:absolute; top:400px; left:300px; padding:10px; border:1px #0099CC solid; display:none;">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td style="cursor:move;" onMouseDown="start_drag(document.getElementById('div_podr_opl'),event)" align="right" valign="top"><a onmouseover="Tip('Закрыть')" onclick="hide_div_opl();return false;" style="font-weight:bold; color:#FF0000;" href="#">X</a></td>
	</tr>
	<tr>
		<td style="cursor:move;" onMouseDown="start_drag(document.getElementById('div_podr_opl'),event)" align="center" valign="top"><strong>Список платежей</strong></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<form name="ff_new_opl" action="" method="get">
	<? for($i=0;$i<=14;$i++) {?>
	<tr>
		<td>
			<div id='opl_feld<?=$i?>' style="display:none;"></div>
		</td>
	</tr>
	<? } if($tpacc) {?>
	<tr>
		<td align="center">
			<input class="frm_podr_opl_butt" name="" type="button" value="Сохранить" onclick="return check_opl();" /><input name="" class="frm_podr_opl_butt" type="button" value="Отмена" onclick="hide_div_opl()" />
		</td>
	</tr>
	<? } ?>
	</form>
</table>
</div>



<!--  >>>>>>******************** СЛОЙ РЕДАКТИРОВАНИЯ ОПЛАТ  *****************  //-->





<div id="debug"></div>

</body>
</html>
<? ob_end_flush(); ?>