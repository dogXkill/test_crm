<?
require_once("../includes/db.inc.php");
$query_id = $_GET["query_id"];
$task_ids = $_GET["task_ids"];
$unselected_task_ids = $_GET["unselected_task_ids"];
$act = $_GET["act"];
$get = $_GET["get"];
//echo "act".$act." ".$query_id." task_ids".$task_ids." unselected_task_ids".$unselected_task_ids;
if($get == "1"){
$query = mysql_query("SELECT task_ids AS task_ids, unselected_task_ids AS unselected_task_ids FROM tasks WHERE query_id = '$query_id'");
$query = mysql_fetch_array($query);
echo $query[task_ids].";".$query[unselected_task_ids];
}else{
if($act == "insert" && $task_ids) {
$query = mysql_query("INSERT INTO tasks(query_id, task_ids, unselected_task_ids) VALUES ('$query_id','$task_ids','$unselected_task_ids')");
}
if($act == "edit") {
$del = mysql_query("DELETE FROM tasks WHERE query_id = '$query_id'");
$query = mysql_query("INSERT INTO tasks(query_id, task_ids, unselected_task_ids) VALUES ('$query_id','$task_ids','$unselected_task_ids')");

}
}
echo mysql_error();
?>