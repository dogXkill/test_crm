<?
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");

$ar = file_get_contents("https://www.paketoff.ru/modules/shop/exp_intra.php", "r");
$ar = json_decode($ar, true);
$affected = 0;
foreach($ar as $a){

$uid = $a["uid"];
$art_id = $a["art_id"];
$title = $a["title"];
//$title = iconv('utf8', 'cp1251', $title);
$izd_type = $a["izd_type"];
$izd_w = $a["izd_w"];
$izd_v = $a["izd_v"];
$izd_b =$a["izd_b"];
$izd_color =$a["izd_color"];
$izd_material  = $a["izd_material"];
$izd_lami  = $a["izd_lami"];
$izd_ruchki = $a["izd_ruchki"];
$price = $a["price"];
$price_our = $a["price_our"];
$sklad = $a["sklad"];
$booked = $a["booked"];
$onn = $a["onn"];


$update = mysql_query("INSERT INTO plan_arts (uid,art_id,title,izd_type,izd_w,izd_v,izd_b,izd_color,izd_material,izd_lami,izd_ruchki,price,price_our,sklad, booked,onn)
VALUES('$uid','$art_id','$title','$izd_type','$izd_w','$izd_v','$izd_b','$izd_color','$izd_material','$izd_lami','$izd_ruchki','$price','$price_our','$sklad', '$booked', '$onn')
ON DUPLICATE KEY UPDATE uid='$uid',art_id='$art_id',title='$title',izd_type='$izd_type',izd_w='$izd_w',izd_v='$izd_v',izd_b='$izd_b',izd_color='$izd_color',izd_material='$izd_material',izd_lami='$izd_lami',izd_ruchki='$izd_ruchki',price='$price',price_our='$price_our',sklad='$sklad', booked='$booked', onn='$onn'");

//echo $uid." ".$onn." ".$title." ".mysql_affected_rows()."<br>";
if(mysql_affected_rows()){
$affected = $affected+1;
}
}

echo $affected;
$update_synch = mysql_query("UPDATE plan_synch SET date=NOW() WHERE type='web_goods'");
echo mysql_error();
//echo mysql_affected_rows();

?>
<pre>
<? //print_r($ar);
//echo $ar[372][title]?>
</pre>