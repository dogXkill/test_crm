<?require_once("../../acc/includes/db.inc.php");

$del = mysql_query("TRUNCATE TABLE crm_clients;");
$del = mysql_query("TRUNCATE TABLE crm_appeals;");
$del = mysql_query("TRUNCATE TABLE crm_outcoming_appeals;");
$del = mysql_query("TRUNCATE TABLE crm_deals;");

if($del == TRUE){echo "<li>���� �������</li>";}
echo mysql_error();


$upd = mysql_query("UPDATE crm_member_emails SET crm_inbox_last_uid = '1', crm_outbox_last_uid='1', crm_last_mail_check = NOW()");

if($upd == TRUE){echo "<li>������� crm_outbox_last_uid � crm_outbox_last_uid �������� � �������� crm_common_emails � users</li>";}
echo mysql_error();



?>