<?
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
if($user_id == '12' || $user_id == '11'){

$id = $_GET["id"];
$checked = $_GET["checked"];
$comment = $_GET["comment"];
$q = "INSERT INTO courier_check (id,checked,comment) VALUES ('$id','$checked','$comment') ON DUPLICATE KEY UPDATE checked='$checked',comment='$comment'";
$update = mysql_query($q);

if($update == true){echo "ok";}else{echo "error ".$q." ".mysql_error();}
}
?>