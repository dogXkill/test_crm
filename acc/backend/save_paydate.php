<?
require_once("../includes/db.inc.php");

// ��������� ���� �� ������� '23.05.1997' � '1997-05-23'
function date_switch($val) {
  $a = explode('.',$val);
  $str =@$a[2].'-'.@$a[1].'-'.@$a[0];
  return $str;
}

$query_id = $_GET['query_id'];
$uid = $_GET['uid'];
$sum = $_GET['sum'];
$paydate = date_switch($_GET['paydate']);
$number_pp = $_GET['number_pp'];
$act = $_GET['act'];
//������ �������
$list_mail =  $_GET['list_mail'];





if($act == "add" and is_numeric($query_id) and is_numeric($sum)){
if($sum>0)
$q = mysql_query("INSERT INTO payment_predm (query_id,sum_accounts,date_ready,number_pp) VALUES ('$query_id', '$sum', '$paydate','$number_pp')");
$new_id = mysql_insert_id();
}

if($act == "edit" and is_numeric($uid) and is_numeric($sum)){
$q = mysql_query("UPDATE payment_predm SET sum_accounts='$sum',date_ready='$paydate',number_pp='$number_pp' WHERE uid='$uid'");
}

if($act == "del" and is_numeric($uid)){
$q = mysql_query("DELETE FROM payment_predm WHERE uid='$uid'");
}

//������� �� ������������ ������������ ����, ����� �����, �� �������
$q1 = mysql_query("SELECT prdm_sum_acc, user_id, client_id FROM queries WHERE uid='$query_id'");
$r1 = mysql_fetch_array($q1);
$summ_acc = $r1['prdm_sum_acc'];
$user_id = $r1['user_id'];
$client_id = $r1['client_id'];

//�������� ���� � ��� ��������� ������
$u = mysql_query("SELECT surname, name, email FROM users WHERE uid = '$user_id'");
$u =  mysql_fetch_array($u);
$surname = $u['surname'];
$name = $u['name'];
$email = $u['email'];

//�������� �������� �������
$c = mysql_query("SELECT short FROM clients WHERE uid = '$client_id'");
$c =  mysql_fetch_array($c);
$client = $c['short'];

//��������� ������ ������ �������
$mails = $email.",".$list_mail;

//������� ����� ���� �����
$q2 = mysql_query("SELECT SUM(sum_accounts) AS sum_accounts FROM payment_predm WHERE query_id='$query_id'");
$r2 = mysql_fetch_array($q2);
$oplaceno = $r2['sum_accounts'];

$dolg = $summ_acc-$oplaceno;

//���� ��� ��, �� ����� ���� � ������� queries
if(!mysql_error()){
if($oplaceno==""){$oplaceno="0";}
if($dolg==""){$dolg="0";}

//������ ����� ����� ����� �������
if(is_numeric($query_id) && is_numeric($oplaceno) && is_numeric($dolg)){
$query = mysql_query("UPDATE queries SET prdm_opl='$oplaceno', prdm_dolg='$dolg' WHERE uid='$query_id'");
}


echo $oplaceno.";;;".$dolg.";;;".$new_id.";;;".$mails.";;;".$surname.";;;".$name.";;;".$act.";;;".$sum.";;;".$number_pp.";;;".$paydate.";;;".$client.";;;".$uid.";;;".$query_id.";;;OK";







 /* $query_id  = $_GET["query_id"];
  $dolg = $_GET["dolg"];
  $new_id = $_GET["new_id"];
  $mails = $_GET["mails"];
  //������� ������� �� ������ �������
  $mails_arr = explode(",", $mails);
  $mails_arr = array_unique($mails_arr);
  $mails = implode(",", $mails_arr);

  $surname = $_GET["surname"];
  $name = $_GET["name"];


  $act = $_GET["act"];
  $sum = $_GET["sum"];
  $number_pp = urldecode($_GET["number_pp"]);
  if($number_pp==""){$number_pp = "�� ������";}

  //�������� ���� ������� � �������� ���
  if($_GET["paydate"]){
  $paydate = date_switch($_GET["paydate"]);
  }else{
  $paydate = "";
  }
  $client = $_GET["client"];
  $uid = $_GET["uid"];  */

function date_switch2($val) {
  $a = explode('-',$val);
  $str =@$a[2].'.'.@$a[1].'.'.@$a[0];
  return $str;
}
$paydate =  date_switch2($paydate);

if($number_pp==""){$number_pp = "�� ������";}

if($act == "add"){
$act_text = $paydate." �������� ����� ������ �� ����� <strong>".$sum."</strong> �� �������� ����� <strong>".$number_pp."</strong> �� <strong>".$paydate."</strong> ��������������� ��� �������� ������� ".$new_id."<br><br>������������� ������� �� ������� ������� ���������� ".$dolg." �.";
$tema = "������ �� ".$client." �� ����� ".$sum;
}

if($act == "edit"){
$act_text = "�� ������ ������� <strong>".$client."</strong> ������� ��������� � ����� ���������� ������. ����� ������: ����� <strong>".$sum."</strong> �� # <strong>".$number_pp."</strong> �� <strong>".$paydate."</strong>. ����� ������: ".$uid."<br><br>������������� ������� �� ������� ������� ���������� ".$dolg." �.";
$tema = "��������� � ������ �� ".$client;
}

if($act == "del"){
$act_text = "������ ����� ��������� ������ �� ����� <strong>".$sum."</strong> �� �������� ����� <strong>".$number_pp."</strong> �� <strong>".$paydate."</strong> ������������������ ����� �� ������� ".$uid."<br><br>������������� ������� �� ������� ������� ���������� ".$dolg." �.";
$tema = "������ ����� ����������� ������ �� ".$client;
}

  $headers= "From: INTRANET_OPLATA \r\n" ;
  $headers.="Content-type: text/html; charset=\"windows-1251\"";

$bod = '<html><head><META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251" />';

$bod .= '<style type="text/css"><!--
body,table,td {font-family: Arial, Helvetica, sans-serif;font-size: 11px;color:#000000;}
body {padding:0px;padding-left:10px;margin:5px;background-color:#FFFFFF;}
--></style></head><body>';


$bod .= '<strong>���������(��) '.$name.' '.$surname.'!</strong><br><br>';
$bod .='�� ������ '.$client." �� �������: <a href=\"http://192.168.1.100/acc/query/query_send.php?show=".$query_id."\"><strong>".$query_id."</strong></a>";
$bod .=' ��������� ��������� ��������<br><br>'.$act_text;

$bod .= '</body></html>';

$mail_temp_insert = mysql_query("INSERT INTO mail_temp(tema,komu,bod) VALUES('$tema','$mails','$bod')");

/*$url = str_replace(" ", "", "http://printfolio.ru/query/backMailAllQuery.php?query_id=".$query_id."&act=".$act."&sum=".$sum."&paydate=".$paydate."&number_pp=".$number_pp."&mails=".$mails."&dolg=".$dolg."&new_id=".$new_id."&surname=".$surname."&name=".$name."&client=".$client."&uid=".$uid);
$handle = fopen($url, "rb");
$contents = stream_get_contents($handle);
fclose($handle); */

}else {echo mysql_error();}

?>