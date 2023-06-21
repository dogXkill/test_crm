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
$tpacc = (($user_type == 'sup') || ($user_type == 'meg') || ($user_type == 'acc')) ? 1 : 0;

if(isset($_GET['del']) && is_numeric($_GET['del'])) {
	$query = "DELETE FROM mail WHERE uid=".$_GET['del'];
	mysql_query($query);
}

$oper = '';
if(isset($_GET['oper']) && ($_GET['oper'] == 'new')) 
	$oper = 'new';
elseif(isset($_GET['edit']) && is_numeric($_GET['edit']))
	$oper = 'edit';

//echo $oper;

if(isset($_POST['butt_send']) && trim($_POST['butt_send'])) {
	$error = '';
	
	if(!trim($_POST['email']))
		$error = 'Поле "email" незаполнено!';
		
	if(!$error) {	
		if(!is_numeric($_POST['edit'])) {
			$query = sprintf("INSERT INTO mail(email,coment) VALUES('%s','%s')",$_POST['email'], $_POST['coment']);
		}
		else {
			$query = sprintf("UPDATE mail SET email='%s',coment='%s' WHERE uid=%d",$_POST['email'],$_POST['coment'],$_POST['edit']);
		}
		mysql_query($query);
		header("Location: options.php");
	}
}

// ---------------------------------------------------------------------------	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.1</title>
<script src="../includes/js/jquery.cookie.js"></script>   
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<? require_once("../templates/top.php"); ?>

<table align=center width=750 border=0>


<tr>
<td colspan=3>
<br>
<? 
$name_curr_page = 'users';
require_once("../templates/main_menu.php");?>
<table width=100% border=0 cellpadding="5" cellspacing="0" bgcolor="#F6F6F6">
<?
switch ($oper) {
	case 'new':
		$tit_r = 'Добавление E-mail';
		break;
	case 'edit':
		$tit_r = 'Редактирование E-mail';
		break;
	default:
		$tit_r = 'Список E-mail';

}
?>
	<tr>
		<td align="center" class="title_razd"><?=$tit_r?></td>
	</tr>
	<tr>
		<td valign="top" align="center">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
						<? if($oper=='new') { ?>
					<td width="130">
						<span class="sublink_pl">+</span> 
						<a href="options.php" class="sublink">список E-mail</a>
					</td>
					<td width="150">
						<font face=tahoma size=4><strong>+</strong></font>
						<font face=tahoma size=2>Добавить E-mail</font>
					</td>
						<? } elseif($oper == 'edit') { ?>
					<td width="130">
						<span class="sublink_pl">+</span> <a href="options.php" class="sublink">список E-mail</a>
					</td>
					<td width="150">
						<span class="sublink_pl">+</span> <a href="?oper=new" class="sublink">Добавить E-mail</a>
					</td>
					<? } else {?>
					<td width="130">
						<span class="sublink_pl_off">+</span> <span class="sublink_off">список E-mail</span>
					</td>
					<td width="150">
						<span class="sublink_pl">+</span> <a href="?oper=new" class="sublink">Добавить E-mail</a>
					</td>
					<? } ?>
				</tr>
			</table>
</td>
</tr>
<tr>
	<td align="center">
		<? if($auth) { 
				if(($oper == 'new') || ($oper == 'edit')) { ?>
				<table border="0" cellspacing="0" cellpadding="0" width="100">
					<? if(@$error) { ?>
					<tr>
						<td colspan="2" align="center" class="err"><?=@$error?></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<? } 
					if($oper == 'edit') {
						$query  = "SELECT * FROM mail WHERE uid=".$_GET['edit'];
						$res = mysql_query($query);
						$r = mysql_fetch_array($res);
					}
					
					?>
				<form action="" method="post" name="editus">
					<input name="edit" type="hidden" value="<?=@$_GET['edit']?>" />
					<tr>
						<td class="tab_first_col" width="100">Email:&nbsp;<span class="err">*</span>&nbsp;</td>
						<td><input name="email" class="users_frm" type=text value="<?=@$r['email']?>" size=30></td>
					</tr>
					<tr>
						<td class="tab_first_col">Кометарий:</td>
						<td><textarea cols="30" class="users_frm" rows="3" name="coment"><?=@$r['coment']?></textarea></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td align="right"><input name="butt_send" type=submit value="Отправить" onclick="return check();"></td>
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
				<td><img src="/i/pix.gif" width="1" height="1"></td>
			</tr>
			<tr class="tab_query_tit">
				<td class="tab_query_tit">E-mail</td>
				<td class="tab_query_tit">Комментарий</td>
				<td class="tab_query_tit">Операция</td>
			</tr>
			<?
			$query = "SELECT * FROM mail ORDER BY email";
			$res = mysql_query($query);
			while($r = mysql_fetch_array($res)) {
					$butt = '<a href="?edit='.$r['uid'].'">';
					$butt .= '<img widt="20" height="20" src="../i/edit2.gif" title="Редактировать" />';
					$butt .= '</a>&nbsp;';
					
					$butt .= '<a href="?del='.$r['uid'].'">';
					$butt .= '<img widt="20" height="20" src="../i/del.gif" title="Удалить" />';
					$butt .= '</a>&nbsp;';
			?>
			<tr>
				<td align="center" class="tab_td_norm"><?=$r['email']?></td>
				<td align="center" class="tab_td_norm"><?=(@$r['coment']) ? @$r['coment'] : '---'?></td>
				<td align="center" class="tab_td_norm"><?=$butt?></td>
			</tr>
			<? } ?>
		</table>

		
		<? } }?></td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>