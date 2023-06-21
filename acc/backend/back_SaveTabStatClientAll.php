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




$arr 			= $_REQUEST['arr'];			


//print_r(array_keys($arr));
foreach($arr as $id => $ar) {
	$query = "UPDATE contractors_list SET ";
	$i=0;
	foreach($ar as $fld => $val) {
//		echo $id.' '.$fld.' '.$val.'<br>';
		if($i)
			$query.=", ";
			
		$query .= $fld."='".$val."' ";
		$i++;
	}
	$query .= " WHERE uid=".$id;
	mysql_query($query);
//	echo $query.'<br>';
}




// возврат значений в родительский скрипт
$GLOBALS['_RESULT'] = array(
	"str"	=> '1',
);

?>
<pre>
<?
//	print_r($arr);
?>
</pre>