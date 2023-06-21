<?
if ($_GET["pass"] == "4309"){
$j_uid = $_GET["j_uid"];
$num_sotr = $_GET["num_sotr"];
$num_ord = $_GET["num_ord"];
$job = $_GET["job"];
$num_of_work = $_GET["num_of_work"];
$cur_time = $_GET["cur_time"];
//приводим обратно в формат 2017-06-02 10:25:48
$cur_time = new DateTime($cur_time);
$cur_time = $cur_time->Format('Y-m-d G:i:00');
require_once("../../includes/db.inc.php");
$q = "UPDATE job SET num_sotr='$num_sotr', num_ord='$num_ord', job='$job', num_of_work='$num_of_work', cur_time = '$cur_time' WHERE j_uid = '$j_uid'";
$edit = mysql_query($q);
//echo $q.mysql_error();
if ($edit == true){echo "ok";}else{echo "Ошибка в запросе!";echo mysql_error();}

}else{echo "Пароль не верен!";}
?>