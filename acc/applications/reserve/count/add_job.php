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

$num_sotr = $_POST["num_sotr"];
$num_ord = $_POST["num_ord"];
$job = $_POST["job"];
$num_of_work = $_POST["num_of_work"];
$order_price = $_POST["order_price"];
$nadomn_num = $_POST["nadomn_num"];
$err = "";
$date_today = date("Y-m-d");

//�������� ������������� ������ (�������, �� ����������� �� ������� ������ �� ����� ���������� �� ��� �� ������)
$s = mysql_query("SELECT * FROM `job` WHERE job = '$job' AND job <> '14' AND num_sotr = '$num_sotr' AND num_ord = '$num_ord' AND num_of_work = '$num_of_work' AND cur_time LIKE '$date_today%'");
$s = mysql_fetch_array($s);
if($s[0] > 0){$err = "�� ������� ��� ��������� ����� �� ������ ������� ����������. ������ �� �������.";}


//�������� ����� � ������������ ���������� �������� (������� / ��������� / ����� )
$app = mysql_query("SELECT title, tiraz, limit_per, paper_num_list, lamination_tp, tiraz*paper_num_list AS max_lami, tiraz*paper_num_list AS max_virub, stamp, tiraz*paper_num_list AS max_tisn, hand_mount_tp FROM `applications` WHERE num_ord = '$num_ord'");
$app = mysql_fetch_array($app);


$limit_per = $app[limit_per];
if($limit_per > 0){$limit = 1+$limit_per/100;}else{$limit = "1";}

$lamin_is = $app[lamination_tp]*$limit;
$tisn_is =  $app[stamp]*$limit;
$hands_is = $app[hand_mount_tp];
$max_lami = $app[max_lami]*$limit;
$max_virub = $app[max_virub]*$limit;
$max_tisn = $app[max_tisn]*$limit;
$max_hands = $app[tiraz]*$limit;
$max_sborka = $app[tiraz]*$limit;
$max_upak = $app[tiraz]*$limit;



$lami = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '1' AND num_ord = '$num_ord'");
$lami = mysql_fetch_array($lami);

$virub = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '2' AND num_ord = '$num_ord'");
$virub = mysql_fetch_array($virub);

$tisn = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '3' AND num_ord = '$num_ord'");
$tisn = mysql_fetch_array($tisn);

$hands = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '15' AND num_ord = '$num_ord'");
$hands = mysql_fetch_array($hands);

$sborka = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '4' AND num_ord = '$num_ord'");
$sborka = mysql_fetch_array($sborka);

$upak = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '11' AND num_ord = '$num_ord'");
$upak = mysql_fetch_array($upak);

//�������� ���� �� � ������ ���������
if($lamin_is == "0" and $job == '1'):
$err = "� ������ ������ �� ������������� ���������. ������ �� �������. ���� ��� ������, �������� ����������� ��� ����������� ������.";
//�������� ��� �� ���������� ���������
elseif((($lami[0] + $num_of_work) > $max_lami) and $job == '1'):
$err = "���� �������� ��� ������, �� ����� ���-�� ���������������� ������ �������� ����������� ��� ���������� ������. ������ �� �������. ��� �������� ���� ������ ��������� ���������� �����������.";
//�������� ��� �� ���������� �������
elseif((($virub[0] + $num_of_work) > $max_virub) and $job == '2'):
$err = "���� �������� ��� ������, �� ����� ���-�� ����������� ������ �������� ����������� ��� ���������� ������. ������ �� �������. ��� �������� ���� ������ ��������� ���������� �����������.";
/*elseif((($virub[0] + $num_of_work) > $lami[0]) and $job == '2'):
$err = "���� �������� ��� ������, �� ����� ���-�� ����������� ������ �������� ���������� ���������������� ������. ������ �� �������. ��� �������� ���� ������ ��������� ���������� �����������.";*/
//�������� ���� �� � ������ ��������
elseif($tisn_is == "0" and $job == '3'):
$err = "� ������ ������ �� ������������� ��������. ������ �� �������. ���� ��� ������, �������� ����������� ��� ����������� ������.";
//�������� ��� �� ���������� ��������
/*elseif((($tisn[0] + $num_of_work) > $max_tisn) and $job == '3'):
$err = "���� �������� ��� ������, �� ����� ���-�� ����������� ������ �������� ����������� ��� ���������� ������. ������ �� �������. ��� �������� ���� ������ ��������� ���������� �����������."; */
//���� �� � ������ ����� � ��������
elseif($hands_is !== "2" and $job == '15'):
$err = "� ������ ������ �� ������������� ����� � ��������. ������ �� �������. ���� ��� ������, �������� ����������� ��� ����������� ������.";
//����� � �������� ������� ������ ��� �����
/*elseif((($hands[0] + $num_of_work) > $max_hands) and $job == '15'):
$err = "���� �������� ��� ������, �� ����� ���-�� ����� � �������� �������� ����������� ��� ���������� ������. ������ �� �������. ��� �������� ���� ������ ��������� ���������� �����������."; */
//�������� �� ������� �� ������ ��� �����
elseif((($sborka[0] + $num_of_work) > $max_sborka) and $job == '4'):
$err = "���� �������� ��� ������, �� ����� ��������� ������� �������� �����. ������ �� �������. ��� �������� ���� ������ ��������� ���������� �����������.";
//��������� ������ ���  �����
elseif((($upak[0] + $num_of_work) > $max_upak) and $job == '11'):
$err = "���� �������� ��� ������, �� ����� ���������� ����������� ������� �������� �����. ������ �� �������. ��� �������� ���� ������ ��������� ���������� �����������.";
endif;




if($err == ""){
//���� ������ ���, �� ��������� ������
$insert = mysql_query("INSERT INTO job(num_sotr, num_ord, job, num_of_work, order_price)VALUES ('$num_sotr','$num_ord','$job','$num_of_work', '$order_price')")or die("������ MySql: " . mysql_error());
echo "ok";
//���� ����������� �������� ������, �� ����� ����������� � ���, ��� ����� ��
if ($nadomn_num > "0"){
$job = '4';
$insert_nadomn = mysql_query("INSERT INTO job(num_sotr, num_ord, job, num_of_work, nadomn, order_price)VALUES ('$nadomn_num','$num_ord','$job','$num_of_work', '1', '$order_price')")or die("������ MySql: " . mysql_error());
}}
else{
  echo "error;".$err;
  $error = mysql_query("INSERT INTO job_error_log(error, user, num_ord, num_sotr, job, num_of_work) VALUES ('$err','$user_id', '$num_ord', '$num_sotr', '$job', '$num_of_work')");
  echo mysql_error();
}

?>