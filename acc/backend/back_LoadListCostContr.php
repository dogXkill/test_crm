<?
require_once "../includes/lib/JsHttpRequest/JsHttpRequest.php";
require_once("../includes/db.inc.php");

// Блок динамической передачи данных в java script
//----------------------------------------------------------------------
$JsHttpRequest =new JsHttpRequest("windows-1251");


// переводит дату из формата '1997-05-23' в '23.05.1997'
function date_switch($val) {
	$a = explode('-',$val);
	$str =@$a[2].'.'.@$a[1].'.'.@$a[0];
	return $str;
}



$query_id = $_REQUEST['id'];			// получение id

$arr_cost = array();

$query = "SELECT * FROM payment_podr WHERE contr_id=".$query_id." ORDER BY nn";
$res = mysql_query($query);
$i=0;
while($r = mysql_fetch_array($res)) {
	$arr_cost[$i]['summ'] 			= $r['sum_accounts'];
	$arr_cost[$i]['num_pp']			= $r['number_pp'];
	$arr_cost[$i]['date']				= date_switch($r['date_ready']);
	$i++;
}



// возврат значений в родительский скрипт
$GLOBALS['_RESULT'] = array(
	"arr"	=> $arr_cost,
);

?>
<pre>
<?
//	print_r($arr_cost);
?>
</pre>