<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //���� � ������� 
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1 
header("Pragma: no-cache"); // HTTP/1.1 
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

$auth = false;

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");
require_once("../includes/hint.inc.php");

if(!$auth) {
	header("Location: /");
	exit;
}

$pereadr = '';

$op = 'new';		// �� ����� ��� �������� ����� ������

// #########################  ��������� ���������  #############################
if(isset($_POST['butt_send'])) {
		if($_POST['client_list'] == 0) {		// � ������ �������� ������� "������"
			$query = sprintf("INSERT INTO clients(name,short,req) VALUES('%s','%s','%s')",$_POST['client'], $_POST['client_short'], $_POST['req']);
			mysql_query($query);
			$client = mysql_insert_id();		// �� ������ �������
		}
		else 			// �� ������� ������� �� ������
			$client = $_POST['client_list'];
			
// ###############  ��������� �����  ###############
	if($_POST['edit_id'] != 0) {	
	
		$query = "UPDATE queries SET date_query=NOW() WHERE uid=".$_POST['edit_id'];
		mysql_query($query);
		$query  = "SELECT query_id FROM queries WHERE uid=".$_POST['edit_id'];
		$res = mysql_query($query);
		$r = mysql_fetch_array($res);
		
		if($user_type == 'mng') {		// ��������
		
			$query = sprintf("UPDATE data_queries SET client_id=%d,req='%s',sub_acc='%s',amount_acc='%s', contractors='%s', cont_pers='%s', cont_tel='%s', total_cost='%s',note='%s' WHERE uid=%d", $client, $_POST['req'], $_POST['predmet'], $_POST['sum'], $_POST['supl'], $_POST['cont_pers'], $_POST['cont_tel'], $_POST['cost'], $_POST['adition'], $r['query_id']);
		}

		elseif(($user_type == 'acc') || ($user_type == 'adm')) { // ��������� ��� �����
			$acc_num = trim($_POST['acc_number']);
			if($acc_num) {
				$query = "UPDATE queries SET date_ready=NOW(), ready='1' WHERE uid=".$_POST['edit_id'];
				mysql_query($query);
			}
			else {
				$query = "UPDATE queries SET ready='0' WHERE uid=".$_POST['edit_id'];
				mysql_query($query);
			}
			if( ($acc_num == '���') || ($acc_num == 'no') || ($acc_num == '-') )
				$acc_num = 'none';

			$query = sprintf("UPDATE data_queries SET client_id=%d,req='%s',sub_acc='%s',amount_acc='%s', contractors='%s',cont_pers='%s', cont_tel='%s',total_cost='%s',note='%s',acc_number='%s' WHERE uid=%d", $client, $_POST['req'], $_POST['predmet'], $_POST['sum'], $_POST['supl'], $_POST['cont_pers'], $_POST['cont_tel'], $_POST['cost'], $_POST['adition'], $acc_num, $r['query_id']);
		}
		mysql_query($query);
	}
	
// ##############  ������������ �����  ###############
	else {		
		$acc_num = trim(@$_POST['acc_number']);
		if( ($acc_num == '���') || ($acc_num == 'no') || ($acc_num == '-') )
			$acc_num = 'none';

			$query = sprintf("INSERT INTO data_queries(client_id,req,sub_acc,amount_acc,contractors,cont_pers,cont_tel,total_cost,note) VALUES(%d,'%s','%s','%s','%s','%s','%s','%s','%s')", $client, $_POST['req'], $_POST['predmet'], $_POST['sum'], $_POST['supl'], @$_POST['cont_pers'], @$_POST['cont_tel'], $_POST['cost'], @$_POST['adition']);
		if(($user_type == 'acc') || ($user_type == 'adm')) {
			if(trim($_POST['acc_number'])) {
				$query = sprintf("INSERT INTO data_queries(client_id,req,sub_acc,amount_acc,contractors,cont_pers,cont_tel,total_cost,note,acc_number) VALUES(%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s')", $client, $_POST['req'], $_POST['predmet'], $_POST['sum'], $_POST['supl'], @$_POST['cont_pers'],@$_POST['cont_tel'], $_POST['cost'], @$_POST['adition'], $acc_num);
			}
		}		
		
		mysql_query($query);
		
		$new_id = mysql_insert_id();
		$query = sprintf("INSERT INTO queries(query_id,user_id,date_query) VALUES(%d,%d, NOW())", $new_id, $user_id);
		if(($user_type == 'acc') || ($user_type == 'adm')) {
			if(trim($_POST['acc_number'])) {
				$query = sprintf("INSERT INTO queries(query_id,user_id,date_query,date_ready,ready) VALUES(%d,%d, NOW(), NOW(), '1')", $new_id, $user_id);
			}
		}
		mysql_query($query);
		
//		header("Location: query_list.php");
//		exit;

		$acc_num = ( $acc_num == 'none' ) ? '���' : $acc_num;

		// ������ ������� �������� ������ � ����� �������� �����
		//----------------------------------------------------------------------------
		$query = "SELECT name,short FROM clients WHERE uid=".$client;
		$res =mysql_query($query);
		$r = mysql_fetch_array($res);
		$client_name = $r['name'];
		$short_name = $r['short'];
		
		$query = "SELECT surname,name,father FROM users WHERE uid=".$user_id;
		$res = mysql_query($query);
		$r = mysql_fetch_array($res);
		$full_name = $r['surname'].' '.$r['name'].' '.$r['father'];
		
		$bod = '<html><body onLoad="document.fff.submit();">'."\r\n";
		$bod .= '<form name="fff" action="http://printfolio.ru/query/send_mail_cmd.php" method="post">'."\r\n";
//		$bod .= '<form name="fff" action="send_mail_cmd.php" method="post">'."\r\n";
		$bod .= '<input name="user_id" type="hidden" value="'.$_POST['user_id'].'" />'."\r\n";
		$bod .= '<input name="client_name" type="hidden" value="'.htmlspecialchars($client_name,ENT_QUOTES).'" />'."\r\n";
		$bod .= '<input name="short_name" type="hidden" value="'.htmlspecialchars($short_name,ENT_QUOTES).'" />'."\r\n";
		$bod .= '<input name="full_name" type="hidden" value="'.htmlspecialchars($full_name,ENT_QUOTES).'" />'."\r\n";
		$bod .= '<input name="req" type="hidden" value="'.htmlspecialchars($_POST['req'],ENT_QUOTES).'" />'."\r\n";
		$bod .= '<input name="predmet" type="hidden" value="'.htmlspecialchars($_POST['predmet'],ENT_QUOTES).'" />'."\r\n";
		if(isset($_POST['acc_number'])) 
			$bod .= '<input name="acc_number" type="hidden" value="'.htmlspecialchars($acc_num,ENT_QUOTES).'" />'."\r\n";
		$bod .= '<input name="sum" type="hidden" value="'.htmlspecialchars($_POST['sum'],ENT_QUOTES).'" />'."\r\n";
		$bod .= '<input name="supl" type="hidden" value="'.htmlspecialchars($_POST['supl'],ENT_QUOTES).'" />'."\r\n";
		$bod .= '<input name="cost" type="hidden" value="'.htmlspecialchars($_POST['cost'],ENT_QUOTES).'" />'."\r\n";
		if(isset($_POST['cont_pers']))
			$bod .= '<input name="cont_pers" type="hidden" value="'.htmlspecialchars($_POST['cont_pers'],ENT_QUOTES).'" />'."\r\n";
		if(isset($_POST['cont_tel']))
			$bod .= '<input name="cont_tel" type="hidden" value="'.htmlspecialchars($_POST['cont_tel'],ENT_QUOTES).'" />'."\r\n";
		if(isset($_POST['adition']))	
			$bod .= '<input name="adition" type="hidden" value="'.htmlspecialchars($_POST['adition'],ENT_QUOTES).'" />'."\r\n";
		$bod .= '<input name="back_adr" type="hidden" value="http://'.$_SERVER['HTTP_HOST'].'/old/query/query_list.php" />'."\r\n";

		$query = "SELECT * FROM mail ORDER BY uid";
		$res = mysql_query($query);
		while($r = mysql_fetch_array($res)) { 
			$bod .= '<input name="mail_arr[]" type="hidden" value="'.htmlspecialchars($r['email'],ENT_QUOTES).'" />'."\r\n";
		}
		$bod .= '</form></body></html>'."\r\n";
		echo $bod;
		$pereadr = 'add';
	}
	if(!$pereadr)
		header("Location: query_list.php");
}

