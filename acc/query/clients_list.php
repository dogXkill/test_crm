<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

$auth = false;

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");
if ($user_access['order_access'] == '0' || empty($user_access['order_access'])) {
	header('Location: /');
}


// старт сессии
ini_set("session.cookie_lifetime",0);
session_start();



if(!$auth) {
	header("Location: /");
	exit;
}

// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;



$oper = @$_SESSION['tp_list'];	// запоминание операции (список, архив)

// текущий тип операции
if(isset($_GET['oper']) && ($_GET['oper'] == 'new'))
	$oper = 'new';	// новый клиент

elseif(isset($_GET['oper']) && ($_GET['oper'] == 'arhiv')) {
	$oper = 'arhiv';						// архив клиентов
	$_SESSION['tp_list'] = $oper;
}

elseif(isset($_GET['oper']) && ($_GET['oper'] == 'list')) {
	$oper = 'list';						// список клиентов
	$_SESSION['tp_list'] = $oper;
}

// редактирование
elseif( isset($_GET['edit']) && is_numeric($_GET['edit']) )
	$oper = 'edit';

$tp_list = (@$_SESSION['tp_list'] == 'arhiv') ? 1 : 0; 	// поле del в базе, 1 - архив, 0 - нет




// пользователеь для которого выводить клиентов
if( (isset($_GET['sel_us'])&& is_numeric($_GET['sel_us'])) && ($tpacc) )
	$sel_us = $_GET['sel_us'];
else	// если не указан - текущий пользователь
	$sel_us = $user_id;







// Распределение списка клиентов по пользователям основываясь по ссылкам в запросах на счет
if(isset($_POST['set_me']) && trim($_POST['set_me'])) {

	$query = "SELECT uid FROM users WHERE 1=1";
	$res = mysql_query($query);
	while( $r = mysql_fetch_array($res) ) {
		// чтение всех полей копируемого клиента
		$query = sprintf("SELECT a.uid as q_id, b.* FROM queries as a, clients as b WHERE a.user_id=%d AND a.client_id=b.uid AND b.user_id=0 AND b.del=0",$r['uid']);
		$res2 = mysql_query($query);
		while($r2 = mysql_fetch_array($res2)) {
			if($r['uid']==0)
				continue;
			$query = "SELECT uid FROM clients WHERE del=0 AND user_id=".$r['uid']." AND short='".$r2['short']."'";
			$res3 = mysql_query($query);
			// если у пользователя такого клиента нет
			if( mysql_num_rows($res3) == 0) {
				// добавление нового клиента
				$query = sprintf("INSERT INTO clients(user_id,short,name,legal_address,postal_address,inn,kpp,cont_pers,cont_tel,rs_acc,bank,bik,korr_acc,dogov_num,firm_tel,email,gen_dir,ogrn) VALUES(%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')", $r['uid'], $r2['short'],$r2['name'],$r2['legal_address'],$r2['postal_address'],$r2['inn'],$r2['kpp'],$r2['cont_pers'],$r2['cont_tel'],$r2['rs_acc'],$r2['bank'],$r2['bik'],$r2['korr_acc'],$r2['dogov_num'],$r2['firm_tel'],$r2['email'],$r2['gen_dir'],$r2['ogrn']);
				mysql_query($query);
				$new_cl = mysql_insert_id();	// ид добавленного клиента

				// изменение ссылки на клиента во всех запросах на счет этого пользователя
				$query = "SELECT uid FROM queries WHERE user_id=".$r['uid']." AND client_id=".$r2['uid'];
				$res4 = mysql_query($query);
				while( $r4 = mysql_fetch_array($res4) ) {
					$query = "UPDATE queries SET client_id='".$new_cl."' WHERE uid=".$r4['uid'];
					mysql_query($query);
				}
			}
		}
	}
	// удаление клиентов из общего списка на которых нету привязки в запросах
	$query = "SELECT uid FROM clients WHERE del=0 AND user_id=0";
	$res = mysql_query($query);
	while($r = mysql_fetch_array($res)) {
		$query = "SELECT uid FROM queries WHERE client_id=".$r['uid']." LIMIT 1";
		$res2 = mysql_query($query);
		if(mysql_num_rows($res2) == 0) {
			$query = "DELETE FROM clients WHERE uid=".$r['uid'];
			mysql_query($query);
		}
	}
}


