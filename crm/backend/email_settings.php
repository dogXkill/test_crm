<?require_once("../../acc/includes/db.inc.php");

$act = $_GET["act"];
$user_id = $_GET["user_id"];


 if($act == "get_users"){

  echo users_list("get_users");

 }

  if($act == "save"){

   $err = save_user_data();
   echo emails_list($user_id, $err);

  }

  if($act == "add"){

   $err = add_user_data();
   echo emails_list($user_id, $err);

  }

  if($act == "del"){

   delete_email();
   echo emails_list($user_id, '');

  }

  if($act == "show_common_emails"){

    echo emails_list("0", '');

  }


function users_list($show_what){


$users = mysql_query("SELECT u.name, u.surname, c.uid, c.user_id FROM crm_members AS c, users AS u WHERE u.crm_member = '1' AND c.uid = u.uid AND c.deleted <> '1'");

    while($u = mysql_fetch_assoc($users)){

        $user_id = $u['user_id'];
        $uid = $u['uid'];
        $name = $u['name'];
        $surname = $u['surname'];
        $users_name = "<li><span onclick=\"show_email_settings('$user_id')\" style=\"cursor:pointer;\"><b>$surname $name</b></span></li>";


           $users_list = emails_list($user_id, '');

            if($show_what == "get_users"){
            $users_list = "$users_name<div id=email_settings_div$user_id class=\"user_settings_tbl_class\">$users_list</div>";
            }

            $all .= $users_list;
            $users_name = "";
            $users_list = "";
            }

            return $all;
    }




function emails_list($user_id, $err){

$user_emails = mysql_query("SELECT * FROM crm_member_emails WHERE user_id = '$user_id' AND deleted <> '1'");

       // echo $users_name." SELECT * FROM crm_member_emails WHERE user_id = '$user_id' AND deleted <> '1'<br>";

             while($e = mysql_fetch_assoc($user_emails)){

             $email_id = $e['id'];
             $crm_mail_login = $e['crm_mail_login'];
             $crm_mail_pass = $e['crm_mail_pass'];
             $crm_imap_host = $e['crm_imap_host'];
             $crm_inbox_check = $e['crm_inbox_check'];
             $crm_outbox_check = $e['crm_outbox_check'];


             $crm_inbox_check_txt = generate_select("crm_inbox_check",array("0" => "нет", "1" => "да"), $crm_inbox_check, $email_id);
             $crm_outbox_check_txt = generate_select("crm_outbox_check",array("0" => "нет", "1" => "да"), $crm_outbox_check, $email_id);

             $email_list .= get_form($user_id, $email_id, $crm_mail_login, $crm_mail_pass, $crm_imap_host, $crm_inbox_check_txt, $crm_outbox_check_txt, $crm_rights_txt, "Сохранить!", "save");

             }

             $crm_inbox_check_txt = generate_select("crm_inbox_check",array("0" => "нет", "1" => "да"), "0", "");
             $crm_outbox_check_txt = generate_select("crm_outbox_check",array("0" => "нет", "1" => "да"), "0", "");
             $email_list .= $err;
             $email_list .=  get_form($user_id, "", "", "", "", $crm_inbox_check_txt, $crm_outbox_check_txt, $crm_rights_txt, "Добавить!", "add");
             //$email_list =  $email_list.$err;
             return $email_list;
}


function get_form($user_id, $email_id, $crm_mail_login, $crm_mail_pass, $crm_imap_host, $crm_inbox_check_txt, $crm_outbox_check_txt, $crm_rights_txt, $button_text, $act){


if($act == "add"){
    $form_id =  "email_form_add_".$user_id;
}
else{
    $form_id =  "email_form_save_".$email_id;
    $button_del = "<button onclick=\"users_settings('del', '$email_id', '$user_id');return false;\">Удалить!</button>";
}

  $users_list = "<form id=\"$form_id\">почтовый ящик: <input type=\"text\" name=\"crm_mail_login\" value=\"$crm_mail_login\"/>
                пароль: <input type=\"text\" name=\"crm_mail_pass\" value=\"$crm_mail_pass\"/><br>
                imap хост: <input type=\"text\" name=\"crm_imap_host\" value=\"$crm_imap_host\"/>
                <br>
                проверять входящие:
                $crm_inbox_check_txt
                проверять исходящие:
                $crm_outbox_check_txt
                <br><button onclick=\"users_settings('$act', '$email_id', '$user_id');return false;\">$button_text</button>$button_del</form><br>";

                return $users_list;
}

