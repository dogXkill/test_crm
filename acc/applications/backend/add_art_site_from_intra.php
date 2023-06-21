<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php');

$str = $_SERVER['QUERY_STRING'];
parse_str($str);
//echo "<pre>";
//print_r($rubrics);
//echo "</pre>";

//для формирования заголовка, получаем типы изделий в массив
$type = mysql_query("SELECT * FROM types");
while ( $row = mysql_fetch_array($type) ) {
$types[$row[0]] = $row[1];
}

//для формирования заголовка, получаем материалы в массив
$color = mysql_query("SELECT * FROM colours");
while ( $row = mysql_fetch_array($color)){$colors[$row[0]] = $row[1];}

//для формирования заголовка, получаем материалы в массив
$material = mysql_query("SELECT * FROM materials");
while ( $row = mysql_fetch_array($type)){$materials[$row[0]] = $row[1];}

//ламинация
if($izd_lami == "1"){$lam_text = " матовый";}
if($izd_lami == "2"){$lam_text = " глянцевый";}

if($vip == "1"){$vip_text = " VIP";}

//print_r($types);
$title = $types[$izd_type].", ".$izd_w."x".$izd_v."x".$izd_b.", ".$colors[$izd_color]."".$materials[$izd_material].$lam_text.$vip_text;

//получаем последний номер артикула
$max_art_id = mysql_query("SELECT MAX(art_id) FROM shop_goods;");
$max_art_id = mysql_fetch_array($max_art_id);
$art_id = $max_art_id[0]+1;
$primechanie = iconv("UTF-8", "windows-1251", $primechanie);
if(is_numeric($art_id)){
$add = "INSERT INTO shop_goods SET
num = '',
cat_id = '0',
art_id = '$art_id',
title='$title',
min_part='$col_in_pack',
izd_type = '$izd_type',
izd_w = '$izd_w',
izd_v = '$izd_v',
izd_b = '$izd_b',
paper_num_list='$paper_num_list',
izd_color = '$izd_color',
izd_color_inn = '$izd_color_inn',
izd_material = '$izd_material',
paper_density = '$paper_density',
izd_gramm = '$izd_gramm',
izd_lami = '$izd_lami',
izd_ruchki = '$izd_ruchki',
hand_color = '$hand_color',
hand_thick = '$hand_thick',
hand_length = '$hand_length',
hands_krepl = '$hands_krepl',
strengt_bot = '$strengt_bot',
strengt_side = '$strengt_side',
html_title = '$title',
pack = '$pack',
col_in_pack = '$col_in_pack',
price_our = '$price_our',
r_price_our = '$r_price_our',
price = '$price',
auto_descr='1',
manufacturer='1',
country='2',
onn = '$onn',
print = '$print',
show_when_zero = '$show_when_zero',
list_h = '$list_h',
list_w = '$list_w',
isdely_per_list = '$isdely_per_list',
primechanie = '$primechanie',
vip = '$vip'";
$add_art = mysql_query($add);


//получаем
if($add_art == true){

$uid = mysql_insert_id();

if($rubrics){
foreach ($rubrics as $t){
$add_rubr = "INSERT INTO shop_add_cats (item, cat) VALUES ('$uid', '$t')";
$res = mysql_query($add_rubr);
//echo $add_rubr."<br>";
}}

}}
if(is_numeric($art_id) && is_numeric($uid)){
echo $art_id.";".$uid;}else{
echo mysql_error();}
?>