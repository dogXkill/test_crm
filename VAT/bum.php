<html>
<head>
	<title>Расчет бумаги</title>
</head>

<body>
<script language="JavaScript" type="text/javascript">
<!--

function check_lists(){
var obj = document.q;
    if(obj.num_lists.value!=""){
		alert("Можно задать только один из параметров ТОННЫ или КОЛИЧЕСТВО ЛИСТОВ");
		obj.num_tonnes.focus();
		return false;
	} }
function check_tonnes(){
var obj = document.q;
    if(obj.num_tonnes.value!=""){
		alert("Можно задать только один из параметров Тонны или Количество листов");
		obj.num_lists.focus();
		return false;
	} }
	//-->
</script>
&nbsp;<font face=arial size=2>Расчет: <a href="index.php"><font face=arial size=2><strong>НДС</strong></font></a> | <font face=arial size=2><strong>бумага</strong></font>

<form action=bum.php method="post" name=q>
<input type=hidden name=act value=count>

<table width=450 border=0>
<tr>
<td colspan=3 height=40><font face=arial size=3><strong>Расчет бумаги</strong></font></td>
</tr>
<tr>
<td><font face=arial size=2>Размер листа:</font></td>
<td><font face=arial size=2><strong><input type=text size=3 maxlength=3 name=a value=""> x <input type=text size=3 maxlength=3 name=b value=""> см.</strong></font></td>
<td bgcolor="#eaeaea"><font face=arial size=2 color=red><strong><?echo $_POST['a']; echo х; echo $_POST['b'];?> см.</strong></font></td>
</tr>
<tr>
<td><font face=arial size=2>Плотность бумаги:</font></td>
<td><input type=text size=13 maxlength=3 name=ves value=""> <font face=arial size=2><strong>гр.м.</strong></font></td>
<td width=150 bgcolor="#eaeaea"><font face=arial size=2 color=red><strong><?echo $_POST['ves'];?> гр.м.</strong></font></td>
</tr>
<?if ($_POST['act']=="count") {
//вычисляем площадь листа в квадратных метрах
$ploshad_lista = $_POST['a'] * $_POST['b']/10000;
//вычисляем вес листа в граммах
$ves_lista = $ploshad_lista * $_POST['ves'];
}?>
<tr>
<td><font face=arial size=2>Количество листов:</font></td>
<td><input type=text size=13 name=num_lists onchange="return check_tonnes();" value="<?if ($_POST['num_tonnes']!="") {
//если поле тонна заполнена, то вычисляем количество листов на тонну при заданных хар-ках
//исходим из того, что в одной тонне у нас 1 000 000 грамм
$skoloko_listov_v_tonne = 1000000/$ves_lista;?>"></td>
<td bgcolor="#eaeaea"><font face=arial size=2 color=red><strong>~ <?echo round($skoloko_listov_v_tonne);} else {echo "\"></td><td bgcolor=#eaeaea><font face=arial size=2 color=red><strong>"; echo $HTTP_POST_VARS['num_lists'];}?> л.</strong></font></td>
</tr>
<tr>
<td><font face=arial size=2>Количество тонн:</font></td>
<td><input type=text size=13 name=num_tonnes onchange="return check_lists();" value="<?if ($_POST['num_lists']!="") {
//если поле Количество листов заполнено то вычисляем количество тонн на заданное количество листов
//исходим из того, что в одной тонне у нас 1 000 000 грамм
$skoloko_tonn = ($ves_lista * $_POST['num_lists'])/1000000;?>"></td>
<td bgcolor="#eaeaea"><font face=arial size=2 color=red><strong>~ <?echo round($skoloko_tonn, 3);} else {echo "\"></td><td bgcolor=#eaeaea><font face=arial size=2 color=red><strong>"; echo $HTTP_POST_VARS['num_tonnes'];}?> т.</strong></font></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type=submit value="Расcчитать!"></td>
<td></td>
</tr>
</table>

</form>
</body>
</html>
