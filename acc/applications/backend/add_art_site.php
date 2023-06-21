<?php
//echo $_GET["act"];
$str = $_SERVER['QUERY_STRING'];

if($_GET["act"] == "add"){
$handle = fopen("https://www.paketoff.ru/modules/shop/backend/add_art_site_from_intra.php?act=add&".$str, "r");
}else{
$handle = fopen("https://www.paketoff.ru/modules/shop/backend/additional_flds.php", "r");
}
$contents = stream_get_contents($handle);
fclose($handle);
echo $contents;

?>