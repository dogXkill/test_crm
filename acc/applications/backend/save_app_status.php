<?php
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");

$str = $_SERVER['QUERY_STRING'];
parse_str($str);

$material_supplier_comment = iconv("UTF-8", "windows-1251", $material_supplier_comment);
$material_arrival_comment = iconv("UTF-8", "windows-1251", $material_arrival_comment);


if(is_numeric($uid)){
    $q = "UPDATE applications SET
    material_supplier_comment='$material_supplier_comment',
    material_arrival_date='$material_arrival_date',
    material_arrival_comment='$material_arrival_comment',
    stamp_order_status='$stamp_order_status',
    deadline_stamp='$deadline_stamp',
    stamp_arrival_date='$stamp_arrival_date',
    klishe_order_status='$klishe_order_status',
    shnur_order_status='$shnur_order_status'
    WHERE uid='$uid'";
   //  echo $q;

 $query = mysql_query($q);
      echo mysql_error();
    if($query == "true"){ echo "OK"; }
}

 ?>