<?
require_once("../acc/includes/db.inc.php");
require_once("backend/lib.php");


//������� ��������� �������� ������, ����� �� ������ ��������� ������ ��������������� ����������� ������, ����� ��� ���������, ����� ��������� �� � ������
get_all_emails_to_check("0");
get_all_emails_to_check("1");


function get_all_emails_to_check($crm_outbox_check){

  if($crm_outbox_check == "1"){$crm_check_vst = " e.crm_outbox_check = '1'";}else{$crm_check_vst = " e.crm_inbox_check = '1'";}


    $mails_to_check_q = "SELECT * FROM crm_member_emails AS e, crm_members AS m WHERE (e.user_id = m.user_id OR e.user_id = '0') AND $crm_check_vst AND e.deleted <> '1' AND m.deleted <> '1' GROUP BY e.id";

    //echo $mails_to_check_q;

     $mails_to_check = mysql_query($mails_to_check_q);
       if(mysql_num_rows($mails_to_check) > 0)
        {mails_to_check($mails_to_check, $crm_outbox_check);}


  }



function set_last_update($email_id, $last_uid, $crm_outbox_check){

  //��������� ������ � �������, �� ���������� ���������� ������ ������ ������� ��������� �����, ��� ������ ����� ��������
  if($crm_outbox_check == "1"){$last_uid_vst = "crm_outbox_last_uid";}else{$last_uid_vst = "crm_inbox_last_uid";}

  //���������� �������, ����� �� ��������� ��������� �����
    $last_uid = $last_uid + 1;

  if($last_uid !=="" and $last_uid_vst !== "" and is_numeric($email_id)){
  $set_last_update_q = "UPDATE crm_member_emails SET $last_uid_vst = '$last_uid', crm_last_mail_check = NOW() WHERE id = '$email_id'";
  $set_last_update = mysql_query($set_last_update_q);
  if($set_last_update == false){echo $set_last_update_q."<br>".mysql_error();}
  }

}




function mails_to_check($mails_to_check, $crm_outbox_check){

if($mails_to_check)

    while($r = mysql_fetch_assoc($mails_to_check)){

    $email_id = $r['id'];
    $crm_mail_login = $r['crm_mail_login'];
    $crm_mail_pass = $r['crm_mail_pass'];
    $crm_imap_host = $r['crm_imap_host'];

    if($crm_outbox_check == "1"){$crm_mail_last_uid = $r['crm_outbox_last_uid'];}else{$crm_mail_last_uid = $r['crm_inbox_last_uid'];}

    if($crm_mail_login !== "" and $crm_mail_pass !== "" and $crm_imap_host !== ""){

    if($crm_outbox_check !== "1"){
    $folder_check = "";
    echo "<h2>�������� - $crm_mail_login</h2>";
    }
    //����� �������� �������� ��������� �����
    else{
    echo "<h2>��������� - $crm_mail_login</h2>";
    $folder_check = inbox_outbox_adapt($crm_imap_host);
    }

    $last_uid = mail_logics($crm_mail_login, $crm_mail_pass, $crm_imap_host, $folder_check, $crm_outbox_check, $crm_mail_last_uid,  $email_id);

    set_last_update($email_id, $last_uid, $crm_outbox_check);

        }

    }

}


//�������, ������� ������������ ��� ������ �������� �����
function mail_logics($crm_mail_login, $crm_mail_pass, $crm_imap_host, $folder_check, $crm_outbox_check, $crm_mail_last_uid, $email_id){

    $mails = get_mails($crm_mail_login, $crm_mail_pass, $crm_imap_host, $folder_check, $crm_mail_last_uid, $email_id, $crm_outbox_check);

    //���� ���� ����� ������
    if($mails){

  //���������� ��� ������
  foreach($mails as $mail){

    list($error_email, $email, $date_email, $subject, $server_uid) = get_email_parts($mail, $crm_outbox_check, $email_id);

    //��������� ��� ����� ��� ����� � ��� ������ ����� �� ������������
    if ($error_email=="")
    {

    //�������� ������ �������, ����� �� ������� ���������� �� ������������� �� UNSEEN
    $check_dubl_email = check_dubl_emails($email, $email_id, $date_email, $crm_outbox_check);

    if($check_dubl_email == "0"){

    //��������� ���� �� ����� ������ ��� � ����, � ���� ����, �� ����� � ���� ����
    list($clients_num, $existing_client_id, $check_deals_num, $existing_deal_id, $date_added) = check_dubl_client_appeals($email);

    //���� ����� ������ ������, �� ������ ��������� ��� id
    if($clients_num > 0){
        //���� ������ ��� ����, �� ����� ��� ID
        $client_id = $existing_client_id;

    }else{
        //��������� �������
        $client_id = add_client($email);
    }

    //���� � ������� ������� ��� ������� ������ � �������� ��������� (���� ������ 0), �� �� ��������� ������
    if($check_deals_num == 0 or $check_deals_num == ""){
    $deal_id = add_deal($email, $date_email, $subject, $client_id);
    }else{
    $deal_id = $existing_deal_id;
        }

    //���� ������ ���������, �� ���������� � ��. �������
    if($crm_outbox_check !== "1"){
    //������ ��������, ������ ��������� � crm_appeals ��������� � ��� ����������� ������� ������
    add_appeal($deal_id, $date_email, $email_id, $email, $subject, $client_id, $server_uid);
    }
    else{
    add_outcoming_email($deal_id, $date_email, $email_id, $email, $subject, $client_id, $server_uid);
    }

   } }



}


}
 return get_last_uid($email_id, $crm_outbox_check);
 echo "<hr>";
}


