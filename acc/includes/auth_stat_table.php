<?
$auth_stat = false;

$pass_stat = @$_COOKIE['pass_des_stat'];

if(!empty($_POST['auth_in_stat'])) {
	$pass_stat = $_POST['in_pass'];
}

if(md5(trim($pass_stat)) == 'c5fe25896e49ddfe996db7508cf00534') {
	setcookie('pass_des_stat', $pass_stat, time() + 2400);
	$auth_stat = true;
} elseif ($user == 'titov') {
	setcookie('pass_des_stat', $pass_stat, time() + 2400);
	$auth_stat = true;
}




if(!$auth_stat) { ?>

<script language="JavaScript" type="text/javascript">
<!--
function inbutt_act() {
	document.getElementById('ent_butt').style.backgroundImage = 'url(/acc/i/login/open2.gif)';
}
function inbutt_dis() {
	document.getElementById('ent_butt').style.backgroundImage = 'url(/acc/i/login/open.gif)';
}
function exitbutt_act() {
	document.getElementById('ex_butt').src = '/acc/i/login/door2.gif';
}
function exitbutt_dis() {
	document.getElementById('ex_butt').src = '/acc/i/login/door.gif';
}
//-->
</script>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<table border="0" cellpadding="0" cellspacing="0" align="center">
  <tr>
    <td valign="top" background="/acc/i/login/fon_08.gif" style="background-repeat:repeat-y;"><img src="/acc/i/login/fon_01.gif" width="18" height="19" alt=""></td>
    <td rowspan="3" valign="top">
			<table border="0" cellspacing="0" cellpadding="0" width="195">
			<form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
				<tr>
					<td colspan="2" background="/acc/i/login/fon_03.gif" style="background-repeat:repeat-x"><img src="/acc/i/pix.gif" width="1" height="10" alt=""></td>
				</tr>
				<tr>
					<td colspan="2" class="auth_title">авторизация:</td>
				</tr>
				<tr>
					<td colspan="2"><img src="/i/pix.gif" width="1" height="5" alt=""></td>
				</tr>
				<tr>
					<td style="padding:3px;" height="45">
						<table border="0" cellspacing="0" cellpadding="0" width="140" align="right">
							<tr>
								<td align="right" class="auth_tx">пароль:&nbsp;&nbsp;</td>
								<td align="left" style="white-space:nowrap"><input  name="in_pass" type="password" size="20" class="auth_input">							</td>
							</tr>
					</table>					</td>
				  <td align="left">
						<button name="auth_in_stat" onMouseOver="inbutt_act();Tip('Войти')" onMouseOut="inbutt_dis()" onFocus="inbutt_act()" onBlur="inbutt_dis()" id="ent_butt" value="ok" type="submit" style="background-image:url(/acc/i/login/open.gif);height: 38px;	width: 20px;border:0;"><img src="/i/pix.gif" width="20" height="38" /></button>					</td>
				</tr>
				<tr>
					<td colspan="2"><img src="/i/pix.gif" width="1" height="5" alt=""></td>
				</tr>
				<tr>
					<td colspan="2" valign="top" background="/acc/i/login/fon_17.gif" style="background-repeat:repeat-x"><img src="/i/pix.gif" width="1" height="2" alt=""></td>
				</tr>
				</form>
			</table>		</td>
    <td valign="top" background="/acc/i/login/fon_10.gif" style="background-repeat:repeat-y;"><img src="/acc/i/login/fon_05.gif" width="19" height="19" alt=""></td>
  </tr>
  <tr>
    <td background="/acc/i/login/fon_08.gif" style="background-repeat:repeat-y;"><img src="/acc/i/pix.gif" width="18" height="1" alt=""></td>
    <td background="/acc/i/login/fon_10.gif" style="background-repeat:repeat-y;"><img src="/acc/i/pix.gif" width="19" height="1" alt=""></td>
  </tr>
  <tr height="20">
    <td valign="bottom" background="/acc/i/login/fon_08.gif" style="background-repeat:repeat-y;"><img src="/acc/i/login/fon_13.gif" width="18" height="20" alt=""></td>
    <td valign="bottom" background="/acc/i/login/fon_10.gif" style="background-repeat:repeat-y;"><img src="/acc/i/login/fon_15.gif" width="19" height="20" alt=""></td>
  </tr>
  <tr height="20">
    <td colspan="3" height="40" valign="middle" align="center" style="color:#999999">
			Для доступа в этот раздел необходимо<br>
 пройти дополнительную авторизацию		</td>
  </tr>
</table>
<?
exit;
} ?>
