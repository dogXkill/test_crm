<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");
$number = $_POST['number'];
if ($_POST['tip']==1){
	//в архив

//$q = "DELETE FROM stamps WHERE number = '$number'";
$q="UPDATE `stamps` SET `deleted` = '1' WHERE `id` = '$number'";
$r = mysql_query($q);
if ($r){
	$mas['result']=1;
}else{
	$mas['result']=0;
}
}else if ($_POST['tip']==2){
	//из архива
	$q="UPDATE `stamps` SET `deleted` = '0' WHERE `id` = '$number'";
$r = mysql_query($q);
if ($r){
	$mas['result']=1;
}else{
	$mas['result']=0;
}
}else{
	$mas['result']=0;
}
echo json_encode($mas);
