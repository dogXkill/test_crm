<?php
$art_id = $_GET['art_id'];
$act = $_GET["act"];
$new_ss = $_GET["new_ss"];
$fld = $_GET["fld"];
$val = $_GET["val"];

//�������� ������� ��� ����������� ������ �� ������ ������� �� ��������� ��� � ����� �� ������ � ������
if($act == "check"){
require_once("../../includes/db.inc.php");
$ago = date("Y-m-d 00:00:00",strtotime("-1 year"));
//��������� ��� �� ������������� �� ��������� ��� ������ �� ������ �������
$q = mysql_query("SELECT uid, num_ord, tiraz, dat_ord FROM applications WHERE art_id = '$art_id' AND dat_ord > '$ago' AND archive <> '1' ORDER BY dat_ord ASC");
while($r = mysql_fetch_assoc($q)){
$uid = $r["uid"];
$num_ord = $r['num_ord'];
$tiraz = $r['tiraz'];
$dat_ord = $r["dat_ord"];
$fd = 'd.m.y';
$dat_ord = date($fd, strtotime($dat_ord));

//��������� �������� �� ���� ��������� �������, �������� ��, ��� ��������� ������ 95%
$qr = mysql_fetch_assoc(mysql_query("SELECT SUM(num_of_work) AS num_of_work FROM job WHERE num_ord = '$num_ord' AND job = '11'"));
$upak = $qr['num_of_work'];

if($upak == ""){$upak = "0";}

if($tiraz*0.95 > $upak){$vst .= "<br><a href=\"/acc/applications/edit.php?uid=".$uid."\" target=\"_blank\"\">".$num_ord."</a> �� $dat_ord (��������� $upak �� $tiraz ��.);";}
}
echo $vst;
}

//�������� ��� ���������� �� �������� � �����  ��� ����������� �/� � �����
if($act == 'get_data' || $act == 'compare_ss' ||  $act == 'change_ss' || $act == 'compare_flds' || $act == 'change_fld'){
if(is_numeric($art_id)){
$handle = fopen("https://www.paketoff.ru/modules/backend/get_art_info_from_intra.php?act=".$act."&new_ss=".$new_ss."&art_id=".$art_id."&fld=".$fld."&val=".$val, "r");
$contents = stream_get_contents($handle);
fclose($handle);
echo $contents;
}else{echo "����� ��������: ��������� art_id �� �������� �������";}}




?>