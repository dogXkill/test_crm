<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

$id = $_GET['id'];
if (!empty($id) && is_numeric($id) && $id !== 0)
{
  $q = "DELETE FROM shipments WHERE id = $id ";
  mysql_query("$q");
}
?>
