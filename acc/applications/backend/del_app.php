<?
require_once("../../includes/db.inc.php");

//получаем из файла номер заявки, формируем json массив и отправляем его обратно для заполнения формы заявки
$num_ord = $_GET["num_ord"];

if(is_numeric($num_ord)){
$del_app = mysql_query("DELETE FROM applications WHERE num_ord = '$num_ord'");
$del_job = mysql_query("DELETE FROM job WHERE num_ord = '$num_ord'");

if($del_app == "true" and $del_job == "true"){echo "ok";}else{echo "Ошибка в запросе ".mysql_error();}

}else{echo "Не верно задан номер заявки -".$num_ord;}

?>