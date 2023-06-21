<?
header('Content-Type: text/html; charset=utf-8');

require_once("../../includes/db.inc.php");
require_once("../../includes/lib.php");

$data = array();
$q = "SELECT MAX(id) as max from shipments";
$queryResult = mysql_query("$q");
if ($queryResult) {
    $r = mysql_fetch_assoc($queryResult);
    $data["newId"] = $r['max'] + 1;
}

// Подразделения
$data["divisionsList"] = array();
$divisions = array();
$q = "SELECT * FROM divisions ORDER BY sort ASC";
mysql_query("SET NAMES 'utf8'");
$r = mysql_query("$q");

while ($row = mysql_fetch_assoc($r))
{
  $division = array();
  $division['id'] = $row['id'];
  $division['name'] = $row['name'];
  $division['sort'] = $row['sort'];
  array_push($divisions, $division);
}
array_push($data["divisionsList"], $divisions);

// Статусы
$data['shipmentStatuses'] = array();
$shipmentStatuses = array();
$q = "SELECT * FROM shipment_status ORDER BY sort ASC";
$r = mysql_query("$q");
while ($row = mysql_fetch_assoc($r))
{
  $status = array();
  $status['id'] = $row['id'];
  $status['name'] = $row['name'];
  $status['sort'] = $row['sort'];
  array_push($shipmentStatuses, $status);
}
array_push($data['shipmentStatuses'], $shipmentStatuses);

// Типы изделий
$data['appTypes'] = array();
$q = "SELECT * FROM types";
$r = mysql_query("$q");
while ($row = mysql_fetch_assoc($r))
{
  $type = array();
  $type['id'] = $row['tid'];
  $type['name'] = $row['type'];
  array_push($data['appTypes'], $type);
}

// Сотрудники отделов
// Ростов
$addRostov = array();
$query = "SELECT * FROM users WHERE user_department = 22";
$result = mysql_query("$query");
while ($row = mysql_fetch_assoc($result))
{
  array_push($addRostov, 'num_sotr = ' . $row['job_id']);
}
$addRostov = implode(' OR ', $addRostov);
// Тверь
$addTver = array();
$query = "SELECT * FROM users WHERE user_department = 23";
$result = mysql_query("$query");
while ($row = mysql_fetch_assoc($result))
{
  array_push($addTver, 'num_sotr = ' . $row['job_id']);
}
$addTver = implode(' OR ', $addTver);

