<?
$auth = false;
require_once("../acc/includes/db.inc.php");
require_once("../acc/includes/auth.php");

//������ ������� ��������� ������� uniq_id � queries ��� ��� ���� �� ���������. ��� ���������� ��� ������������� ������ �� ������. � ���������� ������ �������� ����� �� �����, �.�. u
//uniq_id ������ ���������� ������������� ��� �������� ������ ������

$q = "SELECT uid, uniq_id FROM queries WHERE uniq_id = ''";

$no_id = mysql_query($q);

while($ord = mysql_fetch_array($no_id)){

$uid = $ord[0];

if($uid !== "") {

$uniq_id = md5(uniqid(rand(), 1));

$upd = mysql_query("UPDATE queries SET uniq_id = '$uniq_id' WHERE uid = '$uid'");


}

}

echo mysql_error();?>