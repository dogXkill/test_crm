<script language="JavaScript" type="text/javascript">
<!--
function inbutt_act() {
	document.getElementById('ent_butt').style.backgroundImage = 'url(/i/open2.gif)';
}
function inbutt_dis() {
	document.getElementById('ent_butt').style.backgroundImage = 'url(/i/open.gif)';
}
function exitbutt_act() {
	document.getElementById('ex_butt').src = '/i/door2.gif';
}
function exitbutt_dis() {
	document.getElementById('ex_butt').src = '/i/door.gif';
}
//-->
</script>


<? if(!$auth) { ?>

<table border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top" background="/i/fon_08.gif" style="background-repeat:repeat-y;"><img src="/i/fon_01.gif" width="18" height="19" alt=""></td>
    <td rowspan="3" valign="top">
			<table border="0" cellspacing="0" cellpadding="0" width="195">
			<form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
				<tr>
					<td colspan="2" background="/i/fon_03.gif" style="background-repeat:repeat-x"><img src="/i/pix.gif" width="1" height="10" alt=""></td>
				</tr>
				<tr>
					<td colspan="2" class="auth_title">Авторизация:</td>
				</tr>
				<tr>
					<td colspan="2"><img src="/i/pix.gif" width="1" height="5" alt=""></td>
				</tr>
				<tr>
					<td style="padding:3px;" height="45">
						<table border="0" cellspacing="0" cellpadding="0" width="140" align="right">
							<tr>
								<td align="right" class="auth_tx">логин:&nbsp;&nbsp;</td>
								<td align="left"><input name="in_user" type="text" size="20" class="auth_input"></td>
							</tr>
							<tr>
								<td align="right" class="auth_tx">пароль:&nbsp;&nbsp;</td>
								<td align="left" style="white-space:nowrap"><input  name="in_pass" type="password" size="20" class="auth_input">							</td>
							</tr>
					</table>
					</td>
				  <td align="left">
						<button name="auth_in" onmouseover="inbutt_act()" onmouseout="inbutt_dis()" onfocus="inbutt_act()" onblur="inbutt_dis()" id="ent_butt" value="ok" type="submit" class="auth_butt_ent" title="Войти"><img src="/i/pix.gif" alt="Войти" width="20" height="38" /></button>
					</td>
				</tr>
				<tr>
					<td colspan="2"><img src="/i/pix.gif" width="1" height="5" alt=""></td>
				</tr>
				<tr>
					<td colspan="2" valign="top" background="/i/fon_17.gif" style="background-repeat:repeat-x"><img src="/i/pix.gif" width="1" height="2" alt=""></td>
				</tr>
				</form>
			</table>
		</td>
    <td valign="top" background="/i/fon_10.gif" style="background-repeat:repeat-y;"><img src="/i/fon_05.gif" width="19" height="19" alt=""></td>
  </tr>
  <tr>
    <td background="/i/fon_08.gif" style="background-repeat:repeat-y;"><img src="/i/pix.gif" width="18" height="1" alt=""></td>
    <td background="/i/fon_10.gif" style="background-repeat:repeat-y;"><img src="/i/pix.gif" width="19" height="1" alt=""></td>
  </tr>
  <tr height="20">
    <td valign="bottom" background="/i/fon_08.gif" style="background-repeat:repeat-y;"><img src="/i/fon_13.gif" width="18" height="20" alt=""></td>
    <td valign="bottom" background="/i/fon_10.gif" style="background-repeat:repeat-y;"><img src="/i/fon_15.gif" width="19" height="20" alt=""></td>
  </tr>
</table>




	<? } 
	else { 
		switch($user_type) {
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
				break;
		}
	?>

<table border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top" background="/i/fon_08.gif" style="background-repeat:repeat-y;"><img src="/i/fon_01.gif" width="18" height="19" alt=""></td>
    <td rowspan="3" valign="top">
			<table border="0" cellspacing="0" cellpadding="0"  width="195">
			<form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
				<tr>
					<td colspan="2" background="/i/fon_03.gif" style="background-repeat:repeat-x"><img src="/i/pix.gif" width="1" height="10" alt=""></td>
				</tr>
				<tr>
					<td colspan="2" class="auth_title">Авторизация:</td>
				</tr>
				<tr>
					<td colspan="2"><img src="/i/pix.gif" width="1" height="5" alt=""></td>
				</tr>
				<tr>
					<td valign="top" align="center" height="46" style="padding:3px; padding-top:10px;">
						<span class="auth_user">&nbsp;&nbsp;<?=$user?></span>&nbsp;&nbsp;&nbsp;<span class="auth_tp_user">(<?=$user_header?>)</span>&nbsp;&nbsp;</td>
				  <td align="left" valign="top" style="padding-top:10px;"><a href="/?auth=exit" title="Выход"><img src="/i/door.gif" onmouseover="exitbutt_act()" onmouseout="exitbutt_dis()" alt="Выход" name="ex_butt" width="23" height="23" id="ex_butt" /></a></td>
				</tr>
				<tr>
					<td colspan="2"><img src="/i/pix.gif" width="1" height="5" alt=""></td>
				</tr>
				<tr>
					<td colspan="2" valign="top" background="/i/fon_17.gif" style="background-repeat:repeat-x"><img src="/i/pix.gif" width="1" height="2" alt=""></td>
				</tr>
				</form>
			</table>
		</td>
    <td valign="top" background="/i/fon_10.gif" style="background-repeat:repeat-y;"><img src="/i/fon_05.gif" width="19" height="19" alt=""></td>
  </tr>
  <tr>
    <td background="/i/fon_08.gif" style="background-repeat:repeat-y;"><img src="/i/pix.gif" width="18" height="1" alt=""></td>
    <td background="/i/fon_10.gif" style="background-repeat:repeat-y;"><img src="/i/pix.gif" width="19" height="1" alt=""></td>
  </tr>
  <tr height="20">
    <td valign="bottom" background="/i/fon_08.gif" style="background-repeat:repeat-y;"><img src="/i/fon_13.gif" width="18" height="20" alt=""></td>
    <td valign="bottom" background="/i/fon_10.gif" style="background-repeat:repeat-y;"><img src="/i/fon_15.gif" width="19" height="20" alt=""></td>
  </tr>
</table>
<? } ?>
