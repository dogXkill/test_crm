<?php
header("Content-type: text/html; charset=UTF-8");
define('DB_DRIVER', 'mysqli');
define("DB", "");
define("User", "");
define("Passwd", "");
define("HostName", "");
define('DB_PORT', '3306');
$link = mysqli_connect(HostName,User,Passwd,DB)
    or die("Ошибка " . mysqli_error($link));
	$link->set_charset("utf8");
//print_r($_GET);
//echo mb_internal_encoding();
$text=$_GET['text'];

if (isset($_GET['coord_yandex'])){
	$kod=$_GET['coord_yandex'];
	//$text=iconv('UTF-8', 'windows-1251', $text);
	$query = "SELECT * FROM maps_histori WHERE text LIKE '{$text}%' ";
	//echo $query;

	$res = mysqli_query($link,$query);
	if (mysqli_num_rows($res) == 0) {
		$text2=$_GET['kor_address'];
	$sql="INSERT INTO `maps_histori` (`id`, `text`,`text2`,`kord`) VALUES (NULL, '{$text2}','{$text}', '{$kod[0]},{$kod[1]}');";
		//echo $sql;
		$res1=mysqli_query($link,$sql)or die (mysqli_error());
		
	}
}else{

	//проверяем по адресу есть ли координаты
	$sql1= "SELECT * FROM `maps_histori` WHERE `text` LIKE '{$text}%'";
	//echo $sql1;
	$res1 = mysqli_query($link,$sql1)or die (mysqli_error());
	if ($res1){
		
		//echo mysql_num_rows($res1);
	if (mysqli_num_rows($res1) == 0) {
		echo "0";
	}else{
		$row = mysqli_fetch_assoc($res1);
		echo "{$row['kord']}";
	}
	}else{echo "0";}
}

?>	