// Копирование клиетов другому пользователю
if( (isset($_POST['copy_to']) && trim($_POST['copy_to'])) || (isset($_POST['move_to']) && trim($_POST['move_to'])) ) {

	$cp_us_id = $_POST['sel_cl_copy'];		// ид пользователя
	$arr_cl = @$_POST['sel_str'];			// массив выбранных клиентов

	for($i=0;$i<count($arr_cl);$i++) {

		$query = "SELECT * FROM clients WHERE uid=".$arr_cl[$i];
		$res = mysql_query($query);


		if($r = mysql_fetch_array($res)) {

			$query = "SELECT uid FROM clients WHERE del=0 AND user_id=".$cp_us_id." AND short='".$r['short']."'";
			$res2 = mysql_query($query);
			if( mysql_num_rows($res2) == 0 ) {	// если у пользователя нету такого клиента - добавить
				$query = sprintf("INSERT INTO clients(user_id,short,name,legal_address,postal_address,inn,kpp,cont_pers,cont_tel,rs_acc,bank,bik,korr_acc,dogov_num,firm_tel,email,gen_dir,ogrn) VALUES(%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",$cp_us_id, $r['short'], $r['name'], $r['legal_address'], $r['postal_address'], $r['inn'], $r['kpp'], $r['cont_pers'], $r['cont_tel'], $r['rs_acc'], $r['bank'], $r['bik'], $r['korr_acc'], $r['dogov_num'], $r['firm_tel'], $r['email'], $r['gen_dir'], $r['ogrn']);
				mysql_query($query);
				$new_ins_client = mysql_insert_id();	// ид добавленного клиента
/*
				// Обновление ссылок
				if($sel_type_act == 3) {
					$query = "SELECT uid FROM queries WHERE client_id=".$arr_cl[$i]."";
					$res = mysql_query($query);
					while($r = mysql_fetch_array($res)) {	// обновление ссылок на клиента и пользователя в запросах
						$query = sprintf( "UPDATE queries SET client_id=%d, user_id=%d WHERE uid=%d", $new_ins_client, $cp_us_id, $r['uid'] );
						mysql_query($query);
					}
				}
*/
				// если перемещение - удаление клиента в архив
				if( isset($_POST['move_to']) && trim($_POST['move_to']) ) {
					$query = "UPDATE clients SET del=1 WHERE uid=".$arr_cl[$i];
					mysql_query($query);
				}
			}
		}
	}
}



// Удаление выбранных клиентов в архив или из архива
if( isset($_POST['del_sel']) && trim($_POST['del_sel']) ) {
	$arr_cl = @$_POST['sel_str'];		// массив выбранных клиентов
	if(count($arr_cl)) {	// если выбрано

		if(!$tp_list) {		// перемещение в архив

			foreach($arr_cl as $t) {
				$query = "UPDATE clients SET del=1 WHERE uid=".$t;
				mysql_query($query);
			}

		} else {	// полное удаление клиента и ссылающихся запросов

			foreach($arr_cl as $t) {

				// удаление запросов
				$query = "DELETE FROM queries WHERE client_id=".$t;
				mysql_query($query);

				// удаление клиентов
				$query = "DELETE FROM clients WHERE uid=".$t." AND del=1";
				mysql_query($query);

			}
		}
	}
}




// удаление в архив или полное удаление клиента
if(isset($_GET['del']) && is_numeric($_GET['del'])) {

	if($tp_list) {	// полное удаление из архива

		// удаление запросов
		$query = "DELETE FROM queries WHERE client_id=".$_GET['del'];
		mysql_query($query);

		// удаление клиентов
		$query = "DELETE FROM clients WHERE uid=".$_GET['del']." AND del=1";
		mysql_query($query);

	} else {	// помещение в архив
		$query = "UPDATE clients SET del=1 WHERE uid=".$_GET['del'];
	//	$query = "DELETE FROM clients WHERE uid=".$_GET['del'];
		mysql_query($query);
	}
}




