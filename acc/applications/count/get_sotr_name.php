<?
require_once("../../includes/db.inc.php");

$num_sotr = $_GET["num_sotr"];
$type = $_GET["type"];
$user_id = $_GET["user_id"];
$account_access_dep = explode('|', $_GET['account_access_dep']);
foreach ($account_access_dep as $key => $value) {
  $account_access_dep[$key] = trim($value);
}
if($type == "nadomn"){$vst = " AND (user_group = 3 OR works_at_home = 1 OR nadomn = 1) ";}

//echo $num_sotr;
$q = mysql_query("SELECT uid, surname, name, archive, user_department FROM users WHERE job_id =  '$num_sotr' $vst");


if (mysql_num_rows($q) == 0)  {
  if($type == "nadomn"){
    $error = "error;<span class=\"result_err\" id=sotr_err_span>Сотрудник с допуском на надомную работу с номером <b>$num_sotr</b> не найден!</span>".mysql_error();
  } else {
    $error = "error;<span class=\"result_err\" id=sotr_err_span>Сотрудник с номером <b>$num_sotr </b> не найден!</span>".mysql_error();
  }
  echo $error;
} else {
  $get_sotr = mysql_fetch_assoc($q);
  $sotr_department = $get_sotr['user_department'];

  if (!in_array($sotr_department, $account_access_dep)) {
    $error = "error;<span class=\"result_err\" id=sotr_err_span>Данный сотрудник не входит в ваш отдел!</span>";
    echo $error;
  }

  $full_name = $get_sotr[name]." ".$get_sotr[surname];

  if($get_sotr[archive] == "1"){
    $error = "error;<span class=\"result_err\" id=sotr_err_span>Сотрудник <b>$full_name</b> найден, однако, он был удален архив!</span>";
    echo $error;
  } else {

  }
  if (empty($error)) {
    echo "ok;".$full_name;
  }
}
 ?>
