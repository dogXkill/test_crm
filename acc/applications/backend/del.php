<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
$uid = $_GET["job_uid"];
$pass = $_GET["pass"];





$chk =  mysql_fetch_array(mysql_query("SELECT nadomn, related_to FROM job WHERE uid = '$uid'"));
if($chk[0] == "1"){
  if($chk[1] > 0)
//удаляем сопутствуюущую запись по внесению надомной работы
$del = mysql_query("DELETE FROM job WHERE uid = '$chk[1]'");

}


$del = mysql_query("DELETE FROM job WHERE uid = '$uid'");

if($del == true){
$uids_to_hide = "$uid,$chk[1]";

echo $uids_to_hide;}else{echo mysql_error();}



?>