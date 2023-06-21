<?
require_once("../../includes/db.inc.php");

if (isset($_GET['num_sending'])) {
  $num = $_GET['num_sending'];
  $q = "SELECT applications FROM shipments WHERE id = $num";
  $r = mysql_fetch_assoc(mysql_query($q));
  $apps = explode('||', $r['applications']);
  echo 'Доступные заявки в данной отправке: ' . implode(', ', $apps);
}
?>
