<?php
/*
require_once("/home/crmu660633/crm.upak.me/docs/acc/includes/db.inc.php");
require_once("/home/crmu660633/crm.upak.me/docs/acc/includes/lib.php");*/
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/db.inc.php");
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/lib.php");
ini_set('max_execution_time', 300);

function web_goods(){
    //синхронизация с сайтом
        //$ar = file_get_contents("https://www.paketoff.ru/modules/shop/exp_intra.php?typ=goods", "r");
		//$ar = file_get_contents("http://test.upak.me/acc/plan/test.json", "r");
		//echo __DIR__;
		$ar=file_get_contents( __DIR__ . DIRECTORY_SEPARATOR."test.json","r");//закидываю 1 позицию для теста ,что бы не дёргать магазин
		$ar = json_decode($ar, true);
		$ar[174]['num_sklad']=iconv('utf-8', 'cp1251',$ar[174]['num_sklad']);//на тест
        //print_r($ar);
        foreach ($ar as $a) {
            $fields = array(
                'uid' => $a["uid"],
                'art_id' => $a["art_id"],
                'title' => iconv('utf-8', 'cp1251', $a["title"]),
                'izd_type' => $a["izd_type"],
                'izd_w' => $a["izd_w"],
                'izd_v' => $a["izd_v"],
                'izd_b' => $a["izd_b"],
                'izd_color' => $a["izd_color"],
                'izd_material' => $a["izd_material"],
                'manufacturer' => $a["manufacturer"],
                'izd_lami' => $a["izd_lami"],
                'izd_ruchki' => $a["izd_ruchki"],
                'price' => $a["price"],
                'price_our' => $a["price_our"],
                'r_price_our' => $a["r_price_our"],
                'retail' => $a["retail"],
                'retail_price' => $a["retail_price"],
                'col_in_pack' => $a["col_in_pack"],
                'sklad' => $a["sklad"],
                'onn' => $a["onn"],
                'vip' => $a["vip"],
                'archive' => $a["archive"],
				'num_sklad'=>$a["num_sklad"],
            );

            if ($uid !== "0") {
                $values = array();
                $updates = array();

                foreach ($fields as $key => $value) {
                    $values[] = "'" . mysql_real_escape_string($value) . "'";
                    $updates[] = $key . "='" . mysql_real_escape_string($value) . "'";

                }

                $sql = "INSERT INTO plan_arts (" . implode(', ', array_keys($fields)) . ") VALUES (" . implode(', ', $values) . ") ON DUPLICATE KEY UPDATE " . implode(', ', $updates);

                $update = mysql_query($sql);
            }

            if (mysql_affected_rows()) {
                $affected = $affected+1;
            }


        }


//синхрон типов изделий
$ar = file_get_contents("https://www.paketoff.ru/modules/shop/exp_intra.php?typ=types", "r");
$ar = json_decode($ar, true);
//print_r($ar);
foreach($ar as $a){
$tid = $a["tid"];
$type = $a["type"];
$type = iconv('utf-8', 'cp1251', $type);
$update = mysql_query("INSERT INTO types (tid,type) VALUES('$tid','$type') ON DUPLICATE KEY UPDATE tid='$tid',type='$type'");

if(mysql_affected_rows()){
$affected = $affected+1;
}
}
$ar = "";






//синхрон материалы
$ar = file_get_contents("https://www.paketoff.ru/modules/shop/exp_intra.php?typ=materials", "r");
$ar = json_decode($ar, true);
//print_r($ar);
foreach($ar as $a){
$tid = $a["tid"];
$type = $a["type"];
$type = iconv('utf-8', 'cp1251', $type);
$seq = $a["seq"];
$update = mysql_query("INSERT INTO materials (tid,type,seq) VALUES('$tid','$type','$seq') ON DUPLICATE KEY UPDATE tid='$tid',type='$type',seq='$seq'");
if(mysql_affected_rows()){
$affected = $affected+1;
}

}



//синхрон производители
$ar = file_get_contents("https://www.paketoff.ru/modules/shop/exp_intra.php?typ=manufacturer", "r");
$ar = json_decode($ar, true);
//print_r($ar);
foreach($ar as $a){
$tid = $a["tid"];
$type = $a["type"];
$type = iconv('utf-8', 'cp1251', $type);
$update = mysql_query("INSERT INTO manufacturer (tid,type) VALUES('$tid','$type') ON DUPLICATE KEY UPDATE tid='$tid',type='$type'");

if(mysql_affected_rows()){
$affected = $affected+1;
}
}


//синхрон цветов изделий
$ar = file_get_contents("https://www.paketoff.ru/modules/shop/exp_intra.php?typ=colours", "r");
$ar = json_decode($ar, true);
//$cnt = 1;
foreach($ar as $a){
$cid = $a[cid];
$colour = $ar[$cid][colour];
$colour = iconv('utf-8', 'cp1251', $colour);
$html_colour = $ar[$cid][html_colour];
$seq = $ar[$cid][seq];
$update = mysql_query("INSERT INTO colours (cid,colour,html_colour,seq) VALUES('$cid','$colour','$html_colour','$seq') ON DUPLICATE KEY UPDATE cid='$cid',colour='$colour',html_colour='$html_colour',seq='$seq'");
//$update_text = "INSERT INTO colours (cid,colour,html_colour,seq) VALUES('$cid','$colour','$html_colour','$seq') ON DUPLICATE KEY UPDATE colour='$colour',html_colour='$html_colour',seq='$seq'";
if(mysql_affected_rows()){
$affected = $affected+1;
}
}


update_date("web_goods", $affected);

}

web_goods();

?>