//������� �������� ������������ �������� UID ���������, ��� ����, ����� ����������� �������� � ����� ������
function get_last_uid($email_id, $crm_outbox_check){
    if($crm_outbox_check == "1"){$select_table = "crm_outcoming_appeals";}else{$select_table = "crm_appeals";}

    $get_last_uid_q = "SELECT MAX(server_uid) AS server_uid FROM $select_table WHERE email_id = '$email_id'";
    $last_uid = mysql_fetch_assoc(mysql_query($get_last_uid_q));
    $last_uid = $last_uid["server_uid"];

         //echo $get_last_uid_q;
    echo "<i>������������ �������� ������������� �������� UID ��������� <b><u>$email_id</u></b> � ������� $select_table, ���������� �������� = <b>$last_uid</b></i><br><br>";

    return $last_uid;

}


function get_mails($crm_mail_login, $crm_mail_pass, $crm_imap_host, $folder_check, $crm_mail_last_uid, $email_id, $crm_outbox_check){
//������ mbox, ��� �� ����� �������� ��� ��������� ����������, ����� ���� �������� �� �� �������
    global $mbox;

    $mbox = imap_open("{".$crm_imap_host."}$folder_check", "$crm_mail_login", "$crm_mail_pass")or die("can't connect: " . imap_last_error());
    //����� �� ��������� ������ ������, ��������� ������������� �� ����� 300 �����
    $num_msg = $crm_mail_last_uid + 300;


    $mails = imap_fetch_overview($mbox, $crm_mail_last_uid.":".$num_msg, FT_UID);

    echo "�������� ����� � ����� ID - <b>$email_id</b>, UID c <b>$crm_mail_last_uid</b> �� <b>$num_msg</b><br><br>";

    return $mails;
}





function get_email_parts($mail, $crm_outbox_check, $email_id){
    global $mbox;

    //����� ��������� � ������� �������, �� ������������� UID
    $msgno = $mail->msgno;
    $server_uid = imap_uid($mbox,$msgno);

    $header = imap_headerinfo($mbox, $msgno);
    $email = $header->from[0]->mailbox ."@".$header->from[0]->host;
    $email = clear_some_parts($email);


    if (check_is_email($email) == "1")
    {
    //�������� ��������� ������ �� ������
    $receipent = $header->to[0]->mailbox ."@".$header->to[0]->host;
    $receipent = clear_some_parts($receipent);
    $date_email = date("Y-m-d H:i:s", strtotime($header->date));
    $subject = clear_subject($header->subject, $crm_outbox_check);

    //���� ��������� ���������, �� ���������� �������� ������� �������� ������� ���������� � �����������
    if($crm_outbox_check == "1"){list($email, $email_id) = array($receipent, $email);}

    echo "<br>---------***---------<br><b><u>$email</u></b> UID ������ <b>$server_uid</b>, ����� ������ � ������� ������� <b>$msgno</b>. �����: <b>$date_email</b><br>";

    //�������� �� ���� ����
        $blocked_num = check_block_list($email);
         //echo "<br><u>list($email, $receipent) = array($receipent, $email)</u><br>";
        if($blocked_num > 0){

        $error_email = "email_blocked";

            }


    }else{

        $error_email = "wrong_email_adress";

    }

        return array($error_email,$email,$date_email,$subject,$server_uid);

    }


function check_dubl_emails($email,$email_id,$date_email,$crm_outbox_check){

//���� ���� �������� ��������� �����, �� ���� � ������ �������
    if($crm_outbox_check == "1"){$select_table = "crm_outcoming_appeals";}else{$select_table = "crm_appeals";}

    $check_q = "SELECT * FROM $select_table WHERE email='$email' AND email_id='$email_id' AND date_email='$date_email' AND deleted <> '1'";
    $check = mysql_query($check_q);
    $check_dubl_email = mysql_num_rows($check);

    echo "<br><b>�����</b> email='$email' AND email_id='$email_id' � ������� $select_table �������� �� �����. ������� <b>$check_dubl_email</b> ����������.<br>";

    return $check_dubl_email;
}


//� ������ �������� ������� ����� ������������ ���������� �� �������, �������, ����������
function inbox_outbox_adapt($crm_imap_host){

    if(stristr($crm_imap_host, 'imap.yandex.ru') == TRUE){$folder_check = "Sent";}
    if(stristr($crm_imap_host, 'imap.mastermail.ru') == TRUE){$folder_check = "&BB4EQgQ,BEAEMAQyBDsENQQ9BD0ESwQ1-";}
    if(stristr($crm_imap_host, 'mail.nic.ru') == TRUE){$folder_check = "Sent";}

   /* if($crm_imap_host == ":993/imap/ssl"){$folder_check = "";}
    if($crm_imap_host == ""){$folder_check = "Sent";}   */
    //��� gmail ���� ��� �������
    return $folder_check;
}

