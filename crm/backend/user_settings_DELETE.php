<?require_once("../../acc/includes/db.inc.php");

$act = $_GET["act"];



 if($act == "get_users"){

  echo users_list();

 }

  if($act == "save"){

   echo save_user_data();

  }




function users_list(){


$users = mysql_query("SELECT * FROM crm_members AS c, users AS u WHERE u.crm_member = '1' AND c.uid = u.uid");

    while($u = mysql_fetch_assoc($users)){

        $user_id = $u['user_id'];
        $uid = $u['uid'];
        $name = $u['name'];
        $surname = $u['surname'];
        $users_name = "<li><span onclick=\"show_user_settings('$user_id')\" style=\"cursor:pointer;\"><b>$surname $name</b></span></li>";

        $user_emails = mysql_query("SELECT * FROM crm_member_emails WHERE user_id = '$user_id'");

        //echo "SELECT * FROM crm_member_emails WHERE user_id = '$user_id'";

             while($e = mysql_fetch_assoc($user_emails)){

             $email_id = $e['id'];
             $crm_mail_login = $e['crm_mail_login'];
             $crm_mail_pass = $e['crm_mail_pass'];
             $crm_imap_host = $e['crm_imap_host'];
             $crm_inbox_check = $e['crm_inbox_check'];
             $crm_outbox_check = $e['crm_outbox_check'];
             $crm_rights = $e['crm_rights'];


             $crm_inbox_check_txt = generate_select("crm_inbox_check",array("0" => "нет", "1" => "да"), $crm_inbox_check, $email_id, "");
             $crm_outbox_check_txt = generate_select("crm_outbox_check",array("0" => "нет", "1" => "да"), $crm_outbox_check, $email_id, "");
             $crm_rights_txt = generate_select("crm_rights",array("0" => "обычный", "1" => "старший менеджер", "2" => "админ"), $crm_rights, $id, "");

             $users_list = get_form($user_id, $crm_mail_login, $crm_mail_pass, $crm_imap_host, $crm_inbox_check_txt, $crm_outbox_check_txt, $crm_rights_txt, "Сохранить!");

             }

             $crm_inbox_check_txt = generate_select("crm_inbox_check",array("0" => "нет", "1" => "да"), "0", $email_id, "");
             $crm_outbox_check_txt = generate_select("crm_outbox_check",array("0" => "нет", "1" => "да"), "0", $email_id, "");
             $crm_rights_txt = generate_select("crm_rights",array("0" => "обычный", "1" => "старший менеджер", "2" => "админ"), "0", $id, "");

             $users_list .=  get_form($user_id, "", "", "", $crm_inbox_check_txt, $crm_outbox_check_txt, $crm_rights_txt, "Добавить!");


            }
            $users_list = "$users_name<table id=user_settings_td_$user_id class=\"user_settings_tbl_class\">$users_list</table>";

            return $users_list;
    }


function get_form($user_id, $crm_mail_login, $crm_mail_pass, $crm_imap_host, $crm_inbox_check_txt, $crm_outbox_check_txt, $crm_rights_txt, $button_text){
  $users_list = "<tr><td colspan=2 class=\"user_settings_td_class\">
                почтовый ящик: <input type=\"text\" id=\"crm_mail_login_$email_id\" value=\"$crm_mail_login\"/>
                пароль: <input type=\"text\" id=\"crm_mail_pass_$email_id\" value=\"$crm_mail_pass\"/><br>
                imap хост: <input type=\"text\" id=\"crm_imap_host_$email_id\" value=\"$crm_imap_host\"/>
                <br>
                проверять входящие:
                $crm_inbox_check_txt
                проверять исходящие:
                $crm_outbox_check_txt
                <br>уровень доступа:
                $crm_rights_txt
                <br><button onclick=\"users_email_settings('save','$email_id','toggle')\">$button_text</button>
                </td></tr>";

                return $users_list;
}

function generate_select($id_select, $arr_opt, $selected_opt, $id, $action){

        foreach ($arr_opt as $key => $value) {
        if($selected_opt == $key){$sel = "selected";}
            $res .= "<option value=\"$key\" $sel>$value</option>";
            $sel = "";
        }

$res = "<select id=\"".$id_select."_$id\" $action>$res</select>";

return $res;

}

function save_user_data(){

    $str = $_SERVER['QUERY_STRING'];
    parse_str($str);


        $save_user_data_q = "UPDATE users SET crm_member='$crm_member', crm_mail_login='$crm_mail_login', crm_mail_pass='$crm_mail_pass', crm_imap_host='$crm_imap_host', crm_smtp_host='$crm_smtp_host', crm_inbox_check='$crm_inbox_check', crm_outbox_check='$crm_outbox_check', crm_rights='$crm_rights' WHERE uid = '$uid'";
        $save_user_data = mysql_query($save_user_data_q);
        if($save_user_data == TRUE){echo "ok";}else{echo $save_user_data_q.mysql_error();}

}

?>