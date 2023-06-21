<input type="text" id='list_stamps_in1' style='width:60px'><span>	Номер штампа</span>&nbsp;
<input type="number" id='list_stamps_in2' style='width:60px'><span>	Ширина</span>&nbsp;
<input type="number" id='list_stamps_in3' style='width:60px'><span>	Высота</span>
<?php
//показать штампы
require_once("../../includes/db.inc.php");
$izd_type=$_GET['type'];
//$sql="SELECT * FROM `types` WHERE tid='{$izd_type}'";
//$types=mysql_query($sql);
$types1 = array();

$q = "SELECT * FROM `types` WHERE vis_stamps = 1 ORDER BY seq DESC";
$r = mysql_query($q);
while ($row = mysql_fetch_row($r))
{
  $type = array();
  $type['tid'] = $row[0];
  $type['type'] = $row[1];
$type['prefix'] = $row[5];
  array_push($types1, $type);
  unset($type);
}
function check_prefix($mas,$zn){
	foreach ($mas as $key =>$value){
		//print_r($value);
		if ($value['tid']==$zn){return $value['prefix'];}
	}
	return false;
}
if ($izd_type!=4){
$get = mysql_query("SELECT * FROM stamps WHERE deleted = 0 AND izd_type=".$izd_type);
}else{$get = mysql_query("SELECT * FROM stamps WHERE deleted = 0");}
//$get = mysql_query("SELECT * FROM stamps WHERE izd_type=".$izd_type);
$img_site='<img src="/i/sprav.png" width="16" height="16" alt="">';
$url_site='http://test.upak.me/acc/sprav/stamps/?edit=';
echo '<div class="list_stamps">';
while($g =  mysql_fetch_assoc($get)){
	$sh=$g[shir];
	$vis=$g[vis];
	$bok=$g[bok];
	$num=$g[number];
		//print_r($types1);
	if ($izd_type!=4){
	
	$prefix=check_prefix($types1,$izd_type);
	}else{$prefix=check_prefix($types1,$g['izd_type']);}
	$id_stamps=$g[id];
	$comment=$g[comment];
	if ($g[photo]!=""){
	$img_src="/acc/sprav/".$g[photo];
	}else{
	$img_src="";
	}
	$urls=str_replace("edit=","edit={$id_stamps}",$url_site);
	echo "<p class='select_stamps' data-sh='{$sh}' data-vis='{$vis}' data-bok='{$bok}' data-num='{$num}' data-id='{$id_stamps}' data-img='{$img_src}'><b>{$prefix} {$num}</b>	<span>{$sh}x{$vis}x{$bok} 
	<a href='{$urls}' target='_blank'>{$img_site}</a>
	</span> {$comment}</p>";
}
?>
</div>
<div class='img_list_stamp' style='display:none;'><img src=''/></div>