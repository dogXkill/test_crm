<?
$base_url = "http://www.paketoff.ru/modules/admin/shop/backend/shop_query.php?";
$handle = fopen($base_url."min_part=".$_POST["min_part+"]."&col_in_pack=".$_POST["col_in_pack"]."&onn_show=".$_POST["onn"]."&izd_w=".$_POST["izd_w"]."&izd_v=".$_POST["izd_v"]."&izd_b=".$_POST["izd_b"]."&izd_material=".$_POST["izd_material"]."&izd_color=".$_POST["izd_color"]."&izd_lami=".$_POST["izd_lami"]."&izd_ruchki=".$_POST["izd_ruchki"]."&izd_type=".$_POST["izd_type"]."&price_our=".$_POST["price_our"]."&price=".$_POST["price"]."&pack=".$_POST["pack"], "rb");
$contents = stream_get_contents($handle);
fclose($handle);
echo $contents;
?>
