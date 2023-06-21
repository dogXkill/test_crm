<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

$name = $_POST['name'];
//$sort = $_POST['sort'];

if (isset($_POST['post_id'])) {
  $post_id = $_POST['post_id'];
}

$name = iconv("utf-8", "cp1251", $name);


if (!isset($_POST['new']))
{
//  $q = "UPDATE user_posts SET name = '$name', sort = $sort WHERE id = '$post_id'";
  $q = "UPDATE doljnost SET name = '$name' WHERE id = '$post_id'";
  $r = mysql_query("$q");
}

if (isset($_POST['new']))
{
  $q = "SELECT MAX(id) FROM doljnost";
  $r = mysql_query($q);
  $arr = mysql_fetch_array($r);
  $newid = $arr[0] + 1;
//  $q = "INSERT INTO user_posts (id, name, sort) VALUES ($newid, '$name', $sort)";
  $q = "INSERT INTO doljnost (id, name) VALUES ($newid, '$name')";
  $r = mysql_query($q);
  echo $newid;
}
?>
