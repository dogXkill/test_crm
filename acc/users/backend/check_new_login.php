<?php
require_once("../../includes/db.inc.php");
if (isset($_GET['login'])) {
  $login = $_GET['login'];
  $q = sprintf("SELECT login FROM users WHERE login = '%s'", $login);
  $r = mysql_fetch_assoc(mysql_query($q));
  $login = $r['login'];
  if (!empty($login)) {
    echo '1';
  } else {
    echo '0';
  }
}
 ?>
