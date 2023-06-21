<?
require_once "../includes/lib/JsHttpRequest/JsHttpRequest.php";
require_once("../includes/db.inc.php");

// Блок динамической передачи данных в java script
//----------------------------------------------------------------------
$JsHttpRequest = new JsHttpRequest("windows-1251");


// переводит дату из формата '23.05.1997' в '1997-05-23'
function date_switch($val) {
	$a = explode('.',$val);
	$str =@$a[2].'-'.@$a[1].'-'.@$a[0];
	return $str;
}



$query_id = $_REQUEST['id'];			// ид запроса
$arr_cost = $_REQUEST['arr'];			// получение массива всех значений
$sm 			= $_REQUEST['sm'];			// сумма всех полей оплат для данного запроса



$query = "DELETE FROM payment_podr WHERE contr_id=".$query_id;
mysql_query($query);

for($i=0;$i<count($arr_cost);$i++) {
	$query = sprintf("INSERT INTO payment_podr(contr_id,nn,sum_accounts,number_pp,date_ready) VALUES(%d,%d,'%s','%s','%s')", $query_id,($i+1),$arr_cost[$i]['summ'],$arr_cost[$i]['num_pp'], date_switch($arr_cost[$i]['date']));
	mysql_query($query);
}



$query = "UPDATE contractors_list SET opl='".$sm."' WHERE uid=".$query_id;
mysql_query($query);



// возврат значений в родительский скрипт
$GLOBALS['_RESULT'] = array(
	"str"	=> $query_id,
);

?>
<pre>
<?
//	print_r($arr_cost);
?>
</pre>