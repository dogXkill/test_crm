<?
// �������� �������� ��������, ���������� � ���������
require_once "../includes/lib/JsHttpRequest/JsHttpRequest.php";
require_once("../includes/db.inc.php");

// ������ �� ������� clients ������ � ���������� �� � �������
//---------------------------------------------------------------------------
function read_hint($usid, $text, $uid = false, $accyes = false) {	if($uid === false) {	// ���� ����� ����� ���� ��������� ��������
		if($usid==0)
			$query = sprintf( "SELECT a.uid FROM clients as a, users as b WHERE a.user_id=b.uid AND a.short='%s'", trim($text) );
		else
			$query = sprintf( "SELECT uid FROM clients WHERE user_id=%d AND short='%s'", $usid, trim($text) );
		$res2 = mysql_query($query);
		$num = mysql_num_rows($res2);	// ����� � ������ ����������� ����� �������

		// ������ ��, �������, ��������� �������� �������, ���������� � ���������� ����������
		if($usid==0)
			$query = sprintf("SELECT a.* FROM clients as a, users as b WHERE a.user_id=b.uid AND LEFT(a.short,%d)='%s' ORDER BY a.short LIMIT 1", strlen($text), $text);
	  else
			$query = sprintf("SELECT * FROM clients WHERE user_id=%d AND LEFT(short,%d)='%s' ORDER BY short LIMIT 1", $usid, strlen($text), $text);
		$res = mysql_query($query);
		$r = mysql_fetch_array($res);

	}
	else {		// ����� ���� ����� ����� ������ ��������
		$query = sprintf("SELECT * FROM clients WHERE uid=%d LIMIT 1", $uid);
		$res = mysql_query($query);
		$r = mysql_fetch_array($res);

	$num = 0;
	}


	$arr = array(
			'uid' => $r['uid'],
			'short' => $r['short'],
			'name' => $r['name'],
            'sphere' => $r['sphere'],
            'sphere_other' => $r['sphere_other'],
            'celi' => $r['celi'],
            'celi_other' => $r['celi_other'],
            'potrebnost' => $r['potrebnost'],
            'kak_uznali' => $r['kak_uznali'],
            'kak_uznali_other' => $r['kak_uznali_other'],
			'leg_adr' => $r['legal_address'],
			'post_adr' => $r['postal_address'],
			'deliv_adr' => $r['deliv_address'],
			'inn' => $r['inn'],
			'kpp' => $r['kpp'],
			'okpo' => $r['okpo'],
			'cont_pers' => $r['cont_pers'],
			'cont_tel' => $r['cont_tel'],
			'num' => $num,
			'rs' => $r['rs_acc'],
			'bank' => $r['bank'],
			'bik' => $r['bik'],
			'korr' => $r['korr_acc'],
			'dogov_num' => $r['dogov_num'],
			'firm_tel' => $r['firm_tel'],
			'email' => $r['email'],
			'gen_dir' => $r['gen_dir'],
			'ogrn' => $r['ogrn'],
            'comment' => $r['comment'],
	);

	$acc = '';			// ����� ����� �� ���������
	if($accyes) {		// ���� ������ �� ��������, ����� ����� ����� ��� ������� �������
		$query = "SELECT prdm_num_acc FROM queries WHERE client_id=".$r['uid']." AND prdm_num_acc<>'' AND prdm_num_acc is not NULL AND prdm_num_acc<>'none' ORDER BY uid asc LIMIT 1";
		$res = mysql_query($query);
		@$r2 = mysql_fetch_array($res);
		@$acc = $r2['prdm_num_acc'];

		$arr = array(
			'uid' => $r['uid'],
			'short' => $r['short'],
			'name' => $r['name'],
            'sphere' => $r['sphere'],
            'sphere_other' => $r['sphere_other'],
            'celi' => $r['celi'],
            'celi_other' => $r['celi_other'],
            'potrebnost' => $r['potrebnost'],
            'kak_uznali' => $r['kak_uznali'],
            'kak_uznali_other' => $r['kak_uznali_other'],
			'leg_adr' => $r['legal_address'],
			'post_adr' => $r['postal_address'],
			'deliv_adr' => $r['deliv_address'],
			'inn' => $r['inn'],
			'kpp' => $r['kpp'],
		   	'okpo' => $r['okpo'],
			'num' => $num,
			'acc'=> @$acc,
			'rs' => $r['rs_acc'],
			'bank' => $r['bank'],
			'bik' => $r['bik'],
			'korr' => $r['korr_acc'],
			'dogov_num' => $r['dogov_num'],
            'comment' => $r['comment'],
		);
	}
	return $arr;
}

// ���� ������������ �������� ������ � java script
//----------------------------------------------------------------------
$JsHttpRequest = new JsHttpRequest("windows-1251");
$usid = $_REQUEST['usid'];
$str = $_REQUEST['str'];
$fn = $_REQUEST['fn'];
$ins = $_REQUEST['ins'];
$accyes = @$_REQUEST['accyes'];
$rr = array();

if($ins == "0") {
	if(trim($str))
		$rr = read_hint($usid, $str, false, $accyes);		// ������ �� ��������� ��������
}
else
	$rr = read_hint($usid, '', $str, $accyes);	// ������ �� �� ������� �� ������

// ������� �������� � ������������ ������
$GLOBALS['_RESULT'] = array(
	"res" => $rr,
	"fn" => $fn,
);

?>