function add_client($email){

    $add_client = mysql_query("INSERT INTO crm_clients(email) VALUES ('$email')");
    $client_id = mysql_insert_id();

    echo "<br><b>������ ������</b> � ������� $email<br>";

    return $client_id;


    }




function add_deal($email, $date_email, $subject, $client_id){
  //� ���� title ������ subject ������
    $add_deal = mysql_query("INSERT INTO crm_deals(client_id, title) VALUES ('$client_id', '$subject')");
    $deal_id = mysql_insert_id();

    echo "<br><b>������� ������</b> � ������� <b>$deal_id</b> �� ������ ������ $email<br>";

    return $deal_id;

     }



//������� ��������� ����� ��������, �������� ��������� ������� � ���� �������� ������ � ���������� �������� ������� ������ � ���� ���������� ��� ����������� ���������
function check_dubl_client_appeals($email) {

    $q = "SELECT * FROM crm_clients WHERE email = '$email' AND deleted <> 1";
    $check_client = mysql_query($q);
    $clients_num = mysql_num_rows($check_client);

    if($clients_num>0){

    $check_client = mysql_fetch_assoc($check_client);
    $existing_client_id = $check_client[id];
    //�������� ���������� � ID ������������ ������
    list($check_deals_num,$existing_deal_id,$date_added) = check_deals($existing_client_id);
    }
    echo "<br><b>������ �������� �� �����</b> ����� <b>$email, �������� <b>$clients_num</b> ������</b>. ID - $existing_client_id<br>";
    return array($clients_num,$existing_client_id,$check_deals_num,$existing_deal_id,$date_added);
}


//��������� �������� ������ � ������� �������
function check_deals($existing_client_id){
  //���� ���� ���� �� ���� �������� ������
  $check_deals_q = "SELECT id, date_added FROM crm_deals WHERE client_id = '$existing_client_id' AND status <> 0";
  $check_deals_num = mysql_num_rows(mysql_query($check_deals_q));

  $check_deals_res = mysql_fetch_assoc(mysql_query($check_deals_q));
  $existing_deal_id = $check_deals_res[id];
  $date_added = $check_deals_res[date_added];

  echo "<br><b>��������� ������ ������������ �������</b> id ������� <b>$existing_client_id, �������� <b>$check_deals_num</b> ������. ID ����������� ������ - <b>$existing_deal_id</b></b><br>";

  return array($check_deals_num,$existing_deal_id,$date_added);

}



function add_appeal($deal_id, $date_email, $email_id, $email, $subject, $client_id, $server_uid)
{

    //��������� ������ � ����� ������ (����� ������� ���� ���� �� ��������������� ������)
    $appeals_ins = "INSERT INTO crm_appeals (deal_id, client_id, server_uid, date_email, email_id, email, subject, source, utm,type) VALUES ('$deal_id', '$client_id', '$server_uid', '$date_email', '$email_id', '$email', '$subject', '', '', '1')";
    $add_appeal = mysql_query($appeals_ins);
   echo "<br><b>��������� �������� ���������:</b> �� ������ ������ <b>$deal_id</b>, email ID - $email_id, email - $email, ���� = $subject, $date_email, server_UID - $server_uid<br>".mysql_error();
}

function add_outcoming_email($deal_id, $date_email, $email_id, $email, $subject, $client_id, $server_uid){
       //��������� ������ � ����� ������ (����� ������� ���� ���� �� ��������������� ������)
    $ins_q = "INSERT INTO crm_outcoming_appeals (deal_id, client_id, server_uid, date_email, email_id, email, subject) VALUES ('$deal_id', '$client_id', '$server_uid', '$date_email', '$email_id', '$email', '$subject')";
    $ins = mysql_query($ins_q);
   echo "<br><b>��������� ��������� ���������:</b> �� ������ ������ <b>$deal_id</b>, email ID - $email_id, email - $email, ���� = $subject, $date_email, server_UID - $server_uid<br>".mysql_error();
}



function check_block_list($email){

    $blocked_q = "SELECT * FROM crm_email_stoplist WHERE email = '$email'";
    $blocked = mysql_query($blocked_q);
    $blocked_num = mysql_num_rows($blocked);

if($blocked_num > 0){ echo "<b>������ ����������:</b> email - $email, ������� - <b>����� � ���������</b><br>".mysql_error(); }

    $crm_common_emails_q = "SELECT crm_mail_login FROM crm_member_emails WHERE crm_mail_login = '$email'";
    $crm_common_emails = mysql_query($crm_common_emails_q);
    $crm_common_emails_num = mysql_num_rows($crm_common_emails);

if($crm_common_emails_num > 0){ echo "<b>������ ����������:</b> email - $email, ������� - <b>���������� �����</b><br>".mysql_error();}

    $total_blocked_num = $blocked_num + $crm_common_emails_num;

 return $total_blocked_num;


}









?>