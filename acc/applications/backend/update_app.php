<?
require_once("../../includes/db.inc.php");

$str = $_SERVER['QUERY_STRING'];
parse_str($str);

if(is_numeric($num_ord)){
$upd_app = mysql_query("UPDATE applications SET $type = '$val' WHERE num_ord = '$num_ord'");

if($upd_app == "true"){echo "ok";}else{echo "������ � ������� ".mysql_error();}

}else{echo "�� ����� ����� ����� ������ -".$num_ord;}

?>