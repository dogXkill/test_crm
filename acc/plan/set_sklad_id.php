<?
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");

$act = $_GET["act"];
$id = $_GET["sid"];
$uid = $_GET["uid"];

//смотрим какие уже места товар занимает на складе
$s_id = mysql_query("SELECT sklad_id FROM plan_arts WHERE uid = '$uid'");
$s_id = mysql_fetch_array($s_id);
$s_id = $s_id[0];
$s_ids=explode(",",$s_id);

if($act == "show"){
$sel = mysql_query("SELECT * FROM sklad");
while($r =  mysql_fetch_array($sel)){


$ids = $r["id"];
$name = $r["name"];


$selected = "";
//отмечаем €чейки
foreach($s_ids as $key => $value)
  {
$sid = $value;
if($sid == $ids){$selected = "checked";}
 }

print("<input type=checkbox name=sid id=\"inp_".$ids."\" value=".$ids." ".$selected."> <label for=\"inp_".$ids."\" style=\"cursor:pointer\">".$name."</label><br>");
}
print("<input type=checkbox name=sid id=\"inp_0\" value=\"0\" ".$selected."> <label for=\"inp_0\" style=\"cursor:pointer\">без группы</label><br><button onclick=\"show_sklad('".$uid."', 'save')\">сохранить!</button> <button onclick=\"hide_show_div_sklad('".$uid."', 'hide')\">закрыть</button>");
}

if($act == "save"){
$update = mysql_query("UPDATE plan_arts SET sklad_id='$id' WHERE uid = '$uid'");
if($update == true){echo "EDIT_OK";}else{echo mysql_error();}
}



?>