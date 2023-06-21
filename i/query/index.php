<? if ($idm=="dfgkjshdgjkdshfjdncjkdnkcndsjcdjkr89u8ud89fu8ufusd8af8sdfu8sdf8ds") {?>
<html>
<head>
	<title>Printfolio intranet v.1</title>
</head>

<body>


<table align=center width=750 border=0>

<tr>
<td align=center><a href="http://printfolio.ru" target=_blank><img src="pf.gif" alt="" width="270" height="62" border="0"></a></td>
<td align=center><a href="http://comcad.ru" target=_blank><img src="cm.gif" alt="" width="180" height="51" border="0"></a></td>
<td align=center><a href="http://wbox.ru" target=_blank><img src="wbox.jpg" alt="" width="131" height="50" border="0"></a></td>
</tr>

<tr>
<td colspan=3>
<script language="JavaScript" type="text/javascript">
<!--
function check(){
	var obj = document.q;
    if(obj.manager.value=="-"){
		alert("Выберите ваше имя");
		obj.manager.focus();
		return false;
	}    
	if(obj.client.value==""){
		alert("Введите название клиента");
		obj.client.focus();
		return false;
	}
    
	if(obj.req.value==""){
		alert("Введите реквизиты клиента");
		obj.req.focus();
		return false;
	}
    
    if(obj.predmet.value==""){
		alert("Введите предмет выставляемого счета");
		obj.predmet.focus();
		return false;
	}
    if(obj.sum.value==""){
		alert("Введите сумму счета");
		obj.sum.focus();
		return false;
	}
    if(obj.supl.value==""){
		alert("Перечислите поставщиков, их работы, стоимость услуг и номера счетов");
		obj.supl.focus();
		return false;
	}
    if(obj.cost.value==""){
		alert("Введите общую себестоимость");
		obj.cost.focus();
		return false;
	}
}
//-->
</script>
<center><? if ($id=="ok") {echo "<font face=tahoma color=red><strong>Запрос отправлен!</strong></font><br><br>";}?>
<form action="http://www.printfolio.ru/query/send.php" method=post name=q>
<font face=tahoma><strong>Выставление счета</strong></font></center>
<table align=center width=500 border=0>
<tr>
<td width=250><font face=tahoma>Менеджер проекта</font></td>
<td><select name=manager>
<option value="-">---
<option value="sa@printfolio.ru">sa@printfolio.ru
<option value="alex@printfolio.ru">alex@printfolio.ru
<option value="eva@printfolio.ru">eva@printfolio.ru
<option value="pavel@printfolio.ru">pavel@printfolio.ru
<option value="ira@printfolio.ru">ira@printfolio.ru
<option value="serg@printfolio.ru">serg@printfolio.ru


</select></td>
</tr>
<tr>
<td width=250><font face=tahoma>Название клиента</font></td>
<td><input type=text size=35 name=client></td>
</tr>
<tr>
<td width=250><font face=tahoma>Юр. адрес, ИНН, КПП, Р/с, К/с, банк</font></td>
<td><textarea cols="30" rows="3" name="req"></textarea></td>
</tr>
<tr>
<td width=250><font face=tahoma>Предмет счета</font></td>
<td><textarea cols="30" rows="3" name="predmet"></textarea></td>
</tr>
<tr>
<td width=250><font face=tahoma>Сумма счета</font></td>
<td><input type=text size=25 name=sum></td>
</tr>
<tr>
<td width=250><font face=tahoma>Наименования подрядчиков / какую часть работы они исполняют / номера и суммы счетов</font></td>
<td><textarea cols="30" rows="3" name="supl"></textarea></td>
</tr>
<tr>
<td width=250><font face=tahoma>Общая себестоимость, включая доставку</font></td>
<td><input type=text size=25 name=cost></td>
</tr>
<tr>
<td width=250><font face=tahoma>Примечание</font></td>
<td><textarea cols="30" rows="3" name="adition"></textarea></td>
</tr>
<tr>
<td width=250>&nbsp;</td>
<td><input type=submit value="Отправить !" onclick="return check();"></td>
</tr>
</table><center><br>
<a href="invoice.xls"><font face=tahoma><strong>шаблон счета в экселе</strong></font></a>
<br><br>
<a href="http://192.168.0.1"><font face=tahoma><strong>на главную</strong></font></a></center>
</td>
</form>
</tr>
</table>


</body>
</html>
<?  } else {echo "<font face=tahoma color=red><strong>Доступ закрыт !</strong></font><br><br>";} ?>
