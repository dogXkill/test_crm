<?		// ���������� �������� ��������� � ����
require_once "../includes/lib/JsHttpRequest/JsHttpRequest.php";
require_once("../includes/db.inc.php");

// ���� ������������ �������� ������ � java script
//----------------------------------------------------------------------
$JsHttpRequest = new JsHttpRequest("windows-1251");



$query_id = $_REQUEST['id'];	// �� �������
$val = $_REQUEST['val'];			// �������� ��������



$query = "UPDATE queries SET percent='".$val."' WHERE uid=".$query_id;
mysql_query($query);



// ������� �������� � ������������ ������
$GLOBALS['_RESULT'] = array(
	"str"	=> $query_id,
);

?>
