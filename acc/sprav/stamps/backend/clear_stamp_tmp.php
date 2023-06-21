<?php
//очистка номеров штампа
$auth = false;

require_once("../../../includes/db.inc.php");
require_once("../../../includes/auth.php");
require_once("../../../includes/lib.php");

function check_prefix($mas,$zn){
	foreach ($mas as $key =>$value){
		//print_r($value);
		if ($value['tid']==$zn){return $value['prefix'];}
	}
	return false;
}
$types = array();
$q = "SELECT * FROM `types` WHERE vis_stamps = 1 ORDER BY seq DESC";
$r = mysql_query($q);
while ($row = mysql_fetch_row($r))
{
  $type = array();
  $type['tid'] = $row[0];
  $type['type'] = $row[1];
  $type['prefix'] = $row[5];
  
  array_push($types, $type);
  unset($type);
}
$q = "SELECT * FROM stamps";
//echo $q;
$r = mysql_query($q);
$mas_izm_id=array();
$tip='test';
echo $tip."</br>";
while ($row = mysql_fetch_array($r) )
{		
		if(preg_match("/[^\d]{1}/",$row['number'])){
			$pattern = '/[^0-9]/';
		$new_number = preg_replace($pattern, "", $row['number']);
		$row['new_number']=$new_number;
		$types_id=$row['izd_type'];
		$prefix="";
			if ($types_id!=4){
			$prefix=check_prefix($types,$types_id);
			}
		$mas_izm_id[$row['id']]['number_old']=$row['number'];
		$mas_izm_id[$row['id']]['number']=$new_number;
		$mas_izm_id[$row['id']]['prefix']=$prefix;
		}
		//записываем пути в бд
		$photo_path='/home/crmu660633/'.$tip.'.upak.me/docs/acc/sprav/stamps/photo-stamps/' .$row["number"];
		$photo=null;
		if (file_exists($photo_path)) {
			  $all_photos = scandir($photo_path);
			  foreach ($all_photos as $key => $value) {
				  if ($value == '.' || $value == '..') {
					  unset($all_photos[$key]);
				  }
			  }
			  sort($all_photos);
			  $photo = $all_photos[0];
	  }
      
      $photo1=null;
	  $canvas_path = '/home/crmu660633/'.$tip.'.upak.me/docs/acc/sprav/stamps/canvas-stamps/' . $row['number'];
	  if (file_exists($canvas_path)) {
			  $all_photos = scandir($canvas_path);
			  foreach ($all_photos as $key => $value) {
				  if ($value == '.' || $value == '..') {
					  unset($all_photos[$key]);
				  }
			  }
			  sort($all_photos);
			  $photo1 = $all_photos[0];
	  }
	  if (!empty($photo)) {
		  ///home/crmu660633/{$tip}.upak.me/docs/acc/sprav
      //echo  "/home/crmu660633/{$tip}.upak.me/docs/acc/sprav/stamps/photo-stamps/" .$row['number']."/". $photo."</br>";
	  $mas_obn_img[$row['id']]['img']="/stamps/photo-stamps/" .$row['number']."/". $photo;
	  if (!empty($photo1)) {
	  $mas_obn_img[$row['id']]['img1']="/stamps/canvas-stamps/" .$row['number']."/". $photo1;
	  }else{$mas_obn_img[$row['id']]['img1']='';}
	  } else {
		  //echo  '/acc/i/who.gif </br>';
		  $mas_obn_img[$row['id']]['img']='';
		   if (!empty($photo1)) {
			   $mas_obn_img[$row['id']]['img1']="/stamps/canvas-stamps/" .$row['number']."/". $photo1;
		   }else{$mas_obn_img[$row['id']]['img1']='';}
	  }
	
}
echo "<pre>";
//print_r($mas_obn_img);
echo "</pre>";
//записываем пути 
foreach ($mas_obn_img as $key =>$value){
	$q = "UPDATE stamps SET photo = '$value[img]',karkas='$value[img1]' WHERE id = '$key'";
	//echo $q."</br>";
	//$r = mysql_query($q);
}

		foreach ($mas_izm_id as $key =>$value){
			$q = "UPDATE stamps SET number = '$value[number]' WHERE id = '$key'";
			 //echo $q;
			 $photo_path = __DIR__ . '/photo-stamps/' .$value["number_old"];
			 $photo_path1 = __DIR__ . '/photo-stamps/' . $prefix.''.$value['number'];
			 //echo "tek_pyt:{$photo_path}</br>new:{$photo_path1}</br>";
			 
			 
			//$r = mysql_query($q);
		}
//перезапись в application с номера на id 
/*береём номер ,смотрим 1 он или нет (если да,то записываем id)
если нет то сравниваем их характеристики (если хотя бы 1 совпал - записываем его id , иначе пишем на экране id application которого не нашли)
*/
function check_stamps($mas,$zn,$s,$v,$b){
	foreach ($mas as $key =>$value){
		//print_r($value);
		if ($value['number']==$zn){
			if ($value['shir']==$s && $value['vis']==$b && $value['bok']==$b){
				return $value['id'];
			}
		}
		return false;
	}
}
$mas_stamps=array();
$q = "SELECT * FROM stamps";
$r = mysql_query($q);
while ($row = mysql_fetch_row($r))
{
  $type_st = array();
  $type_st['id'] = $row[0];
  $type_st['number'] = $row[1];
  $type_st['shir'] = $row[3];
  $type_st['vis'] = $row[4];
  $type_st['bok'] = $row[5];
  
  array_push($mas_stamps, $type_st);
  unset($type_st);
}
//
//print_r($mas_stamps);
$q = "SELECT * FROM applications";
//echo $q;
$r = mysql_query($q);
$mas_izm_id_application=array();
$kol_g=0;
$kol_l=0;
while ($row = mysql_fetch_row($r) )
{	
//print_r($row);
echo $row[26].".".$row[21].".".$row[22].".".$row[23]."=".check_stamps($mas_stamps,$row[26],$row[21],$row[22],$row[23])."</br>";
	if (check_stamps($mas_stamps,$row[26],$row[21],$row[2],$row[23])!=false){
		//вернулся id 
		//echo $row['uid']."</br>";
		$kol_g++;
	}else{
		//id не найден (выводим)
		//echo $row['uid']."</br>";
		$kol_l++;
		
	}
	
}
$sum=$kol_g+$kol_l;
echo "Resu:{$kol_g}/{$kol_l}/{$sum}";
?>
