<?
require_once("../../includes/db.inc.php");
if (isset($_GET['oper']) ) {
  $oper = $_GET['oper'];
  if (isset($_GET['uid']) && is_numeric($_GET['uid'])) {
    $uid = $_GET['uid'];
    switch ($oper) {
      case 'archive':
        $query = "UPDATE users SET archive = 1, email = '' WHERE uid = $uid";
        mysql_query($query);
        break;
      case 'restore':
        $query = "UPDATE users SET archive = 0 WHERE uid = $uid";
        mysql_query($query);
        break;
      case 'delFinal':
        $query = "DELETE FROM users WHERE uid = $uid";
        mysql_query($query);
        break;
    }
  }
}
?>
