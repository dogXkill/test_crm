<?

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

$id = $_POST['id'];
$number = $_POST['number'];
$shir = $_POST['shir'];
$vis = $_POST['vis'];
$bok = $_POST['bok'];
$size_x = $_POST['size_x'];
$size_y = $_POST['size_y'];
$another_stamp = $_POST['another_stamp'];
$id_another_stamp = $_POST['id_another_stamp'];
$izd_type = $_POST['izd_type'];
$comment = $_POST['comment'];
$status_vk_r=$_POST['status_vk_r'];
$skleika_tip=$_POST['skleika_tip'];
$kol_izd=$_POST['kol_izd'];

$comment = iconv("utf-8", "cp1251", $comment);

$allow_save = 1;




// Check another stamp in DB
if (!empty($id_another_stamp)) {
	//проверяем на кол-во связанных 
	if (strpos($id_another_stamp, ",") != false) {//более 1
		$mas_another_id=explode(",",$id_another_stamp);
		//print_r($mas_another_id);
		$save_stamp='';
		foreach ($mas_another_id as $value){
			$q = "SELECT * FROM stamps WHERE id = '$value'";
			$r = mysql_query ($q);
			$arr = mysql_fetch_assoc($r);
			if (empty($arr['id'])) {
			  $save_stamp = '';
			  echo 'Номер похожего штампа не найден. Введите существующий номер!';
			} else {
				if ($save_stamp==''){
					$save_stamp=$value;
				}else{
					$save_stamp.= ",".$value;
				}
			}
		}
	}else{//1
		$q = "SELECT * FROM stamps WHERE id = '$id_another_stamp'";
	$r = mysql_query ($q);
	$arr = mysql_fetch_assoc($r);
	if (empty($arr['id'])) {
	  $save_stamp = '';
	  echo 'Номер похожего штампа не найден. Введите существующий номер!';
	} else {
	  $save_stamp = $id_another_stamp;
	}
	}
	
  }

if (!isset($_POST['new']))
{
  // Edit existing stamp

  // Check this stamp number in DB
  $q = "SELECT * FROM stamps WHERE id = $id";
  $r = mysql_query($q);
  $arr = mysql_fetch_assoc($r);
  $number_in_db = $arr['number'];
  $izd_tip_db=$arr['izd_type'];
  if ($number_in_db !== $number) {//изменился номер 
    $q = "SELECT * FROM stamps WHERE number = '$number'";
    $r = mysql_query($q);
    $arr = mysql_fetch_assoc($r);
    if (!empty($arr['id'])) {
		$kol_sht=mysql_num_rows($r);
		//проверка на префиксы
		
		  if ($kol_sht>1){//если таких штампов больше 1-го(по номеру)
	  //print_r($arr);
	  while( $row = mysql_fetch_assoc($r) ){
			 if  ($row['izd_type']==$_POST['izd_type']){echo "Найден штамп с таким же номером!Введите другой номер";exit();}
		  }
		   $allow_save = 1;
	  }else if ($kol_sht==1){//если 1
	  echo "K:{$kol_sht}|{$_POST['izd_type']}|{$arr['izd_type']}";
	  
		  if ($_POST['izd_type']!=$arr['izd_type']){
		  //другой префикс
		  //echo "изменяем с другим префиксом";
		  $allow_save = 1;
		  }
	  }else{
		  //штамп есть..но префикса не найдено
		  $allow_save = 1;
	  }
    }
  }else if($number_in_db === $number && $izd_tip_db==$izd_type){//тот же номер и тот же префикс(изменение данных)
	  $allow_save = 1;
  }else{//другой номер и другой тип
	  //проверяем изменился ли префикс(номер тот же)
	   $q = "SELECT * FROM stamps WHERE number = '$number'";
	   $r = mysql_query($q);
	   $kol_sht=mysql_num_rows($r);
	   if ($kol_sht>1){//если таких штампов больше 1-го(по номеру)
	  //print_r($arr);
	  while( $row = mysql_fetch_assoc($r) ){
			 if  ($row['izd_type']==$_POST['izd_type']){echo "Найден штамп с таким же префиксом!Введите другой номер";exit();}
		  }
	  }else if ($kol_sht==1){//если 1
		  if ($_POST['izd_type']!=$arr['izd_type']){
		  //другой префикс
		  //echo "изменяем с другим префиксом";
		  $allow_save = 1;
		  }
	  }else{
		  //штамп есть..но префикса не найдено
		  $allow_save = 1;
	  }
  }
  unset($arr);

  if ($allow_save == 1)
  {
    $q = "SELECT * FROM stamps WHERE id = $id";
    $r = mysql_query($q);
    $arr = mysql_fetch_assoc($r);
    $sql_number = $arr['number'];
    if ($sql_number !== $number) {
      //rename(__DIR__ . '/canvas-stamps/' . $sql_number . '/', __DIR__ . '/canvas-stamps/' . $number . '/');
      //rename(__DIR__ . '/photo-stamps/' . $sql_number . '/', __DIR__ . '/photo-stamps/' . $number . '/');
    }
	$photo_dir = $_POST['nums_folder'];
	$paths = __DIR__ . '/photo-stamps/' . $photo_dir . '/';
	if (is_dir($paths)!==false){//если пустое поле photo , то добавляем туда путь(если нашли такую папку)
		$files = scandir($paths);
		//print_r($files);
		if (count($files)>2){
			$photo_stamp='/stamps/photo-stamps/'. $photo_dir . '/'.$files[2];
		}else{
			$photo_stamp=null;
		}
	}
	$paths = __DIR__ . '/canvas-stamps/' . $photo_dir . '/';
	//echo $paths;
	if (is_dir($paths)!==false){//если пустое поле canvas , то добавляем туда путь(если нашли такую папку)
		$files = scandir($paths);
		//print_r($files);
		if (count($files)>2){
			$canvas_stamp='/stamps/canvas-stamps/'. $photo_dir . '/'.$files[2];
		}else{$canvas_stamp=null;}
	}
    unset ($arr);
	$photo_and='';
	if ($photo_stamp!=null){
		$photo_and=",photo='$photo_stamp'";
	}else{$photo_and=",photo=''";}
	
	$canvas_and='';
	if ($canvas_stamp!=null){
		$canvas_and=",karkas='$canvas_stamp'";
	}else{$canvas_and=",karkas=''";}
	
    $q = "UPDATE stamps SET number = '$number', shir = '$shir', vis = '$vis', bok = '$bok', size_x = '$size_x', size_y = '$size_y', another_stamp = '$save_stamp',status_vk_r='$status_vk_r',skleika='$skleika_tip',kol_izd='$kol_izd', izd_type = '$izd_type'{$photo_and}{$canvas_and}, comment = '$comment' WHERE id = '$id'";
    //echo $q;
	$r = mysql_query($q);
  }

}


