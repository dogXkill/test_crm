<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //���� � �������
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
ob_start();
$auth = false;

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

$str = $_SERVER['QUERY_STRING'];
parse_str($str);
$err = "";

if($num_ord == "" or $job == "" or $num_sotr == "" or $num_of_work == ""){$err = "������������ ���� �� ���������";}else{

$date_today = date("Y-m-d");
//��������� �� �����, ������ ���� ��� �� ������ ���������
if($job !== '14'){
//�������� ������������� ������ (�������, �� ����������� �� ������� ������ �� ����� ���������� �� ��� �� ������)
$check_dubles = mysql_query("SELECT * FROM job WHERE job = '$job' AND job <> '14' AND num_sotr = '$num_sotr' AND num_ord = '$num_ord' AND num_of_work = '$num_of_work' AND cur_time LIKE '$date_today%'");
$obr = "����������";
//$q = "SELECT * FROM job WHERE job = '$job' AND job <> '14' AND num_sotr = '$num_sotr' AND num_ord = '$num_ord' AND num_of_work = '$num_of_work' AND cur_time LIKE '$date_today%'";
}else{
$check_dubles = mysql_query("SELECT * FROM job WHERE nadomn = '1' AND num_sotr = '$num_sotrnadomn' AND num_ord = '$num_ord' AND num_of_work = '$num_of_work' AND cur_time LIKE '$date_today%'");
$obr = "���������";
}
$dubles = mysql_num_rows($check_dubles);


if($dubles > 0){$err = "�� ������� ��� ��������� ����� �� ������ ������� $obr. ������ �� �������. <br>";}

//�������� ����� � ������������ ���������� �������� (������� / ��������� / ����� )
$app = mysql_query("SELECT title, archive,dressing, tiraz, limit_per, paper_num_list, lami_isdely_per_list, isdely_per_list, izd_lami_storon, virub_isdely_per_list, col_ottiskov_izd,
tiraz/lami_isdely_per_list AS max_lami,
tiraz/virub_isdely_per_list AS max_virub,
tiraz*col_ottiskov_izd AS max_tisn,
dat_ord AS dat_ord,
plan_in AS plan_in
FROM applications WHERE num_ord = '$num_ord'");
$app = mysql_fetch_array($app);

$dat_ord = $app[dat_ord];
$izd_lami_storon = $app[izd_lami_storon];
$archive = $app[archive];
$dressing=$app[dressing];
$limit_per = $app[limit_per];
if($limit_per > 0){$limit = 1+$limit_per/100;}else{$limit = "1.01";}


$max_lami = round($app[max_lami]*$limit);
$max_virub = round($app[max_virub]*$limit);
$max_tisn = round($app[max_tisn]*$limit);
$max_sborka = round($app[tiraz]*$limit);
$max_upak = round($app[tiraz]*$limit);




$lami = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '1' AND num_ord = '$num_ord'");
$lami = mysql_fetch_array($lami);

$virub = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '2' AND num_ord = '$num_ord'");
$virub = mysql_fetch_array($virub);

$tisn = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '3' AND num_ord = '$num_ord'");
$tisn = mysql_fetch_array($tisn);
 /*
$hands = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '15' AND num_ord = '$num_ord'");
$hands = mysql_fetch_array($hands);
*/
$sborka = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '4' AND num_ord = '$num_ord'");
$sborka = mysql_fetch_array($sborka);

$upak = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '11' AND num_ord = '$num_ord'");
$upak = mysql_fetch_array($upak);

$dressings = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '31' AND num_ord = '$num_ord'");
$dressings = mysql_fetch_array($dressings);

//������� % ����������� ����������� ������, ����� �� ��������� ��������, ����� ��������� ������ ��� ���������������
//�������������
/*$proc_lami = round($lami[0]*100/$max_lami,2);
//������� ����� ����������
$proc_virub = round(($virub[0]+$num_of_work)*100/$max_virub,2);*/




//�������� ��� �� ���������� ���������
if($izd_lami_storon == '1' and $job == '1'):
$err = "� ������ ������ �������, ��� ������������� ����� ������������� �� �������. ������ �� �������. ��� �������� ���� ������ ��������� ���������� �����������.";
//������ ��� ��� � ����� - ������ �������� ������
elseif($app[plan_in] == '1'):
$err = "������ �������� ������ �� ������, ������� ��� ������ � ������. ������ �� �������. ��� �������� ���� ������ ���������� ������ ������� � ������� &quot;����&quot; � ������ ������.";
elseif($app[dressing] == '0' and $job == '31'):
$err="��������� ����� ���������";
elseif($job == '31' and ($dressings[0]+$num_of_work > $app[tiraz])):
$err="��������� ��������� �����";
//�������� �� �������� �� ��������� �� �������
elseif((($lami[0] + $num_of_work) > $max_lami) and $job == '1'):
$err = "���� �������� ��� ������, �� ����� ���-�� ���������������� ������ �������� ����������� ��� ���������� ������ (<b>$max_lami</b>). ������ �� �������. ��� �������� ���� ������ ��������� ���������� �����������.";
//������ �������� �������� ������ ��� 2�� �� 1 ���
elseif($num_of_work > 2 and ($job == '7' or $job == '8' or $job == '9' or $job == '10')):
$err = "�������� ������ �������� ������ 2 �� ���. ������ �� �������.";
elseif($num_of_work > 4 and  $job == '25'):
$err = "�������� �� ����������� ������ �������� ������ 4 �� ���. ������ �� �������.";
elseif((($virub[0] + $num_of_work) > $max_virub) and $job == '2'):
$err = "���� �������� ��� ������, �� ����� ���-�� ����������� ������ �������� ����������� ��� ���������� ������ (<b>$max_virub</b>). ������ �� �������. ��� �������� ���� ������ ��������� ���������� �����������.";
/*elseif(($proc_virub>$proc_lami) and $job == '2'):
$err = "���� �������� ��� ������, �� ����� ���-�� ����������� ������ �������� ���������� ���������������� ������ ($proc_virub>$proc_lami). ������ �� �������. ��� �������� ���� ������ ��������� ���������� �����������.";
*/
//�������� ��� �� ���������� ��������
elseif((($tisn[0] + $num_of_work) > $max_tisn) and $job == '3'):
$err = "���� �������� ��� ������, �� ����� ���-�� ����������� ������ �������� ����������� ��� ���������� ������ (<b>$max_tisn</b>). ������ �� �������. ��� �������� ���� ������ ��������� ���������� �����������.";
//�������� �� ������� �� ������ ��� �����
elseif((($sborka[0] + $num_of_work) > $max_sborka) and ($job == '4' or $job == '14')):
$err = "���� �������� ��� ������, �� ����� ��������� ������� �������� �������� (<b>$max_sborka</b>). ������ �� �������. ��� �������� ���� ������ ��������� ���������� �����������.";
//��������� ������ ���  �����
elseif((($upak[0] + $num_of_work) > $max_upak) and $job == '11'):
$err = "���� �������� ��� ������, �� ����� ���������� ����������� ������� �������� ����� (<b>$max_upak</b>). ������ �� �������. ��� �������� ���� ������ ��������� ���������� �����������.";

elseif($archive == 1):
$err = "������ ������ ��������� � ������";

endif;




if($err == ""){
//���� ������ ���, �� ��������� ������
if (empty($num_sending)) {
  $insert = mysql_query("INSERT INTO job(num_sotr, num_ord, job, num_of_work, order_price, who_entered)VALUES ('$num_sotr','$num_ord','$job','$num_of_work', '$order_price', '$who_entered')")or die("������ MySql: " . mysql_error());
} else {
  $insert = mysql_query("INSERT INTO job(num_sotr, num_ord, job, num_of_work, order_price, who_entered, otpravka)VALUES ('$num_sotr','$num_ord','$job','$num_of_work', '$order_price', '$who_entered', '$num_sending')")or die("������ MySql: " . mysql_error());

}
$last = mysql_insert_id();
echo "ok;".$last;
$related_to = mysql_insert_id();
//���� ����������� �������� ������, �� ����� ����������� � ���, ��� ����� ��
if ($num_sotrnadomn > "0"){
$job = '4';
$insert_nadomn = mysql_query("INSERT INTO job(num_sotr, num_ord, job, num_of_work, nadomn, order_price, related_to, who_entered)VALUES ('$num_sotrnadomn','$num_ord','$job','$num_of_work', '1', '$order_price', '$related_to', '$who_entered')")or die("������ MySql: " . mysql_error());
}}
else{
   echo "error;".$err;
  echo mysql_error();
  $error = mysql_query("INSERT INTO job_error_log(error, user, num_ord, num_sotr, job, num_of_work) VALUES ('$err','$who_entered', '$num_ord', '$num_sotr', '$job', '$num_of_work')");
  $last_error = mysql_insert_id();
 // echo "!$last_error!";
  echo mysql_error();
}}

?>
