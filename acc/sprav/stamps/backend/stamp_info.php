<?php
//получаем префикс по типу изделия
$auth = false;

require_once("../../../includes/db.inc.php");
require_once("../../../includes/auth.php");
require_once("../../../includes/lib.php");
$id=$_POST['id'];
if ($id!=null && $id!=0){
$q = "SELECT * FROM `types` WHERE tid = {$id}";
//echo $q;
$r = mysql_query($q);
$row = mysql_fetch_row($r);
echo $row[5];
}else{echo "";}
?>