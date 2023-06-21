<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");


$post_id = $_POST['post_id'];
$q = "DELETE FROM doljnost WHERE id = '$post_id'";
$r = mysql_query($q);

?>
