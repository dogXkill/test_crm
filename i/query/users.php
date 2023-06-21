<? 
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом 
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1 
header("Pragma: no-cache"); // HTTP/1.1 
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

define('IMG_PATCH', '/i/users/');
$auth = false;
$oper = '';
$id='';

if(isset($_GET['oper']) && ($_GET['oper'] == 'new'))
	$oper = 'new';
	
$error = '';
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");
require_once("../includes/im_rez.inc.php");

// ----- перейти на главную если доступ запрещен ---------
if(!$auth || $user_type != 'adm') {
	header("Location: /");
	exit;
}

				if(isset($_POST['save_us']) && trim($_POST['save_us'])) {
					$id = $_POST['save_id'];
					$oper = $_POST['oper'];
					
					if((!trim($_POST['surn']))
						|| (!trim($_POST['name']))
						|| (!trim($_POST['fat'])) || (($_POST['type']!='oth') && (
							 (!trim($_POST['login'])) 
						|| (!trim($_POST['pass']) || !trim($_POST['repass']))
						|| ($_POST['pass'] != $_POST['repass'])
						|| (strlen($_POST['pass']) < 6)))
					) 
							$error = 'Не заполнено одно из полей!';
		
					if(!$error) {	
						
						if($oper == 'edit') {
						
							if((@$_POST['pass'] == '******') || ($_POST['type'] == 'oth')) {
								$query = sprintf("UPDATE users SET surname='%s', name='%s', father='%s', date_birth='%s', date_work='%s', email='%s', mobile='%s', home_tel='%s', login='%s', type='%s' WHERE uid=%d", $_POST['surn'], $_POST['name'], $_POST['fat'], $_POST['br_year'].'-'.$_POST['br_month'].'-'.$_POST['br_day'], $_POST['pr_year'].'-'.$_POST['pr_month'].'-'.$_POST['pr_day'], $_POST['email'], $_POST['mobile'], $_POST['home_tel'], @$_POST['login'], $_POST['type'], $id);
							}
							else {
								$query = sprintf("UPDATE users SET surname='%s', name='%s', father='%s', date_birth='%s', date_work='%s', email='%s', mobile='%s', home_tel='%s', login='%s', pass='%s', type='%s' WHERE uid=%d", $_POST['surn'], $_POST['name'], $_POST['fat'], $_POST['br_year'].'-'.$_POST['br_month'].'-'.$_POST['br_day'], $_POST['pr_year'].'-'.$_POST['pr_month'].'-'.$_POST['pr_day'], $_POST['email'], $_POST['mobile'], $_POST['home_tel'], $_POST['login'], $_POST['pass'], $_POST['type'], $id);
							}
							mysql_query($query);
							if($_POST['type'] == 'oth') {
								$query = sprintf("UPDATE users SET login=NULL, pass=NULL WHERE uid=%d", $id);
								mysql_query($query);
							}	
							
							if(trim($_FILES['file']['name']))	{
								ImResize($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].IMG_PATCH.'small_'.$id.'.jpeg', 100, 100);
								ImResize($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].IMG_PATCH.'big_'.$id.'.jpeg', 0, 0);
							}
							
							header("Location: users.php");
						}
						elseif($oper == 'new') {
//							echo 'wdwd';
							if($_POST['type'] == 'oth') {
								$query = sprintf("INSERT INTO users(surname,name,father,date_birth,date_work,email,mobile,home_tel,type) VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s')",$_POST['surn'], $_POST['name'], $_POST['fat'], $_POST['br_year'].'-'.$_POST['br_month'].'-'.$_POST['br_day'], $_POST['pr_year'].'-'.$_POST['pr_month'].'-'.$_POST['pr_day'], $_POST['email'], $_POST['mobile'], $_POST['home_tel'],  $_POST['type']);
							}
							else {
								$query = sprintf("INSERT INTO users(surname,name,father,date_birth,date_work,email,mobile,home_tel,login,pass,type) VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",$_POST['surn'], $_POST['name'], $_POST['fat'], $_POST['br_year'].'-'.$_POST['br_month'].'-'.$_POST['br_day'], $_POST['pr_year'].'-'.$_POST['pr_month'].'-'.$_POST['pr_day'], $_POST['email'], $_POST['mobile'], $_POST['home_tel'], $_POST['login'], $_POST['pass'], $_POST['type']);
							}
							mysql_query($query);
							$new_id = mysql_insert_id();
							
							if(trim($_FILES['file']['name']))	{
								ImResize($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].IMG_PATCH.'small_'.$new_id.'.jpeg', 100, 100);
								ImResize($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].IMG_PATCH.'big_'.$new_id.'.jpeg', 0, 0);
							}
							
							header("Location: users.php");
						}
					}	
				}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Expires" content="Mon, 26 Jul 1997 05:00:00 GMT" /> 