// Список отгрузок
$data["shipmentsList"] = array();
$shipments = array();
$q = "SELECT * FROM shipments ORDER BY shipping_date DESC";
$r = mysql_query("$q");
while ($row = mysql_fetch_assoc($r))
{
  $shipment = array();
  $shipment['id'] = $row['id'];
  $shipId = $shipment['id'];
  $shipment['applications'] = array();
  $shipment['applications'] = explode('||', $row['applications']);
  $shipment['jobs'] = array();
  $jobsData = explode('||', $row['jobs']);
  $shipment['jobs']['jobsList'] = array();
  foreach ($jobsData as $key => $value) {
    $jobAppId = explode('_', $value)[0];
    $jobNames = explode('_', $value)[1];
    $shipment['jobs']['jobsList'][$key]['applicationId'] = $jobAppId;
    $shipment['jobs']['jobsList'][$key]['jobsList'] = array();
    $applicationJobs = array();
    array_push($shipment['jobs']['jobsList'][$key]['jobsList'], explode('-', $jobNames) );
  }
  $shipment['shippingDate'] = $row['shipping_date'];
  $rawData = explode('-', $shipment['shippingDate']);
  switch ($rawData[1]) {
    case '01':
      $textDate = $rawData[2] . ' января';
      break;
    case '02':
      $textDate = $rawData[2] . ' февраля';
      break;
    case '03':
      $textDate = $rawData[2] . ' марта';
      break;
    case '04':
      $textDate = $rawData[2] . ' апреля';
      break;
    case '05':
      $textDate = $rawData[2] . ' мая';
      break;
    case '06':
      $textDate = $rawData[2] . ' июня';
      break;
    case '07':
      $textDate = $rawData[2] . ' июля';
      break;
    case '08':
      $textDate = $rawData[2] . ' августа';
      break;
    case '09':
      $textDate = $rawData[2] . ' сентября';
      break;
    case '10':
      $textDate = $rawData[2] . ' октября';
      break;
    case '11':
      $textDate = $rawData[2] . ' ноября';
      break;
    case '12':
      $textDate = $rawData[2] . ' декабря';
      break;
  }
  $shipment['textDate'] = $textDate;
  unset($textDate);

  $shipment['division'] = $row['division'];
  $shipment['status'] = $row['status'];
  foreach ($divisions as $key => $value) { if ($value['id'] == $shipment['division']) {$shipment['divisionName'] = $value['name'];} }
  foreach ($shipmentStatuses as $key => $value) { if ($value['id'] == $shipment['status']) {$shipment['statusName'] = $value['name'];} }
  $shipment['archive'] = $row['archive'];
  $shipment['percentDone'] = 'test';
  $shipment['invoiceLink'] = '';

  $tirazData = explode('||', $row['tiraz']);
  $shipment['tiraz'] = array();
  $shipment['countAllJobs'] = 0;
  foreach ($tirazData as $key => $value) {
    $tirazItem = array();
    $tirazAppId = explode('_', $value)[0];
    $tirazQuantity = explode('_', $value)[1];
    $tirazItem['tirazAppId'] = $tirazAppId;
    $tirazItem['tirazQuantity'] = $tirazQuantity;
    $tirazItem['countError'] = '';
    foreach ($shipment['jobs']['jobsList'] as $k => $v) {
      if ($v['applicationId'] == $tirazAppId) {
        $shipment['countAllJobs'] = $shipment['countAllJobs'] + $tirazQuantity * count($v['jobsList'][0]);
      }
    }
    array_push($shipment['tiraz'], $tirazItem);
  }

  switch ($shipment['division']) {
    case 1:
      $sotr = $addRostov;
      break;
    case 2:
      $sotr = $addTver;
      break;
  }
  $shipment['sobranoQuantity'] = array();
  foreach ($shipment['tiraz'] as $key => $value) {
    $elemid = $value['tirazAppId'];
    $quant = array();
    $query = "SELECT SUM(num_of_work) AS sum FROM job WHERE ( {$sotr} ) AND num_ord = {$elemid} AND job = 4 AND otpravka = {$shipId}";
    $queryResult = mysql_query($query);
    if ($queryResult) {
        $result = mysql_fetch_assoc($queryResult);
        $quant['numOrd'] = $elemid;
        $quant['quantity'] = (!empty($result['sum']) ? $result['sum'] : 0 );
        if ($quant['quantity'] > $value['tirazQuantity']) {$quant['quantity'] = $value['tirazQuantity'];}
        $quant['percent'] = ceil($quant['quantity'] / $value['tirazQuantity'] * 100);
        $quant['percent'] = (!empty($quant['percent'])) ? $quant['percent'] : 0;
        if ($quant['percent'] >= 100) {$quant['percent'] = 100;}
        array_push($shipment['sobranoQuantity'], $quant);
    }
  }

  $shipment['packedQuantity'] = array();
  foreach ($shipment['tiraz'] as $key => $value) {
    $elemid = $value['tirazAppId'];
    $quant = array();
    $query = "SELECT SUM(num_of_work) AS sum FROM job WHERE ({$sotr}) AND num_ord = {$elemid} AND job = 11 AND otpravka = {$shipId}";
    $queryResult = mysql_query("$query");
    if ($queryResult) {
        $result = mysql_fetch_assoc($queryResult);
        $quant['numOrd'] = $elemid;
        $quant['quantity'] = (!empty($result['sum']) ? $result['sum'] : 0);
        if ($quant['quantity'] > $value['tirazQuantity']) {
            $quant['quantity'] = $value['tirazQuantity'];
        }
        $quant['percent'] = ceil($quant['quantity'] / $value['tirazQuantity'] * 100);
        $quant['percent'] = (!empty($quant['percent'])) ? $quant['percent'] : 0;
        if ($quant['percent'] >= 100) {
            $quant['percent'] = 100;
        }
        array_push($shipment['packedQuantity'], $quant);
    }
  }

  $shipment['works'] = array();
  $shipment['countCurJobs'] = 0;                            // Количество начисленных работ
  foreach ($jobsData as $key => $value) {
    $jobAppId = explode('_', $value)[0];
    $jobNames = explode('_', $value)[1];
    $jobsId = explode('-', $jobNames);
    foreach ($jobsId as $k => $v) {
      $query = "SELECT SUM(num_of_work) AS sum FROM job WHERE otpravka = {$shipId} AND job = {$v} AND num_ord = {$jobAppId}";
      $queryResult = mysql_query($query);
      if ($queryResult) {
          $res = mysql_fetch_assoc($queryResult);
          $sum = (!empty($res['sum'])) ? $res['sum'] : 0;
          $shipment['countCurJobs'] = $shipment['countCurJobs'] + $sum;
      }
    }
  }

  $shipment['allJobsPercent'] = $shipment['countAllJobs'] === 0
      ? 0
      : ceil($shipment['countCurJobs'] / $shipment['countAllJobs'] * 100);
  if ($shipment['allJobsPercent'] > 100) {$shipment['allJobsPercent'] = 100;}

  array_push($shipments, $shipment);
}
array_push($data["shipmentsList"], $shipments);

// Типы работ
$data["jobTypes"] = array();
$jobTypes = array();
$q = "SELECT * FROM job_types ORDER BY sort ASC";
$r = mysql_query("$q");
while ($row = mysql_fetch_assoc($r))
{
  $jobType = array();
  $jobType['id'] = $row['id'];
  $jobType['sort'] = $row['sort'];
  $jobType['name'] = $row['name'];

  array_push($jobTypes, $jobType);
}
array_push($data["jobTypes"], $jobTypes);