if(!$pereadr) {
// --------------������ ������ ������������ ----------------------------------
	$query = "SELECT * FROM users WHERE uid=".$user_id;
	$res = mysql_query($query);
	$r = mysql_fetch_array($res);
	$full_name = @$r['surname'].' '.@$r['name'].' '.@$r['father'];

// --------------���� ������� � ������ ��������������- ���������--------------	
	if(isset($_GET['show']) && is_numeric($_GET['show'])) {
		$op = 'edit';
		$query = sprintf("SELECT a.user_id,a.ready, b.client_id,b.sub_acc,b.req,b.amount_acc,b.contractors,b.cont_pers,b.cont_tel,b.total_cost,b.note,b.acc_number,c.name,c.short FROM queries as a,data_queries as b, clients as c WHERE a.uid=%d AND a.query_id=b.uid AND b.client_id=c.uid", $_GET['show']);
		$res_qr = mysql_query($query);
		$r_qr = mysql_fetch_array($res_qr);
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



// �������� ������������ ����� �� ������ ��������
//---------------------------------------------------------------------
function check(){
	var obj = document.f_send;
	
	if(obj.client_list.value==0) {
		if(obj.client_short.value==""){
			alert("������� �������� �������� �������");
			obj.client_short.focus();
			return false;
		}
		if(obj.client.value==""){
			alert("������� ������ �������� �������");
			obj.client.focus();
			return false;
		}
	}
	if(obj.req.value==""){
		alert("������� ��������� �������");
		obj.req.focus();
		return false;
	}
  if(obj.predmet.value==""){
		alert("������� ������� ������������� �����");
		obj.predmet.focus();
		return false;
	}
	obj.sum.value = replace_price(obj.sum.value);		// �������������� ����
	
  if(obj.sum.value==""){
		alert("������� ����� �����");
		obj.sum.focus();
		return false;
	}
	
  if(obj.supl.value==""){
		alert("����������� �����������, �� ������, ��������� ����� � ������ ������");
		obj.supl.focus();
		return false;
	}
	
	obj.cost.value = replace_price(obj.cost.value);	// �������������� ����
	
  if(obj.cost.value==""){
		alert("������� ����� �������������");
		obj.cost.focus();
		return false;
	}
	
  if(obj.cont_pers.value==""){
		alert("������� ���������� ����");
		obj.cont_pers.focus();
		return false;
	}
  if(obj.cont_tel.value==""){
		alert("������� ���������� �������");
		obj.cont_tel.focus();
		return false;
	}
	obj.cont_tel.value = replace_tel(obj.cont_tel.value); // �������������� ��������
	obj.acc_number.value = replace_acc(obj.acc_number.value);	// �������������� ������ �����
}

// --------------------- �������������� ���� --------------------- 
function replace_price(v) {
	for(i=0;i<3;i++) {
		var reg_sp = /[^\d,\.]*/g;		// ��������� ���� �������� ����� ����, ������� � �����
		v = v.replace(reg_sp, '');
		var reg_sp = /\.|,{2,}|\.{2,}|,\.|\.,/g; 	// ��������� ������ ������ ������� � �����
		v = v.replace(reg_sp, ',');
		var reg_sp = /^,|^\./g;				// ���� ������ ������ ����� ��� �������, �������� �� '0,'
		v = v.replace(reg_sp, '0,');
	}
	var reg_sp = /^0*$/g;						// ���� � ������ ������ ���� ����, �������
	v = v.replace(reg_sp, '0');
	
	for(i=0;i<20;i++) {
		var reg_sp = /(\s*(\d{3})\b)/g;			// �������������� ��������� �� 3 �����
		v = v.replace(reg_sp, " $2");
	}
	var reg_sp = /^\s|,$/g;						// ������ ����� ������ ������
	v = v.replace(reg_sp, "");
	var reg_sp = /,(\s)/g;					// ������ ������� ����� �������
	v = v.replace(reg_sp, ",");
	var reg_sp = /^0,$/g;						// ������� ��� ���� � ��������� ������ '0,'
	v = v.replace(reg_sp, "");
	
	return v;
}

// ---------- �������������� ������ �����, ���� � ��� ������� '���' ---------- 
function replace_acc(val) {
	if((val == '���') || (val == 'no') || (val == '-'))
		return '���';
	else 
		return replace_num_acc(val);
}

// --------------------- �������������� ������ ����� --------------------- 
function replace_num_acc(v) {
	var reg_sp = /[^\d]*/g;		// ��������� ���� �������� ����� ����
	v = v.replace(reg_sp, '');
	var reg_sp = /^0*$/g;						// ���� � ������ ������ ���� ����, �������
	v = v.replace(reg_sp, '');
	
	return v;
}

// --------------------- �������������� ������ �������� --------------------- 
function replace_tel(v) {
	var reg_sp = /[^\d\(\)]*/g;		// ��������� ���� �������� ����� ���� � ������
	v = v.replace(reg_sp, '');
	for(i=0;i<5;i++) {
		var reg_sp = /\({2,}/g;				// ������ ������� ����� ������
		v = v.replace(reg_sp, '(');		
		var reg_sp = /\){2,}/g;				// ������ ������� ������ ������
		v = v.replace(reg_sp, ')');
		var reg_sp = /\(\)/g;					// ������ ������ ������
		v = v.replace(reg_sp, '');
		var reg_sp = /\s*\(/g;				// ��������� ������ ����� ����� �������
		v = v.replace(reg_sp, ' (');
		var reg_sp = /\)\s*/g;				// ������ ����� ������ �������
		v = v.replace(reg_sp, ') ');
	}
	var reg_sp = /\s*\)\s*|\s*\(\s*|\s*/g;		// ��� ������ � �������
	num_cf = v.replace(reg_sp, '').length;		// ����� ������ ���� (��� ������ � ��������)
	
	if( num_cf == 11 )	{			// �������������� ��� ���������� ��������
		var reg_sp = /^(\d{1})\s*(\(?\d{3}\)?)\s*(\d{3})/g;		//
		v = v.replace(reg_sp, '$1 $2 $3 ');
	}
	else if( num_cf == 10 ) {	// ��� ������� �������� ��� 8
		var reg_sp = /^\s*(\(?\d{3}\)?)\s*(\d{3})/g;		//
		v = v.replace(reg_sp, '$1 $2 ');
	}
	
	var reg_sp = /^\s*/g;		// �������� ������� �������
	v = v.replace(reg_sp, '');

	return v;
}

//-->
</script>

<body onload="document.f_send.client_short.focus();">
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
<td background="/i/bgr.jpg" align=center width="122"><a href="/" class="menu_act">�����</a></td>
<td background="/i/bg.jpg" align=center width="122"><a href="query_list.php" class="menu_act">���������</a></td>
<? if(@$auth && ($user_type == 'adm')) {?>
<td background="/i/bgr.jpg" align=center width="122"><a href="users.php" class="menu_act">������������</a></td>
<? } ?>
<td align=center width="50">
	<a class="menu_act" title="������������� �� ����� ������" href="/acc/query/query_list.php"><img alt="������������� �� ����� ������" src="/i/strel2.gif" /></a>
</td>

</tr>
</table>
<table width=100% border=0 cellpadding="5" cellspacing="0" bgcolor="#F6F6F6">
	<tr>
		<td valign="top" align="center">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="150">
						<span class="sublink_pl_off">+</span> <span class="sublink_off">��������� ����</span>
					</td>
					<td width="150">
						<span class="sublink_pl">+</span> <a href="query_doc.php" class="sublink">��������� ���������</a>
					</td>
				</tr>
			</table>
</td>
</tr>
<tr>
	<td align="center"><h4>������ �����</h4></td>
</tr>
<tr>
	<td align="center">
		<? if($auth) {  ?>
		<table border="0" cellspacing="0" cellpadding="0" align="center">
			<form action="" method=post name="f_send">
			<input name="user_id" type="hidden" value="<?=$user?>" />
			<input name="edit_id" type="hidden" value="<?=($op == 'edit') ? $_GET['show'] : 0;?>" />
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td class="tab_first_col">�������� �������:&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="tab_two_col"><strong><?=$full_name?></strong></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td class="tab_first_col">�������� ��������:&nbsp;<span class="err">*</span>&nbsp;</td>
				<td class="tab_two_col">
					<input name="client_short" type=text class="frm_wdfull" onKeyUp="doLoad(this,0)" value="<?=@$r_qr['short']?>" maxlength="50" onmouseover="Tip('�������� �������� �������')" />
				</td>
			</tr>
			<tr>
				<td class="tab_first_col">������ ��������:&nbsp;&nbsp;</td>
				<td class="tab_two_col">
					<?
					$query = "SELECT * FROM clients ORDER BY name";
					$res_cl  = mysql_query($query);
					?>
					<select name="client_list" size="4" class="frm_wdfull" id="client_list" onChange="doLoad(this,1);" onclick="doLoad(this,1);" onmouseover="Tip('���� ������ �������� �������� ��������')"  >
						<option value="0"<?=($op != 'edit') ? ' selected' : ''?> style="background-color:#E2E2E2" >������...</option>
						<? 
						$i=0;
						while($r_cl = mysql_fetch_array($res_cl)) { 
							$sel='';
							if($op == 'edit') {
									if($r_cl['uid'] == $r_qr['client_id'])
										$sel = ' selected';
							}
						?>
						<option value="<?=$r_cl['uid']?>"<?=$sel?>><?=$r_cl['short']?></option>
						<? $i++; } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tab_first_col">������ ��������:&nbsp;<span class="err">*</span>&nbsp;</td>
				<td class="tab_two_col"><input onmouseover="Tip('������ �������� �������')" name=client type=text class="frm_wdfull" id="client" onkeyup="document.f_send.client_list.value = '0'" value="<?=htmlspecialchars(@$r_qr['name'])?>" maxlength="50" /></td>
			</tr>
			<tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>				<td class="tab_first_col">��. �����, ���, ���, �/�, �/�, ����:&nbsp;<span class="err">*</span>&nbsp;</td>
				<td class="tab_two_col"><textarea onmouseover="Tip('���������')" class="frm_wdfull" rows="5" name="req"><?=@$r_qr['req']?></textarea></td>
			</tr>
			<tr>
				<td class="tab_first_col">������� �����:&nbsp;<span class="err">*</span>&nbsp;</td>
				<td class="tab_two_col"><textarea class="frm_wdfull" rows="5" name="predmet"><?=@$r_qr['sub_acc']?></textarea></td>
			</tr>
			<tr>
				<td class="tab_first_col">����� �����:&nbsp;<span class="err">*</span>&nbsp;</td>
				<td class="tab_two_col">
					<table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td><input onmouseover="Tip('� ������, ������ ����� � �������.')" name=sum type=text class="frm_wdpol" value="<?=@$r_qr['amount_acc']?>" maxlength="50" onblur="this.value = replace_price(this.value)"></td>
							<td onmouseover="Tip('���������� ���')"><a href="#" onClick="javascript: window.open('/VAT/index.php?sum=' + document.f_send.sum.value,'','screenX=400,screenY=250,left=50,top=50,scrollbars=no,menubar=0,resizable=0,width=400,height=350'); return false;"><img src="/i/icons/rm_icon.gif"></a></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="tab_first_col">������������ �����������, <span class="err">*</span>&nbsp;&nbsp;<br />����� �����
				 ������ ��� ���������,&nbsp;&nbsp;&nbsp;&nbsp;<br />������ � ����� ������:&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td class="tab_two_col"><textarea class="frm_wdfull" rows="5" name="supl"><?=@$r_qr['contractors']?></textarea></td>
			</tr>
			<tr>
				<td class="tab_first_col">����� �������������, ������� ��������:&nbsp;<span class="err">*</span>&nbsp;</td>
				<td class="tab_two_col"><input onmouseover="Tip('� ������, ������ ����� � �������')" name=cost type=text class="frm_wdfull" value="<?=@$r_qr['total_cost']?>" onblur="this.value = replace_price(this.value)" maxlength="50"></td>
			</tr>
			<tr>
				<td class="tab_first_col">���������� ����:&nbsp;<span class="err">*</span>&nbsp;</td>
				<td class="tab_two_col"><input name=cont_pers type=text class="frm_wdfull" value="<?=@$r_qr['cont_pers']?>" maxlength="50"></td>
			</tr>
			<tr>
				<td class="tab_first_col">���������� �������:&nbsp;<span class="err">*</span>&nbsp;</td>
				<td class="tab_two_col"><input onmouseover="Tip('���������� �������, ������ ����� � ������')" name=cont_tel type=text class="frm_wdfull" value="<?=@$r_qr['cont_tel']?>" onblur="this.value = replace_tel(this.value)" maxlength="50"></td>
			</tr>
			<? if(($user_type == 'acc') || ($user_type == 'adm')) { 
				$def_acc_num = (@$r_qr['acc_number'] == 'none') ? '���' : @$r_qr['acc_number'];
			?>
			<tr>
				<td class="tab_first_col">����� �����:</td>
				<td class="tab_two_col"><input onmouseover="Tip('������ �����, ���� ���������,<br>������ �� ���� ����� ������������� ���������.')" name="acc_number" type=text class="frm_wdfull" value="<?=$def_acc_num?>" onblur="this.value = replace_acc(this.value)" maxlength="50" /></td>
			</tr>
			<? } ?>
			<tr>
				<td class="tab_first_col">����������:</td>
				<td class="tab_two_col"><textarea class="frm_wdfull" rows="3" name="adition"><?=@$r_qr['note']?></textarea></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td width=250>&nbsp;</td>
				<td align="right">
				<? if((($op == 'edit') && (!@$r_qr['ready'])) || ($op == 'new') || ($user_type == 'adm') || ($user_type == 'acc')) { ?>
				<input class="frm_butt_send" name="butt_send" type=submit value="��������� !" onClick="return check();">
				<? } ?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<br><br>
					<a href="#" onClick="history.back()"><font face=tahoma><strong>�����</strong></font></a>
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
<? } ?>