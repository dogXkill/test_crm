<?
require_once("../acc/includes/db.inc.php");
require_once("backend/lib.php");


//сначала проверяем входящие письма, чтобы на основе входящего письма преимущественно создавались сделки, потом уже исходящие, чтобы привязать их к сделке
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

  //формируем запрос в таблицу, на обновление последнего номера письма данного почтового ящика, для каждой папки отдельно
  if($crm_outbox_check == "1"){$last_uid_vst = "crm_outbox_last_uid";}else{$last_uid_vst = "crm_inbox_last_uid";}

  //прибавляем единицу, чтобы не проверять последний емейл
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
    echo "<h2>ВХОДЯЩИЕ - $crm_mail_login</h2>";
    }
    //тогда вызываем проверку исходящих писем
    else{
    echo "<h2>ИСХОДЯЩИЕ - $crm_mail_login</h2>";
    $folder_check = inbox_outbox_adapt($crm_imap_host);
    }

    $last_uid = mail_logics($crm_mail_login, $crm_mail_pass, $crm_imap_host, $folder_check, $crm_outbox_check, $crm_mail_last_uid,  $email_id);

    set_last_update($email_id, $last_uid, $crm_outbox_check);

        }

    }

}


//функция, которая обрабатывает всю логику входящих писем
function mail_logics($crm_mail_login, $crm_mail_pass, $crm_imap_host, $folder_check, $crm_outbox_check, $crm_mail_last_uid, $email_id){

    $mails = get_mails($crm_mail_login, $crm_mail_pass, $crm_imap_host, $folder_check, $crm_mail_last_uid, $email_id, $crm_outbox_check);

    //если есть новые письма
    if($mails){

  //перебираем все письма
  foreach($mails as $mail){

    list($error_email, $email, $date_email, $subject, $server_uid) = get_email_parts($mail, $crm_outbox_check, $email_id);

    //проверяем что емейл это емейл и что данный емейл не заблокирован
    if ($error_email=="")
    {

    //проверка дублей емейлов, чтобы не вносить одинаковые не зацикливаться на UNSEEN
    $check_dubl_email = check_dubl_emails($email, $email_id, $date_email, $crm_outbox_check);

    if($check_dubl_email == "0"){

    //проверяем есть ли такой клиент УЖЕ в базе, и если есть, то какой у него айди
    list($clients_num, $existing_client_id, $check_deals_num, $existing_deal_id, $date_added) = check_dubl_client_appeals($email);

    //если такой клиент найден, то просто записваем его id
    if($clients_num > 0){
        //если клиент уже есть, то пишем его ID
        $client_id = $existing_client_id;

    }else{
        //добавляем клиента
        $client_id = add_client($email);
    }

    //если у данного клиента уже имеются сделки в активном состоянии (этап больше 0), то не добавляем сделку
    if($check_deals_num == 0 or $check_deals_num == ""){
    $deal_id = add_deal($email, $date_email, $subject, $client_id);
    }else{
    $deal_id = $existing_deal_id;
        }

    //если письмо исходящее, то записываем в др. таблицу
    if($crm_outbox_check !== "1"){
    //сделку добавили, теперь добавляем в crm_appeals обращение с УЖЕ присвоенным номером сделки
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


//функция получает максимальное значение UID сообщения, для того, чтобы последующую проверку с этого номера
function get_last_uid($email_id, $crm_outbox_check){
    if($crm_outbox_check == "1"){$select_table = "crm_outcoming_appeals";}else{$select_table = "crm_appeals";}

    $get_last_uid_q = "SELECT MAX(server_uid) AS server_uid FROM $select_table WHERE email_id = '$email_id'";
    $last_uid = mysql_fetch_assoc(mysql_query($get_last_uid_q));
    $last_uid = $last_uid["server_uid"];

         //echo $get_last_uid_q;
    echo "<i>Осуществлена проверка максимального значения UID сообщения <b><u>$email_id</u></b> в таблице $select_table, полученное значение = <b>$last_uid</b></i><br><br>";

    return $last_uid;

}


function get_mails($crm_mail_login, $crm_mail_pass, $crm_imap_host, $folder_check, $crm_mail_last_uid, $email_id, $crm_outbox_check){
//делаем mbox, где по факту хранятся все сообщения глобальной, чтобы была доступна из др функций
    global $mbox;

    $mbox = imap_open("{".$crm_imap_host."}$folder_check", "$crm_mail_login", "$crm_mail_pass")or die("can't connect: " . imap_last_error());
    //чтобы не загружать сильно сервер, проверяем единовременно не более 300 писем
    $num_msg = $crm_mail_last_uid + 300;


    $mails = imap_fetch_overview($mbox, $crm_mail_last_uid.":".$num_msg, FT_UID);

    echo "Проверка писем в ящике ID - <b>$email_id</b>, UID c <b>$crm_mail_last_uid</b> по <b>$num_msg</b><br><br>";

    return $mails;
}





function get_email_parts($mail, $crm_outbox_check, $email_id){
    global $mbox;

    //номер сообщения в текущей выборке, не соответствует UID
    $msgno = $mail->msgno;
    $server_uid = imap_uid($mbox,$msgno);

    $header = imap_headerinfo($mbox, $msgno);
    $email = $header->from[0]->mailbox ."@".$header->from[0]->host;
    $email = clear_some_parts($email);


    if (check_is_email($email) == "1")
    {
    //получаем остальные данные из письма
    $receipent = $header->to[0]->mailbox ."@".$header->to[0]->host;
    $receipent = clear_some_parts($receipent);
    $date_email = date("Y-m-d H:i:s", strtotime($header->date));
    $subject = clear_subject($header->subject, $crm_outbox_check);

    //если проверяем исходящие, то необходимо поменять местами значения емейлов получателя и отправителя
    if($crm_outbox_check == "1"){list($email, $email_id) = array($receipent, $email);}

    echo "<br>---------***---------<br><b><u>$email</u></b> UID емейла <b>$server_uid</b>, номер емейла в текущей выборке <b>$msgno</b>. Время: <b>$date_email</b><br>";

    //проверка на блок лист
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

//если идет проверка исходящих писем, то ищем в другой таблице
    if($crm_outbox_check == "1"){$select_table = "crm_outcoming_appeals";}else{$select_table = "crm_appeals";}

    $check_q = "SELECT * FROM $select_table WHERE email='$email' AND email_id='$email_id' AND date_email='$date_email' AND deleted <> '1'";
    $check = mysql_query($check_q);
    $check_dubl_email = mysql_num_rows($check);

    echo "<br><b>ЕМЕЙЛ</b> email='$email' AND email_id='$email_id' в таблице $select_table проверен на дубли. Найдено <b>$check_dubl_email</b> совпадений.<br>";

    return $check_dubl_email;
}


//в разных почтовых службах папка отправленные называется по разному, поэтому, адаптируем
function inbox_outbox_adapt($crm_imap_host){

    if(stristr($crm_imap_host, 'imap.yandex.ru') == TRUE){$folder_check = "Sent";}
    if(stristr($crm_imap_host, 'imap.mastermail.ru') == TRUE){$folder_check = "&BB4EQgQ,BEAEMAQyBDsENQQ9BD0ESwQ1-";}
    if(stristr($crm_imap_host, 'mail.nic.ru') == TRUE){$folder_check = "Sent";}

   /* if($crm_imap_host == ":993/imap/ssl"){$folder_check = "";}
    if($crm_imap_host == ""){$folder_check = "Sent";}   */
    //над gmail пока нет решения
    return $folder_check;
}

function add_client($email){

    $add_client = mysql_query("INSERT INTO crm_clients(email) VALUES ('$email')");
    $client_id = mysql_insert_id();

    echo "<br><b>СОЗДАН КЛИЕНТ</b> с емейлом $email<br>";

    return $client_id;


    }




function add_deal($email, $date_email, $subject, $client_id){
  //в поле title вносим subject письма
    $add_deal = mysql_query("INSERT INTO crm_deals(client_id, title) VALUES ('$client_id', '$subject')");
    $deal_id = mysql_insert_id();

    echo "<br><b>СОЗДАНА СДЕЛКА</b> с номером <b>$deal_id</b> на основе емейла $email<br>";

    return $deal_id;

     }



//функция проверяют дубли клиентов, косвенно проверяет наличие у этих клиентов сделок и возвращает айдишник текущей сделки и дату добавления для последующей обработки
function check_dubl_client_appeals($email) {

    $q = "SELECT * FROM crm_clients WHERE email = '$email' AND deleted <> 1";
    $check_client = mysql_query($q);
    $clients_num = mysql_num_rows($check_client);

    if($clients_num>0){

    $check_client = mysql_fetch_assoc($check_client);
    $existing_client_id = $check_client[id];
    //получаем количество и ID существующей сделки
    list($check_deals_num,$existing_deal_id,$date_added) = check_deals($existing_client_id);
    }
    echo "<br><b>КЛИЕНТ ПРОВЕРЕН НА ДУБЛИ</b> емейл <b>$email, выявлено <b>$clients_num</b> дублей</b>. ID - $existing_client_id<br>";
    return array($clients_num,$existing_client_id,$check_deals_num,$existing_deal_id,$date_added);
}


//проверяем открытые сделки у данного клиента
function check_deals($existing_client_id){
  //если есть хотя бы одна активная сделки
  $check_deals_q = "SELECT id, date_added FROM crm_deals WHERE client_id = '$existing_client_id' AND status <> 0";
  $check_deals_num = mysql_num_rows(mysql_query($check_deals_q));

  $check_deals_res = mysql_fetch_assoc(mysql_query($check_deals_q));
  $existing_deal_id = $check_deals_res[id];
  $date_added = $check_deals_res[date_added];

  echo "<br><b>ПРОВЕРЕНЫ ЗАКАЗЫ ДЕЙСТВУЮЩЕГО КЛИЕНТА</b> id клиента <b>$existing_client_id, выявлено <b>$check_deals_num</b> сделок. ID действующей сделки - <b>$existing_deal_id</b></b><br>";

  return array($check_deals_num,$existing_deal_id,$date_added);

}



function add_appeal($deal_id, $date_email, $email_id, $email, $subject, $client_id, $server_uid)
{

    //добавляем письмо в любом случае (позже добавим стоп лист на заблокированные адреса)
    $appeals_ins = "INSERT INTO crm_appeals (deal_id, client_id, server_uid, date_email, email_id, email, subject, source, utm,type) VALUES ('$deal_id', '$client_id', '$server_uid', '$date_email', '$email_id', '$email', '$subject', '', '', '1')";
    $add_appeal = mysql_query($appeals_ins);
   echo "<br><b>ДОБАВЛЕНО ВХОДЯЩЕЕ ОБРАЩЕНИЕ:</b> на основе сделки <b>$deal_id</b>, email ID - $email_id, email - $email, тема = $subject, $date_email, server_UID - $server_uid<br>".mysql_error();
}

function add_outcoming_email($deal_id, $date_email, $email_id, $email, $subject, $client_id, $server_uid){
       //добавляем письмо в любом случае (позже добавим стоп лист на заблокированные адреса)
    $ins_q = "INSERT INTO crm_outcoming_appeals (deal_id, client_id, server_uid, date_email, email_id, email, subject) VALUES ('$deal_id', '$client_id', '$server_uid', '$date_email', '$email_id', '$email', '$subject')";
    $ins = mysql_query($ins_q);
   echo "<br><b>ДОБАВЛЕНО ИСХОДЯЩЕЕ ОБРАЩЕНИЕ:</b> на основе сделки <b>$deal_id</b>, email ID - $email_id, email - $email, тема = $subject, $date_email, server_UID - $server_uid<br>".mysql_error();
}



function check_block_list($email){

    $blocked_q = "SELECT * FROM crm_email_stoplist WHERE email = '$email'";
    $blocked = mysql_query($blocked_q);
    $blocked_num = mysql_num_rows($blocked);

if($blocked_num > 0){ echo "<b>КЛИЕНТ БЛОКИРОВАН:</b> email - $email, причина - <b>емейл в стоплисте</b><br>".mysql_error(); }

    $crm_common_emails_q = "SELECT crm_mail_login FROM crm_member_emails WHERE crm_mail_login = '$email'";
    $crm_common_emails = mysql_query($crm_common_emails_q);
    $crm_common_emails_num = mysql_num_rows($crm_common_emails);

if($crm_common_emails_num > 0){ echo "<b>КЛИЕНТ БЛОКИРОВАН:</b> email - $email, причина - <b>внутренний емейл</b><br>".mysql_error();}

    $total_blocked_num = $blocked_num + $crm_common_emails_num;

 return $total_blocked_num;


}









?>