<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");


$group_id = $_POST['group_id'];
$q = "DELETE FROM user_groups WHERE id = '$group_id'";
$r = mysql_query($q);

?>