// Работы
$data["jobNames"] = array();
$jobNames = array();

foreach ($jobTypes as $key => $value) {
  $type = $value['id'];
  $q = "SELECT * FROM job_names WHERE job_type = '{$type}' ORDER BY seq ASC";
  $r = mysql_query("$q");
  $jobHeading = array("name" => '--- ' . $value['name'] . ' ---', "disabled" => "disabled");
  array_push($jobNames, $jobHeading);
  while ($row = mysql_fetch_assoc($r))
  {
    $jobName = array();
    $jobName["id"] = $row["id"];
    $jobName["name"] = $row["name"];
    $jobName["type"] = $row["job_type"];

    foreach ($jobTypes as $key => $value) {
      if ($value['id'] == $jobName["type"]) { $jobName['typeName'] = $value['name']; }
    }
    array_push($jobNames, $jobName);
  }
}
array_push($data["jobNames"], $jobNames);


// Заявки на производство

$data["applicationsList"] = array();
$applicationsList = array();
$q = "SELECT uid, num_ord, dat_ord, izd_type, title, ClientName, art_id, text_on_izd, spec_req, deadline, tiraz, izd_w, izd_v, izd_b, izd_material, izd_lami FROM applications WHERE archive != 1 AND plan_in != 1 ORDER BY uid DESC";
$r = mysql_query("$q");
while ($row = mysql_fetch_assoc($r))
{
  $application = array();
  $application['id'] = $row['uid'];
  $application['numOrd'] = $row['num_ord'];

  $numOrd = $application['numOrd'];
  $query = "SELECT SUM(num_of_work) as packed FROM job WHERE num_ord = {$numOrd} AND job = 11";
  $queryResult = mysql_query("$query");
  if ($queryResult) {
      $res = mysql_fetch_array($queryResult);
      $application['packed'] = (!empty($res['packed'])) ? $res['packed'] : 0;
  }

  $query = "SELECT SUM(num_of_work) AS sobrano FROM job WHERE num_ord = {$numOrd} AND job = 4";
  $queryResult = mysql_query("$query");
  if ($queryResult) {
      $res = mysql_fetch_assoc($queryResult);
      $application['sobrano'] = (!empty($res['sobrano'])) ? $res['sobrano'] : 0;
  }

  $application['datOrd'] = $row['dat_ord'];
  $application['izdType'] = $row['izd_type'];
  $izdType = $row['izd_type'];
  $application['title'] = $row['title'];
  $application['clientName'] = (!empty($row['ClientName']) ? $row['ClientName'] : $row['art_id']);
  $application['textOnIzd'] = $row['text_on_izd'];
  $application['specReq'] = $row['spec_req'];
  $application['deadline'] = $row['deadline'];
  $application['tiraz'] = $row['tiraz'];
  $application['izdW'] = $row['izd_w'];
  $application['izdV'] = $row['izd_v'];
  $application['izdB'] = $row['izd_b'];

  $query = "SELECT * FROM types WHERE tid = {$izdType}";
  $queryResult = mysql_query("$query");
  if ($queryResult) {
      $arr = mysql_fetch_array($queryResult);
      $application['izdTypeName'] = $arr['type'];
  }

  $application['descriptionText'] = $application['izdTypeName'] . ' ' . $row['izd_w'] . 'x' . $row['izd_v'] . 'x' . $row['izd_b'] . ', ';
  if (!empty($row['ClientName'])) {$application['descriptionText'] .= $row['ClientName'];}
  if (!empty($row['text_on_izd'])) {$application['descriptionText'] .= ' (' . $row['text_on_izd'] . ')';}

  $artId = $row['art_id'];
  $artQ = "SELECT * FROM plan_arts WHERE art_id = '{$artId}'";
  $queryResult = mysql_query("$artQ");
  if ($queryResult) {
      $artR = mysql_fetch_array($queryResult);
      $artName = $artR['title'];
      $application['descriptionText'] .= (!empty($artName) ? $artName . ',' : '');
  }

  if (!empty($row['tiraz'])) {$application['descriptionText'] .= ' тираж ' . $row['tiraz'] . 'шт';}
  if (!empty($row['izd_material'])) {
    $material = $row['izd_material'];
    $matQ = "SELECT type FROM materials WHERE tid = {$material}";
    $queryResult = mysql_query("$matQ");
    if ($queryResult) {
        $matR = mysql_fetch_assoc($queryResult);
        $application['descriptionText'] .= ', ' . $matR['type'] ;
    }
  }
  if (!empty($row['izd_lami'])) {
    $lami = $row['izd_lami'];
    $lamiQ = "SELECT type FROM lamination WHERE tid = {$lami}";
    $queryResult = mysql_query("$lamiQ");
    if ($queryResult) {
        $lamiR = mysql_fetch_assoc($queryResult);
        $application['descriptionText'] .= ', ламинация ' . $lamiR['type'];
    }
  }


  array_push($applicationsList, $application);
}
array_push($data["applicationsList"], $applicationsList);

echo json_encode($data);
