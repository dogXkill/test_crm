<style>
td{border: 3px solid black;padding-left:2px;padding-right:2px;}
.td_border_number{
	border-bottom: 3px dashed blue;
	border-top: 3px dashed blue;
}
.kol1{
	width:25px;text-align: center;
}
.left_text{
    text-align: center;
    width: 70%;
    display: inline-block;}
.right_text{
	display: inline-block;
	vertical-align: super;
}	
.font_b{font-weight:900;}
.border_none{border:0px;}
</style>
<?php
//вывод таблицы для принта
require_once("../../includes/db.inc.php");
function print_block($text_left,$text_right){
	if ($text_left=="тип изделия"){$css_dop="min-width: 80px;";}
	return  "<table>
	<td class='border_none' style='text-align:center;{$css_dop}'>{$text_left}</td>
	<td class='font_b border_none'>{$text_right}</td>
	</table>";
}
function print_block_mas($text_left,$text_right){
	$text_right_new='';
	foreach ($text_right as $value){
		if ($text_right_new!=''){$text_right_new=$text_right_new.",".$value;}else{$text_right_new=$value;}
	}
	return  "<table>
	<td class='border_none' style='text-align:center;{$css_dop}'>{$text_left}</td>
	<td class=' border_none' style='max-height: 55px;
    overflow-y: hidden;
    display: block;' >{$text_right_new}</td>
	</table>";
}
function check_prefix($mas,$zn,$tip){
	foreach ($mas as $key =>$value){
		//print_r($value);
		if ($value['tid']==$zn && $tip==1){return $value['type'];}else if ($value['tid']==$zn && $tip==2){return $value['prefix'];}
	}
	return false;
}
function mm_sm($zn){
	return round($zn/10,true,PHP_ROUND_HALF_ODD);
}
if (!empty($_GET['ids'])){
	//
	$types=array();
	$q = "SELECT * FROM `types` WHERE vis_stamps = 1 ORDER BY seq DESC";
	$r = mysql_query($q);
	while ($row = mysql_fetch_row($r))
	{
		$type = array();
		$type['tid'] = $row[0];
		$type['type'] = $row[1];
		$type['prefix']=$row[5];

		array_push($types, $type);
		unset($type);
	}
	//
	$ids=explode("|",$_GET['ids']);
	$ids=array_diff($ids, array('', NULL, false));
	$ids_vid=null;
	if (count($ids)>=2){
		//больше 2 элементов (ставим AND)
		//print_r($ids);
		for ($i=0;$i<count($ids);$i++){
			if ($i==0){
				$ids_vid=" id=".$ids[$i];
			}
			else if ($i>=1 && $i<count($ids)){
				$ids_vid.=" OR id=".$ids[$i];
			}
		
		}
		$q1 = "SELECT * FROM stamps WHERE".$ids_vid;
	}else{
		
		$q1 = "SELECT * FROM stamps WHERE id=".$ids[0];
	}
	//echo $q1;
	$r1 = mysql_query($q1);
	//echo $q."|".mysql_num_rows($r);
	echo "<table style='border-collapse: collapse;'>";
	while ($row1 = mysql_fetch_array($r1) )
	{
		$name_type=check_prefix($types,$row1['izd_type'],1);
		$prefix=check_prefix($types,$row1['izd_type'],2);
		if ($row1['another_stamp']!=''){
		$kol_sv=explode(",",$row1['another_stamp']);
		$kol_sv=count($kol_sv);
		}else{$kol_sv=0;}
		if ($row1['photo']!=''){
			$photo_stamp='да';
		}else{$photo_stamp='нет';}
		if ($row1['skleika'] == 1) {
			$skleika = 'Внутр';
		  } elseif ($row['skleika'] == 2) {
			$skleika = 'Внешн';
		  } else {
			$skleika = '-';
		  }
		$mas_zak=array();
		//смотри заявки по id и пишем в массив
		$ids1=$row1['id'];
		$q1_1 = "SELECT * FROM `applications` WHERE `stamp_num` = ".$ids1;
		$r1_1=mysql_query($q1_1);
		while ($row2 = mysql_fetch_array($r1_1) ){
			$mas_zak[]=$row2['ClientName'];
		}
		echo "<tr>";
		echo "<td style='background-color:black;color:white;font-weight:900;font-size:20px;text-align:center;width:100px;' class='td_border_number'>".$prefix."".$row1['number']."</td>";
		echo "<td style='width:50px;text-align: center;' class='kol1'>".print_block('тип изделия',$name_type)."</td>";
		echo "<td style='width:50px;text-align: center;' class='kol1'>".print_block('шир',$row1['shir'])."</td>";
		echo "<td style='width:50px;text-align: center;' class='kol1'>".print_block('выс',$row1['vis'])."</td>";
		echo "<td style='width:50px;text-align: center;' class='kol1'>".print_block('бок',$row1['bok'])."</td>";
		//echo "<td style='width:50px;text-align: center;' class='kol1'>".print_block('позиция штампа',"вер")."</td>";
		//echo "<td style='width:50px;text-align: center;' class='kol1'>".print_block('из скольких частей',$kol_sv)."</td>";
		echo "<td style='width:50px;text-align: center;' class='kol1'>".print_block('склейка',$skleika)."</td>";
		echo "<td style='width:50px;text-align: center;' class='kol1'>".print_block('шир разв',mm_sm($row1['size_x']))."</td>";
		echo "<td style='width:50px;text-align: center;' class='kol1'>".print_block('выс разв',mm_sm($row1['size_y']))."</td>";
		//echo "<td style='width:50px;text-align: center;' class='kol1'>".print_block_mas('заказчики',$mas_zak)."</td>";
		echo "<td style='width:50px;text-align: center;' class='kol1'>".print_block('примечание',$row1['comment'])."</td>";
		//echo "<td style='width:50px;text-align: center;' class='kol1'>".print_block('фото',$photo_stamp)."</td>";
		
		echo "</tr>";
	}
	echo "</table>";
}else{
	echo "<h3>Вы не выбрали штампы</h3><a href='index.php>Назад</a>";
}
?>