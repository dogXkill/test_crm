
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

<center><? if ($id=="ok") {echo "<font face=tahoma color=red><strong>������ ���������!</strong></font><br><br>";}?>
<form action="http://www.printfolio.ru/query/send.php" method=post name=q>
<font face=tahoma><strong>��������� ����������� ���������</strong></font></center>
<table align=center width=500 border=0>
<tr>
<td width=250><font face=tahoma>��������:</font></td>
<td><font face=tahoma><strong>��������� ������</strong></font></td>
</tr>
<tr>
<td width=250><font face=tahoma>�������� �������</font></td>
<td><input type=text size=35 name=client></td>
</tr>
<tr>
<td width=250><font face=tahoma>����� �����</font></td>
<td><input type=text size=35 name=num_invoice></td>
</tr>
<tr>
<td width=250><font face=tahoma>����� ���������� ���������</font></td>
<td><textarea cols="30" rows="3" name="kakie">��� (���������), ���� �������</textarea></td>
</tr>
<tr>
<td width=250><font face=tahoma>�����������</font></td>
<td><textarea cols="30" rows="3" name="comment"></textarea></td>
</tr>

<tr>
<td width=250>&nbsp;</td>
<td><input type=submit value="���������!" onclick="return check();"></td>
</tr>
</table><center><br>

<a href="http://192.168.0.1"><font face=tahoma><strong>�� �������</strong></font></a></center>
</td>
</form>
</tr>
</table>


</body>
</html>
