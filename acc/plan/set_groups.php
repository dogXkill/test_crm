<?
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");

$act = $_GET["act"];
$gid = $_GET["gid"];
$uid = $_GET["uid"];

if($act == "show"){
?>
<table>
<tr>
<td valign=top>
<?
$sel = mysql_query("SELECT * FROM plan_groups ORDER BY gname ASC");
$i = 0;
while($r =  mysql_fetch_array($sel)){

$gids = $r["id"];
$gname = $r["gname"];
if($gids == $gid){$selected = "selected";}else{$selected = "";}
if($i == 12){echo "</td><td valign=top>";}
print("<input type=radio name=gid id=\"inp_".$gids."\" value=".$gids." ".$selected."> <label for=\"inp_".$gids."\" style=\"cursor:pointer\">".$gname."</label><br>");
$i = $i + 1;
}
print("<input type=radio name=gid id=\"inp_0\" value=\"0\" ".$selected."> <label for=\"inp_0\" style=\"cursor:pointer\">без группы</label>");

?>
</td>
</tr>
<tr><td colspan=3>
<button onclick="show_groups('<?=$uid?>', 'save')">сохранить!</button> <button onclick="hide_show_div('<?=$uid?>', 'hide')">закрыть</button></td></tr>
</table>
<?
}

if($act == "save"){
$update = mysql_query("UPDATE plan_arts SET grup='$gid' WHERE uid = '$uid'");
if($update == true){

//получаем название группы
$get_group_name = mysql_query("SELECT gname FROM plan_groups WHERE id = '$gid'");
$gname =  mysql_fetch_array($get_group_name);
echo $gname[0]; echo mysql_error();}else{echo mysql_error();}
}



?>