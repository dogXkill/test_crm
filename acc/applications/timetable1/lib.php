<?
if($_GET['type']){$type = $_GET['type'];}else{$type = "proizvodstvo";}
if($_GET["year"]){$year=$_GET["year"];}else{$year=date("Y");}
if($_GET["month"]){$month=$_GET["month"];}else{$month=date("n");}
$current_month = date('m'); $current_year = date('Y');
if(strlen ($month) < "2"){ $month = str_pad($month, 2, 0, STR_PAD_LEFT);}

$working_days_social = "29.4";
//����� �� ������
$progul_multa = "500";
$months = array("01"=>"������", "02"=>"�������", "03"=>"����", "04"=>"������", "05"=>"���", "06"=>"����", "07"=>"����", "08"=>"������", "09"=>"��������", "10"=>"�������", "11"=>"������", "12"=>"�������");
$uid = $_GET["uid"];
$block = $_GET["block"];
$num_month = number_format($month);
if($num_month == 1){
$next_month = $num_month+1;
$next_month_link = " <a href=\"?type=".$type."&year=".$year."&month=".$next_month."\">>>></a>";
$prev_month = "12";
$prev_year = $year - 1;
$prev_month_link = " <a href=\"?type=".$type."&year=".$prev_year."&month=".$prev_month."\"><<<</a> ";
}
else if($num_month == 12){
$next_month = "1";
$next_year = $year+1;
$next_month_link = " <a href=\"?type=".$type."&year=".$next_year."&month=".$next_month."\">>>></a>";
$prev_month = $num_month-1;
$prev_month_link = " <a href=\"?type=".$type."&year=".$year."&month=".$prev_month."\"><<<</a> ";
}
else if($num_month < 12 and $num_month > 1){
$next_month = $num_month+1;
$next_month_link = " <a href=\"?type=".$type."&year=".$year."&month=".$next_month."\">>>></a>";
$prev_month = $num_month-1;
$prev_month_link = " <a href=\"?type=".$type."&year=".$year."&month=".$prev_month."\"><<<</a> ";
}

 
?>