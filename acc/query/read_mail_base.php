<?
require_once("../includes/db.inc.php");


$delid = (isset($_GET['delid'])) ? trim($_GET['delid']) : false;  // �� ������������� ��������� ��� ��������


if( $delid && intval($delid) ) {

		// �������� ������
		$query = "DELETE FROM mail_temp WHERE uid=".$delid;
		if(!mysql_query($query)) {
			// ���� �������� �� ���������� - ���������
		    header("Location: /acc/query/");
		}
	}

$query = "SELECT uid FROM mail_temp";
$res = mysql_query($query);
$nums_sec = mysql_num_rows($res);

// ������ ������ ��� ��������
$query = "SELECT * FROM mail_temp LIMIT 1";
$res = mysql_query($query);

if(!($r = mysql_fetch_array($res))) { // ���� ���� ���� ���� ������, ����������
	header("Location: /acc/query/");
}
else {
$arr_mail = $r['komu'];
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>�������� ������, ���������</title>
</head>

<body onload="document.fff.submit();">
���������� ���������, �������� <?=intval($nums_sec)?> ���.
<form name="fff" action="https://www.paketoff.ru/modules/query/send_mail_query3.php" method="POST">
 	<input name="nums_sec" type="hidden" value="<?=@$nums_sec?>" />
	<input name="tema" type="hidden" value="<?=htmlspecialchars($r['tema'],ENT_QUOTES)?>" />
	<input name="bod" type="hidden" value="<?=htmlspecialchars($r['bod'],ENT_QUOTES)?>" />
    <input name="mail_arr" type="hidden" value="<?=$arr_mail;?>" />
	<input name="delid" type="hidden" value="<?echo $r['uid'];?>" />
    <input type="submit" />
</form>
</body>
</html>
<?  } ?>