<?
//if($_GET['type']){$type = $_GET['type'];}else{$type = "proizvodstvo";}
if (isset($_GET['department'])) {$department = '&department=' . $_GET['department'];}
if($_GET["year"]){$year=$_GET["year"];}else{$year=date("Y");}
if($_GET["month"]){$month=$_GET["month"];}else{$month=date("n");}
if (isset($_GET['name'])) {$name_s = '&name=' . $_GET['name'];}
$current_month = date('m'); $current_year = date('Y');
if(strlen ($month) < "2"){ $month = str_pad($month, 2, 0, STR_PAD_LEFT);}

if (stristr($_SERVER['REQUEST_URI'], 'index.php')) {
  if (!isset($_GET['year']) || !isset($_GET['month'])) {
    $gets = array();
    if (!stristr($_SERVER['QUERY_STRING'], 'year')) {array_push($gets, 'year='.$year);}
    if (!stristr($_SERVER['QUERY_STRING'], 'month')) {array_push($gets, 'month='.$month);}
    $path = (!empty($_SERVER['QUERY_STRING'])) ? 'index.php?' . $_SERVER['QUERY_STRING'] . '&' . implode('&', $gets) : 'index.php?' . implode('&', $gets);
    header("Location: $path");
  }
}

if (stristr($_SERVER['REQUEST_URI'], 'report.php')) {
  if (!isset($_GET['year']) || !isset($_GET['month'])) {
    $gets = array();
    if (!stristr($_SERVER['QUERY_STRING'], 'year')) {array_push($gets, 'year='.$year);}
    if (!stristr($_SERVER['QUERY_STRING'], 'month')) {array_push($gets, 'month='.$month);}
    $path = (!empty($_SERVER['QUERY_STRING'])) ? 'report.php?' . $_SERVER['QUERY_STRING'] . '&' . implode('&', $gets) : 'report.php?' . implode('&', $gets);
    header("Location: $path");
  }
}

$working_days_social = "29.4";
//штраф за прогул
$progul_multa = "500";
$months = array("01"=>"€нварь", "02"=>"февраль", "03"=>"март", "04"=>"апрель", "05"=>"май", "06"=>"июнь", "07"=>"июль", "08"=>"август", "09"=>"сент€брь", "10"=>"окт€брь", "11"=>"но€брь", "12"=>"декабрь");
$uid = $_GET["uid"];
$block = $_GET["block"];
$num_month = number_format($month);
if($num_month == 1){
$next_month = $num_month+1;
$next_month_link = " <a href=\"?type=".$type."&year=".$year.$department."&month=".$next_month."".$name_s."\">>>></a>";
$prev_month = "12";
$prev_year = $year - 1;
$prev_month_link = " <a href=\"?type=".$type."&year=".$prev_year.$department."&month=".$prev_month."".$name_s."\"><<<</a> ";
}
else if($num_month == 12){
$next_month = "1";
$next_year = $year+1;
$next_month_link = " <a href=\"?type=".$type."&year=".$next_year.$department."&month=".$next_month."".$name_s."\">>>></a>";
$prev_month = $num_month-1;
$prev_month_link = " <a href=\"?type=".$type."&year=".$year.$department."&month=".$prev_month."".$name_s."\"><<<</a> ";
}
else if($num_month < 12 and $num_month > 1){
$next_month = $num_month+1;
$next_month_link = " <a href=\"?type=".$type."&year=".$year.$department."&month=".$next_month."".$name_s."\">>>></a>";
$prev_month = $num_month-1;
$prev_month_link = " <a href=\"?type=".$type."&year=".$year.$department."&month=".$prev_month."".$name_s."\"><<<</a> ";
}


?>
