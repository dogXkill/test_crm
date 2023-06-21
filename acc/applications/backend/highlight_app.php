<?php
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");

$uid = $_GET["uid"];
$highlight_color = $_GET["highlight_color"];
$app_status = $_GET["app_status"];


//проверяем не проставлена ли уже дата, если да, то не ставим дату. Иногда вместо статуса заявка принята, производство может сразу перепрыгнуть на следующий этап, таким образом дата приема
//заявки не будет сохранена, что не есть гуд


$check = mysql_query("SELECT COUNT(*) FROM applications WHERE uid='$uid' AND app_status_update <> '0000-00-00 00:00:00'");
$chk = mysql_fetch_array($check);

if($app_status == '1'){$sql_app_status_upd = ", app_status_update=NOW()";} if($app_status == '0'){$sql_app_status_upd = ", app_status_update=''";}


if(is_numeric($uid)){
    $q = "UPDATE applications SET
    highlight_color = '$highlight_color', app_status = '$app_status' $sql_app_status_upd
    WHERE uid='$uid'";
    // echo $q;

 $query = mysql_query($q);
      echo mysql_error();
    if($query == "true"){ echo "OK"; }
}

 ?>