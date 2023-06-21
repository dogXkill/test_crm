<?php

function load_report(){
	require_once("/home/crmu660633/test.upak.me/docs/acc/includes/db.inc.php");//поменять путь на боевом

	$file = fopen(__DIR__ . '/file.txt', 'r');
$sql=null;
while (!feof($file)) {
    //echo fgets($file);
    //echo '<br>';
	$fileq=explode("|",fgets($file));
	$sql="UPDATE `report2` SET `procee`='{$fileq[1]}' WHERE `id` LIKE '{$fileq[0]}'";
		$result = mysql_query($sql);
		echo $sql."</br>";
}
//echo $sql;
fclose($file);
}
function save_client(){
	require_once("/home/crmu660633/test.upak.me/docs/acc/includes/db.inc.php");//поменять путь на боевом

	$file = fopen(__DIR__ . '/file.txt', 'a');
	$sql=null;
    //echo fgets($file);
    //echo '<br>';
	$sqls="SELECT * FROM `clients`";
	$results=mysql_query($sqls);
	while ($row = 	mysql_fetch_assoc($results)) {
		$zn=$row['email'];
		$id_zn=$row['uid'];
		$sql="UPDATE `clients` SET `email`='{$zn}' WHERE `uid` = '{$id_zn}'";
		//$result = mysql_query($sql);
		echo $sql."</br>";
		fwrite($file,$sql . PHP_EOL);
	}
	
	
	fclose($file);
}
function load_client(){
	require_once("/home/crmu660633/test.upak.me/docs/acc/includes/db.inc.php");//поменять путь на боевом

	$file = fopen(__DIR__ . '/file.txt', 'r');
	$sql=null;
	while (!feof($file)) {
    //echo fgets($file);
    //echo '<br>';
	$fileq=explode("|",fgets($file));
	$sql="UPDATE `clients` SET `email`='{$fileq[1]}' WHERE `id` = '{$fileq[0]}'";
		$result = mysql_query($sql);
		echo $sql."</br>";
	}
	fclose($file);
}
save_client();
				//$result = mysql_query($sql);//снять комментарий для обновления состояния
?>