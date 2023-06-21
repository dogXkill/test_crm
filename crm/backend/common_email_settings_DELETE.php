<?require_once("../../acc/includes/db.inc.php");

$act = $_GET["act"];


 if($act == "get_common_emails_list"){

 echo crm_common_emails_list();

 }

  if($act == "save"){

   echo edit_common_email();

  }

   if($act == "add"){

   echo edit_common_email();

  }

   if($act == "delete"){

   echo edit_common_email();

  }


function crm_common_emails_list(){

$res = mysql_query("SELECT * FROM crm_member_emails WHERE type = '0' AND deleted <> '1'");

            while($r = mysql_fetch_array($res)){

                    $id = $r['id'];
                    
                    $crm_mail_login = $r['crm_mail_login'];
                    $crm_mail_pass = $r['crm_mail_pass'];
                    $crm_imap_host = $r['crm_imap_host'];
                    $crm_inbox_check = $r['crm_inbox_check'];
                    $crm_outbox_check = $r['crm_outbox_check'];

                $crm_inbox_check_txt = generate_select("crm_inbox_check",array("0" => "нет", "1" => "да"), $crm_inbox_check, $id);
                $crm_outbox_check_txt = generate_select("crm_outbox_check",array("0" => "нет", "1" => "да"), $crm_outbox_check, $id);


                $common_emails_list .= "<div style=\"cursor:pointer;\"><span onclick=\"show_common_email_settings('$id')\"><b>$crm_mail_login</b></span> <button onclick=\"common_email_settings('delete','$id')\" class=\"del_but_class\">-</button></div><br>

                <div id=common_email_settings_tr_$id class=\"common_email_settings_div_class\">
                почтовый €щик: <input type=\"text\" id=\"crm_mail_login$id\" value=\"$crm_mail_login\"/>
                пароль: <input type=\"text\" id=\"crm_mail_pass$id\" value=\"$crm_mail_pass\"/><br>
                imap хост: <input type=\"text\" id=\"crm_imap_host$id\" value=\"$crm_imap_host\"/>


                <br>
                провер€ть вход€щие:
                $crm_inbox_check_txt
                провер€ть исход€щие:
                $crm_outbox_check_txt
                <br><button onclick=\"common_email_settings('save','$id')\">—охранить!</button>
                </div>";

            }


                $crm_inbox_check_txt = generate_select("crm_inbox_check",array("0" => "нет", "1" => "да"), '0', '');
                $crm_outbox_check_txt = generate_select("crm_outbox_check",array("0" => "нет", "1" => "да"), '0', '');

            $common_emails_list_empty = "
                <br><div class=\"empty_new_common_email\">
                почтовый €щик: <input type=\"text\" id=\"crm_mail_login\" class=crm_form_text value=\"\"/>
                пароль: <input type=\"text\" id=\"crm_mail_pass\" class=crm_form_text value=\"\"/><br>
                imap хост: <input type=\"text\" id=\"crm_imap_host\" class=crm_form_text value=\"\"/>

                
                <br>
                провер€ть вход€щие:
                $crm_inbox_check_txt
                провер€ть исход€щие:
                $crm_outbox_check_txt
                <br><button onclick=\"common_email_settings('add','')\">ƒобавить!</button>
                </div>";


            $common_emails_list = "$common_emails_list $common_emails_list_empty";
            return $common_emails_list;

}



function generate_select($id_select, $arr_opt, $selected_opt, $id){

foreach ($arr_opt as $key => $value) {
        if($selected_opt == $key){$sel = "selected";}
$res .= "<option value=\"$key\" $sel>$value</option>";
$sel = "";
}

$res = "<select id=\"".$id_select."$id\" onchange=\"users_settings('save','$id')\" class=\"crm_select\">$res</select>";

return $res;

}



function edit_common_email(){

    $str = $_SERVER['QUERY_STRING'];
    parse_str($str);

        if($act == "save"){
        $save_data_q = "UPDATE crm_member_emails SET crm_mail_login='$crm_mail_login', crm_mail_pass='$crm_mail_pass', crm_imap_host='$crm_imap_host', crm_inbox_check='$crm_inbox_check', crm_outbox_check='$crm_outbox_check' WHERE id = '$id'";
        $save_data = mysql_query($save_data_q);
        if($save_data == TRUE){echo "ok";}else{echo $save_data_q.mysql_error();}
        }

        if($act == "add"){
        $add_q = "INSERT INTO crm_member_emails(type, crm_mail_login, crm_mail_pass, crm_imap_host, crm_inbox_check, crm_outbox_check) VALUES ('0', $crm_mail_login', '$crm_mail_pass', '$crm_imap_host', '$crm_inbox_check', '$crm_outbox_check')";
        $add = mysql_query($add_q);
        if($add == TRUE){
          return crm_common_emails_list();}else{echo $add_q.mysql_error();}
        }

        if($act == "delete"){
        $del_q = "UPDATE crm_member_emails SET deleted = '1' WHERE id = '$id'";
        $del = mysql_query($del_q);
        if($del == TRUE){return crm_common_emails_list();}else{echo $del_q.mysql_error();}

                }

}

?>