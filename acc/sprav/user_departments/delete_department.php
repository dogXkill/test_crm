<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

$department_id = $_POST['department_id'];

$q = "SELECT COUNT(uid) as d FROM users WHERE archive <> '1' AND user_department = '$department_id'";
$r = mysql_fetch_assoc(mysql_query($q));
$numb = $r['d'];

if ($numb == 0) {
  $q = "DELETE FROM user_departments WHERE id = '$department_id'";
  $r = mysql_query($q);
}

echo $numb;
?>
