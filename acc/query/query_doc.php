<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //���� � �������
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
// ���� 1 -����� ��� ���������, ����� 0 - ��������
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;

$op = 'new';		// �� ����� ��� �������� ����� ������

$ed_us_id = $user_id;

// ������ ��������� ����� ������ �� ����
if(isset($_GET['doc']) && is_numeric($_GET['doc'])) {
		$op = 'doc';
		$query = sprintf("SELECT user_id,ready,client_id,prdm_num_acc FROM queries WHERE uid=%d", $_GET['doc']);
		$res_qr = mysql_query($query);
		$r_qr = mysql_fetch_array($res_qr);
		$ed_us_id = $r_qr['user_id'];
}

$ed_us_id = ($tpacc) ? 0 : $ed_us_id;

// -------------- ����������, �������������� -----------------
if(isset($_POST['butt_send'])) {
/*	if($_POST['client_list'] == 0) {
		$query = "INSERT INTO clients(name) VALUES('".$_POST['client']."')";
		mysql_query($query);
		$client = mysql_insert_id();
	}
	else */
	$client = $_POST['client_list'];

	$acc_num_f = trim(@$_POST['num_acc']);
	if( ($acc_num_f == '���') || ($acc_num_f == 'no') || ($acc_num_f == '-') )
		$acc_num_f = 'none';




	if($_POST['doc_id'] != 0) {		// ������ ����� ������ �� ����
		$query = sprintf("INSERT INTO queries(client_id,type,user_id,prdm_num_acc,doc,note,date_query) VALUES(%d,'1',%d,'%s','%s','%s',NOW())",$client, $user_id,$acc_num_f,$_POST['kakie'],$_POST['comment']);
		mysql_query($query);
	}




	// �������������� ������� �� ��������
	elseif($_POST['edit_id'] != 0) {
		$query = sprintf("UPDATE queries SET client_id=%d,prdm_num_acc='%s',doc='%s',note='%s' WHERE uid=%d", $client, $acc_num_f, $_POST['kakie'],$_POST['comment'], $_POST['edit_id']);
		mysql_query($query);
/*
		$query = "UPDATE queries SET date_query=NOW() WHERE uid=".$_POST['edit_id'];
		mysql_query($query);
		$query  = "SELECT query_id FROM queries WHERE uid=".$_POST['edit_id'];
		$res = mysql_query($query);
		$r = mysql_fetch_array($res);
		$query = sprintf("UPDATE data_queries SET client_id=%d,sub_acc='%s',note='%s',acc_number='%s' WHERE uid=%d", $client, $_POST['kakie'], $_POST['comment'], $acc_num_f, $r['query_id']);
		mysql_query($query);
*/
	}





	// ���������� ������ ������� �� ��������
	else {
		$query = sprintf("INSERT INTO queries(client_id,type,user_id,prdm_num_acc,doc,note,date_query) VALUES(%d,'1',%d,'%s','%s','%s',NOW())", $client, $user_id, $acc_num_f, $_POST['kakie'], $_POST['comment']);
		mysql_query($query);
/*
		$query = sprintf("INSERT INTO data_queries(client_id,sub_acc,acc_number,note) VALUES('%s','%s','%s','%s')", $client, $_POST['kakie'], $acc_num_f, $_POST['comment']);
		mysql_query($query);
		$new_id = mysql_insert_id();
		//$ready_stat = (trim($_POST['num_acc'])) ? 1 : 0;
		$query = sprintf("INSERT INTO queries(query_id,type,user_id,date_query) VALUES(%d,'1',%d, NOW())", $new_id, $user_id);
		mysql_query($query);
*/
	}
		header("Location: /acc/query/");
}


$query = "SELECT * FROM users WHERE uid=".$user_id;
$res = mysql_query($query);
$r = mysql_fetch_array($res);
$full_name = @$r['surname'].' '.@$r['name'].' '.@$r['father'];

