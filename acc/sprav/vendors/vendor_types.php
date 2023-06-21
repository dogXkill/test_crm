<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

$act = $_GET["act"];
$id = $_GET["id"];
$name = $_GET["name"];
$tbl_name = $_GET["tbl_name"];

if($act == "add"){
$add = mysql_query("INSERT INTO vendor_types (name) VALUES ('$name')");
if($add == true){echo mysql_insert_id();}else{echo mysql_error();}
}

if($act == "save"){
$update = mysql_query("UPDATE vendor_types SET name='$name' WHERE id = '$id'");
if($update == true){echo "EDIT_OK";}else{echo mysql_error();}
}

if($act == "del"){
$del_groups = mysql_query("DELETE FROM vendor_types WHERE id = '$id'");
//$del_arts = mysql_query("UPDATE vendors SET grup='' WHERE grup = '$id'");

if($del_groups == "true"){echo "DEL_OK";}else{echo mysql_error();}
}

?>