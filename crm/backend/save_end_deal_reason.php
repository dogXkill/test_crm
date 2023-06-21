<?require_once("../../acc/includes/db.inc.php");

 $reason_end_id = $_GET['reason_end_id'];
 $deal_id = $_GET['deal_id'];

 //echo "reply $stage_num $ids<br>";

 if(is_numeric($reason_end_id) and is_numeric($deal_id)){
   $update_deals_q = "UPDATE crm_deals SET reason_end_id = '$reason_end_id' WHERE id = '$deal_id'";
   $update_deals = mysql_query($update_deals_q);
   //echo mysql_error().$update_deals_q;
   if($update_deals == TRUE){echo "updated";}else{echo mysql_error().$update_deals_q;}
 }




?>