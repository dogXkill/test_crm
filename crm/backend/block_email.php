<?require_once("../../acc/includes/db.inc.php");

 $client_id = $_GET['client_id'];
 $email = $_GET['email'];
 //echo "reply $stage_num $ids<br>";

 if(is_numeric($client_id)){

   $update_deals_q = "UPDATE crm_deals SET deleted = '1' WHERE client_id = '$client_id'";
   $update_deals = mysql_query($update_deals_q);
   //if($update_deals == TRUE){echo "blocked";}else{echo mysql_error().$update_deals_q;}

   $update_appeals_q = "UPDATE crm_appeals SET deleted = '1' WHERE client_id = '$client_id'";
   $update_appeals = mysql_query($update_appeals_q);
   //if($update_appeals == TRUE){echo "blocked";}else{echo mysql_error().$update_appeals_q;}

   $update_clients_q = "UPDATE crm_clients SET deleted = '1' WHERE id = '$client_id'";
   $update_clients = mysql_query($update_clients_q);
   //if($update_client == TRUE){echo "blocked";}else{echo mysql_error().$update_clients_q;}

   $insert_block_list_q = "INSERT INTO crm_email_stoplist (email) VALUES ('$email')";
   $insert_block_list = mysql_query($insert_block_list_q);
   if($insert_block_list == TRUE){echo "blocked";}else{echo mysql_error().$insert_block_list_q;}


 }




?>