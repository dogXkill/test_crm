<?
require_once "../includes/lib/JsHttpRequest/JsHttpRequest.php";
require_once("../includes/db.inc.php");

// ������ �� ������� clints ������ � ���������� �� � �������
//---------------------------------------------------------------------------
function read_hint($text, $uid = false, $accyes = false) {
	if($uid === false) {	// ���� ����� ����� ���� ��������� ��������
		$query = sprintf("SELECT uid FROM clients WHERE short='%s'", trim($text));
		$res2 = mysql_query($query);
		$num = mysql_num_rows($res2);	// ����� � ������ ����������� ����� �������
		
		$query = sprintf("SELECT uid,short,name,req FROM clients WHERE LEFT(short,%d)='%s' ORDER BY short LIMIT 1", strlen($text), $text);
	}
	else {		// ����� ���� ����� ����� ������ ��������
		$query = sprintf("SELECT * FROM clients WHERE uid=%d", $uid);
		$num = 0;
	}
	$res = mysql_query($query);
	$r = mysql_fetch_array($res);
	
	$acc = '';		// ����� ����� �� ���������
	if($accyes) {		// ���� ������ �� ��������, ����� ����� ����� ��� ������� �������
		$query = "SELECT acc_number FROM data_queries WHERE client_id=".$r['uid']." AND acc_number<>'' AND acc_number<>'NULL' ORDER BY uid asc LIMIT 1";
		$res = mysql_query($query);
		@$r2 = mysql_fetch_array($res);
		@$acc = $r2['acc_number'];
	}	
	$arr = array( 0 => $r['uid'], 1 => $r['short'], 2 => $r['name'], 3 => $r['req'], 4 => $num, 5 => @$acc);
	return $arr;
}

// ���� ������������ �������� ������ � java script
//----------------------------------------------------------------------
$JsHttpRequest =& new JsHttpRequest("windows-1251");
$str = $_REQUEST['str'];
$fn = $_REQUEST['fn'];
$accyes = @$_REQUEST['accyes'];

if(!$fn)
	$rr = read_hint($str, false, $accyes);		// ������ �� ��������� ��������
else 
	$rr = read_hint('', $str, $accyes);	// ������ �� �� ������� �� ������

// ������� �������� � ������������ ������
$GLOBALS['_RESULT'] = array(
	"uid"   => $rr[0],
	"short"	=> $rr[1],
	"name"	=> $rr[2],
	"reqv" => $rr[3],
	"num" => $rr[4],
	"acc" => $rr[5],
	"fn"	=> $fn,
);

?>