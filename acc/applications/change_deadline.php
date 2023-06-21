<?
// обновление дедлайна и создание нового комментари€ в за€вке на эту тему

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");
require_once("/home/crmu660633/test.upak.me/docs/acc/query/backend/phpmailer/email_class.php");
if (isset($_POST['num_ord']) && isset($_POST['uid']) && isset($_POST['from']) && isset($_POST['to'])) 
{

    $from = $_POST['from'];
    $to = $_POST['to'];
    $num_ord = $_POST['num_ord'];
    $uid = $_POST['uid'];
    $date = date("d.m.Y H:i"); 

    $q = "UPDATE applications SET deadline = '$to' WHERE num_ord = $num_ord"; 
    mysql_query("$q");

    $date_from = explode('-', $from);
    $date_from = array_reverse($date_from);
    $date_from = implode('.', $date_from);
 
    $date_to = explode('-', $to);
    $date_to = array_reverse($date_to);
    $date_to = implode('.', $date_to);

    if ($date_from != $date_to) 
    {
        $q = "SELECT name, surname,email FROM users WHERE uid = '$uid'";
        $r = mysql_query("$q");
        $arr = mysql_fetch_assoc($r);
        $name = $arr['name'];
        $surname = $arr['surname'];

        $q = "SELECT comment FROM applications WHERE num_ord = '$num_ord'";
        $r = mysql_query("$q");
        $arr = mysql_fetch_assoc($r);
        $comment = $arr['comment'];

        $comment .= '<br><b><i>' . $name . ' ' . $surname . '</i></b> <i>' . $date . '</i>: ƒедлайн за€вки изменен с ' . $date_from . ' на ' . $date_to;
        $q = "UPDATE applications SET comment = '$comment' WHERE num_ord = '$num_ord'";
        mysql_query($q);
		//
		$sql="SELECT val FROM `options` WHERE `name`='application_title'";
		$result = mysql_query($sql);
		$row = mysql_fetch_row($result);
		$application_title=$row[0];
		//
		$sql="SELECT val FROM `options` WHERE `name`='application_status'";
		$result = mysql_query($sql);
		$row = mysql_fetch_row($result);
		$application_status=$row[0];
		if ($application_status==1 && $_POST['email_check']!=false && $_POST['email_check']!="false"){
			$get_comment = mysql_fetch_array(mysql_query("SELECT comment,num_ord,ClientName,type,art_id,user_id FROM applications WHERE num_ord = '$num_ord'"));
			$sql="SELECT val FROM `options` WHERE `name`='application_title'";
			$result = mysql_query($sql);
			$row = mysql_fetch_row($result);
			//$application_title="В заявке № {$num_ord} - Изменения";
			$application_title=$row[0];
			$client_name=$get_comment[2];
			$type=$get_comment[3];
			$application_title=str_replace("{NUM}",$num_ord,$application_title);
			if ($client_name!=""){
				$application_title=str_replace("{CLIENT}",$client_name,$application_title);
				
			}else{
				$application_title=str_replace("{CLIENT}","",$application_title);
				$application_title=str_replace("(","",$application_title);
				$application_title=str_replace(")","",$application_title);
			}
			if ($type==2){
				$art=$get_comment[4];
				$application_title=str_replace("{ART}",$art,$application_title);
			}else{
				$application_title=str_replace("{ART}","",$application_title);
			}
			
			$emails=new Emails(0);
			$id_us=$get_comment[5];
			//$body="В заявке № {$num_ord} - Добавлен комментарий";
			$body=$comment;
			$get_user = mysql_fetch_array(mysql_query("SELECT email FROM users WHERE uid = '$id_us'"));
			$email_client=$get_user[0];
			//****
			$result_email=$emails->send_mail(1,$email_client,$application_title,$body);
			$emails->log_save("Дедлайн заявки изменен с {$date_from} на {$date_to}");
			$get_users = mysql_query("SELECT email FROM users WHERE `user_department` = '2' AND `archive`=0 ");
			while ($get_user_dep = mysql_fetch_array($get_users)) {
				$email_dep=trim($get_user_dep[0]);
				if (filter_var($email_dep, FILTER_VALIDATE_EMAIL) !== false){
					$result_email=$emails->send_mail(1,$email_dep,$application_title,$body);
					$emails->log_save("Измн.Комментариев в заявке - ({$email_dep}) - OK");
				}else{
					$emails->log_save("Ошибка отправки уведомления на email - ({$email_dep}) - OK");
				}
			
			}
		}
    }

}



?> 