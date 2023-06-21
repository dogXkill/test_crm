<?require_once("../../acc/includes/db.inc.php");

 $stage_num = $_GET['stage_num'];
 $id = $_GET['id'];

 //echo "reply $stage_num $ids<br>";

 if(is_numeric($id) and is_numeric($stage_num)){
   $update_deals_q = "UPDATE crm_deals SET stage_num = '$stage_num' WHERE id = '$id'";
   $update_deals = mysql_query($update_deals_q);
   //echo mysql_error().$update_deals_q;
 }


 //не целевой запрос
 if($stage_num == "not_target_lead" and is_numeric($id)){

    //получаем id клиента для дальнейшего удаления
   $client_id = mysql_fetch_assoc(mysql_query("SELECT client_id FROM crm_deals WHERE id = '$id' AND status = '1' and deleted <> '1'"));

   $client_id = $client_id["client_id"];
    //echo mysql_error()."SELECT client_id FROM crm_deals WHERE id = '$id' AND status = '0';"." "." id клиента".$client_id."<br>";


   //ставим статус = 0, что означает нецелевой лид
   $update_deals_q = "UPDATE crm_deals SET stage_num = '0', status = '0' WHERE id = '$id'";
   $update_deals = mysql_query($update_deals_q);
   //echo mysql_error().$update_deals_q." id клиента".$client_id."<br>";

   if(is_numeric($client_id)){
   $update_clients_q = "UPDATE crm_clients SET deleted = '1' WHERE id = '$client_id'";
   $update_clients = mysql_query($update_clients_q);
   //echo mysql_error().$update_clients_q."<br>";
   }

   $update_appeals_q = "UPDATE crm_appeals SET deleted = '1' WHERE deal_id = '$id'";
   $update_appeals = mysql_query($update_appeals_q);
  //echo mysql_error().$update_appeals_q."<br>";

   //добавлять ли емейл в блок лист?

 }

 //отмена сделки
 if($stage_num == "end_of_deal" and is_numeric($id)){

  //ставим статус = 2, что означает окончание сделки
   $update_deals_q = "UPDATE crm_deals SET stage_num = '0', status = '2' WHERE id = '$id'";
   $update_deals = mysql_query($update_deals_q);
   //echo mysql_error().$update_deals_q;

   echo get_end_deal_reasons($id);



 }



 function get_end_deal_reasons($deal_id){

    $get_reasons = mysql_query("SELECT * FROM crm_end_deal_reasons WHERE deleted <> '1'");
                  echo mysql_error();

    if(is_numeric($deal_id))

    $reasons_list = "<span class=\"container_end_deal\"><h2>Укажите причину отмены сделки:</h2><br>";
    while($r = mysql_fetch_assoc($get_reasons)){
      $reason_end_id = $r["id"];
      $name = $r["name"];
    $reasons_list .= "<span id=\"reason_$reason_end_id\" class=\"end_deal_reason\" onclick=\"end_deal_save_reason('$reason_end_id')\">$name</span>";
    }
    $reasons_list .= "<input type=hidden id=\"end_deal_reason_deal_id\" value=\"$deal_id\"></span>";
    return $reasons_list;
}


?>