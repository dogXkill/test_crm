<?php
/*
echo $_POST["art_id"];
echo $_POST["act"];
*/

if ($_POST["act"] == "get_new_art_id"){

$handle = fopen("http://www.paketoff.ru/modules/shop/get_art_num.php?act=get_new_art_id", "rb");
$contents = stream_get_contents($handle);
fclose($handle);
echo $contents;
}

if ($_POST["act"] == "get_uid"){

$handle = fopen("http://www.paketoff.ru/modules/shop/get_art_num.php?act=get_uid&art_id=".$_POST["art_id"], "rb");
$contents = stream_get_contents($handle);
fclose($handle);
echo $contents;
}
?>
