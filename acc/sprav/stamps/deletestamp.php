<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

$number = $_POST['number'];
$ids=$number;
/*получить по id папку для удаления*/
 $q = "SELECT * FROM stamps WHERE id = '$ids'";
 $r = mysql_query($q);
 $stamp_bd = mysql_fetch_assoc($r);
 $photo_path_bd=$stamp_bd['photo'];
 $canvas_path_bd=$stamp_bd['karkas'];
	if ($photo_path_bd!='' && $photo_path_bd!=null){
		//берём путь к файлу и разбиваем по "/" и пишем в пер number(чтобы не менять скрипт)
		$numbers=explode("/",$photo_path_bd);
		$number_photo=$numbers[3];
	}else{$number_photo=null;}
	if ($canvas_path_bd!='' && $canvas_path_bd!=null){
		//берём путь к файлу и разбиваем по "/" и пишем в пер number(чтобы не менять скрипт)
		$numbers=explode("/",$canvas_path_bd);
		$number_canvas=$numbers[3];
	}else{$number_canvas=null;}
$q = "DELETE FROM stamps WHERE id = '$ids'";
$r = mysql_query($q);
if ($number_photo!=null && $number_photo!=''){
	$photo_path = 'photo-stamps/' . $number_photo;
	$all_photos = scandir($photo_path);
	foreach ($all_photos as $key => $value) {
	  if ($value !== '.' && $value !== '..') {
		unlink($photo_path . '/' . $value);
	  }
	}
	rmdir($photo_path);
}
if ($number_canvas!=null && $number_canvas!=''){
	$canvas_path = 'canvas-stamps/' . $number;
	$all_canvas = scandir($canvas_path);
	foreach ($all_canvas as $key => $value) {
	  if ($value !== '.' && $value !== '..') {
		unlink($canvas_path . '/' . $value);
	  }
	}
	rmdir($canvas_path);
}