if (isset($_POST['new']))
{
  // New stamp

  // Check this stamp number in DB
  $q = "SELECT * FROM stamps WHERE number = '$number'";
  //echo $q;
  $r = mysql_query($q);
  
  $arr = mysql_fetch_assoc($r);
  $allow_save =0;
  if (!empty($arr['id'])) {
	  $mas_prefix=array();
	  $kol_sht=mysql_num_rows($r);
	  //echo "Найдено:{$kol_sht}";
	  if ($kol_sht>1){//если таких штампов больше 1-го(по номеру)
	  //print_r($arr);
	  while( $row = mysql_fetch_assoc($r) ){
			 // $mas_prefix[]=
			 //echo $row['izd_type'];
			 if  ($row['izd_type']==$_POST['izd_type']){echo "Найден штамп с таким же номером!Введите другой номер";exit();}
		  }
		  $allow_save = 1;
	  }else if ($kol_sht==1){//если 1
		echo "{$kol_sht}| {$_POST['izd_type']} | {$arr['izd_type']}";
		  if ($_POST['izd_type']!=$arr['izd_type']){
		  //другой префикс
		  //echo "Создаём с другим префиксом";
		  $allow_save = 1;
		  }else{echo "Найден штамп с таким же номером!Введите другой номер";exit();}
	  }else{
		  //штамп есть..но префикса не найдено
		  //echo "Номер штампа с таким префиксом уже есть!Введите другой номер";
		  $allow_save = 1;
	  }
	  
	  //сверяем с какими prefix он есть 
    //echo 'Такой номер штампа уже существует. Введите другой номер! ';
    
  }else{$allow_save =1;}
  
  unset ($arr);

  if ($allow_save == 1)
  {
    $q = "SELECT MAX(id) FROM stamps";
    $r = mysql_query($q);
    $arr = mysql_fetch_array($r);
    $newid = $arr[0] + 1;
	//ловим id юзера
	$userIdAdded = intval($user_access['uid']);
	$photo_dir = $_POST['nums_folder'];//получаем папку
	$paths = __DIR__ . '/photo-stamps/' . $photo_dir . '/';
	if (is_dir($paths)!==false){// добавляем туда путь(если нашли такую папку)
		$files = scandir($paths);
		//print_r($files);
		if (count($files)>2){
			$photo_stamp='/stamps/photo-stamps/'. $photo_dir . '/'.$files[2];
		}else{
			$photo_stamp=null;
		}
	}
	$paths = __DIR__ . '/canvas-stamps/' . $photo_dir . '/';
	//echo $paths;
	if (is_dir($paths)!==false){//если пустое поле canvas , то добавляем туда путь(если нашли такую папку)
		$files = scandir($paths);
		//print_r($files);
		if (count($files)>2){
			$canvas_stamp='/stamps/canvas-stamps/'. $photo_dir . '/'.$files[2];
		}else{$canvas_stamp=null;}
	}
	$photo_and='';
	if ($photo_stamp!=null){
		$photo_and=$photo_stamp;
	}else{$photo_and='';}
	
	$canvas_and='';
	if ($canvas_stamp!=null){
		$canvas_and=$canvas_stamp;
	}else{$canvas_and='';}
	/*
	$status_vk_r=$_POST['status_vk_r'];
$skleika_tip=$_POST['skleika_tip'];
$kol_izd=$_POST['kol_izd'];
	*/
    $q = "INSERT INTO stamps (id, number, shir, vis, bok, size_x, size_y, another_stamp, izd_type,photo,karkas, comment,create_user,status_vk_r,skleika,kol_izd) VALUES ($newid, '$number', '$shir', '$vis', '$bok', '$size_x', '$size_y', '$save_stamp', '$izd_type','$photo_and','$canvas_and','$comment',$userIdAdded,'$status_vk_r','$skleika_tip','$kol_izd')";
    $r = mysql_query($q);
  }

}
?>
