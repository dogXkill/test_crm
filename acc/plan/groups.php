<?
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");

$act = $_GET["act"];
$id = $_GET["id"];
$gname = $_GET["gname"];
$gdesc = $_GET["gdesc"];

if($act == "add"){
$add = mysql_query("INSERT INTO plan_groups(gname, gdesc) VALUES ('$gname','$gdesc')");
if($add == true){echo mysql_insert_id();}else{echo mysql_error();}
}

if($act == "save"){
$update = mysql_query("UPDATE plan_groups SET gname='$gname', gdesc='$gdesc' WHERE id = '$id'");
if($update == true){echo "EDIT_OK";}else{echo mysql_error();}
}

if($act == "del"){
$del_groups = mysql_query("DELETE FROM plan_groups WHERE id = '$id'");
$del_arts = mysql_query("UPDATE plan_arts SET grup='' WHERE grup = '$id'");

if($del_groups == "true"){echo "DEL_OK";}else{echo mysql_error();}
}

?>