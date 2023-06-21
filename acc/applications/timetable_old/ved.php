<?
$auth = false;
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("lib.php");
?>
<html>
<head>
  <title>Отчет</title>
  <link href="style.css" rel="stylesheet" type="text/css" />
  <script src="../../includes/js/autoblock.js"></script>
  <script type="text/javascript" src="../../includes/js/jquery-1.9.1.min.js"></script>
  <script src="../../includes/js/jquery.cookie.js"></script>
  <script type="text/javascript" src="../../includes/js/jscalendar/calendar.js"></script>
  <script type="text/javascript" src="../../includes/js/jscalendar/lang/calendar-ru.js"></script>
  <script type="text/javascript" src="../../includes/js/jscalendar/calendar-setup-art-stat.js"></script>

  <link rel="stylesheet" type="text/css" media="all" href="../../includes/js/jscalendar/calendar-blue.css">
</head>

<body onload="sum()">
<script type="text/javascript" src="../../includes/js/wz_tooltip.js"></script>
<div id=block_div style="display:<?if($_COOKIE["auth"] == "on"){echo "block";}else{echo "none";}?>">
<h2>
</h2>

<? if ($user_type == "sup" || $user_type == "acc" || $user_type == "adm"){  ?>


<table cellpadding=3 cellspacing=0>

<tbody>
<tr>
<td class="table_title" style="width:50px">#</td>
<td class="table_title" style="width:200px">ФИО</td>
<td name="oklad_col" class="table_title" style="width:200px">Сумма</td>
<td name="oklad_col" class="table_title" style="width:200px">Галочка</td>
</tr>
<?
echo "<h2>Дата: ".$_GET["date"]."</h2>";
$date = $_GET["date"];

$report = mysql_query("SELECT * FROM report WHERE pay1date='$date' OR  pay2date='$date' OR  pay3date='$date' OR  pay4date='$date' OR  pay5date='$date' OR  pay6date='$date' OR  pay6date='$date'");
while ($row = mysql_fetch_array($report)) {
if($row[pay1date] == $date){$rpt[$row[uid]] = $row[pay1];}
if($row[pay2date] == $date){$rpt[$row[uid]] = $row[pay2];}
if($row[pay3date] == $date){$rpt[$row[uid]] = $row[pay3];}
if($row[pay4date] == $date){$rpt[$row[uid]] = $row[pay4];}
if($row[pay5date] == $date){$rpt[$row[uid]] = $row[pay5];}
if($row[pay6date] == $date){$rpt[$row[uid]] = $row[pay6];}
//$rpt[$row[uid]] = $row[pay1].$row[pay2].$row[pay3].$row[pay4].$row[pay5].$row[pay6];

}

echo mysql_error();
//print_r($rpt);


if($_GET["administration"] == "1"){$vstavka1 = " AND administration = '1'";}
if($_GET["proizvodstvo"] == "1"){$vstavka2 = " AND proizv = '1'";}
if($_GET["nadomniki"] == "1"){$vstavka3 = " AND nadomn = '1'";}
//получаем список сотрудниов с базовыми параметрами
$query = "SELECT uid, job_id, surname, name, doljnost FROM users WHERE archive != '1' AND job_id != '1000' $vstavka1 $vstavka2 $vstavka3  ORDER BY surname ASC";
$res = mysql_query($query);
while($us = mysql_fetch_array($res)) {

if($rpt[$us[job_id]] > 0){?>
<tr style="height:40px;">
<td align=center><?=$us[job_id];?></td>
<td align=center><?=$us[surname];?> <?=$us[name];?></td>
<td align=center><?=$rpt[$us[job_id]]; $sum = $rpt[$us[job_id]]+$sum;?></td>
<td style="border-bottom: 1px solid black;"></td>
</tr>
<?} } ?>
<tr style="height:50px;">
<td></td>
<td></td>
<td align=center><strong><?=$sum;?></strong></td>
<td></td>
</tr>
</tbody>
</table>



<? } else { ?>  доступ ограничен    <? } ?>

</div>

<? include("auth_form.php"); ?>
 <pre>
 <?//print_r ($rpt);?>
 </pre>




</body>

</html>