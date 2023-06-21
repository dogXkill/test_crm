<html>
<head>
	<title>Расчет НДС</title>
</head>

<body>

&nbsp;<font face=arial size=2>Расчет: <strong>НДС</strong></font> | <a href="bum.php"><font face=arial size=2><strong>бумага</strong></font></a>

<form action=index.php method="post" name=form>
<input type=hidden name=act value=count>

<table width=300 border=0>
<tr>
<td colspan=2 height=40><font face=arial size=3><strong>Расчет НДС</strong></font></td>
</tr>
<tr>
<td><font face=arial size=2>Сумма</font></td>
<td><input type=text size=20 name=sum value="<?
$sum = (isset($_POST['sum'])) ? $_POST['sum'] : $_POST['sum'];
$sum = str_replace(" ", "" , str_replace(",", "." , $sum));
echo $sum; ?>"></td>
</tr>
<tr>
<td><font face=arial size=2>НДС (18%)</font></td>
<td><input type=text size=20 style="background-color:#d8d8d8;" name=vat value="<?if ($_POST['act']=="count") {$vat=$sum*18/118; echo round($vat, 2); }?>"></td>
</tr>
<tr>
<td><font face=arial size=2>Количество единиц</font></td>
<td><input type=text size=20 name=num value="<?$num=$_POST["num"]; echo $num;?>"></td>
</tr>
<tr>
<td><font face=arial size=2>Стоимость за ед.</font></td>
<td><input type=text size=20 style="background-color:#d8d8d8;" name=price1 value="<?if ($num!==""){echo @round($sum/$num, 2);}?>"></td>
</tr>
<tr>
<td><font face=arial size=2>Стоимость за ед. без НДС</font></td>
<td><input type=text size=20 style="background-color:#d8d8d8;" name=price2 value="<?if ($num!==""){$price2=@round(($sum-$vat)/$num, 2);  echo $price2;}?>"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type=submit value="Рассчитать!"></td>
</tr>
</table>

</form>

</body>
</html>
