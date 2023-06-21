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
// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;





// удаление 
if(isset($_GET['del']) && is_numeric($_GET['del'])) {
	$query = "DELETE FROM contractors  WHERE uid=".$_GET['del'];
	mysql_query($query);
}



$oper = '';
if(isset($_GET['oper']) && ($_GET['oper'] == 'new')) 
	$oper = 'new';
elseif(isset($_GET['edit']) && is_numeric($_GET['edit']))
	$oper = 'edit';





if(isset($_POST['butt_send']) && trim($_POST['butt_send'])) {
	$error = '';
	
	if(!trim($_POST['name']))
		$error = 'Поле "Наименование" не заполнено!';
	
	if((!$error) && (!is_numeric($_POST['edit']))) {
		$query = "SELECT * FROM contractors WHERE name='".trim($_POST['name'])."'";
		$res = mysql_query($query);
		if(mysql_num_rows($res)) {
			$error = 'Поставщик с таким наименованием уже существует, <br>попробуйте еще раз.';
		}
	}
		
	if(!$error) {	
		if(!is_numeric($_POST['edit'])) {
			$query = sprintf("INSERT INTO contractors(name,cont_pers,cont_tel,email) VALUES('%s','%s','%s','%s')", $_POST['name'], $_POST['cont_pers'], $_POST['cont_tel'],$_POST['email']);
		}
		else {
			$query = sprintf("UPDATE contractors SET name='%s',cont_pers='%s',cont_tel='%s',email='%s' WHERE uid=%d",$_POST['name'], $_POST['cont_pers'], $_POST['cont_tel'],$_POST['email'],$_POST['edit']);
		}
		mysql_query($query);
		header("Location: contractors_list.php");
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
<?
switch ($oper) {
	case 'new':
		$tit_r = 'Добавление поставщика';
		break;
	case 'edit':
		$tit_r = 'Редактирование поставщика';
		break;
	default:
		$tit_r = 'Список поставщиков';

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
						<a href="?oper=list" class="sublink">Список поставщиков</a>					</td>
					<td width="150">
						<font face=tahoma size=4><strong>+</strong></font>
						<font face=tahoma size=2>Добавить поставщика</font>					</td>
					
					
					
					
						<? } elseif($oper == 'edit') { ?>
						
					<td width="150">
						<span class="sublink_pl">+</span> <a href="?oper=list" class="sublink">Список поставщиков</a>					</td>
					<td width="150">
						<span class="sublink_pl">+</span> <a href="?oper=new" class="sublink">Добавить поставщика</a>					</td>
					
					<? } else {?>
					
					
					
					<td width="150">
						<span class="sublink_pl_off">+</span> <span class="sublink_off">Список поставщиков</span>					</td>
					<td width="150">
						<span class="sublink_pl">+</span> <a href="?oper=new" class="sublink">Добавить поставщика</a>					</td>
					
					
					<? } ?>
				</tr>
			</table>		</td>
	</tr>
<tr>
	<td align="center">&nbsp;</td>
</tr>

<tr>
	<td align="center">
		<? if($auth) { 
				if(($oper == 'new') || ($oper == 'edit')) { ?>
				
				
				
					<table border="0" cellspacing="0" cellpadding="0" width="400" align="center">
					<? if(@$error) { ?>
					<tr>
						<td colspan="2" align="center" class="err"><?=@$error?></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<? } 
					if($oper == 'edit') {
						$query  = "SELECT * FROM contractors WHERE uid=".$_GET['edit'];
						$res = mysql_query($query);
						$r = mysql_fetch_array($res);
					}
					
					?>
				<form action="" method="post" name="editus">
					<input name="edit" type="hidden" value="<?=@$_GET['edit']?>" />
					<tr>
						<td class="tab_first_col" width="200">Наименование:&nbsp;<span class="err">*</span>&nbsp;</td>
						<td><input name="name" class="users_frm" type=text value="<?=@$r['name']?>" size=30></td>
					</tr>
					<tr>
						<td class="tab_first_col">Контактное лицо:</td>
						<td><input name="cont_pers" class="users_frm" value="<?=@$r['cont_pers']?>" type=text size=30></td>
					</tr>
					<tr>
						<td class="tab_first_col">Контактный телефон:</td>
						<td><input name="cont_tel" class="users_frm" value="<?=@$r['cont_tel']?>" type=text size=30></td>
					</tr>
					<tr>
						<td class="tab_first_col">E-Mail:</td>
						<td><input name="email" class="users_frm" value="<?=@$r['email']?>" type=text size=30></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td align="right"><input name="butt_send" type=submit value="Сохранить" onclick="return check();"></td>
					</tr>
					</form>
				</table>

				
				
		<?
				} else {
				?>
		<table border="0" cellspacing="2" cellpadding="3">
			<tr>
				<td><img src="/i/pix.gif" width="120" height="1"></td>
				<td><img src="/i/pix.gif" width="120" height="1"></td>
				<td><img src="/i/pix.gif" width="120" height="1"></td>
				<td><img src="/i/pix.gif" width="120" height="1"></td>
				<td><img src="/i/pix.gif" width="1" height="1"></td>
			</tr>
			<tr class="tab_query_tit">
				<td class="tab_query_tit">Наименование</td>
				<td class="tab_query_tit">Контактное лицо</td>
				<td class="tab_query_tit">Контактный телефон</td>
				<td class="tab_query_tit">E-Mail</td>
				<td class="tab_query_tit">Опер.</td>
			</tr>
			<?
			$query = "SELECT * FROM contractors ORDER BY name";
			$res = mysql_query($query);
			while($r = mysql_fetch_array($res)) {
					$butt = '<a href="?edit='.$r['uid'].'" onmouseover="Tip(\'Редактировать\')">';
					$butt .= '<img widt="20" height="20" src="../i/edit2.gif" />';
					$butt .= '</a>&nbsp;';
					
					$butt .= '<a href="?del='.$r['uid'].'" onmouseover="Tip(\'Удалить\')">';
					$butt .= '<img widt="20" height="20" src="../i/del.gif" />';
					$butt .= '</a>&nbsp;';
			?>
			<tr>
				<td align="left" width="200" class="tab_td_norm" style="padding-left:20px;"><?=$r['name']?></td>
				<td align="center" class="tab_td_norm"><?=(@$r['cont_pers']) ? @$r['cont_pers'] : '---'?></td>
				<td align="center" class="tab_td_norm"><?=(@$r['cont_tel']) ? @$r['cont_tel'] : '---'?></td>
				<td align="center" class="tab_td_norm"><?=(@$r['email']) ? @$r['email'] : '---'?></td>
				<td align="center" class="tab_td_norm"><?=$butt?></td>
			</tr>
			<? } ?>
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
</body>
</html>