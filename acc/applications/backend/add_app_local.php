<?php
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");

$str = $_SERVER['QUERY_STRING'];
parse_str($str);
parse_str($str,$str_mas);
//ѕќ—“ј¬»“№ ”—Ћќ¬»я!
$spec_req = iconv("UTF-8", "windows-1251", $spec_req);
$material_comment = iconv("UTF-8", "windows-1251", $material_comment);
$tisn_comment = iconv("UTF-8", "windows-1251", $tisn_comment);
$color_pantone = iconv("UTF-8", "windows-1251", $color_pantone);
$ClientName = iconv("UTF-8", "windows-1251", $ClientName);
$text_on_izd = iconv("UTF-8", "windows-1251", $text_on_izd);
$hand_txt = iconv("UTF-8", "windows-1251", $hand_txt);
$stamp_num = iconv("UTF-8", "windows-1251", $stamp_num);
//дата за€вки!!!!
$dat_ord = date("Y-m-d H:i:s");
//сравнение полей (если не новый)
$pole_izm=0;

$izm_pole=[
"art_id","art_uid","user_id",
"tiraz","limit_per","deadline",
"deadline_stamp","deadline_material","deadline_pechat","izd_w",
"izd_v","izd_b","klapan","podvorot",
"stamp_num","izd_color",
"color_pantone","izd_color_inn","izd_material",
"material_suppl","izd_gramm","material_comment",
"virub_isdely_per_list","izd_ruchki",
"list_h","list_w","isdely_per_list","paper_suppl",
"sborka_type","no_sborka","paper_num_list",
"paper_list_typ","luve","izd_lami","izd_lami_storon","izd_virub_storon",
"lami_isdely_per_list","tisnenie","izd_tisn_storon","col_ottiskov_izd",
"tisn_comment","hands_krepl","hand_thick","hand_color",
"hand_length","hand_type","hand_txt","gluing_material","strengt_bot","strengt_side","strengt_tip",
"pack","col_in_pack","spec_req","resperson_material","resperson_pechat","rate_1","rate_2","rate_3","rate_4","rate_5","rate_6","rate_7","rate_8",
"rate_9","rate_10","rate_11","rate_12","rate_13","rate_14","rate_15","rate_16","rate_17","rate_18","rate_19","rate_20","rate_21",
"rate_22","rate_23","rate_24","rate_25","rate_26","rate_27","rate_28","rate_30","rate_31",
"app_type","shelko_art","shelko_num_colors","shelko_prokatok","shelko_storon","izd_type","ClientName",
"text_on_izd","preview_link","zakaz_id","vip","dressing","plan_in","stamp_order","klishe_order","shnur_order","utv_pech_list",
"utv_got_izd","utv_ruchki"
];
$pole_izm="";
$res=mysql_query("SELECT * FROM `applications` WHERE `uid` = {$uid}");
if ($res){
if (mysql_num_rows($res)>=1){
	
	$fl_izm=0;
	 $mas_izm=mysql_fetch_assoc($res);
	 foreach ($izm_pole as $key => $val){
		 //echo "key:{$val}  - {$str_mas[$val]} / {$mas_izm[$val]} </br>";
		 if (mb_convert_encoding($str_mas[$val], 'windows-1251', 'utf-8')!=$mas_izm[$val] && $str_mas[$val]!=""){
			 //echo "K:{$val}	".mb_convert_encoding($str_mas[$val], 'windows-1251', 'utf-8')."|".$mas_izm[$val]."</br>";
			$pole_izm.=$val."|";
			$fl_izm=1;
		 }
	 }
	 if ($fl_izm!=1){$pole_izm=0;}
	 
	 
}else{$pole_izm=0;}
}else{$pole_izm=0;}

$max_num_ord = mysql_fetch_array(mysql_query("SELECT MAX(num_ord) FROM applications"));
$new_num_ord = $max_num_ord[0] + 1;

