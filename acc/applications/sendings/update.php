<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

if (isset($_GET['oper']))
{
  $oper = $_GET['oper'];
  $id = $_GET['id'];
  switch ($oper) {
    case 'status':
      $status = $_GET['status'];
      if ($id !== 0 && $status !== 0)
      {
        $q = "UPDATE shipments SET status = $status WHERE id = $id";
        mysql_query("$q");
      }
      break;
    case 'inarchive':
      $q = "UPDATE shipments SET archive = 1 WHERE id = $id";
      mysql_query("$q");
      break;
    case 'fromarchive':
      $q = "UPDATE shipments SET archive = 0 WHERE id = $id";
      mysql_query("$q");
      break;
  }
}
?>
