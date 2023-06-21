<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом 
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1 
header("Pragma: no-cache"); // HTTP/1.1 
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

$auth = false;

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");

if(!$auth) {
	header("Location: /");
	exit;
}

$op = 'new';		// по умолч тип операции новый запрос

// запрос документа через заявку на счет
if(isset($_GET['doc']) && is_numeric($_GET['doc'])) {
	$op = 'doc';
		$query = sprintf("SELECT a.ready, b.client_id,b.acc_number,c.short FROM queries as a,data_queries as b, clients as c WHERE a.uid=%d AND a.query_id=b.uid AND b.client_id=c.uid", $_GET['doc']);
		$res_qr = mysql_query($query);
		$r_qr = mysql_fetch_array($res_qr);
}

// -------------- ДОБАВЛЕНИЕ, РЕДАКТИРОВАНИЕ -----------------
if(isset($_POST['butt_send'])) {
/*	if($_POST['client_list'] == 0) {
		$query = "INSERT INTO clients(name) VALUES('".$_POST['client']."')";
		mysql_query($query);
		$client = mysql_insert_id();
	}
	else */
	$client = $_POST['client_list'];
	$acc_num_f = trim(@$_POST['num_acc']);	
	if( ($acc_num_f == 'нет') || ($acc_num_f == 'no') || ($acc_num_f == '-') )
		$acc_num_f = 'none';
		
	if($_POST['doc_id'] != 0) {		// запрос через заявку на счет
		$query = "UPDATE queries SET date_ready=NOW(),ready='1' WHERE uid=".$_POST['doc_id'];
		mysql_query($query);

		$query  = "SELECT query_id FROM queries WHERE uid=".$_POST['doc_id'];
		$res = mysql_query($query);
		$r = mysql_fetch_array($res);

		$query = sprintf("UPDATE data_queries SET client_id=%d,sub_acc='%s',acc_number='%s',note='%s' WHERE uid=%d", $client, $_POST['kakie'], $acc_num_f, $_POST['comment'], $r['query_id']);
		mysql_query($query);
		$query = sprintf("INSERT INTO queries(query_id,type,user_id,date_query) VALUES(%d,'1',%d, NOW())", $r['query_id'], $user_id);
		mysql_query($query);
	}
	// редактирование запроса на документ
	elseif($_POST['edit_id'] != 0) {
		$query = "UPDATE queries SET date_query=NOW() WHERE uid=".$_POST['edit_id'];
		mysql_query($query);
		$query  = "SELECT query_id FROM queries WHERE uid=".$_POST['edit_id'];
		$res = mysql_query($query);
		$r = mysql_fetch_array($res);
		$query = sprintf("UPDATE data_queries SET client_id=%d,sub_acc='%s',note='%s',acc_number='%s' WHERE uid=%d", $client, $_POST['kakie'], $_POST['comment'], $acc_num_f, $r['query_id']);
		mysql_query($query);
	}
	// добавление нового запроса на документ
	else {
		$query = sprintf("INSERT INTO data_queries(client_id,sub_acc,acc_number,note) VALUES('%s','%s','%s','%s')", $client, $_POST['kakie'], $acc_num_f, $_POST['comment']);
		mysql_query($query);
		$new_id = mysql_insert_id();
		//$ready_stat = (trim($_POST['num_acc'])) ? 1 : 0;
		$query = sprintf("INSERT INTO queries(query_id,type,user_id,date_query) VALUES(%d,'1',%d, NOW())", $new_id, $user_id);
		mysql_query($query);
	}
		header("Location: query_list.php");
}


$query = "SELECT * FROM users WHERE uid=".$user_id;
$res = mysql_query($query);
$r = mysql_fetch_array($res);
$full_name = @$r['surname'].' '.@$r['name'].' '.@$r['father'];

