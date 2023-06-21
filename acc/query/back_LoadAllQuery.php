<?
require_once "../includes/lib/JsHttpRequest/JsHttpRequest.php";
require_once("../includes/db.inc.php");

// Блок динамической передачи данных в java script
//----------------------------------------------------------------------
$JsHttpRequest = new JsHttpRequest("windows-1251");

// переводит дату из формата '1997-05-23' в '23.05.1997'
function date_switch($val) {
	$a = explode('-',$val);
	$str =$a[2].'.'.$a[1].'.'.$a[0];
	return $str;
}


$query_id = $_REQUEST['id'];

$arr_all_data = array();	// массив всех полей


// запрос
$query = "SELECT * FROM queries WHERE uid=".$query_id;
$res = mysql_query($query);
$r = mysql_fetch_array($res);
$arr_all_data['predm']['num_acc'] = $r['prdm_num_acc'];
$arr_all_data['note'] =  $r['note'];
$arr_all_data['form_of_payment'] = $r['form_of_payment'];
$arr_all_data['deliv_id'] = $r['deliv_id'];
$arr_all_data['corsina_order_uid'] = $r['corsina_order_uid'];
$arr_all_data['corsina_order_num'] = $r['corsina_order_uid'];
// предмет счета
$query = "SELECT * FROM obj_accounts WHERE query_id=".$query_id." ORDER BY nn";
$res = mysql_query($query);
$i = 0;
while($r = mysql_fetch_array($res)) {
	$arr_all_data['predm']['list'][$i]['art_num'] 	=	$r['art_num'];
	$arr_all_data['predm']['list'][$i]['name'] 	=		$r['name'];
	$arr_all_data['predm']['list'][$i]['num']		=   $r['num'];
	$arr_all_data['predm']['list'][$i]['price']	=		$r['price'];
	$arr_all_data['predm']['list'][$i]['r_price_our']	=		$r['r_price_our'];
   //	$arr_all_data['predm']['list'][$i]['price_our']	=		$r['price_our'];
	$i++;
}

// поля оплаты для предмета счета
$query = "SELECT * FROM payment_predm WHERE query_id=".$query_id." ORDER BY nn";
$res = mysql_query($query);
$i=0;
while($r = mysql_fetch_array($res)) {
	$arr_all_data['predm']['opl'][$i]['summ'] 		=		$r['sum_accounts'];
	$arr_all_data['predm']['opl'][$i]['num_pp'] 	=		$r['number_pp'];
	$arr_all_data['predm']['opl'][$i]['date'] 		=		date_switch($r['date_ready']);
	$i++;
}

// список подрядчиков
$query = "SELECT * FROM contractors_list WHERE query_id=".$query_id." ORDER BY nn";
$res = mysql_query($query);
$i=0;
while($r = mysql_fetch_array($res)) {
	$arr_all_data['podr']['list'][$i]['podr'] 		=		$r['contr_id'];
	$arr_all_data['podr']['list'][$i]['name'] 		=		$r['name'];
	$arr_all_data['podr']['list'][$i]['price'] 		=		$r['price'];
	$arr_all_data['podr']['list'][$i]['num'] 			=	$r['num'];
	$arr_all_data['podr']['list'][$i]['acc_num'] 	=		$r['acc_number'];
	$arr_all_data['podr']['list'][$i]['note'] 		=		$r['note'];
    
	
	// список полей оплат для каждого подрядчика
	$query = "SELECT * FROM payment_podr WHERE contr_id=".$r['uid']." ORDER BY nn";
	$res2 = mysql_query($query);
	$j=0;
	while($r2 = mysql_fetch_array($res2)) {
		$arr_all_data['podr']['list'][$i]['opl'][$j]['summ'] 		=		$r2['sum_accounts'];
		$arr_all_data['podr']['list'][$i]['opl'][$j]['num_pp'] 	=		$r2['number_pp'];
		$arr_all_data['podr']['list'][$i]['opl'][$j]['date'] 		=		date_switch($r2['date_ready']);
		$j++;
	}
	$i++;
}






function replace_multiarray(&$item, $key){
     $item = str_replace("\"", "", $item);
}
array_walk_recursive($arr_all_data['predm'], 'replace_multiarray');

function replace_multiarray2(&$item, $key){
     $item = str_replace("\"", "", $item);
}
array_walk_recursive($arr_all_data['podr'], 'replace_multiarray2');


// возврат значений в родительский скрипт
$GLOBALS['_RESULT'] = array(
	"res"	=> $arr_all_data,
);
?>
<pre>
<?
print_r($arr_all_data);
?>
</pre>