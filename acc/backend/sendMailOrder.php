<?php

    $url = "http://www.paketoff.ru/controllers/components/SendMailOrderIntranet.php?uniq_id=".$_GET['uniq_id']."&email=".$_GET['email'];
    $handle = fopen($url, "rb"); 
    $contents = stream_get_contents($handle);

    fclose($handle);

?>  