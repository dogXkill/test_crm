<?php
// Для PHP 5 и выше
$handle = fopen("http://www.paketoff.ru/modules/admin/shop/backend/shop_query.php", "rb");
$contents = stream_get_contents($handle);
fclose($handle);
echo $contents;
?>
