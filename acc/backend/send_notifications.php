<?php //�������� ������ �������, �� ������� ������������ ������

//require_once("../includes/db.inc.php");




// ������� �������� �����
function send_mail($tema, $bod, $user_id) {

$spis_mail = '';
$mail_q = mysql_query("SELECT * FROM mail");
while($r_m = mysql_fetch_array($mail_q)) {
        $spis_mail .= $r_m['email'].',';
}

//��������� � ��� ����� �������� ���������
$query_men = "SELECT email FROM users WHERE uid='$user_id'";
$res_men = mysql_query($query_men);
if($r_men = mysql_fetch_array($res_men)) {

 if( (trim($r_men['email'])) && (substr_count($spis_mail,trim($r_men['email']))==0)) {
        $spis_mail .= $r_men['email'].',';
    }
}



    $headers= "From: CRM.UPAK.ME \r\n" ;
    $headers.="Content-type: text/html; charset=\"windows-1251\"";
    //��������� �� ������ ����� ������������
    $tema  =  '=?windows-1251?B?'.base64_encode($tema).'?=';
   mail($spis_mail, $tema, $bod, $headers);




 //�� ���� ������ ������ � �������, ��� ������
    $query = "INSERT INTO mail_temp(tema,komu,bod) VALUES('".mysql_escape_string($tema)."', '".mysql_escape_string(trim($spis_mail))."', '".mysql_escape_string($bod)."')";
    mysql_query($query);


}
?>