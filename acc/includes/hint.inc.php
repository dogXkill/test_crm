<?
function read_hint($tab, $text, $uid = false) {
	if($uid === false) {
		$query = sprintf("SELECT uid FROM clients WHERE short='%s'", trim($text));
		$res2 = mysql_query($query);
		$num = mysql_num_rows($res2);
		$query = sprintf("SELECT uid,short,name,req FROM clients WHERE LEFT(short,%d)='%s' ORDER BY uid LIMIT 1", strlen($text), $text);
	}
	else {
		$query = sprintf("SELECT * FROM clients WHERE uid=%d", $uid);
		$num = 0;
	}
	$res = mysql_query($query);
	$r = mysql_fetch_array($res);
	$arr = array( 0 => $r['uid'], 1 => $r['short'], 2 => $r['name'], 3 => $r['req'], 4 => $num);
	return $arr;
}
?>