// Восстановление выбранных клиентов
if( isset($_POST['rest_sel']) && trim($_POST['rest_sel']) ) {
	$arr_cl = @$_POST['sel_str'];		// массив выбранных клиентов
	if(count($arr_cl)) {	// если выбрано
		foreach($arr_cl as $t) {
			$query = "UPDATE clients SET del=0 WHERE uid=".$t;
			mysql_query($query);
		}
	}
}




// восстановление из архива клиента
if(isset($_GET['rest']) && is_numeric($_GET['rest'])) {
	$query = "UPDATE clients SET del=0 WHERE uid=".$_GET['rest'];
	mysql_query($query);
}


















// установка оции в базе
function set_opt($name,$val) {
	$query = "DELETE FROM options WHERE name='".$name."'";
	mysql_query($query);
	$query = "INSERT INTO options(name,val) VALUES('".$name."','".$val."')";
	mysql_query($query);
}

// чтение опции из базы
function sel_opt($name) {
	$query = "SELECT val FROM options WHERE name='".$name."' LIMIT 1";
	$res = mysql_query($query);
	if($r = mysql_fetch_array($res))
		return trim($r['val']);
	else
		return false;
}






// если нажата сбросить счетчик договоров
if(!empty($_GET['cl_dog']) && ($_GET['cl_dog'] == 1)) {
	cl_num_dog();
	header("Location: ?sel_us=".$sel_us);
}

// сброс счетчика договоров и установка тек даты
function cl_num_dog() {
	set_opt('dogovor_num','0');
	set_opt('dogovor_dat',date("d-m"));
	return '0';
}

/*
if(!($dt = sel_opt('dogovor_dat'))) { 	// если не установлена дата
	$dt = date("d-m");
	set_opt('dogovor_dat',$dt);
	$nm = 0;
}
if( $dt != date("d-m") ) {	// след день
	$nm = 0;
	set_opt('dogovor_dat', date("d-m"));
}
*/


// проверка состояния счетчика договоров
if($r = sel_opt('dogovor_num')) {  // если есть установленный счетчик договора
	$dogov_num = intval($r);
	if( $r = sel_opt('dogovor_dat') ) {	// если установлена дата формата "день"-"месяц"
		$dogov_dat = $r;
		if( (date("d-m") != $dogov_dat) || ( $dogov_num > 98 ) ) 		// если договор не в этот же день - сброс счетчика
			$dogov_num = cl_num_dog();
	}
	else
		$dogov_num = cl_num_dog();
}
else
	$dogov_num = cl_num_dog();




// количество сформированных за сегодня договоров
if($nums_ht_dog = sel_opt('dogovor_num')) {
	$nums_ht_dog = intval($nums_ht_dog);
} else {
	$nums_ht_dog = 0;
}





// Сохранение редактируемого клиента, или сохранение и формирование договора