function generate_select($id_select, $arr_opt, $selected_opt, $email_id){

        foreach ($arr_opt as $key => $value) {
        if($selected_opt == $key){$sel = "selected";}
            $res .= "<option value=\"$key\" $sel>$value</option>";
            $sel = "";
        }

$res = "<select name=\"$id_select\">$res</select>";

return $res;

}

function save_user_data(){



    $str = $_SERVER['QUERY_STRING'];
    parse_str($str);

        $err = check_dubl_crm_member_email($crm_mail_login, $email_id);

            if($err == ""){

            $err = check_email_fields($crm_mail_login, $crm_mail_pass, $crm_imap_host);

                if($err == ""){
                $save_user_data_q = "UPDATE crm_member_emails SET crm_mail_login='$crm_mail_login', crm_mail_pass='$crm_mail_pass', crm_imap_host='$crm_imap_host', crm_inbox_check='$crm_inbox_check', crm_outbox_check='$crm_outbox_check' WHERE id = '$email_id'";
                $save_user_data = mysql_query($save_user_data_q);
                if($save_user_data !== TRUE){echo $save_user_data_q.mysql_error();}
                }

            }

             return $err;

}

function add_user_data(){


    $str = $_SERVER['QUERY_STRING'];
    parse_str($str);

    $err = check_dubl_crm_member_email($crm_mail_login, "");

    if($err == ""){
                 $err = check_email_fields($crm_mail_login, $crm_mail_pass, $crm_imap_host);
                 if($err == ""){
                 $add_q = "INSERT INTO crm_member_emails (type, crm_mail_login, crm_mail_pass, crm_imap_host, crm_inbox_check, crm_outbox_check, user_id) VALUES ('1', '$crm_mail_login', '$crm_mail_pass', '$crm_imap_host', '$crm_inbox_check', '$crm_outbox_check', '$user_id')";
                 $add_user_data = mysql_query($add_q);
                 if($add_user_data !== TRUE){echo $add_q.mysql_error();}
                 }
    }

    return $err;


}

function check_email_fields($crm_mail_login, $crm_mail_pass, $crm_imap_host){
  if($crm_imap_host == ""){$err = "<div class=crm_error>Ошибка! Вы не ввели imap host</div>";}
  if($crm_mail_pass == ""){$err = "<div class=crm_error>Ошибка! Вы не ввели пароль</div>";}
  if($crm_mail_login == ""){$err = "<div class=crm_error>Ошибка! Вы не ввели логин</div>";}

  return $err;
}

function check_dubl_crm_member_email($crm_mail_login, $email_id){

    $check_q_active = "SELECT * FROM crm_member_emails WHERE crm_mail_login = '$crm_mail_login' AND deleted <> '1' AND id <> '$email_id'";
    $res_num_active = mysql_num_rows(mysql_query($check_q_active));

        if($res_num_active > 0){$err = "<div class=crm_error>Ошибка! Такой ($crm_mail_login) емейл уже есть в базе данных.</div>";}

    $check_q_deleted = "SELECT * FROM crm_member_emails WHERE crm_mail_login = '$crm_mail_login' AND deleted = '1' AND id <> '$email_id'";
    $res_num_deleted = mysql_num_rows(mysql_query($check_q_deleted));

        if($res_num_deleted > 0){$err = "<div class=crm_error>Ошибка! Такой ($crm_mail_login) емейл уже есть в базе данных в архиве. Необходимо его восстановить из архива.</div>";}

        return $err;

}

function delete_email(){

    $str = $_SERVER['QUERY_STRING'];
    parse_str($str);

    $del_email = mysql_query("UPDATE crm_member_emails SET deleted = '1' WHERE id = '$email_id'");

    
    return $del_email;

}

?>