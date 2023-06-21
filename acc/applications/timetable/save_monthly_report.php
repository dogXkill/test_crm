<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
$str = $_SERVER['QUERY_STRING'];
parse_str($str,$mas_izm);
parse_str($str);

$mas_isk=array(
'user_fines',
'user_other'
);
//print_r($mas_izm);
$id=$year."-".$month."-".$uid;
$sql="SELECT * FROM report2 WHERE id = '{$id}'";
$result  = mysql_query($sql);
if (mysql_num_rows($result) <= 0){
	//не найдено такой строки - создаем
	$update = mysql_query("INSERT INTO report2 (`id`, `uid`, `year`, `month`, `work_time`, `oklad`, `nachisleno`, `socoklad`, `sdelka`, `procee`, `pay1`, `pay1date`, `pay2`, `pay2date`, `pay3`,`pay3date`, `pay4`, `pay4date`, `pay5`, `pay5date`, `pay6`, `pay6date`, `pay7`, `pay7date`, `pay8`, `pay8date`) VALUES('$id', '$uid', '$year', '$month', '$work_time', '$oklad', '$nachisleno', '$socoklad', '$sdelka', '$procee', '$pay1', '$pay1date', '$pay2', '$pay2date', '$pay3', '$pay3date', '$pay4', '$pay4date', '$pay5', '$pay6date', '$pay6', '$pay6date', '$pay7', '$pay7date', '$pay8', '$pay8date') ON DUPLICATE KEY UPDATE year='$year', month='$month', work_time='$work_time', oklad='$oklad', nachisleno='$nachisleno', socoklad='$socoklad', sdelka='$sdelka', procee='$procee', pay1='$pay1', pay2='$pay2', pay3='$pay3', pay4='$pay4', pay5='$pay5', pay6='$pay6', pay7='$pay7', pay8='$pay8',  pay1date='$pay1date', pay2date='$pay2date', pay3date='$pay3date', pay4date='$pay4date', pay5date='$pay5date', pay6date='$pay6date', pay7date='$pay7date', pay8date='$pay8date'");
}else{
	$zn_izm=explode("_",$row_izm);
	//print_r($zn_izm);
	//echo count($zn_izm)."</br>";
	if (count($zn_izm)>1){
		for ($i=0;$i<count($zn_izm)-1;$i++){
			$new_zn.=$zn_izm[$i]."_";
			//echo $zn_izm[$i];
		}
		$new_zn= trim( $new_zn , "_" );
		$zn_izm=$new_zn;
		//echo "zn:izm:".$zn_izm;
	}else{$zn_izm=$zn_izm[0];}
	//echo $zn_izm;
	//echo "t:".in_array($zn_izm, $mas_isk);
	if ($zn_izm!="" && in_array($zn_izm, $mas_isk)!==true){
	//echo "{$zn_izm} - {$mas_izm[$zn_izm]}";
	$vals="";
	switch($zn_izm){
		case "pay1date":
		case "pay1":
		$days="1";
		$zn_day=$pay1;//, '$pay1', '$pay1date',
		$zn_days=$pay1date;
		$vals="`pay{$days}` = '{$zn_day}',`pay{$days}date` = '{$zn_days}'";
		break;
		case "pay2date":
		case "pay2":
		$days="2";
		$zn_day=$pay2;
		$zn_days=$pay2date;
		$vals="`pay{$days}` = '{$zn_day}',`pay{$days}date` = '{$zn_days}'";
		break;
		case "pay3date":
		case "pay3":
		$days="3";
		$zn_day=$pay3;
		$zn_days=$pay3date;
		$vals="`pay{$days}` = '{$zn_day}',`pay{$days}date` = '{$zn_days}'";
		break;
		case "pay4date":
		case "pay4":
		$days="4";
		$zn_day=$pay4;
		$zn_days=$pay4date;
		$vals="`pay{$days}` = '{$zn_day}',`pay{$days}date` = '{$zn_days}'";
		break;
		case "pay5date":
		case "pay5":
		$days="5";
		$zn_day=$pay5;
		$zn_days=$pay5date;
		$vals="`pay{$days}` = '{$zn_day}',`pay{$days}date` = '{$zn_days}'";
		break;
		case "pay6date":
		case "pay6":
		$days="6";
		$zn_day=$pay6;
		$zn_days=$pay6date;
		$vals="`pay{$days}` = '{$zn_day}',`pay{$days}date` = '{$zn_days}'";
		break;
		case "pay7date":
		case "pay7":
		$days="7";
		$zn_day=$pay7;
		$zn_days=$pay7date;
		$vals="`pay{$days}` = '{$zn_day}',`pay{$days}date` = '{$zn_days}'";
		break;
		case "pay8date":
		case "pay8":
		$days="8";
		$zn_day=$pay8;
		$zn_days=$pay8date;
		$vals="`pay{$days}` = '{$zn_day}',`pay{$days}date` = '{$zn_days}'";
		break;
	}
	if ($vals==""){
		$vals="`{$zn_izm}` = '{$mas_izm[$zn_izm]}'";
	}
	//echo $vals;
	if ($vals!=""){
	$sql="UPDATE `report2` SET $vals WHERE `report2`.`id` = '{$id}';";
	$update = mysql_query($sql);
	}else{$update="false";}
	}else{$update="true";}
	//echo $sql;
	//$update="true";
}

echo mysql_error();

if($update=="true"){echo "ok".mysql_error();}
else
{echo mysql_error();}
 
?>