if( (isset($_POST['butt_send']) && trim($_POST['butt_send'])) || (isset($_POST['butt_send2']) && trim($_POST['butt_send2'])) ) {
	$error = '';

	if(!trim($_POST['short']))
		$error = 'Поле "Короткое название" не заполнено!';
	elseif(!trim($_POST['name']))
		$error = 'Поле "Полное юр. наименование" не заполнено!';
  elseif(!trim($_POST['gen_dir']))
    $error = 'Поле "Генеральный директор" не заполнено!';

	if((!$error) && (!is_numeric($_POST['edit']))) {
		$query = "SELECT * FROM clients WHERE del=".$tp_list." AND short='".trim($_POST['short'])."'";
		$res = mysql_query($query);
		if(mysql_num_rows($res)) {
			$error = 'Клиент с таким наименованием уже существует, <br>попробуйте еще раз.';
		}
	}

	$dogov_num = trim($_POST['dogov_num']);		// номер договора
/*
	if(!$dogov_num) {	// если номер договора не заполнен
		$dogov_num++;
		if($dogov_num<10)		// формат 05
			$dogov_num = '0'.$dogov_num;

		set_opt('dogovor_num',$dogov_num);
		$dogov_num = $dogov_num.'-'.date("d-m");
	}
*/
	if(!$error) {
		if(!is_numeric($_POST['edit'])) {
			$query = sprintf("INSERT INTO clients(user_id,short,name,legal_address,postal_address,inn,kpp,cont_pers,cont_tel,rs_acc,bank,bik,korr_acc,dogov_num,firm_tel,email,gen_dir,ogrn) VALUES(%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')", $sel_us, $_POST['short'],$_POST['name'],$_POST['leg_add'],$_POST['post_add'],$_POST['inn'],$_POST['kpp'],$_POST['cont_pers'],$_POST['cont_tel'],$_POST['rs_acc'],$_POST['bank'],$_POST['bik'],$_POST['korr_acc'],$dogov_num,$_POST['firm_tel'],$_POST['email'],$_POST['gen_dir'],$_POST['ogrn']);
		} else {
			$query = sprintf("UPDATE clients SET short='%s',name='%s',legal_address='%s',postal_address='%s',inn='%s',kpp='%s',cont_pers='%s',cont_tel='%s',rs_acc='%s',bank='%s',bik='%s',korr_acc='%s',dogov_num='%s',firm_tel='%s',email='%s',gen_dir='%s', ogrn='%s' WHERE uid=%d",$_POST['short'], $_POST['name'], $_POST['leg_add'],$_POST['post_add'],$_POST['inn'],$_POST['kpp'],$_POST['cont_pers'],$_POST['cont_tel'],$_POST['rs_acc'],$_POST['bank'],$_POST['bik'],$_POST['korr_acc'],$dogov_num,$_POST['firm_tel'],$_POST['email'],$_POST['gen_dir'],$_POST['ogrn'],$_POST['edit']);
		}
		mysql_query($query);
		if(isset($_POST['butt_send2']) && trim($_POST['butt_send2']))
			header("Location: clients_list.php?sel_us=".$sel_us."&dog=".$_POST['edit']);
		else
			header("Location: clients_list.php?sel_us=".$sel_us);
	}
}





// ---------------------------------------------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.1</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<script type="text/javascript" src="../includes/js/jquery-1.9.1.min.js"></script>

<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>
<script type="text/javascript" src="client_list.js"></script>

<script language="JavaScript" type="text/javascript">

	<? if( !empty($_GET['dog']) && is_numeric($_GET['dog']) ) { ?>
	function op_dog() {
		msgWindow = open("/1.php?dog=<?=$_GET['dog']?>");
	}
	document.onload=op_dog();
	<? } ?>


</script>

<? require_once("../templates/top.php"); ?>

<table align=center width=750 border=0>


<tr>
<td colspan=3>
<br>
<?
$name_curr_page = 'query_list';
require_once("../templates/main_menu.php");?>
<table width=100% border=0 cellpadding="5" cellspacing="0" bgcolor="#F6F6F6">
<?
switch ($oper) {
	case 'new':
		$tit_r = 'Добавление клиента';
		break;
	case 'edit':
		$tit_r = 'Редактирование клиента';
		break;
	default:
		$tit_r = 'Список клиентов';

}
?>
	<tr>
		<td align="center" class="title_razd"><?=$tit_r?></td>
	</tr>
	<tr>
		<td valign="top" align="center">
			<table border="0" cellspacing="0" cellpadding="0" align="center">
				<tr>



					<? if($oper=='new') { ?>

					<td width="150">
						<span class="sublink_pl">+</span>
						<a href="?oper=list&sel_us=<?=$sel_us?>" class="sublink">Список клиентов</a>
					</td>
					<td width="150">
						<font face=tahoma size=4><strong>+</strong></font>
						<font face=tahoma size=2>Добавить клента</font>
					</td>

					<? } elseif($oper == 'arhiv') { ?>
						<td width="150">
							<span class="sublink_pl">+</span>
							<a href="?oper=list&sel_us=<?=$sel_us?>" class="sublink">Список клиентов</a>
						</td>
						<td width="150">
							<span class="sublink_pl">+</span>
							<a href="?oper=arhiv&sel_us=<?=$sel_us?>" class="sublink_off">Архив клиентов</a>
						</td>

					<? } elseif($oper == 'edit') { ?>

						<td width="150">
							<span class="sublink_pl">+</span> <a href="?oper=list&sel_us=<?=$sel_us?>" class="sublink">Список клиентов</a>
						</td>
						<? if(!$tp_list) { ?>
						<td width="150">
							<span class="sublink_pl">+</span> <a href="?oper=new&sel_us=<?=$sel_us?>" class="sublink">Добавить клиента</a>
						</td>
						<? } else { ?>
						<td width="150">
							<span class="sublink_pl">+</span>
							<a href="?oper=arhiv&sel_us=<?=$sel_us?>" class="sublink_off">Архив клиентов</a>
						</td>
						<? } ?>

					<? } else {?>

						<td width="150">
							<span class="sublink_pl_off">+</span> <a href="?oper=list&sel_us=<?=$sel_us?>" class="sublink_off">Список клиентов</a>
						</td>
						<td width="150">
							<span class="sublink_pl">+</span> <a href="?oper=new&sel_us=<?=$sel_us?>" class="sublink">Добавить клиента</a>
						</td>
						<td width="150">
							<span class="sublink_pl">+</span>
							<a href="?oper=arhiv&sel_us=<?=$sel_us?>" class="sublink">Архив клиентов</a>
						</td>

					<? } ?>
				</tr>
			</table>
		</td>
	</tr>
