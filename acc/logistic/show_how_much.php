<?
$date = format_date($_GET["date"]);
$courier_id = $_GET["courier_id"];

//грузим места на складе
$sklad = mysql_query("SELECT id, name FROM sklad");
while ( $row = mysql_fetch_row($sklad) ) {
$skl[$row[0]] = $row[1];
}

//грузим информацию о местах расположения на складе
$sklad_place = mysql_query("SELECT art_id, sklad_id, sklad FROM plan_arts");
while ( $row = mysql_fetch_row($sklad_place) ) {
$skl_place[$row[0]] = $row[1];
}


if($courier_id == "all"){
	$sql_vst = "";
    $courier_name = "все водители";
} else {

	$sql_vst = " AND c.courier_id = '".$courier_id."'";

	$name = mysql_query("SELECT name FROM couriers WHERE id = '$courier_id'");
	$name = mysql_fetch_array($name);
    $courier_name = $name[0];
}

echo "<h2>Информация о грузе <strong>".$date."</strong><br>Водитель: ".$courier_name."</h2>";
?>
* справка ориентировочная, сверяйтесь по накладным
<table id="tbl" border=1 width=800 cellpadding=5 cellspacing=0>
<tr>
<td align=center style="font-weight:bold;font-size:15px;">Артикул</td>
<td align=center style="font-weight:bold;font-size:15px;">Название</td>
<td align=center style="font-weight:bold;font-size:15px;">Количество</td>
<td align=center style="font-weight:bold;font-size:15px; width:400px;">Где можно искать</td>
</tr>
<?
$query = mysql_query("SELECT o.art_num AS art_num, o.name AS name, SUM(o.num) AS num FROM obj_accounts AS o, courier_tasks AS c, queries AS q WHERE q.courier_task_id = c.id AND o.query_id =  q.uid AND o.art_num REGEXP '[0-9]+' AND c.date = '$date' ".$sql_vst." GROUP BY o.art_num ORDER BY o.art_num ASC");

echo mysql_error();

//echo "SELECT o.art_num AS art_num, o.name AS name, SUM(o.num) AS num FROM obj_accounts AS o, courier_tasks AS c, queries AS q WHERE q.courier_task_id = c.id AND o.query_id =  q.uid AND o.art_num REGEXP '[0-9]+' AND c.date = '$date' ".$sql_vst." GROUP BY o.art_num ORDER BY o.art_num ASC";


while($r =  mysql_fetch_array($query)){
?>
<tr>
<td align=center style="font-weight:bold;font-size:15px;"><?=$r["art_num"];?></td>
<td style="font-weight:bold;font-size:15px;"><?=$r["name"];?></td>
<td align=center style="font-weight:bold;font-size:15px;"><?=$r["num"];?></td>
<td align=center style="font-weight:bold;font-size:15px; width:400px;">
<?
 $sklad_place = $skl_place[$r["art_num"]];
echo $skl[$sklad_place];?></td>
</tr>
<?}?>
</table><?

?>
