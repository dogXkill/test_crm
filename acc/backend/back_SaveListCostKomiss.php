<?
require_once "../includes/lib/JsHttpRequest/JsHttpRequest.php";
require_once("../includes/db.inc.php");

// ���� ������������ �������� ������ � java script
//----------------------------------------------------------------------
$JsHttpRequest = new JsHttpRequest("windows-1251");


// ��������� ���� �� ������� '23.05.1997' � '1997-05-23'
function date_switch($val) {
	$a = explode('.',$val);
	$str =@$a[2].'-'.@$a[1].'-'.@$a[0];
	return $str;
}



$query_id = $_REQUEST['id'];			// �� �������
$arr_cost = $_REQUEST['arr'];			// ��������� ������� ���� ��������
$sm 			= $_REQUEST['sm'];			// ����� ���� ����� ����� ��� ������� �������



$query = "DELETE FROM komis_opl WHERE query_id=".$query_id;
mysql_query($query);

for($i=0;$i<count($arr_cost);$i++) {
	$query = sprintf("INSERT INTO komis_opl(query_id,nn,sum,date_ready) VALUES(%d,%d,'%s','%s')", $query_id,($i+1),$arr_cost[$i]['summ'],date_switch($arr_cost[$i]['date']));
	mysql_query($query);
}



$query = "UPDATE queries SET komis_opl='".$sm."' WHERE uid=".$query_id;
mysql_query($query);




// ������� �������� � ������������ ������
$GLOBALS['_RESULT'] = array(
	"str"	=> $query_id,
);

?>
<pre>
<?
	print_r($arr_cost);
?>
</pre>