<tr>
	<td align="center">&nbsp;</td>
</tr>

<tr>
	<td align="center">
		<? if($auth) {
				if(($oper == 'new') || ($oper == 'edit')) { ?>

					<table border="0" cellspacing="0" cellpadding="0" width="600" align="center">
					<? if(@$error) { ?>
					<tr>
						<td colspan="2" align="center" class="err"><?=@$error?></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<? } ?>
				<form action="?sel_us=<?=$sel_us?>" method="post" name="editus">
					<input name="edit" type="hidden" value="<?=@$_GET['edit']?>" />

					<?
					if($oper == 'edit') {
						$query  = "SELECT * FROM clients WHERE uid=".$_GET['edit'];
						$res = mysql_query($query);
						@$r = mysql_fetch_array($res);

						$query = "SELECT surname,name,father FROM users WHERE uid=".$r['user_id'];
						$res2 = mysql_query($query);

						if($r2 = mysql_fetch_array($res2))
							$name_user = @$r2['surname'].' '.@$r2['name'].' '.@$r2['father'];
						else
							$name_user = 'не определен';
					?>
					<tr>
						<td class="tab_first_col" width="200">Менеджер:&nbsp;</td>
						<td align="left"><?=$name_user?></td>
					</tr>
					<? } else
						unset($r);
					?>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td class="tab_first_col" width="200">Короткое название:&nbsp;<span class="err">*</span>&nbsp;</td>
						<td><input name="short" id="short" type=text value="<?=htmlspecialchars(@$r['short'])?>" size=70></td>
					</tr>
					<tr>
						<td class="tab_first_col">Полное юр. наименование:&nbsp;<span class="err">*</span>&nbsp;</td>
						<td><input name="name" id="name" value="<?=htmlspecialchars(@$r['name'])?>" type=text size=70></td>
					</tr>
					<tr>
						<td class="tab_first_col">Юридический адрес:</td>
						<td><input name="leg_add" value="<?=htmlspecialchars(@$r['legal_address'])?>" type=text size=70></td>
					</tr>
					<tr>
						<td class="tab_first_col">Фактический/почтовый адрес:</td>
						<td><input name="post_add" value="<?=htmlspecialchars(@$r['postal_address'])?>" type=text size=70></td>
					</tr>
					<tr>
						<td class="tab_first_col">ИНН:</td>
						<td><input name="inn" value="<?=@$r['inn']?>" type=text size=70></td>
					</tr>
					<tr>
						<td class="tab_first_col">КПП:</td>
						<td><input name="kpp" value="<?=@$r['kpp']?>" type=text size=70></td>
					</tr>
					<tr>
						<td class="tab_first_col">р/с:</td>
						<td><input name="rs_acc" value="<?=@$r['rs_acc']?>" type=text size=70></td>
					</tr>
					<tr>
						<td class="tab_first_col">Открыт в банке:</td>
						<td><input name="bank" value="<?=htmlspecialchars(@$r['bank'])?>" type=text size=70></td>
					</tr>
					<tr>
						<td class="tab_first_col">БИК:</td>
						<td><input name="bik" value="<?=@$r['bik']?>" type=text size=70></td>
					</tr>
					<tr>
						<td class="tab_first_col">корр/с:</td>
						<td><input name="korr_acc" value="<?=@$r['korr_acc']?>" type=text size=70></td>
					</tr>
					<tr>
						<td class="tab_first_col">ОГРН:</td>
						<td><input name="ogrn" value="<?=@$r['ogrn']?>" type=text size=70></td>
					</tr>
					<tr>
						<td class="tab_first_col">Договор №:</td>
						<td align="left"><input name="dogov_num" value="<?=@$r['dogov_num']?>" type=text size=30></td>
					</tr>
					<tr>
						<td class="tab_first_col">Телефон организации:</td>
						<td><input name="firm_tel" value="<?=@$r['firm_tel']?>" type=text size=70></td>
					</tr>
					<tr>
						<td class="tab_first_col">Генеральный директор:&nbsp;<span class="err">*</span>&nbsp;</td>
						<td><input id="gen_dir" name="gen_dir" value="<?=htmlspecialchars(@$r['gen_dir'])?>" type=text size=70></td>
					</tr>
					<tr>
						<td class="tab_first_col">E-Mail организации:</td>
						<td><input name="email" value="<?=htmlspecialchars(@$r['email'])?>" type=text size=70></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td class="tab_first_col">Контактное лицо:</td>
						<td><input name="cont_pers" value="<?=@$r['cont_pers']?>" type=text size=70></td>
					</tr>
					<tr>
						<td class="tab_first_col">Телефон контактного лица:</td>
						<td><input name="cont_tel" value="<?=@$r['cont_tel']?>" type=text size=70></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td align="right"><input name="butt_send" type="submit" value="Сохранить" onclick="return check();" ></td>
					</tr>
					</form>
				</table>



				<? } else { ?>
		<table border="0" cellspacing="2" cellpadding="3">
			<form name="f_act" action="?sel_us=<?=$sel_us?>" method="post">
			<tr>
				<td><img src="/i/pix.gif" width="20" height="1"></td>
				<td><img src="/i/pix.gif" width="200" height="1"></td>
				<td><img src="/i/pix.gif" width="300" height="1"></td>
				<td><img src="/i/pix.gif" width="1" height="1"></td>
			</tr>
			<?

			$query = "SELECT uid,surname FROM users WHERE 1=1 ORDER BY surname";
			$res_usr = mysql_query($query);
			$arr_users = array();		// массив пользователей

			while($r_usr = mysql_fetch_array($res_usr))
				$arr_users[] = array($r_usr['uid'],$r_usr['surname']);

			?>
			<tr>
				<td colspan="3" align="center">
					<table align="center" cellpadding="0" cellspacing="0">
						<tr>
						<? if($tpacc) { ?>
							<td>
								Пользователь

								<select size="1" name="Name" onchange="document.location='?sel_us='+this.value;">
									<option value="0" >Все...</option>
									<? foreach($arr_users as $t) { ?>
								 		 <option value="<?=$t[0]?>" <?=(( $sel_us==$t[0] )?'selected="selected"':'')?>><?=$t[1]?></option>
								 	<? } ?>
								</select>
							</td>
							<td width="20">&nbsp;</td>
							<? if(!$tp_list) { ?>
								<td><a href="?cl_dog=1&sel_us=<?=$sel_us?>" onmouseover="Tip('сбросить счетчик')">Счетчик договоров:&nbsp;<?=$nums_ht_dog?></a></td>
							<? }

						 } elseif(!$tp_list) { ?>
								<td>Счетчик договоров:&nbsp;<?=$nums_ht_dog?></td>
						<? } ?>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td span="3">&nbsp;</td>
			</tr>

			<tr class="tab_query_tit">
				<td class="tab_query_tit">&nbsp;</td>
				<td class="tab_query_tit">Короткое название</td>
				<td class="tab_query_tit">Полное юр. наименование</td>
				<td class="tab_query_tit">Опер.</td>
			</tr>

			<?
			if( $sel_us == 0 )	// список для всех пользователей
				$query = "SELECT * FROM clients WHERE del=".$tp_list." ORDER BY user_id,short";
			else				// список клиентов текущего пользователя
				$query = "SELECT * FROM clients WHERE user_id=".$sel_us." AND del=".$tp_list." ORDER BY short";

			$res = mysql_query($query);
			$curr_us2 = '';

			while($r = mysql_fetch_array($res)) {
					if( $sel_us == 0 ) {
						$query = "SELECT surname,name,father FROM users WHERE uid=".$r['user_id'];
						$res2 = mysql_query($query);
						if($r2 = mysql_fetch_array($res2))
							$curr_us = @$r2['surname'].' '.@$r2['name'].' '.@$r2['father'];
						else
							$curr_us = 'не оперделен';
					}




					if($tp_list) {	// в архиве показать кнопку восстановить

						$butt = '<a onclick="rest_one('.$r['uid'].','.$sel_us.')" href="#" onmouseover="Tip(\'Восстановить\')">';
						$butt .= '<img widt="20" height="20" src="../i/za.gif" />';
						$butt .= '</a>&nbsp;';

					} else {

						if(!trim($r['dogov_num'])) {	// если номер договора не заполнен

							$js_nm_dog = '';
							$lnk_nm_dog = 'form_dog.php?dog='.$r['uid'].'&fl=1&urlico=kpf';	// установить автоматически


						} else {		// если заполнен, спросить

							$js_nm_dog = ' onclick="conf_auto_nm('.$r['uid'].');return false;" ';
							$lnk_nm_dog = "#";

						}

						$butt = '<input type=radio name=urlico_'.$r['uid'].' value=kpf id=kpf_'.$r['uid'].'><label for="kpf_'.$r['uid'].'"> КПФ</label> <input type=radio onchange=set_urlico(\''.$r['uid'].'\', \'cmd\') name=urlico_'.$r['uid'].' value=cmd id=cmd_'.$r['uid'].'><label for="cmd_'.$r['uid'].'"> КМД</label> <a href="'.$lnk_nm_dog.'" '.$js_nm_dog.' id="link_'.$r['uid'].'" onmouseover="Tip(\'Сформировать договор\')">';
						$butt .= '<img widt="20" height="20" src="../i/za.gif" />';
						$butt .= '</a>&nbsp;';

						$butt .= '<a href="#" onclick="show_specif('.$r['uid'].');return false;" onmouseover="Tip(\'Сформировать спецификацию\')">';
						$butt .= '<img widt="20" height="20" src="../i/za.gif" />';
						$butt .= '</a>&nbsp;';
					}

					$butt .= '<a href="?edit='.$r['uid'].'&sel_us='.$sel_us.'" onmouseover="Tip(\'Редактировать\')">';
					$butt .= '<img widt="20" height="20" src="../i/edit2.gif" />';
					$butt .= '</a>&nbsp;';

					$butt .= '<a onclick="del_cl('.$r['uid'].','.$sel_us.','.$tp_list.');return false;" href="#" onmouseover="Tip(\''.(($tp_list)?'Удалить':'В архив').'\')">';
					$butt .= '<img widt="20" height="20" src="../i/del.gif" />';
					$butt .= '</a>&nbsp;';


			if( ($sel_us == 0) && ($curr_us2 != $curr_us) ) {
				$curr_us2 = $curr_us;
			?>
			<tr>
				<td colspan="4" class="td_tit_us_clients">
					Менеджер: <strong><?=$curr_us?></strong>
				</td>
			</tr>
			<? } ?>
			<tr>
				<td class="tab_td_norm">
					<input name="sel_str[]" type="checkbox" value="<?=$r['uid']?>">
				</td>
      	<?
					$sh_name_alt = @$r['short'];
					$sh_name = (strlen($sh_name_alt)>25) ? substr($sh_name_alt,0,25).'...' : $sh_name_alt;
      	?>
				<td align="left" class="tab_td_norm" onmouseover="Tip('<?=htmlspecialchars($sh_name_alt)?>')" style="padding-left:20px;">
					<a href="?edit=<?=$r['uid']?>&sel_us=<?=$sel_us?>"><?=$sh_name?></a>
				</td>
        <?
					$full_name_alt = @$r['name'];
					$full_name = (strlen($full_name_alt)>45) ? substr($full_name_alt,0,45).'...' : $full_name_alt;

        ?>
				<td align="left" class="tab_td_norm" style="padding-left:20px;" onmouseover="Tip('<?=htmlspecialchars($full_name_alt)?>')">
						<?=$full_name?>
         		</td>
				<td align="center" class="tab_td_norm"><?=$butt?></td>
			</tr>

			<? } ?>
			<tr>
				<td colspan="4">
					<? if( $tpacc && !$tp_list) {
						if( $sel_us == 0 ) { ?>
					<input type="submit" name="set_me" value="Распределить клиентов из общего списка"><br /><br />
						<? } ?>
					<select size="1" name="sel_cl_copy">
						<? foreach($arr_users as $a) { ?>
					 	 <option value="<?=$a[0]?>"><?=$a[1]?></option>
					 	<? } ?>
					</select>&nbsp;

					<input type="submit" name="copy_to" onclick="return run_type_act(1);" value="Копировать пользователю"> <input type="submit"  onclick="return run_type_act(2);" name="move_to" value="Переместить пользователю"><br /><br />
					<? }

					if($tp_list) { ?>
						<input onclick="return rest_vyb();" type="submit" name="rest_sel" value="Восстановить выбранные">&nbsp;
						<input onclick="return del_vyb(1);" type="submit" name="del_sel" value="Удалить выбранные">

					<? } else { ?>
						<input onclick="return del_vyb(0);" type="submit" name="del_sel" value="В архив выбранные">
					<? } ?>
				</td>
			</tr>
			</form>
		</table>


		<? } }?></td>
