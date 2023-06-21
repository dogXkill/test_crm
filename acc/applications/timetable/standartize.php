<!DOCTYPE HTML>

<html>

<head>
  <title></title>
</head>

<body>
<? require_once("../../includes/db.inc.php");

$report = mysql_query("SELECT year, month, uid FROM report");
while ($r = mysql_fetch_array($report)) {
$year = $r["year"];
$month = $r["month"];
$uid = $r["uid"];
$nid = $year."-".$month."-".$uid;
echo $nid."<br>";

$q = mysql_query("UPDATE report SET nid = '$nid' WHERE year = '$year' AND month = '$month' AND uid = '$uid'");
}

echo mysql_error();
 ?>
</body>

</html>