$ins_text_pole = "uid, num_ord, art_id, art_uid,  user_id, tiraz, limit_per, dat_ord, deadline, deadline_stamp, deadline_material,deadline_pechat, izd_w, izd_v, izd_b, klapan, podvorot, stamp_num, izd_color, color_pantone, izd_color_inn, izd_material, material_suppl, izd_gramm, material_comment, virub_isdely_per_list, izd_ruchki, list_h, list_w, isdely_per_list, paper_suppl, sborka_type, no_sborka, paper_num_list, paper_list_typ, luve, izd_lami, izd_lami_storon, izd_virub_storon, lami_isdely_per_list, tisnenie, izd_tisn_storon, col_ottiskov_izd, tisn_comment, hands_krepl, hand_thick, hand_color, hand_length, hand_type, hand_txt, gluing_material, strengt_bot, strengt_side,strengt_tip, pack, col_in_pack, spec_req, resperson_material,resperson_pechat, rate_1, rate_2, rate_3, rate_4, rate_5, rate_6, rate_7, rate_8, rate_9, rate_10, rate_11, rate_12, rate_13, rate_14, rate_15, rate_16, rate_17, rate_18, rate_19, rate_20, rate_21, rate_22, rate_23, rate_24, rate_25, rate_26, rate_27, rate_28, rate_30, rate_31, app_type, shelko_art, shelko_num_colors, shelko_prokatok, shelko_storon, izd_type, ClientName, text_on_izd, preview_link, zakaz_id, vip,dressing, plan_in, stamp_order, klishe_order, shnur_order, utv_pech_list, utv_got_izd, utv_ruchki";
$ins_text_val = "'$uid', '$new_num_ord', '$art_id', '$art_uid', '$user_id', '$tiraz', '$limit_per', '$dat_ord', '$deadline', '$deadline_stamp', '$deadline_material','$deadline_pechat','$izd_w', '$izd_v', '$izd_b', '$klapan', '$podvorot', '$stamp_num', '$izd_color', '$color_pantone', '$izd_color_inn', '$izd_material', '$material_suppl', '$izd_gramm', '$material_comment', '$virub_isdely_per_list', '$izd_ruchki', '$list_h', '$list_w', '$isdely_per_list', '$paper_suppl', '$sborka_type', '$no_sborka','$paper_num_list', '$paper_list_typ', '$luve', '$izd_lami', '$izd_lami_storon', '$izd_virub_storon', '$lami_isdely_per_list', '$tisnenie', '$izd_tisn_storon', '$col_ottiskov_izd', '$tisn_comment', '$hands_krepl', '$hand_thick', '$hand_color', '$hand_length', '$hand_type', '$hand_txt', '$gluing_material', '$strengt_bot', '$strengt_side','$strengt_tip', '$pack', '$col_in_pack', '$spec_req', '$resperson_material','$resperson_pechat', '$rate_1', '$rate_2', '$rate_3', '$rate_4', '$rate_5', '$rate_6', '$rate_7', '$rate_8', '$rate_9', '$rate_10', '$rate_11', '$rate_12', '$rate_13', '$rate_14', '$rate_15', '$rate_16', '$rate_17', '$rate_18', '$rate_19', '$rate_20', '$rate_21', '$rate_22', '$rate_23', '$rate_24', '$rate_25', '$rate_26', '$rate_27', '$rate_28', '$rate_30', '$rate_31', '$app_type', '$shelko_art', '$shelko_num_colors', '$shelko_prokatok', '$shelko_storon','$izd_type', '$ClientName', '$text_on_izd', '$preview_link','$zakaz_id', '$vip','$dressing', '$plan_in', '$stamp_order', '$klishe_order', '$shnur_order', '$utv_pech_list', '$utv_got_izd', '$utv_ruchki'";
$upd_text = "art_id='$art_id', art_uid='$art_uid', user_id='$user_id', tiraz='$tiraz', limit_per='$limit_per', deadline='$deadline', deadline_stamp='$deadline_stamp', deadline_material='$deadline_material',deadline_pechat='$deadline_pechat', izd_w='$izd_w', izd_v='$izd_v', izd_b='$izd_b', klapan='$klapan', podvorot='$podvorot', stamp_num='$stamp_num', izd_color='$izd_color', color_pantone='$color_pantone',izd_color_inn='$izd_color_inn', izd_material='$izd_material', material_suppl='$material_suppl', izd_gramm='$izd_gramm', material_comment='$material_comment', virub_isdely_per_list='$virub_isdely_per_list', izd_ruchki='$izd_ruchki', list_h='$list_h', list_w='$list_w', isdely_per_list='$isdely_per_list', paper_suppl='$paper_suppl', sborka_type='$sborka_type', no_sborka='$no_sborka',paper_num_list='$paper_num_list', paper_list_typ='$paper_list_typ', luve='$luve', izd_lami='$izd_lami', izd_lami_storon='$izd_lami_storon', izd_virub_storon='$izd_virub_storon', lami_isdely_per_list='$lami_isdely_per_list', tisnenie='$tisnenie', izd_tisn_storon='$izd_tisn_storon', col_ottiskov_izd='$col_ottiskov_izd', tisn_comment='$tisn_comment', hands_krepl='$hands_krepl', hand_thick='$hand_thick', hand_color='$hand_color', hand_length='$hand_length', hand_type='$hand_type', hand_txt='$hand_txt', gluing_material='$gluing_material', strengt_bot='$strengt_bot', strengt_side='$strengt_side',strengt_tip='$strengt_tip', pack='$pack', col_in_pack='$col_in_pack', spec_req='$spec_req', resperson_material='$resperson_material',resperson_pechat='$resperson_pechat', rate_1='$rate_1', rate_2='$rate_2', rate_3='$rate_3', rate_4='$rate_4', rate_5='$rate_5', rate_6='$rate_6', rate_7='$rate_7', rate_8='$rate_8', rate_9='$rate_9', rate_10='$rate_10', rate_11='$rate_11', rate_12='$rate_12', rate_13='$rate_13', rate_14='$rate_14', rate_15='$rate_15', rate_16='$rate_16', rate_17='$rate_17', rate_18='$rate_18', rate_19='$rate_19', rate_20='$rate_20', rate_21='$rate_21', rate_22='$rate_22', rate_23='$rate_23', rate_24='$rate_24', rate_25='$rate_25', rate_26='$rate_26', rate_27='$rate_27', rate_28='$rate_28', rate_30='$rate_30', rate_31='$rate_31', app_type='$app_type', shelko_art='$shelko_art', shelko_num_colors='$shelko_num_colors',shelko_prokatok='$shelko_prokatok', shelko_storon='$shelko_storon', izd_type='$izd_type', ClientName='$ClientName', text_on_izd='$text_on_izd', preview_link='$preview_link', zakaz_id='$zakaz_id', vip='$vip',dressing='$dressing', plan_in='$plan_in', stamp_order='$stamp_order', klishe_order='$klishe_order', shnur_order='$shnur_order', utv_pech_list='$utv_pech_list', utv_got_izd='$utv_got_izd', utv_ruchki='$utv_ruchki'";

$q = "INSERT INTO applications (".$ins_text_pole.") VALUES(".$ins_text_val.") ON DUPLICATE KEY UPDATE ".$upd_text."";

$query = mysql_query($q);
if($query == "true"){
$insert_id = mysql_insert_id();
//если создали новый арт, то уида пон€тное дело пока не было и мы не можем найти номер заказа
if($uid == ""){$uid = $insert_id;}
$num_ord = mysql_fetch_array(mysql_query("SELECT num_ord FROM applications WHERE uid = '$uid'"));
$num_ord = $num_ord[0];
echo $insert_id.";".$num_ord.";".$pole_izm;
}else{
  echo "error;".mysql_error()."ON DUPLICATE KEY UPDATE ".$upd_text."";
}

?>
