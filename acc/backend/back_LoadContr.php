<?
// скрипт загрузки списка подрядчиков в поле селекта

require_once "../includes/lib/JsHttpRequest/JsHttpRequest.php";
require_once("../includes/db.inc.php");

// Блок динамической передачи данных в java script
//----------------------------------------------------------------------
$JsHttpRequest =new JsHttpRequest("windows-1251");

$query = "SELECT uid,name FROM contractors ORDER BY name ASC";
$res = mysql_query($query);

$i = 0;
$res_arr = array();
while($r = mysql_fetch_array($res)) {
	$res_arr[$i]['id'] = $r['uid'];
	$res_arr[$i]['name'] = $r['name'];
	$i++;
}


// сортировка массива подрядчиков
$fl = 1;
while($fl) {
	$fl = 0;
	for($i=0;$i<(count($res_arr)-1);$i++) {
		if(strtolower($res_arr[$i]['name'][0])>strtolower($res_arr[$i+1]['name'][0])) {
			$t = $res_arr[$i]['name'];
			$res_arr[$i]['name'] = $res_arr[$i+1]['name'];
			$res_arr[$i+1]['name'] = $t;
			
			$t = $res_arr[$i]['id'];
			$res_arr[$i]['id'] = $res_arr[$i+1]['id'];
			$res_arr[$i+1]['id'] = $t;
			
			$fl = 1;
		}
	}
}

// возврат значений в родительский скрипт
$GLOBALS['_RESULT'] = array(
	"res"   => $res_arr,
);

?>