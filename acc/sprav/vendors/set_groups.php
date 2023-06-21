<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

$act = $_GET["act"];
$gid = $_GET["gid"];
$uid = $_GET["uid"];

if($act == "show"){
?>
<table>
<tr>
<td valign=top>
<?
//выбираем все привязанные к данному поставщику товары
$gidss = mysql_query("SELECT gid FROM vendor_gid WHERE id = '$uid'");
while($g =  mysql_fetch_array($gidss)){
$gids_arr[] = $g[gid];
}
//print_r($gids_arr);


$sel = mysql_query("SELECT * FROM vendor_types ORDER BY name ASC");
$i = 0;
while($r =  mysql_fetch_array($sel)){

$gids = $r["id"];
$gname = $r["name"];

if(is_array($gids_arr)){
if(in_array($gids,$gids_arr))
{$checked = "checked";}else{$checked = "";}
}

if($i == 12){echo "</td><td valign=top>";}
print("<input type=checkbox name=gid id=\"inp_".$gids."\" value=".$gids." ".$checked."> <label for=\"inp_".$gids."\" style=\"cursor:pointer\">".$gname."</label><br>");
$i = $i + 1;
}
echo mysql_error();

?>
</td>
</tr>
<tr><td colspan=3>
<button onclick="show_groups('<?=$uid?>', 'save')">сохранить!</button> <button onclick="hide_show_div('<?=$uid?>', 'hide')">закрыть</button></td></tr>
</table>
<?
}

if($act == "save"){

if($gid !== "" || $gid !== ","){
$gids = explode(",", $gid);

//чтобы не заморачиваться с редактированием / удалением, просто вычищаем для начала все типы, привязанные к данному поставщику
mysql_query("DELETE FROM vendor_gid WHERE id = '$uid'");

foreach($gids as $key => $value)
{
if (is_numeric ($value))
$update = mysql_query("INSERT INTO vendor_gid (gid, id) VALUES ('$value','$uid')");
}

}



if($update == true){


}

}

echo mysql_error();

?>