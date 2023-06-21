<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

$num_ord = $_GET['num_ord'];
$division = $_GET['division'];

if ($division != 0) {
  $add = "AND division = $division";
} else {
  $add = '';
}

/*$q = "SELECT * FROM applications WHERE num_ord = $num_ord AND archive != 1";
$r = mysql_fetch_assoc(mysql_query($r));
if (!empty($r)) {*/
  $q = "SELECT * FROM shipments WHERE jobs LIKE '%$num_ord" . "_%' $add";
  $r = mysql_query("$q");
  $out = array();
//  array_push($out, $q);
  while ($row = mysql_fetch_assoc($r))
  {
    $jobs = explode('||', $row['jobs']);
    foreach ($jobs as $key => $value)
    {
      if (stristr($value, $num_ord))
      {
        $list = explode('_', $value);
        $list = $list[1];
        foreach (explode('-', $list) as $k => $v)
        {
          array_push($out, $v);
        }
      }
    }
  }
//}


if (!empty($out)) {
  print_r(json_encode(array_unique($out)));
} else {
  echo 0;
}
?>
