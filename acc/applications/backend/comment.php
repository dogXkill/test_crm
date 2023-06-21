 <?
require_once("../../includes/db.inc.php");
require_once("/home/crmu660633/test.upak.me/docs/acc/query/backend/phpmailer/email_class.php");
//получаем из файла номер заявки, формируем json массив и отправляем его обратно для заполнения формы заявки
if (isset($_GET['num_ord1'])){
	$num_ord = $_GET["num_ord1"];
}else{
	$num_ord = $_GET["num_ord"];
}
$user_name_full = $_GET["user_name_full"];
$comment = $_GET["comment"];

$tek_time = date('d.m.Y H:i');
// = preg_replace('%[^A-Za-zА-Яа-я0-9\s]%', '', $_GET["comment"]);

$act = $_GET["act"];

if(is_numeric($num_ord)){

if($act == "get_comment"){
 $get_comment = mysql_fetch_array(mysql_query("SELECT comment FROM applications WHERE num_ord = '$num_ord'"));
 echo $get_comment[0];
}

if($act == "save_comment"){

//удалить комментарии все, моя фича исключительно
 if($comment == "delete_all"){
 $udate_comment = mysql_query("UPDATE applications SET comment = '' WHERE num_ord = '$num_ord'");
 }else{
 $udate_comment = mysql_query("UPDATE applications SET comment = CONCAT(comment, '<br><b><i>$user_name_full</i></b> <i>$tek_time</i>: $comment') WHERE num_ord = '$num_ord'");
 }


 if($udate_comment == true){echo "updated";}else{echo "ошибка ".mysql_error();};
}
if($act == "save_comment1"){
	

//удалить комментарии все, моя фича исключительно
 if($comment == "delete_all"){
	 if (isset($_GET['num_ord1'])){
		 $udate_comment = mysql_query("UPDATE applications SET comment = '' WHERE num_ord = '$num_ord'");
	 }else{
		$udate_comment = mysql_query("UPDATE applications SET comment = '' WHERE uid = '$num_ord'");
	 }
 }else{
	 $kol_com=explode("|;|",$comment);
	 if (count($kol_com)>2){
		$comment=str_replace("|;|","<br>",$comment);
		$comment=substr($comment, 0, -4);
	 }else{
		 $comment=str_replace("|;|","",$comment);
	 }
	 //print_r($kol_com);
	 //echo $comment;
	 if (isset($_GET['num_ord1'])){
		 $udate_comment = mysql_query("UPDATE applications SET comment = CONCAT(comment, '<br><b><i>$user_name_full</i></b> <i>$tek_time</i>: $comment') WHERE num_ord = '$num_ord'");
	 }else{
		$udate_comment = mysql_query("UPDATE applications SET comment = CONCAT(comment, '<br><b><i>$user_name_full</i></b> <i>$tek_time</i>: $comment') WHERE uid = '$num_ord'");
	 }
 }


 if($udate_comment == true){
	 $email_check=$_GET['email_check'];
	 
	 if ($act == "save_comment1" && $email_check!=false && $email_check!="false"){		
	 //отправка на email
	 //option
	 if (isset($_GET['num_ord1'])){
		$get_comment = mysql_fetch_array(mysql_query("SELECT comment,num_ord,ClientName,type,art_id,user_id FROM applications WHERE num_ord = '$num_ord'"));
	 }else{
		 $get_comment = mysql_fetch_array(mysql_query("SELECT comment,num_ord,ClientName,type,art_id,user_id FROM applications WHERE uid = '$num_ord'"));
	 }
		$sql="SELECT val FROM `options` WHERE `name`='application_title'";
		$result = mysql_query($sql);
		$row = mysql_fetch_row($result);
		$application_title=$row[0];
		//
		$sql="SELECT val FROM `options` WHERE `name`='application_status'";
		$result = mysql_query($sql);
		$row = mysql_fetch_row($result);
		$application_status=$row[0];
		if ($application_status==1){
			//
			$emails=new Emails(0);
			$number=$get_comment[1];
			$client_name=$get_comment[2];
			$type=$get_comment[3];
			$application_title=str_replace("{NUM}",$number,$application_title);
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
			
			$id_us=$get_comment[5];
			$body=$get_comment[0];
			$get_user = mysql_fetch_array(mysql_query("SELECT email FROM users WHERE uid = '$id_us'"));
			$email_client=$get_user[0];
			//****
			$result_email=$emails->send_mail(1,$email_client,$application_title,$body);//менеджер
			$emails->log_save("Измн.Комментариев в заявке - ({$email_client}) - OK");
			//отправка всем deportament==2
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
	 echo 1;
	 }else{echo "ошибка ".mysql_error();};
}

}

?>