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

  <script type="text/javascript" src="../../includes/js/jquery-1.9.1.min.js"></script>
  <script src="../../includes/js/jquery.cookie.js"></script>
  <script type="text/javascript" src="../../includes/js/jscalendar/calendar.js"></script>
  <script type="text/javascript" src="../../includes/js/jscalendar/lang/calendar-ru.js"></script>
  <script type="text/javascript" src="../../includes/js/jscalendar/calendar-setup-art-stat.js"></script>

  <link rel="stylesheet" type="text/css" media="all" href="../../includes/js/jscalendar/calendar-blue.css">
</head>

<body>
<script type="text/javascript" src="../../includes/js/wz_tooltip.js"></script>


<? if ($user_type == "sup" || $user_type == "acc" || $user_type == "adm"){  ?>


<table cellpadding=3 cellspacing=0>

<tbody>
<tr>
<td class="table_title" style="width:50px">#</td>
<td class="table_title" style="width:200px">ФИО</td>
<td name="oklad_col" class="table_title" style="width:200px">Сумма</td>
<td name="oklad_col" class="table_title" style="width:200px">Подпись</td>
</tr>
<?
$tip_pl=$_GET['tip_p'];
$sql1 = "SELECT * FROM tip_pay WHERE id=$tip_pl";
						if($res1 = mysql_query($sql1)){
							{
								if (mysql_num_rows($res1) > 0){
									$row1 = mysql_fetch_assoc($res1);
									$name_pl=$row1['name'];
								}else{
									$name_pl="";
								}
							}
						}
echo "<h2>Дата: ".$_GET["date"]." {$name_pl}</h2>";
$date = $_GET["date"];
$sql="SELECT * FROM report2 
LEFT JOIN report_day_comment ON report_day_comment.id_report=report2.id
WHERE 
(pay1date='$date' OR  pay2date='$date' OR  pay3date='$date' OR  pay4date='$date' OR  pay5date='$date' OR  pay6date='$date' OR  pay7date='$date' OR  pay8date='$date') ";//AND report_day_comment.tip_pay=$tip_pl;
echo $sql;
//$report = mysql_query("SELECT * FROM report2 WHERE pay1date='$date' OR  pay2date='$date' OR  pay3date='$date' OR  pay4date='$date' OR  pay5date='$date' OR  pay6date='$date' OR  pay7date='$date' OR  pay8date='$date'");
$report=mysql_query($sql);
while ($row = mysql_fetch_array($report)) {
	
	//проверка на tip платежа
	//$data = mysql_query("SELECT * FROM ` report_day_comment` WHERE `id_uid` = '".$tip_pl."' ");
         // $res1 = mysql_fetch_assoc($data);
          //if (!empty($res1)) {
			  /*
if($row[pay1date] == $date ){$rpt[$row[uid]] = $row[pay1];}
if($row[pay2date] == $date){$rpt[$row[uid]] = $row[pay2];}
if($row[pay3date] == $date){$rpt[$row[uid]] = $row[pay3];}
if($row[pay4date] == $date){$rpt[$row[uid]] = $row[pay4];}
if($row[pay5date] == $date){$rpt[$row[uid]] = $row[pay5];}
if($row[pay6date] == $date){$rpt[$row[uid]] = $row[pay6];}
if($row[pay7date] == $date){$rpt[$row[uid]] = $row[pay7];}*/
if($row[pay1date] == $date && $row[tip_pay]==$tip_pl && $row[day]==1){$rpt[$row[uid]] = $row[pay1];}
if($row[pay2date] == $date  && $row[tip_pay]==$tip_pl && $row[day]==2){$rpt[$row[uid]] = $row[pay2];}
if($row[pay3date] == $date  && $row[tip_pay]==$tip_pl && $row[day]==3){$rpt[$row[uid]] = $row[pay3];}
if($row[pay4date] == $date  && $row[tip_pay]==$tip_pl && $row[day]==4){$rpt[$row[uid]] = $row[pay4];}
if($row[pay5date] == $date  && $row[tip_pay]==$tip_pl && $row[day]==5){$rpt[$row[uid]] = $row[pay5];}
if($row[pay6date] == $date  && $row[tip_pay]==$tip_pl && $row[day]==6){$rpt[$row[uid]] = $row[pay6];}
if($row[pay7date] == $date  && $row[tip_pay]==$tip_pl && $row[day]==7){$rpt[$row[uid]] = $row[pay7];}
if($row[pay8date] == $date  && $row[tip_pay]==$tip_pl && $row[day]==8){$rpt[$row[uid]] = $row[pay8];}

//$rpt[$row[uid]] = $row[pay1].$row[pay2].$row[pay3].$row[pay4].$row[pay5].$row[pay6];

}

echo mysql_error();
//print_r($rpt);
/*if($_GET["administration"] == "1"){array_push($vstavka, 'user_group = 1');}
if($_GET["proizvodstvo"] == "1"){array_push($vstavka, 'user_group = 2');}
if($_GET["nadomniki"] == "1"){array_push($vstavka, 'user_group = 3');}*/
//$vstavka = 'AND (' . implode(' OR ', $vstavka) . ')';

//if($_GET["all"] == "1"){$vstavka = "AND (user_group = 1 OR user_group = 2 OR user_group = 3)";}

/*if($_GET["administration"] == "1"){$vstavka1 = " AND administration = '1'";}
if($_GET["proizvodstvo"] == "1"){$vstavka2 = " AND proizv = '2'";}
if($_GET["nadomniki"] == "1"){$vstavka3 = " AND nadomn = '3'";}
if($_GET["all"] == "1"){$vstavka3 = ""; $vstavka2 = ""; $vstavka1 = "";}*/
//получаем список сотрудниов с базовыми параметрами
//$query = "SELECT uid, job_id, surname, name, doljnost FROM users WHERE archive != '1' AND job_id != '1000' $vstavka ORDER BY surname ASC";

$vstavka = '';
if (isset($_GET['department']))
{
  $deps = explode('_', $_GET['department']);
  foreach ($deps as $key => $value) {
    $deps[$key] = ' user_department = ' . $value;
  }
  $vstavka .= " AND (" . implode(' OR ', $deps) . ") ";
}
$query = "SELECT uid, job_id, surname, name, doljnost FROM users WHERE archive != '1' AND job_id != '1000' $vstavka ORDER BY surname ASC";
echo $query;
//$query = "SELECT uid, job_id, surname, name, doljnost FROM users WHERE archive != '1' AND job_id != '1000' $vstavka1 $vstavka2 $vstavka3  ORDER BY surname ASC";
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




</body>

</html>
