<?
require_once("../../includes/db.inc.php");

$num_ord = $_POST["num_ord"];

$get_uid = mysql_query("SELECT uid AS uid, title AS title, exec_on AS exec_on, tiraz AS tiraz, paper_num_list AS paper_num_list FROM applications WHERE num_ord = '$num_ord'");
$get_uid = mysql_fetch_array($get_uid);

echo $get_uid[uid];
if (!$get_uid[uid]){
$error = "no_uid";
echo $error;
}
elseif ($get_uid[exec_on] == "1"){
$error = "uid_over";
echo $error;
}
else{
echo "ok;".$get_uid[title].";".$get_uid[tiraz].";".$get_uid[paper_num_list];
}
 ?>