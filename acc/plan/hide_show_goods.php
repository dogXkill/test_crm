<?
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");

$uid = $_GET["uid"];
$new_vis = $_GET["new_vis"];

if($uid){

$current_vis = mysql_query("SELECT vis FROM plan_arts WHERE uid = '$uid'");
$current_vis = mysql_fetch_array($current_vis);
$tek_vis = $current_vis[0];

if($tek_vis == '' or $tek_vis == '1'){$new_vis = '0';}
if($tek_vis == '0'){$new_vis = '1';}

$q = mysql_query("UPDATE plan_arts SET vis = '$new_vis' WHERE uid = '$uid'");
echo mysql_error();
echo $new_vis;
}
?>