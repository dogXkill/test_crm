<?

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

$id = $_GET['id'];
$applications = str_replace('slsh', '||', $_GET['applications']);
$jobs = str_replace('slsh', '||', $_GET['jobs']);
$tiraz = str_replace('slsh', '||', $_GET['tiraz']);
$shippingDate = str_replace('slsh', '||', $_GET['shippingDate']);
if ($shippingDate == '0000-00-00') {
  $shippingDate = date('Y-m-d');
}
$division = $_GET['division'];
$status = str_replace('slsh', '||', $_GET['status']);
$create = $_GET['create'];

switch ($create) {
  case 'Y':
    $q = sprintf("INSERT INTO shipments (applications, jobs, tiraz, shipping_date, division, status) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')", $applications, $jobs, $tiraz, $shippingDate, $division, $status);
    break;

  case 'N':
    $q = sprintf("UPDATE shipments SET applications = '%s', jobs = '%s', tiraz = '%s', shipping_date = '%s', division = '%s', status = '%s' WHERE id = '%s'", $applications, $jobs, $tiraz, $shippingDate, $division, $status, $id);
    break;
}

mysql_query($q);





?>
