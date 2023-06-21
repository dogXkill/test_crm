<?
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php"); ?>

<html>

<head>
  <title>Информация со склада</title>
</head>
<link href="../style.css" rel="stylesheet" type="text/css" />
<body>
<? $type = $_GET["type"];?>

<a href="?type=all" <?if($type == "all"){echo "style=\"font-weight:bold;\"";}?>>все</a> | <a href="?type=full" <?if($type == "full"){echo "style=\"font-weight:bold;\"";}?>>только заполненные</a>
 <br>
<table id="tbl" border=1 width=1100 cellpadding=5 cellspacing=0>
<tr>
<td align=center style="font-weight:bold;font-size:15px;">Артикул</td>
<td align=center style="font-weight:bold;font-size:15px;">Название</td>
<td align=center style="font-weight:bold;font-size:15px; width:400px;">Где можно искать</td>
</tr>
<?
$sklad = mysql_query("SELECT * FROM sklad");
while ( $row = mysql_fetch_row($sklad) ) {
$skl[$row[0]] = $row[1];
}
/*
echo "<pre>";
print_r ($skl);
echo "</pre>";
*/

if($type == "all"){$sql_vst = "";}else{$sql_vst = " WHERE sklad_id <> ''";}

$arts = mysql_query("SELECT art_id, uid, title, sklad, sklad_id FROM plan_arts".$sql_vst);
while($r =  mysql_fetch_array($arts)){
?>
<tr>
<td align=center><?=$r["art_id"];;?></td>
<td align=center><?=$r["title"];;?></td>
<td><?
$sid=explode(",",$r["sklad_id"]);
foreach($sid as $key => $value)
{
echo $skl[$value]."<br>";
}
}
?></td>
</tr>

</table>
</body>

</html>