// --------------���� ������� � ������ ��������������- ���������--------------
	if(isset($_GET['show']) && is_numeric($_GET['show'])) {
		$op = 'edit';
		$query = sprintf("SELECT * FROM queries WHERE uid=%d", $_GET['show']);
//		$query = sprintf("SELECT a.user_id,a.ready, b.client_id,b.acc_number,b.sub_acc,b.note,c.short FROM queries as a,data_queries as b, clients as c WHERE a.uid=%d AND a.query_id=b.uid AND b.client_id=c.uid", $_GET['show']);
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

	var ed_us_id = <?=@$ed_us_id?>;


// ---- ������� ������������ ��������� �������� �������� � ���������� ----
//------------------------------------------------------------------------
function doLoad(val,fn,accyes,accfl) {
    var req = new JsHttpRequest();
    req.onreadystatechange = function() {
        if (req.readyState == 4) {
						res = req.responseJS.res;																									// ������ ������������ ��������
						res['name'] = (res['name'] == null) ? ''  : res['name'];										// ������ �������� �������
						res['leg_adr'] = (res['leg_adr'] == null) ? ''  : res['leg_adr'];								// ����������� �����
						res['post_adr'] = (res['post_adr'] == null) ? ''  : res['post_adr'];							// �����������/�������� �����
						res['inn'] = (res['inn'] == null) ? ''  : res['inn'];											// ���
						res['kpp'] = (res['kpp'] == null) ? ''  : res['kpp'];											// ���
						res['acc'] = (res['acc'] == null) ? ''  : res['acc'];											// ����� �����

						fn = req.responseJS.fn;					// 0 - ���� ��������� ����. 1 - ����� �� ������

						// ���� ����� ���. ����. ������ 1 ��� ������ ������, ���������� 0- ������
						if((val.length < 1) || (res['uid'] == null))
							res['uid'] = 0;

						if(res['uid'] == 0) {		// ������ ����� ������
							res['acc'] = '';
						}
						// ���� �������� �������� ��������
						if(fn == 0) {
							num =document.f_send.client_list.options.length;
							for(i=0; i<num; i++) {
								if(document.f_send.client_list.options[i].value == res['uid'])
									document.f_send.client_list.selectedIndex = i;
							}
						}
						else {
							if(res['uid'] == 0)
								res['short'] = '';

							document.f_send.client_short.value = res['short'];
						}
						if(accfl == 1)
							document.f_send.num_acc.value = res['acc'];
					}
    }
    req.open(null, '../backend/back_LoadReqClient.php', true);
    req.send( { usid: ed_us_id, str: val, fn: fn, accyes: accyes } );
}

function check() {
	var obj = document.f_send;

	if(obj.client_list.value == 0){
		alert("�������� ������� �� ������");
		obj.client_list.focus();
		return false;
	}
	obj.num_acc.value = replace_acc(obj.num_acc.value);

	if(obj.num_acc.value==""){
		alert("������� ����� �����");
		obj.num_acc.focus();
		return false;
	}
}

// --------------------- �������������� ������ ����� ---------------------
function replace_num_acc(v) {
	var reg_sp = /[^\d]*/g;		// ��������� ���� �������� ����� ����
	v = v.replace(reg_sp, '');
	var reg_sp = /^0*$/g;						// ���� � ������ ������ ���� ����, �������
	v = v.replace(reg_sp, '');

	return v;
}

function replace_acc(val) {
	if((val == '���') || (val == 'no') || (val == '-'))
		return '���';
	else
		return replace_num_acc(val);
}

// ������� � ������ ��������� � �������� �������
function replace_str(v) {
	var reg_sp = /^\s*|\s*$/g;
	v = v.replace(reg_sp, "");
	return v;
}
//-->
</script>

<body<? if($op == 'edit') {?> onload="doLoad(<?=@$r_qr['client_id']?>,1,<?=($op = 'edit') ? '1' : '0'?>)"<?}?>>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>
<? require_once("../templates/top.php"); ?>

<table align=center width=750 border=0>



<tr>
<td colspan=3>
<br>
<?
$name_curr_page = 'query_list';
require_once("../templates/main_menu.php");?>
<table width=100% border=0 cellpadding="5" cellspacing="0" bgcolor="#F6F6F6">
	<tr>
		<td valign="top" align="center">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="150">
						<span class="sublink_pl">+</span> <a href="query_send.php" class="sublink">��������� ����</a>
					</td>
					<td width="150">
						<span class="sublink_pl_off">+</span> <span class="sublink_off">��������� ���������</span>
					</td>
					<? if($tpacc) { ?>
					<td width="100">
						<span class="sublink_pl">+</span>
						<a href="clients_list.php" class="sublink">�������</a>
					</td>
					<td width="100">
						<span class="sublink_pl">+</span>
						<a href="contractors_list.php" class="sublink">����������</a>
					</td>
					<? } ?>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center"><h4>������ ���������</h4></td>
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
					<td class="tab_first_col">�������� �������:</td>
					<td align="left">
					<? if($tpacc) {?><a onmouseover="Tip('����������/������������� ������ ������ ������������')" href="users.php?edit=<?=$r['uid']?>"><?}?><strong><?=$full_name?></strong><? if($tpacc) {?></a><?}?>&nbsp;&nbsp;&nbsp;
					<?
					if(trim($r['email'])) {
						echo '<a onmouseover="Tip(\'������� E-Mail\')" href="mailto:'.$r['email'].'">('.$r['email'].')</a>';
					}
					?>
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td class="tab_first_col">������� �����:&nbsp;&nbsp;</td>
					<td class="tab_two_col">
						<input onKeyUp="doLoad(this.value,0,1,1)"  onchange="this.value=replace_str(this.value)" onmouseover="Tip('������� ����� �� ��������� �������� �������')" class="frm_wdfull" name="client_short" type=text value="<?=@$r_qr['short']?>" />
					</td>
				</tr>
				<tr>
					<td class="tab_first_col">������ �������� �������:&nbsp;<span class="err">*</span>&nbsp;</td>
					<td class="tab_two_col">
						<?
						$query = "SELECT a.*, b.surname FROM clients as a, users as b WHERE a.del=0 AND a.user_id=b.uid ORDER BY a.user_id,a.name";
/*						
						if($tpacc) {	// ������ ������ �������� ���� �������������
							$query = "SELECT a.*, b.surname FROM clients as a, users as b WHERE a.del=0 AND a.user_id=b.uid ORDER BY a.user_id,a.name";
						} else {		// �� ������
							if($op == 'doc')	// ������� �������������� ������������
								$query = "SELECT * FROM clients WHERE del=0 AND user_id=".$ed_us_id." ORDER BY name";
							else				// ������� ��������������� ������������
								$query = "SELECT * FROM clients WHERE del=0 AND user_id=".$user_id." ORDER BY name";
						}
*/
//						$query = "SELECT * FROM clients ORDER BY name";
//						$query = "SELECT a.*, b.surname FROM clients as a, users as b WHERE a.del=0 AND a.user_id=b.uid ORDER BY a.user_id,a.name";
						$res_cl  = mysql_query($query);
						?>

						<select name="client_list" size="<?=(($tpacc)?10:10)?>" class="frm_wdfull" id="client_list" onChange="doLoad(this.value,1,1,1);" onclick="doLoad(this.value,1,1,1);" onmouseover="Tip('���� ������ ������ �������� ��������')"  >
						<option value="0"<?=($op != 'edit') ? ' selected' : ''?> style="background-color:#E2E2E2;" >---- �� ������� ----</option>
						<?
						$i=0;
						$optgr = -1;	// �� ��� �������
						$fl_optgr = 0;	// ����� �������� 1�� ���� optgroup=1

						while($r_cl = mysql_fetch_array($res_cl)) {
							$sel='';
							if(($op == 'edit') || ($op == 'doc')) {
									if($r_cl['uid'] == $r_qr['client_id'])
										$sel = ' selected';
							}
							$gr_name = '';	// ��� ������������ (������ �������)

//							if($tpacc) {	// ��� ������� ���������� �������� ���� �������������
								if($r_cl['user_id'] != $optgr) {	// ������� ���� ������������
									$optgr = $r_cl['user_id'];		// ��������� �����
									$gr_name = $r_cl['surname'];	// ��� ������������
								}
//							}

							if($gr_name) { 	/* �������� ������ */ ?>
							<? if($fl_optgr) { /* ������� ���������� �� ���� ������� */ ?></optgroup><? }
								$fl_optgr = 1;	/* ���� - ���� ������ ������� */ ?>
							<optgroup label="<?=$gr_name?>" >
							<? } ?>

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
						$def_acc_num = (@$r_qr['prdm_num_acc'] == 'none') ? '���' : @$r_qr['prdm_num_acc'];
					?>
					<td class="tab_first_col">����� �����:&nbsp;<span class="err">*</span>&nbsp;</td>
					<td class="tab_two_col"><input  onmouseover="Tip('������ �����.')" name=num_acc type=text value="<?=$def_acc_num?>" onblur="this.value = replace_acc(this.value,1)" class="frm_wdfull"></td>
				</tr>
				<tr>
					<td class="tab_first_col">����� ���������� ���������:</td>
					<td class="tab_two_col">
						<textarea onchange="this.value=replace_str(this.value)" class="frm_wdfull" rows="3" name="kakie"><?=($op == 'edit') ? @$r_qr['doc'] : '��� (���������), ���� �������'?></textarea>
					</td>
				</tr>
				<tr>
					<td class="tab_first_col">�����������:</td>
					<td class="tab_two_col"><textarea  onchange="this.value=replace_str(this.value)" class="frm_wdfull" rows="3" name="comment"><?=@$r_qr['note']?></textarea></td>
				</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
				<tr>
					<td align="right" colspan="2">
					<? if((($op == 'edit') && (!@$r_qr['ready'])) || ($op == 'new') || ($op == 'doc') || $tpacc) { ?>
					<input name="butt_send" type=submit value="��������� !" onclick="return check();" class="frm_butt_send">
					<? } ?>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<br><br>
						<a href="#" onclick="history.back()"><font face=tahoma><strong>�����</strong></font></a>
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
