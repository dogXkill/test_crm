<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php');
$art_id = $_GET['art_id'];
if(is_numeric($art_id)){

$q = mysql_fetch_array(mysql_query("SELECT title,izd_type,izd_w,izd_v,izd_b,izd_color,paper_num_list,list_h,list_w,isdely_per_list,strengt_bot,strengt_side,gluing_material,izd_color_inn,izd_material,izd_gramm,izd_lami,izd_ruchki,hand_thick,hand_length,hands_krepl,hand_color,pack,col_in_pack,price_our,r_price_our,vip FROM shop_goods WHERE art_id = '$art_id'"));
print_r($q);
}else{echo "Ответ сайта: Введенный art_id не является номером";}
?>