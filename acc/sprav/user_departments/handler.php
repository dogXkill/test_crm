<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

$name = $_POST['name'];
$sort = $_POST['sort'];
$submission = $_POST['submission'];
$is_division = $_POST['is_division'];

if (isset($_POST['department_id'])) {
  $department_id = $_POST['department_id'];
}

$name = iconv("utf-8", "cp1251", $name);


if (!isset($_POST['new']))
{
  $q = "UPDATE user_departments SET name = '$name', sort = $sort, submission = $submission, is_division = $is_division WHERE id = '$department_id'";
  $r = mysql_query("$q");
}

if (isset($_POST['new']))
{
  $q = "SELECT MAX(id) FROM user_departments";
  $r = mysql_query($q);
  $arr = mysql_fetch_array($r);
  $newid = $arr[0] + 1;
  $q = "INSERT INTO user_departments (id, name, sort, submission, is_division) VALUES ($newid, '$name', $sort, $submission, $is_division)";
  $r = mysql_query($q);
  echo $newid;
}
?>
