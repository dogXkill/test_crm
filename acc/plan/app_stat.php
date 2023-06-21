<?php
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/db.inc.php");
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/auth.php");
require_once("/home/crmu660633/test.upak.me/docs/acc/includes/lib.php");
ini_set('max_execution_time', 500);

function app_stat(){

//чтобы избежать повторного добавления, предварительно очищаем столбец
mysql_query("UPDATE plan_arts SET in_work = ''");



//делаем выборку всех артикулов
$select_arts = mysql_query("SELECT art_id FROM plan_arts WHERE archive <> '1' GROUP BY art_id");
while($r =  mysql_fetch_array($select_arts)){
$upakovano = 0;
//остаток
$ostatok = 0;
$art_id = $r["art_id"];
//ограничиваем заявки последними 6 месяцами
$dat_ord_ago = date("Y-m-d h:i:s",strtotime("-6 months"));
//смотрим таблицу, если есть упакованные, то суммируем их и формируем массив, если нет упакованных, то это просто заявка которая еще не начата
//в последствии, нужно будет обусловить добавление остатков наличием какого нибудь маркера в заявках, например "сырье куплено" и/или "заявка выполнена полностью"
//также, необходимо создать заявки на закупку тех или иных артикулов, т.к. производится учет также внешних товаров
$select = mysql_query("SELECT a.art_id AS art_id, a.tiraz AS tiraz, a.num_ord AS num_ord, SUM(b.num_of_work) AS upakovano, a.tiraz-SUM(b.num_of_work) AS ostatok, rate_4 AS rate_4
FROM
applications AS a,
job AS b
WHERE ((a.art_id = '$art_id' AND a.num_ord = b.num_ord AND b.job = '11' AND b.cur_time > '$dat_ord_ago')
OR (a.art_id = '$art_id' AND b.uid = '0'))
AND a.dat_ord > '$dat_ord_ago'
AND a.archive <> '1'
GROUP BY a.num_ord
");
while($r2 =  mysql_fetch_array($select)){
//тираж по заявке
$tiraz = $r2["tiraz"];
//сколько упаковано
$upakovano = $r2["upakovano"];
//остаток
$ostatok = $r2["ostatok"];

$sborka_cost = round($r2["rate_4"],2);



//позже добавлена sborka_cost . Совершенно не помню для чего и где используется
$update_sborka_cost = mysql_query("UPDATE plan_arts SET sborka_cost = '$sborka_cost' WHERE art_id = '$art_id'");


//если остаток больше хотя бы 20% от тиража, то мы предполагаем, что заказ еще не довыполнен и соответственно, мы добавляем этот остаток
//мы делаем суммирование, т.к. если заявок на 1 артикул несколько, то эти данные нужно собрать
//иногда делаем больше чем в заявке, поэтому бывает что остаток уходит в минус. Обнуляем.
if($ostatok  > $tiraz * 0.2 and $ostatok > 0){
//$update = mysql_query("UPDATE plan_arts SET in_work = in_work + '$ostatok' WHERE art_id = '$art_id'");
$update = mysql_query("UPDATE plan_arts SET in_work = in_work + '$ostatok' WHERE art_id = '$art_id'");
 // echo "UPDATE plan_arts SET in_work = in_work + '$ostatok' WHERE art_id = '$art_id'";

if(mysql_affected_rows()){
$affected = $affected+1;
}
}

}}

//дата последнего обновления. Сама функция находится в lib.php
//update_date("app_stat", $affected);

echo mysql_error();


}


//если запуск скриптна не из synch.php
if(!$type){
app_stat();
}
 ?>