<meta http-equiv="Pragma" content="no-cache" /> 
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.1</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>
<script language="JavaScript" type="text/javascript">
<!--

function check(){

	var obj = document.editus;
	if(obj.surn.value==""){
		alert("Введите Фамилию");
		obj.surn.focus();
		return false;
	}
	if(obj.name.value==""){
		alert("Введите Имя");
		obj.name.focus();
		return false;
	}
	if(obj.fat.value==""){
		alert("Введите Отчество");
		obj.fat.focus();
		return false;
	}
	if(document.getElementById('type').value != 'oth') {
		if(obj.login.value==""){
			alert("Введите Логин");
			obj.login.focus();
			return false;
		}
		if(obj.pass.value==""){
			alert("Введите Пароль");
			obj.pass.focus();
			return false;
		}
		if(obj.pass.value.length < 6){
			alert("Длина пароля должна быть не менее 6 символов");
			obj.pass.focus();
			return false;
		}
		if(obj.pass.value != obj.repass.value){
			alert("Пароль и подтверждение не совпадают");
			obj.repass.select();
			return false;
		}
	}
}

function del_query(id) {
	if(confirm("Удалить пользователя?")) 
		document.location = 'users.php?del=' + id;
	}

function dis_guest() {
	if(document.getElementById('type').value == 'oth') {
		document.getElementById('login').value='';
		document.getElementById('login').disabled='disabled';
//		document.getElementById('pass').value='******';
		document.getElementById('pass').disabled='disabled';
//		document.getElementById('repass').value='******';
		document.getElementById('repass').disabled='disabled';
	}
	else {
		document.getElementById('login').disabled=false;
		document.getElementById('pass').disabled=false;
//		document.getElementById('pass').value='';
		document.getElementById('repass').disabled=false;
//		document.getElementById('repass').value='';
	}
} 
//-->
</script>


<body onload="dis_guest()">
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
<td background="/i/bgr.jpg" align=center width="122"><a href="/" class="menu_act">Общие</a></td>
<td background="/i/bgr.jpg" align=center width="122">
	<a href="query_list.php" class="menu_act">Документы</a>
</td>
<? if(!isset($_GET['oper']) && !isset($_GET['edit'])) { ?>
<td background="/i/bg.jpg" align=center class="menu_no_act" width="122">Пользователи</td>
<? } else {?>
<td background="/i/bg.jpg" align=center width="122"><a href="users.php" class="menu_act">Пользователи</a></td>
<? } ?>
<td align=center width="50">
	<a class="menu_act" title="Переключиться на новую версию" href="/acc/query/query_list.php"><img alt="Переключиться на новую версию" src="/i/strel2.gif" /></a>
</td>

</tr>
</table>
<table width=100% border=0 cellpadding="5" cellspacing="0" bgcolor="#F6F6F6">
<?
$tit = 'Пользователи';
if($oper == 'new')
	$tit = 'Новый пользователь';
if(isset($_GET['edit']))	
	$tit = 'Редактирование пользователя';
	
