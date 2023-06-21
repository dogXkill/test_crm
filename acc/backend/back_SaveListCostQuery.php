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
$sm 	  = $_REQUEST['sm'];			// сумма всех полей оплат для данного запроса


$query = "SELECT a.user_id,a.prdm_num_acc,a.prdm_sum_acc,b.name FROM queries as a, clients as b WHERE a.uid=".$query_id."  AND a.client_id=b.uid";
$res = mysql_query($query);
$r = mysql_fetch_array($res);
//echo mysql_error();

$summ_acc = $r['prdm_sum_acc'];

$summ_dolg = $summ_acc - $sm;

$query = "SELECT surname,name,father,email FROM users WHERE uid=".$r['user_id'];
$res = mysql_query($query);
$r_us = mysql_fetch_array($res);
$full_name = $r_us['surname'].' '.$r_us['name'].' '.$r_us['father'];

$arr_ret = array( 
	'dolg'			=> $summ_dolg, 
	'men_name' 	=> $full_name, 
	'men_mail' 	=> $r_us['email'],
	'num_acc'		=> $r['prdm_num_acc'],
	'client'		=> $r['name']
);



$query = "DELETE FROM payment_predm WHERE query_id=".$query_id;
mysql_query($query);

for($i=0;$i<count($arr_cost);$i++) {
	$query = sprintf("INSERT INTO payment_predm(query_id,nn,sum_accounts,number_pp,date_ready) VALUES(%d,%d,'%s','%s','%s')", $query_id,($i+1),$arr_cost[$i]['summ'],$arr_cost[$i]['num_pp'], date_switch($arr_cost[$i]['date']));
	mysql_query($query);
}

echo mysql_insert_id();



$query = "UPDATE queries SET prdm_opl='".$sm."', prdm_dolg='".$summ_dolg."' WHERE uid=".$query_id;
mysql_query($query);




// возврат значений в родительский скрипт
$GLOBALS['_RESULT'] = array(
	"arr_ret"	=> $arr_ret,
);

?>