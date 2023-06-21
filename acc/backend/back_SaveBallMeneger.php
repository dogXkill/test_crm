<?		// Сохранение процента менеджера в базе
require_once "../includes/lib/JsHttpRequest/JsHttpRequest.php";
require_once("../includes/db.inc.php");

// Блок динамической передачи данных в java script
//----------------------------------------------------------------------
$JsHttpRequest = new JsHttpRequest("windows-1251");



$query_id = $_REQUEST['id'];	// ид запроса
$val = $_REQUEST['val'];			// значение процента



$query = "UPDATE queries SET percent='".$val."' WHERE uid=".$query_id;
mysql_query($query);



// возврат значений в родительский скрипт
$GLOBALS['_RESULT'] = array(
	"str"	=> $query_id,
);

?>