?>
	<tr>
		<td align="center" class="title_razd"><?=@$tit?></td>
	</tr>
	<tr>
		<td valign="top" align="center">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="150">
						<? if($oper == 'new') { ?>
						<span class="sublink_pl_off">+</span> 
						<span class="sublink_off">создать пользователя</span>
						<? } else { ?>
						<span class="sublink_pl">+</span> 
						<a href="users.php?oper=new" class="sublink">создать пользователя</a>
						<? } ?>
					</td>
					<td width="130">
						<span class="sublink_pl">+</span> 
						<a href="options.php" class="sublink">список E-mail</a>
					</td>
				</tr>
			</table></td>
</tr>

<tr>
	<td align="center">
		<? if($auth) {
				if(isset($_GET['del']) && is_numeric($_GET['del'])) {
					$query = "DELETE FROM users WHERE uid=".$_GET['del'];
					mysql_query($query);
					@unlink($_SERVER['DOCUMENT_ROOT'].IMG_PATCH.'small_'.$_GET['del'].'.jpeg');
					@unlink($_SERVER['DOCUMENT_ROOT'].IMG_PATCH.'big_'.$_GET['del'].'.jpeg');
				}
				
				if(isset($_GET['edit']) && is_numeric($_GET['edit']) || $oper == 'new') {
					if($oper != 'new') {
						$oper = 'edit';
						$query = "SELECT * FROM users WHERE uid=".$_GET['edit'];
						$res = mysql_query($query);
						$r = mysql_fetch_array($res);
						
						$f_surn = $r['surname'];
						$f_name = $r['name'];
						$f_fat = 	$r['father'];
						$date_br = explode('-',@$r['date_birth']);
						$date_pr = explode('-',@$r['date_work']);
						$f_users_tp = $r['type'];
						$f_email = $r['email'];
						$f_mobile = $r['mobile'];
						$f_home_tel = @$_POST['home_tel'];
						
						$f_pass = '******';
						$f_repass = '******';
						
					}
					else {
						$f_surn = @$_POST['surn'];
						$f_name = @$_POST['name'];
						$f_fat =	@$_POST['fat'];
						$date_br = array(@$_POST['br_year'], @$_POST['br_month'], @$_POST['br_day']);
						$date_pr = array(@$_POST['pr_year'], @$_POST['pr_month'], @$_POST['pr_day']);
						$f_email = @$_POST['email'];
						$f_mobile = @$_POST['mobile'];
						$f_home_tel = @$_POST['home_tel'];
						
						if(isset($_POST['save_us']) && trim($_POST['save_us'])) {
							$f_pass = @$_POST['pass'];
							$f_repass = @$_POST['repass'];
							$f_users_tp = @$_POST['type'];
						}
						else {
							$f_pass = $f_repass = '';
							$f_users_tp = 'oth';
						}	
					}
				?>
				<form action="" method="post" name="editus" enctype="multipart/form-data">
				<input name="save_id" type="hidden" value="<?=$_GET['edit']?>" />
				<input name="oper" type="hidden" value="<?=$oper?>" />
				<table border="0" cellspacing="3" cellpadding="0">
					<? if(@$error) { ?>
					<tr>
						<td colspan="2" align="center" class="err"><?=@$error?></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<? } ?>
					<tr>
						<td align="right">Фамилия:&nbsp;<span class="err">*</span>&nbsp;</td>
						<td align="left"><input name="surn" type="text" class="users_frm" id="surn" value="<?=$f_surn?>" size="30" /></td>
					</tr>
					<tr>
						<td align="right">Имя:&nbsp;<span class="err">*</span>&nbsp;</td>
						<td align="left"><input name="name" type="text" class="users_frm" id="name" value="<?=$f_name?>" size="30" /></td>
					</tr>
					<tr>
						<td align="right">Отчество:&nbsp;<span class="err">*</span>&nbsp;</td>
						<td align="left"><input name="fat" type="text" class="users_frm" id="fat" value="<?=$f_fat?>" size="30" /></td>
					</tr>
					<tr>
						<td align="right">Дата рождения:&nbsp;&nbsp;</td>
						<td align="left">
						<select class="users_day" name="br_day" id="tt" >
							<? 
							$sel = ' selected="selected"';
							if($id)
								$sel = '';
							for($i=1;$i<=31;$i++) { 
								if(($oper=='edit') && ($i == $date_br[2]))
									$sel = ' selected="selected"';
							?>
						  <option value="<?=$i?>" <?=$sel?>><?=$i?></option>
							<? 
							$sel = '';
							} ?>
						</select>
						<select class="users_month" name="br_month" >
							<? 
							$sel = ' selected="selected"';
							if($id)
								$sel = '';
							for($i=0;$i<12;$i++) {
								if(($oper=='edit') && (($i+1) == intval($date_br[1])))
									$sel = ' selected="selected"';
							?>
						  <option value="<?=$i+1?>" <?=$sel?>><?=$month_sel[$i]?></option>
							<?  $sel = '';
							} ?>
						</select>
						<select class="users_year" name="br_year" >
							<?
							$sel = ' selected="selected"';
							if($id)
								$sel = '';
							for($i=1930;$i<=2025;$i++) {
								if($oper=='edit') {
									if($i == $date_br[0])
										$sel = ' selected="selected"';
								}
								elseif($i == 1950)
									$sel = ' selected="selected"';
							 ?>
						  <option value="<?=$i?>" <?=$sel?>><?=$i?></option>
							<? $sel = '';
							} ?>
						</select>
						</td>
					</tr>
					<tr>
						<td align="right">Дата поступл. на работу:&nbsp;&nbsp;</td>
						<td align="left">
						<select class="users_day" name="pr_day" id="tt">
							<? 
							$sel = ' selected="selected"';
							if($id)
								$sel = '';
							for($i=1;$i<=31;$i++) { 
								if(($oper=='edit') && ($i == $date_pr[2]))
									$sel = ' selected="selected"';
							 ?>
						  <option value="<?=$i?>" <?=$sel?>><?=$i?></option>
							<? 
							$sel = '';
							} ?>
						</select>
						<select class="users_month" name="pr_month">
							<?
							$sel = ' selected="selected"';
							if($id)
								$sel = '';
							for($i=0;$i<12;$i++) {
								if(($oper=='edit') && (($i+1) == intval($date_pr[1])))
									$sel = ' selected="selected"';
							?>
						  <option value="<?=$i+1?>" <?=$sel?>><?=$month_sel[$i]?></option>
							<? $sel = '';
							} ?>
						</select>
						<select class="users_year" name="pr_year">
							<?
							$sel = ' selected="selected"';
							if($id)
								$sel = '';
							for($i=1930;$i<=2025;$i++) {
								if($oper=='edit') {
									if($i == $date_pr[0])
										$sel = ' selected="selected"';
								}
								elseif($i == 2003)
									$sel = ' selected="selected"';
							?>
						  <option value="<?=$i?>" <?=$sel?>><?=$i?></option>
							<? $sel = '';
							} ?>
						</select>
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<?
					if(($oper == 'edit') || $id) {
						$sel = array();
						$tx = ' selected="selected"';
						$sel[0] = ($f_users_tp == 'oth') ? $tx : '';
						$sel[1] = ($f_users_tp == 'mng') ? $tx : '';
						$sel[2] = ($f_users_tp == 'acc') ? $tx : '';
						$sel[3] = ($f_users_tp == 'adm') ? $tx : '';
					}
					else
						$sel = array(' selected="selected"','','','');
					?>
					<tr>
						<td align="right">Тип доступа:&nbsp;&nbsp;</td>
						<td align="left">
							<select class="users_tp" id="type" onchange="dis_guest(this.value)" name="type" onmouseover="Tip('Тип доступа')" >
							  <option value="oth" <?=$sel[0]?>>Гость</option>
							  <option value="mng" <?=$sel[1]?>>Менеджер</option>
							  <option value="acc" <?=$sel[2]?>>Бухгалтер</option>
							  <option value="adm" <?=$sel[3]?>>Админитсратор</option>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right">Логин:&nbsp;<span class="err">*</span>&nbsp;</td>
						<td align="left"><input name="login" type="text" class="users_frm" id="login" value="<?=@$r['login']?>" size="30" /></td>
					</tr>
					<tr>
						<td align="right">Пароль:&nbsp;<span class="err">*</span>&nbsp;</td>
						<td align="left"><input name="pass" type="password" class="users_frm" id="pass" value="<?=$f_pass?>" size="30" onmouseover="Tip('не менее 6 символов')"  onfocus="dis_guest()" title="не менее 6 символов" /></td>
					</tr>
					<tr>
						<td align="right">Подтверждение:&nbsp;<span class="err">*</span>&nbsp;</td>
						<td align="left"><input name="repass" type="password" class="users_frm" id="repass" value="<?=$f_repass?>" size="30" /></td>
					</tr>
					<tr>
						<td align="right">E-mail:&nbsp;&nbsp;</td>
						<td align="left"><input name="email" type="text" class="users_frm" id="email" value="<?=$f_email?>" size="30" /></td>
					</tr>
					<tr>
						<td align="right">Мобильный тел.:&nbsp;&nbsp;</td>
						<td align="left"><input name="mobile" type="text" class="users_frm" id="mobile" value="<?=$f_mobile?>" size="30" /></td>
					</tr>
					<tr>
						<td align="right">Домашний тел.:&nbsp;&nbsp;</td>
						<td align="left"><input name="home_tel" type="text" class="users_frm" value="<?=$f_home_tel?>" size="30" /></td>
					</tr>
					<tr valign="middle">
						<td align="right">Фото:&nbsp;&nbsp;</td>
						<td align="left" valign="top">
							<table border="0" cellspacing="0" cellpadding="0">
								<?
									$img = $_SERVER['DOCUMENT_ROOT'].IMG_PATCH.'small_'.@$r['uid'].'.jpeg';
									$link_s = '<a href="'.IMG_PATCH.'big_'.@$r['uid'].'.jpeg" target="_blank" title="Увеличить">';
									$link_e = '</a>';
									$alt = "Увеличить";
									
									if((!@file_exists($img)) || ($oper == 'new')) {
										$img = '/i/icons/no_im.gif';
										$link_s = $link_e = '';
										$alt = "Нет фото";
									}	
								?>
								<tr valign="middle">
									<td><?=$link_s?><img alt="<?=$alt?>" src="<?=$img?>" width="30" height="30" border="0" /><?=$link_e?></td>
									<td>&nbsp;&nbsp;<input size="10" name="file" type="file" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td align="left">
							<input class="users_frm" name="save_us" type="submit" value="Сохранить" onclick="return check();" />
							<input class="users_frm" type="button" value="Отмена" onclick="history.back()" />
						</td>
					</tr>

				</table>
				</form>	
				<?
				}
				else {
		  ?>
			<table width="200" border="0" cellpadding="3" cellspacing="2" bordercolor="#999999">
				<tr>
					<td><img src="/i/pix.gif" width="120" height="1"></td>
					<td><img src="/i/pix.gif" width="1" height="1"></td>
					<td><img src="/i/pix.gif" width="1" height="1"></td>
					<td><img src="/i/pix.gif" width="1" height="1"></td>
					<td><img src="/i/pix.gif" width="1" height="1"></td>
					<td><img src="/i/pix.gif" width="1" height="1"></td>
					<td><img src="/i/pix.gif" width="1" height="1"></td>
				</tr>	
				<tr class="tab_query_tit">
					<td align="center" class="tab_query_tit">Ф.И.О.</td>
					<td align="center" class="tab_query_tit">Фото</td>
					<td align="center" class="tab_query_tit">Дата рождения</td>
					<td align="center" class="tab_query_tit">Дата поступл. на работу</td>
					<td align="center" class="tab_query_tit">Мобильный тел.</td>
					<td align="center" class="tab_query_tit">Домашний тел.</td>
					<td align="center" class="tab_query_tit">Логин, пароль</td>
					<td align="center" class="tab_query_tit">Тип доступа</td>
					<td align="center" class="tab_query_tit">Операция</td>
				</tr>
				<? 
				$query = "SELECT * FROM users ORDER BY surname";
				$res = mysql_query($query);
				while($r_us = mysql_fetch_array($res)) {
					$fio = $r_us['surname'].' '.$r_us['name'].' '.$r_us['father'];
					if(trim($r_us['date_birth'])) {
						$tmp = explode('-',$r_us['date_birth']);
						$date_birth = $tmp[2].' '.$month[intval($tmp[1])-1].' '.$tmp[0].'г.';
					}
					else 
						$date_birth = '---';

					if(trim($r_us['date_work'])) {
						$tmp = explode('-',$r_us['date_work']);
						$date_work = $tmp[2].' '.$month[intval($tmp[1])-1].' '.$tmp[0].'г.';
					}
					else 
						$date_work = '---';
						
					switch($r_us['type']) {
						case 'adm':
							$user_header = 'администратор';
							break;
						case 'mng':
							$user_header = 'менеджер';
							break;
						case 'acc':
							$user_header = 'бухгалтер';
							break;
						default:
							$user_header = 'гость';	
					}
					$butt = '<a href="?edit='.$r_us['uid'].'">';
					$butt .= '<img widt="16" height="16" src="/i/icons/edit2.gif" title="Редактировать" />';
					$butt .= '</a>&nbsp;';
					
					$butt .= '<a href="#" onclick="del_query('.$r_us['uid'].')">';
					$butt .= '<img widt="16" height="16" src="/i/icons/del2.gif" title="Удалить" />';
					$butt .= '</a>&nbsp;';

					
				?>
				<tr >
					<td align="center" class="tab_td_norm"><strong><?=$fio?></strong></td>
					<?
						$img = IMG_PATCH.'small_'.@$r_us['uid'].'.jpeg';
						$img_sm = 'showim.php?s='.@$r_us['uid'];
						$img_alt ="'<img width=100px height=100px src=\'showim.php?s=".@$r_us['uid']."\' />', BORDERWIDTH, 1, TITLE, 'Фото'";
//					$img_alt = IMG_PATCH.'big_'.@$r_us['uid'].'.jpeg';
						$link_s = '<a href="'.IMG_PATCH.'big_'.@$r_us['uid'].'.jpeg" target="_blank" title="Увеличить">';
						$link_e = '</a>';
						$alt = "Увеличить";
						if((!@file_exists($_SERVER['DOCUMENT_ROOT'].$img)) || ($oper == 'new')) {
							$img_sm = 'showim.php?n=1';
							$img_alt = "'Не загружено', FONTCOLOR, '#FF0000'";
							$img = '/i/icons/no_im.gif';
							$link_s = $link_e = '';
							$alt = "Нет фото";
						}	
					?>
					<td align="center" class="tab_td_norm"><?=$link_s?><img alt="<?=$alt?>" src="<?=$img_sm?>" border="0" width="30" height="30" onmouseover="Tip(<?=$img_alt?>)" /><?=$link_e?></td>
					<td align="center" class="tab_td_norm"><?=$date_birth?></td>
					<td align="center" class="tab_td_norm"><?=$date_work?></td>
					<td align="center" class="tab_td_norm"><?=(trim($r_us['mobile'])) ? $r_us['mobile'] : '---'?></td>
					<td align="center" class="tab_td_norm"><?=(trim($r_us['home_tel'])) ? $r_us['home_tel'] : '---'?></td>
					<td align="center" class="tab_td_norm"><?=(trim($r_us['login'])) ? $r_us['login'] : '---'?><br><?=(trim($r_us['pass'])) ? $r_us['pass']: '---'?></td>
					<td align="center" class="tab_td_norm"><?=$user_header?></td>
					<td class="tab_td_norm" align="center"><?=$butt?></td>
				</tr>
			<? } } ?>
			</table>
	</td>
	<? } ?>
</tr>
</table>
<br><br>

</td>

</tr>
</table>

</body>
</html>
