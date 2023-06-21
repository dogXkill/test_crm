<?
header('Content-type: text/html; charset=windows-1251');
//данный скрипт добавляет текст путевого листа в таблицу mail_temp  с целью последующей отправки на выбранный емейл, текст письма сформирован в task_list.tpl и отправлен при помощи аякса
require_once("../includes/db.inc.php");

$bod = iconv("UTF-8", "windows-1251", $_POST["mail_text"]);
$tema = iconv("UTF-8", "windows-1251", $_POST["task_date"]);
//$tema = $_POST["task_date"];

require_once("../backend/send_notifications.php");
send_mail($tema, $bod, $user_id);



if($mail_text !== "" and $task_date !== ""){
$insert = mysql_query("INSERT INTO `mail_temp`(tema, komu, bod) VALUES ('$task_date','rop@upak.me,buh@paketoff.ru','$mail_text')");
echo mysql_error();
}

?>