// --------------Если открыто в режиме редактирования- просмотра--------------	
	if(isset($_GET['show']) && is_numeric($_GET['show'])) {
		$op = 'edit';
		$query = sprintf("SELECT a.user_id,a.ready, b.client_id,b.acc_number,b.sub_acc,b.note,c.short FROM queries as a,data_queries as b, clients as c WHERE a.uid=%d AND a.query_id=b.uid AND b.client_id=c.uid", $_GET['show']);
		$res_qr = mysql_query($query);
		@$r_qr = mysql_fetch_array($res_qr);
		$ed_us_id = $r_qr['user_id'];
		
		$query = "SELECT * FROM users WHERE uid=".$ed_us_id;
		$res = mysql_query($query);
		$r = mysql_fetch_array($res);
		$full_name = @$r['surname'].' '.@$r['name'].' '.@$r['father'];
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
<script src="../includes/lib/JsHttpRequest/JsHttpRequest.js"></script>
<script language="JavaScript" type="text/javascript">
<!--

// ---- Функция динамической подгрузки названий клиентов и реквизитов ----
//------------------------------------------------------------------------
function doLoad(obj,fn,accyes) {
    var req = new JsHttpRequest();
    req.onreadystatechange = function() {
        if (req.readyState == 4) {
						uid = req.responseJS.uid;				// ид клиента
						short = req.responseJS.short;		// короткое название
						nam = req.responseJS.name;			// полное название
						reqv = req.responseJS.reqv;			// реквизиты
						numm = req.responseJS.num;			// 1 - если полное совпадение кор. назв.
																						// и назв. из списка.
						acc = req.responseJS.acc;				// номер счета										
						fn = req.responseJS.fn;					// 0 - ввод короткого назв. 1 - выбор из списка
						
						if(short == null)
							short = '';
						if(acc == null)
							acc = '';
						if(acc == 'none')
							acc = 'нет';	
							
						// если длина кор. назв. меньше 1 или выбран другой, установить 0- другой
						if((obj.value.length < 1) || (uid == null))	
							uid = 0;
							
						if(uid == 0) {		// выбран новый клиент
							nam = '';
							reqv = '';
							acc = '';
						}	
						// если вводится короткое название
						if(fn == 0) {
							num =document.f_send.client_list.options.length;
							for(i=0; i<num; i++) {
								if(document.f_send.client_list.options[i].value == uid)
									document.f_send.client_list.selectedIndex = i;
							}
							dis = (numm==0) ? false : true;		// если полное совпадение короткого названия и названия в списке - отключить поле полного названия клиента
						}
						else {
							dis = (uid ==0) ? false : true;
							if(uid == 0) 
								short = '';

							document.f_send.client_short.value = short;
						}
						document.f_send.num_acc.value = acc;
//						document.f_send.req.value = reqv;
//						document.f_send.client.disabled=dis;
//						document.f_send.client.value = nam;
					}
    }
    req.open(null, 'back_LoadReqClient.php', true);
    req.send( { str: obj.value, fn: fn, accyes: accyes } );
}

function check() {
	var obj = document.f_send;

	if(obj.client_list.value == 0){
		alert("Выберите клиента из списка");
		obj.client_list.focus();
		return false;
	}
	obj.num_acc.value = replace_acc(obj.num_acc.value);
	
	if(obj.num_acc.value==""){
		alert("Введите номер счета");
		obj.num_acc.focus();
		return false;
	}
}

// --------------------- ФОРМАТИРОВАНИЕ НОМЕРА СЧЕТА --------------------- 
function replace_num_acc(v) {
	var reg_sp = /[^\d]*/g;		// вырезание всех символов кроме цифр
	v = v.replace(reg_sp, '');
	var reg_sp = /^0*$/g;						// если в строке только одни нули, стереть
	v = v.replace(reg_sp, '0');
	
	return v;
}

function replace_acc(val) {
	if((val == 'нет') || (val == 'no') || (val == '-'))
		return 'нет';
	else 
		return replace_num_acc(val);
}
//-->
</script>

<body>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>
<table align=center width=750 border=0>

<tr>
<td align=center width="270" height="62">
<a href="http://printfolio.ru"><img src="/i/pf.gif" alt="" width="270" height="62" border="0"></a>
</td>
<td align=center width="190"><a href="http://comcad.ru"><img src="/i/cm.gif" alt="" width="180" height="51" border="0"></a></td>
<td align=center width="200"><? if($auth) require_once("../includes/auth_form.php"); ?></td>
</tr>

<tr>
<td colspan=3>
<br>
<table align=center border=0 cellpadding="0" cellspacing="0">
<tr>
<td background="/i/bgr.jpg" align=center width="122"><a class="menu_act" href="/">Общие</a></td>
<td background="/i/bg.jpg" align=center width="122">
	<a class="menu_act" href="query_list.php">Документы</a>
</td>
<? if(@$auth && ($user_type == 'adm')) {?>
<td background="/i/bgr.jpg" align=center width="122">
	<a class="menu_act" href="users.php">Пользователи</a>
</td>
<? } ?>
<td align=center width="50">
	<a class="menu_act" title="Переключиться на новую версию" href="/acc/query/query_list.php"><img alt="Переключиться на новую версию" src="/i/strel2.gif" /></a>
</td>

</tr>
</table>
<table width=100% border=0 cellpadding="5" cellspacing="0" bgcolor="#F6F6F6">
	<tr>
		<td valign="top" align="center">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="150">
						<span class="sublink_pl">+</span> <a href="query_send.php" class="sublink">запросить счет</a>
					</td>
					<td width="150">
						<span class="sublink_pl_off">+</span> <span class="sublink_off">запросить документы</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center"><h4>Запрос документа</h4></td>
	</tr>
	<tr>
		<td align="center">
			<? if($auth) {  ?>
			<table border="0" cellspacing="0" cellpadding="0" align="center">
			<form action="" method=post name="f_send">
				<input name="user_id" type="hidden" value="<?=$user?>" />
				<input name="edit_id" type="hidden" value="<?=($op == 'edit') ? $_GET['show'] : 0;?>" />
				<input name="doc_id" type="hidden" value="<?=($op == 'doc') ? $_GET['doc'] : 0;?>" />
				<tr>
					<td class="tab_first_col">Менеджер проекта:</td>
					<td align="left"><strong><?=$full_name?></strong></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td class="tab_first_col">Быстрый поиск:&nbsp;&nbsp;</td>
					<td class="tab_two_col">
						<input onKeyUp="doLoad(this,0,1)" onmouseover="Tip('Быстрый поиск по короткому названию клиента')" class="frm_wdfull" name="client_short" type=text value="<?=@$r_qr['short']?>" />
					</td>
				</tr>
				<tr>
					<td class="tab_first_col">Полное название клиента:&nbsp;<span class="err">*</span>&nbsp;</td>
					<td class="tab_two_col">
						<?
						$query = "SELECT * FROM clients ORDER BY name";
						$res_cl  = mysql_query($query);
						?>
						<select name="client_list"  onmouseover="Tip('Весь список полных названий клиентов')" size="4" class="frm_wdfull" onChange="doLoad(this,1,1);" onclick="doLoad(this,1);">
							<option value="0"<?=($op != 'edit') ? ' selected' : ''?> style="background-color:#E2E2E2;" >---- не выбрано ----</option>
							<? 
							$i=0;
							while($r_cl = mysql_fetch_array($res_cl)) { 
								$sel='';
								if(($op == 'edit') || ($op == 'doc')) {
										if($r_cl['uid'] == $r_qr['client_id'])
											$sel = ' selected';
								}
							?>
							<option value="<?=$r_cl['uid']?>"<?=$sel?>><?=$r_cl['name']?></option>
							<? $i++; } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<?
						$def_acc_num = (@$r_qr['acc_number'] == 'none') ? 'нет' : @$r_qr['acc_number'];
					?>
					<td class="tab_first_col">Номер счета:&nbsp;<span class="err">*</span>&nbsp;</td>
					<td class="tab_two_col"><input  onmouseover="Tip('Только цифры.')" name=num_acc type=text value="<?=$def_acc_num?>" onblur="this.value = replace_acc(this.value,1)" class="frm_wdfull"></td>
				</tr>
				<tr>
					<td class="tab_first_col">Какие необходимы документы:&nbsp;<span class="err">*</span>&nbsp;</td>
					<td class="tab_two_col">
						<textarea class="frm_wdfull" rows="3" name="kakie"><?=($op == 'edit') ? @$r_qr['sub_acc'] : 'Акт (накладная), Счет фактура'?></textarea>
					</td>
				</tr>
				<tr>
					<td class="tab_first_col">Комментарии:</td>
					<td class="tab_two_col"><textarea class="frm_wdfull" rows="3" name="comment"><?=@$r_qr['note']?></textarea></td>
				</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
				<tr>
					<td align="right" colspan="2">
					<? if((($op == 'edit') && (!@$r_qr['ready'])) || ($op == 'new') || ($op == 'doc')) { ?>
					<input name="butt_send" type=submit value="Отправить !" onclick="return check();" class="frm_butt_send">
					<? } ?>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<br><br>
						<a href="#" onclick="history.back()"><font face=tahoma><strong>назад</strong></font></a>
						<br><br>
					</td>
				</tr>
				</form>
			</table>
	
			<? } ?>	
		</td>
	</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>
