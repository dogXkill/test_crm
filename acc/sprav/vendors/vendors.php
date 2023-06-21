<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

$act = $_GET["act"];
$id = $_GET["id"];
$name = $_GET["name"];
$cont_person = $_GET["cont_person"];
$cont_phone = $_GET["cont_phone"];

if($act == "add"){
$add = mysql_query("INSERT INTO vendors(name, cont_person, cont_phone) VALUES ('$name','$cont_person','$cont_phone')");
if($add == true){echo mysql_insert_id();}else{echo mysql_error();}
}

if($act == "save"){
$update = mysql_query("UPDATE vendors SET name='$name', cont_person='$cont_person', cont_phone='$cont_phone' WHERE id = '$id'");
if($update == true){echo "EDIT_OK";}else{echo mysql_error();}
}

if($act == "del"){
$del_groups = mysql_query("DELETE FROM vendors WHERE id = '$id'");
//$del_arts = mysql_query("UPDATE vendors SET grup='' WHERE grup = '$id'");

if($del_groups == "true"){echo "DEL_OK";}else{echo mysql_error();}
}

?>