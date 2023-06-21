<?
require_once("../includes/db.inc.php");
$done_tasks= $_GET["done_tasks"];
$act = $_GET["act"];
$query_id = $_GET["query_id"];


if($act == "update") {
//$del = mysql_query("DELETE done_ids FROM tasks WHERE query_id = '$query_id'");
$query = mysql_query("UPDATE tasks SET done_ids='$done_tasks' WHERE query_id = '$query_id'");
echo "OK";
}
if($act == "delete") {
//$del = mysql_query("DELETE done_ids FROM tasks WHERE query_id = '$query_id'");
$query = mysql_query("UPDATE tasks SET status='0' WHERE query_id = '$query_id'");
echo "OK";
}
if($act == "get_tasks") {

$curr_task_name = array( "0" => "Заключение договора (бланка заказа)",
"1" => "Выставление счета",
"2" => "Ожидание оплаты",
"3" => "Утверждение превью",
"4" => "Утвеждение спуска клиентом",
"5" => "Составить заявку на производство",
"6" => "Заказ печати",
"7" => "Вывоз листов из типографии",
"8" => "Контроль качества",
"9" => "Организация отгрузки",
"10" => "Заказ штампа",
"11" => "Заказ бумаги",
"12" => "Заказ клише",
"13" => "Заказ ручек",
"14" => "Посещение приладки",
"15" => "Заказ люверсов",
"16" => "Отвезти пакеты на нанесение",
"17" => "Изготовление сигнальника",
"18" => "Цветопроба");

   $tasks = mysql_query("SELECT done_ids, task_ids FROM tasks WHERE query_id = '$query_id'");
   $tasks = mysql_fetch_array($tasks);
   $done_tasks_ar = explode(",", $tasks["0"]);
   $tasks_ar = explode(",", $tasks["1"]);
   $undone = array_diff($tasks_ar, $done_tasks_ar);
   $spisok = '';
//   print_r  ($done_tasks_ar);
//  print_r  ($undone);

foreach ($done_tasks_ar as $t) {

     if(is_numeric($t)){
   $spisok = $spisok."<li class=\"ui-state-done\" id=\"li_".$query_id."_".$t."\" id=\"\"><input type=\"checkbox\" onchange=\"set_view('".$query_id."','".$t."')\" name=done value=\"".$t."\" id=\"chk_".$query_id."_".$t."\"  checked/><label for=\"chk_".$query_id."_".$t."\">".$curr_task_name[$t]."</label></li>";
}}

foreach ($undone as $t) {
  //  echo $t."<br>";
  if(is_numeric($t)){

$spisok = $spisok."<li class=\"ui-state-default\" id=\"li_".$query_id."_".$t."\"><input type=\"checkbox\" onchange=\"set_view('".$query_id."','".$t."')\" name=done value=\"".$t."\" id=\"chk_".$query_id."_".$t."\"/><label for=\"chk_".$query_id."_".$t."\">".$curr_task_name[$t]."</label></li>";
}}
echo $spisok."<input type=hidden name=hdn value=\"\"><input type=\"button\" onclick=\"save_tasks('".$query_id."')\" style=\"margin:0 25% 0 25%;width:50%;height:25px;\" value=\"сохранить\"/>";


  }



echo mysql_error();
?>