<?require_once("../../acc/includes/db.inc.php");
 $id = $_GET['id'];
 $act = $_GET['act'];
 $new_end_deal_reason = $_GET['new_end_deal_reason'];

if($act == "add" and $new_end_deal_reason !== ""){

    $add = mysql_query("INSERT INTO crm_end_deal_reasons (name) VALUES ('$new_end_deal_reason')");
    if($add == TRUE){echo mysql_insert_id();}else{echo mysql_error();}

}


if($act == "delete" and is_numeric($id)){

    $delelte = mysql_query("UPDATE crm_end_deal_reasons SET deleted = '1' WHERE id = '$id'");
    if($delelte == TRUE){echo "ok";}else{echo mysql_error();}

}




?>