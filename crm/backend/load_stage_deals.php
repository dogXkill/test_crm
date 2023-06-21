<?
require_once("../../acc/includes/db.inc.php");
require_once("../../acc/includes/auth.php");
global $tpacc;
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;


$stage_num = $_GET["stage_num"];

function get_managers_select(){
    $res = mysql_query("SELECT uid,surname,name FROM users WHERE (type='mng' OR type='sup' OR type='adm' OR type='meg' OR type = 'acc') AND archive <> 1 ORDER BY surname");
    $users_list = "<option value=\"\">выбрать</option> ";
        $uid = $r['uid'];
        $name = $r['name'];
        $surname = $r['surname'];
            while($r = mysql_fetch_array($res)){
                $users_list .= "<option value=\"$uid\">$surname $name</option>";
            }

        if($tpacc){$select_dis = '';}else{$select_dis = 'disabled="disabled"';}
        $otv_select = "<br><select $select_dis name=\"user_select_$uid\" id=\"user_select_$uid\" size=1>$users_list</select>";

        return $otv_select;
    }

get_deal_cards($stage_num);

function get_deal_cards($stage_num){

    $deal_get_q = "SELECT d.id AS deal_id, c.id AS client_id, c.email, a.email_id, d.title, d.date_added, d.user_id, a.type FROM crm_deals AS d, crm_clients AS c, crm_appeals AS a WHERE c.id = d.client_id AND a.client_id = d.client_id AND d.stage_num = '$stage_num' AND d.deleted = '0' AND d.status = '1' GROUP BY d.client_id ORDER BY d.date_added DESC";
    $get = mysql_query($deal_get_q);
    //echo $deal_get_q;
    //echo mysql_error();
    while($r = mysql_fetch_assoc($get)){
              //echo "TEST".$stage_num;
    foreach($r as $key=>$value){eval("$$key = \"$value\";");}

        if($email !== ""){$email_txt = "<br><b>Емейл:</b> <a href=\"mailto:$email\">$email</a>";}else{$email_txt="";}
        if($receipent !== ""){$receipent_txt = "<br><b>кому:</b> $receipent";}else{$receipent_txt="";}
        if($title !== ""){$title = substr($title, 0, 50); $title_txt = "<br><b>описание:</b> $title";}else{$title_txt="описание: нет";}
        if($date_added !== ""){$date_added_txt = "<br><b>время:</b> $date_added";}else{$date_added_txt="";}
        if($source !== "0"){$source_txt = "<br><b>источник:</b> $source";}else{$source_txt="";}
        if($utm !== ""){$utm_txt = "<br><b>utm:</b> $utm";}else{$utm_txt="";}

        //отображаем выбор менеджера только на этапе выше нуля
        if($stage_num>0){$otv_select = get_managers_select();}
        else{$otv_select = "";$block_gif = "<img src=\"i/block.gif\" onclick=\"block_email('$client_id', '$email')\" class=\"block_gif\"/>";}

            if($type == 1){
            echo "<div class=\"crm_kartochka_sdelki\" id=\"$deal_id\">$email_txt $block_gif
            $receipent_txt
            $date_added_txt
            $title_txt
            $otv_select</div>";
                }

            if($type == 2){
            if($company !== ""){$company_txt="<br><b>компания:</b> $company";}
            if($fio !== ""){$fio_txt="<br><b>фио:</b> $fio";}
            if($phone !== ""){$phone_txt="<br><b>телефон:</b> $phone";}
            if($desc !== ""){$desc_txt="<br><b>текст:</b> $desc";}
            echo "<div class=\"crm_kartochka_sdelki\" id='$deal_id'>
            $company_txt
            $fio_txt
            $email_txt
            $phone_txt
            $desc_txt
            $date_added_txt
            $otv_select
            </div>";
            }

            unset($email_txt,$receipent_txt,$subject_txt,$source_txt,$utm_txt,$company_txt,$fio_txt,$phone_txt,$desc_txt);
        }
}



?>