</tr>
<tr>
  <td align="center" height="50">&nbsp;</td>
</tr>
</table>
</td>
</tr>
</table>


<!-- ******************** СЛОЙ ВЫБОРА ТИПА СПЕЦИФИКАЦИИ  <<<< *****************  //-->


<div onMouseUp="end_drag()" onMouseMove="dragIt(this,event)" id="div_spec" style="background-color:#FFFFFF; position:absolute; top:300px; left:600px; width:220px; padding:10px; border:1px #0099CC solid; display:none;">
<table border="0" cellspacing="0" cellpadding="0" align="center" width="200">

	<tr>
		<td style="cursor:move;" onMouseDown="start_drag(document.getElementById('div_spec'),event)" align="center" valign="top"><strong><div id="div_fltr_num_tit">Выберите тип спецификации</div></strong></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<form name="ff_fltr_num" action="form_spec.php"  method="post">
	<input id="id_dog" name="dog" type="hidden" value="0">
	<tr>
	  	<td align="left">
	  		<input name="rad_tp_spec" id="rad_sp1" type="radio" value="1" checked="checked">&nbsp;<a href="#" onclick="document.getElementById('rad_sp1').checked=true;return false;">бумажные пакеты</a><br />
	  		<input name="rad_tp_spec" id="rad_sp2" type="radio" value="2" >&nbsp;<a href="#" onclick="document.getElementById('rad_sp2').checked=true;return false;">полиэтиленовые пакеты</a><br />
	  		<input name="rad_tp_spec" id="rad_sp3" type="radio" value="3" >&nbsp;<a href="#" onclick="document.getElementById('rad_sp3').checked=true;return false;">растительная продукция</a>
	  	</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center">
			<input class="frm_podr_opl_butt" name="" type="submit" value="ОК" onclick="document.getElementById('div_spec').style.display = 'none';" /><input name="" class="frm_podr_opl_butt" type="button" value="Отмена" onclick="document.getElementById('div_spec').style.display = 'none';return false;" />
		</td>
	</tr>
	</form>
</table>
</div>



<!--  >>>>>>******************** СЛОЙ ВЫБОРА ТИПА СПЕЦИФИКАЦИИ  *****************  //-->



</body>
</html>
