<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Новый подрядчик</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>
<script language="JavaScript" type="text/javascript">
<!--
function close_win() {
	window.close();
	timer1=setTimeout('close_win()',200);
}

function replace_str(v) {
	var reg_sp = /^\s*|\s*$/g;	
	v = v.replace(reg_sp, "");
	return v;
}

function check(){
	var obj = document.ff_new_podr;
	obj.name.value = replace_str(obj.name.value);
	if(replace_str(obj.name.value) =='') {
		alert("Введите название");
		obj.name.focus();
		return false;
	}
	window.opener.add_predm_name(obj.name.value, obj.pers.value, obj.tel.value, obj.mail.value);
	timer1=setTimeout('close_win()',200);
}

//-->
</script>

<body>
<table border="0" cellspacing="0" cellpadding="0">
	<form name="ff_new_podr" action="" method="get">
  <tr>
    <td><img src="/i/pix.gif" width="130" height="1" /></td>
    <td><img src="/i/pix.gif" width="200" height="1" /></td>
  </tr>
  <tr>
    <td align="right">Название:&nbsp;<span class="err">*</span>&nbsp;&nbsp;</td>
    <td><input name="name" type="text" class="new_podr" /></td>
  </tr>
  <tr>
    <td align="right">Контактное лицо:&nbsp;&nbsp;</td>
    <td><input name="pers" type="text" class="new_podr" /></td>
  </tr>
  <tr>
    <td align="right">Контактный телефон:&nbsp;&nbsp;</td>
    <td><input name="tel" type="text" class="new_podr" /></td>
  </tr>
  <tr>
    <td align="right">E-Mail:&nbsp;&nbsp;</td>
    <td><input name="mail" type="text" class="new_podr" /></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="right"><input name="" type="button" value="Отмена" onclick="window.close()" /><input name="" type="button" value="ОК" onclick="return check();" /></td>
  </tr>
	</form>
</table>
</body>
</html>
