<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

$name = $_POST['name'];
$sort = $_POST['sort'];

if (isset($_POST['group_id'])) {
  $group_id = $_POST['group_id'];
}

$name = iconv("utf-8", "cp1251", $name);


if (!isset($_POST['new']))
{
  $q = "UPDATE user_groups SET name = '$name', sort = $sort WHERE id = '$group_id'";
  $r = mysql_query("$q");
}

if (isset($_POST['new']))
{
  $q = "SELECT MAX(id) FROM user_groups";
  $r = mysql_query($q);
  $arr = mysql_fetch_array($r);
  $newid = $arr[0] + 1;
  $q = "INSERT INTO user_groups (id, name, sort) VALUES ($newid, '$name', $sort)";
  $r = mysql_query($q);
  echo $newid;
}
?>
