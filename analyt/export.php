<?php


require_once("/home/crmu660633/crm.upak.me/docs/acc/includes/db.inc.php");

$out = fopen('/home/crmu660633/crm.upak.me/docs/upd/plan_arts_upd.sql', 'w');	//для обновления plan_arts
$out2 = fopen('/home/crmu660633/crm.upak.me/docs/upd/shop_goods_upd.sql', 'w');	//для обновления shop_goods
$in_str = '';
$in_str2 = '';
$keys = '';
$values = '';

$query = "SELECT * FROM `plan_arts`";

$q = mysql_query($query);

while ($row = mysql_fetch_assoc($q)) {
	$length = count($row);
	$i = 0;

	foreach ($row as $key => $value) {
		$keys .= "`" . $key . "`";
		$values .= "'" . $value . "'";
		if (++$i < $length) {
			$keys .= ", ";
			$values .= ", ";
		}
	}

	$in_str = "INSERT INTO `plan_arts` (" . $keys . ") VALUES (" . $values . ");\n";
	
	fwrite($out, $in_str);

	$in_str2 = "UPDATE `shop_goods` SET `months_of_sales`='" . $row['months_of_sales'] . "', `monthly_sales`='" . $row['monthly_sales'] . "', `monthly_profit`='" . $row['monthly_profit'] . "' WHERE `art_id`='" . $row['art_id'] . "';\n";

	fwrite($out2, $in_str2);

	$keys = '';
	$values = '';
}

fclose($out);
fclose($out2);

//ftp
$ftp_server = 'ftp.h003301803.nichost.ru';
$ftp_login = 'h003301803_ftp';
$ftp_pass = '7C7h1lW3D9';

$file = '/home/crmu660633/crm.upak.me/docs/upd/plan_arts_upd.sql';
$file2 = '/home/crmu660633/crm.upak.me/docs/upd/shop_goods_upd.sql';



$remote_file = '/paketoff.ru/docs/upd/plan_arts_upd.sql';
$remote_file2 = '/paketoff.ru/docs/upd/shop_goods_upd.sql';



$conn_id = ftp_connect($ftp_server);
$login_result = ftp_login($conn_id, $ftp_login, $ftp_pass);
ftp_pasv($conn_id, true);


$x = ftp_put($conn_id, $remote_file, $file, FTP_ASCII);

if ($x) {
 echo "данные plan_arts_upd.sql успешно загружены на сайт<br>";
} else {
 echo "ошибка загрузки plan_arts_upd.sql данных на сайт<br>";
}


$x2 = ftp_put($conn_id, $remote_file2, $file2, FTP_ASCII);

if ($x2) {
 echo "данные shop_goods_upd.sql успешно загружены на сайт<br>";
} else {
 echo "ошибка загрузки shop_goods_upd.sql данных на сайт<br>";
}

ftp_close($conn_id);

