<?php
/*
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/db.inc.php");
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/auth.php");
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/lib.php"); */
ini_set('max_execution_time', 300);

function booked(){

//чтобы избежать повторного добавления, предварительно очищаем столбец
mysql_query("UPDATE plan_arts SET booked = ''");

$affected = 0;

//делаем выборку всех артикулов
$select_arts = mysql_query("SELECT o.art_num AS art_num, SUM(num) AS booked FROM queries AS q, obj_accounts AS o WHERE
((q.booking_till > DATE(CONVERT_TZ(NOW(),'MST7MDT','EET')) AND q.prdm_opl = 0) OR (q.booking_till > DATE(CONVERT_TZ(NOW() - INTERVAL 30 DAY,'MST7MDT','EET'))
AND q.prdm_opl > 0)) AND o.art_num <> '' AND o.art_num <> '0' AND o.art_num <> 'd' AND o.art_num <> 'n' AND q.booking_till <> '0000-00-00'
AND q.shipped = '0' AND (q.prdm_num_acc = '' OR q.prdm_num_acc = '0') AND (q.typ_ord = '2' OR q.typ_ord = '3') AND q.uid = o.query_id
AND (q.client_id <> '0' AND q.client_id <> '') AND o.deleted <> '1' AND q.deleted  = '0' GROUP BY art_num ORDER BY art_num ASC");
while($r =  mysql_fetch_array($select_arts)){

$art_num = $r[art_num];
$booked = $r[booked];

if(is_numeric($art_num)){
$update = mysql_query("UPDATE plan_arts SET booked = '$booked' WHERE art_id = '$art_num'");

//echo "UPDATE plan_arts SET booked = '$booked' WHERE art_id = '$art_num'<br>";
}

$art_num = "";
$booked = "";

if(mysql_affected_rows()){
$affected = $affected+1;
}
}


//дата последнего обновления. Сама функция находится в lib.php
//update_date("booked", $affected);

echo mysql_error();


}


//если запуск скриптна не из synch.php
if(!$type){
booked();
}
 ?>