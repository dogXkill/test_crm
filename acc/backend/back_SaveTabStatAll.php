<?
require_once "../includes/lib/JsHttpRequest/JsHttpRequest.php";
require_once("../includes/db.inc.php");

// Блок динамической передачи данных в java script
//----------------------------------------------------------------------
$JsHttpRequest = new JsHttpRequest("windows-1251");



$arr 			= $_REQUEST['arr'];			

foreach($arr as $id => $ar) {
	$query = "UPDATE queries SET ";
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
	"str"	=> 'a',
);

?>
<pre>
<?
//	print_r($arr);
?>
</pre>