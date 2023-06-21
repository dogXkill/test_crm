<?
require_once("../../includes/db.inc.php");

$num_sotr = $_POST["num_sotr"];

//echo $art_id;
$get_sotr = mysql_query("SELECT uid, surname, name
FROM  `users`
WHERE job_id =  '$num_sotr'");
$get_sotr = mysql_fetch_array($get_sotr);

if (!$get_sotr[uid]){

$error = "no_sotr".mysql_error();
echo $error;
}
else{
$full_name = $get_sotr[name]." ".$get_sotr[surname];
echo $full_name;
}
 ?>