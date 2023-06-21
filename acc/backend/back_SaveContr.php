<?
// Серверная часть скрипта AJAX, 
// Сохранение подрядчика в базе


require_once "../includes/lib/JsHttpRequest/JsHttpRequest.php";
require_once("../includes/db.inc.php");

// Блок динамической передачи данных в java script
//----------------------------------------------------------------------
$JsHttpRequest = new JsHttpRequest("windows-1251");
$name = $_REQUEST['name'];
$pers = $_REQUEST['pers'];
$tel = $_REQUEST['tel'];
$mail = $_REQUEST['mail'];

$query = sprintf("INSERT INTO contractors(name,cont_pers,cont_tel,email) VALUES('%s', '%s', '%s', '%s')", $name, $pers, $tel, $mail);
mysql_query($query);
$new_id = mysql_insert_id();

// возврат значений в родительский скрипт
$GLOBALS['_RESULT'] = array(
	"id"   => $new_id,
	"name"	=> $name,
);

?>