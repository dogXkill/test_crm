<?require_once("../../acc/includes/db.inc.php");
 $id = $_GET['id'];
 $act = $_GET['act'];
 $new_tag = $_GET['new_tag'];

if($act == "add" and $new_tag !== ""){

$add = mysql_query("INSERT INTO crm_deal_tags(tag) VALUES ('$new_tag')");
if($add == TRUE){echo mysql_insert_id();}else{echo mysql_error();}

}


if($act == "delete" and is_numeric($id)){

$delelte = mysql_query("UPDATE crm_deal_tags SET deleted = '1' WHERE id = '$id'");
if($delelte == TRUE){echo "ok";}else{echo mysql_error();}

}




?>