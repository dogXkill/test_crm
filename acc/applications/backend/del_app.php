<?
require_once("../../includes/db.inc.php");

//�������� �� ����� ����� ������, ��������� json ������ � ���������� ��� ������� ��� ���������� ����� ������
$num_ord = $_GET["num_ord"];

if(is_numeric($num_ord)){
$del_app = mysql_query("DELETE FROM applications WHERE num_ord = '$num_ord'");
$del_job = mysql_query("DELETE FROM job WHERE num_ord = '$num_ord'");

if($del_app == "true" and $del_job == "true"){echo "ok";}else{echo "������ � ������� ".mysql_error();}

}else{echo "�� ����� ����� ����� ������ -".$num_ord;}

?>