<?
require_once("../../includes/db.inc.php");
if (isset($_GET['email'])) {
  $email = $_GET['email'];
  $q = sprintf("SELECT email FROM users WHERE email = '%s'", $email);
  $r = mysql_fetch_assoc(mysql_query($q));
  $email = $r['email'];
  if (!empty($email)) {
    echo '1';
  } else {
    echo '0';
  }
}
?>
