<?
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");
//$tpus = $user_type;		// тип пользователя
// ----- перейти на главную если доступ запрещен ---------
if(!$auth) {
  header("Location: /");
  exit;
}
// если 1 -админ или бухгалтер, иначе 0 - менеджер
//$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;

$year_num_from = $_GET["year_num_from"];
$month_num_from = $_GET["month_num_from"];
$year_num_to = $_GET["year_num_to"];
$month_num_to = $_GET["month_num_to"];
$statistics_access = $user_access['statistics_access'];



if($statistics_access == 2){

$dates_between = "'$year_num_from-$month_num_from-01 00:00:00' AND '$year_num_to-$month_num_to-31 23:59:29'";

//echo $dates_between;
/*
$q = "SELECT
Year(t2.date_query) AS 'Год', Month(t2.date_query) AS 'Месяц',


ROUND(t1.prdm_sum_acc) AS 'выручка по заказам',
t1.num AS 'заказы с лого',
ROUND(t1.prdm_sum_acc/t1.num,0) AS 'средний заказ',
ROUND(t1.podr_sebist) AS 'c/c заказов',
ROUND(t1.prdm_sum_acc-t1.podr_sebist) AS 'доход с заказов',
ROUND((t1.prdm_sum_acc - t1.podr_sebist)*0.1) AS '% менегера по заказам',


ROUND(t2.prdm_sum_acc) AS 'выручка магазин',
t2.num AS 'заказов магазин',
ROUND(t2.prdm_sum_acc/t2.num) AS 'средний чек',
ROUND(t4.r_cost) AS 'с/с готовой продукции',
t3.transport_num AS 'доставок',
ROUND(t3.transport_cost) AS 'с/с доставок',
ROUND(t2.prdm_sum_acc-t3.transport_cost) AS 'выручка магазин за вычетом доставки',
ROUND(t2.prdm_sum_acc-t3.transport_cost-t4.r_cost) AS 'маржа магазин без учета налогов и %',
ROUND((t2.prdm_sum_acc-t3.transport_cost-t4.r_cost)*0.06) AS '% менеджера по магазину',


ROUND(t33.prdm_sum_acc) AS 'выручка магазин c ЛОГО',
t33.num AS 'заказов магазин с ЛОГО',
ROUND(t33.prdm_sum_acc/t33.num) AS 'средний чек маг. с ЛОГО',
ROUND(t33.podr_sebist) AS 'c/c гот. с ЛОГО',
ROUND(t33.prdm_sum_acc-t33.podr_sebist) AS 'маржа магазин с ЛОГО без учета налогов и %',
ROUND((t33.prdm_sum_acc-t33.podr_sebist)*0.1) AS '% менеджера по магазину с ЛОГО',


ROUND(t1.prdm_sum_acc + t2.prdm_sum_acc + t33.prdm_sum_acc) AS 'общая выручка',
ROUND((t1.prdm_sum_acc + t2.prdm_sum_acc + t33.prdm_sum_acc)*0.04) AS 'налог + банк %',
ROUND((t1.prdm_sum_acc + t2.prdm_sum_acc + t33.prdm_sum_acc)-t1.podr_sebist-((t1.prdm_sum_acc - t1.podr_sebist)*0.1)-t3.transport_cost - t33.podr_sebist - (t33.prdm_sum_acc-t33.podr_sebist)*0.1 - t4.r_cost - ((t2.prdm_sum_acc-t3.transport_cost-t4.r_cost)*0.06)) AS 'доход'

FROM
(SELECT date_query, typ_ord, COUNT(*) AS num, SUM(prdm_sum_acc) AS prdm_sum_acc, SUM(podr_sebist) AS podr_sebist FROM queries WHERE date_query BETWEEN $dates_between AND typ_ord='1' GROUP BY Year(date_query), Month(date_query))t1,
(SELECT date_query, typ_ord, COUNT(*) AS num, SUM(prdm_sum_acc) AS prdm_sum_acc FROM `queries` WHERE `date_query` BETWEEN $dates_between AND typ_ord='2' GROUP BY Year(date_query), Month(date_query))t2,
(SELECT date_query, typ_ord, COUNT(*) AS num, SUM(prdm_sum_acc) AS prdm_sum_acc, SUM(podr_sebist) AS podr_sebist FROM queries WHERE date_query BETWEEN $dates_between AND typ_ord='3' GROUP BY Year(date_query), Month(date_query))t33,
(SELECT q.date_query, YEAR(q.date_query), MONTH(q.date_query), SUM(c.num) AS transport_num, SUM(c.price*c.num) AS transport_cost FROM queries AS q, contractors_list AS c WHERE q.date_query BETWEEN $dates_between AND q.typ_ord='2' AND ((c.contr_id = '186' OR c.name = '1' OR c.name = 'д' OR c.name = 'Д' OR c.name LIKE '%Доставка%' OR c.contr_id = '183') AND num < '4') AND q.uid = c.query_id GROUP BY Year(q.date_query), Month(q.date_query))t3,
(SELECT q.date_query, YEAR(q.date_query), MONTH(q.date_query), ROUND(SUM(o.num*o.r_price_our)) AS r_cost FROM queries AS q, obj_accounts AS o WHERE q.date_query BETWEEN $dates_between AND q.typ_ord='2' AND o.art_num != 'd' AND q.uid = o.query_id GROUP BY Year(q.date_query), Month(q.date_query))t4

WHERE
Year(t1.date_query)=Year(t2.date_query) AND Month(t1.date_query)=Month(t2.date_query) AND Year(t2.date_query)=Year(t3.date_query) AND Month(t2.date_query)=Month(t3.date_query)
AND Year(t3.date_query)=Year(t4.date_query) AND Month(t3.date_query)=Month(t4.date_query) AND ((Year(t1.date_query)=Year(t33.date_query) AND Month(t1.date_query)=Month(t33.date_query)) OR (Month(t1.date_query) = '' AND Year(t1.date_query) = ''))";
*/

$q1 = "SELECT
Year(t2.date_query) AS 'Год', Month(t2.date_query) AS 'Месяц',


ROUND(t1.prdm_sum_acc) AS 'выручка по заказам',
t1.num AS 'заказы с лого',
ROUND(t1.prdm_sum_acc/t1.num,0) AS 'средний заказ',
ROUND(t1.podr_sebist) AS 'c/c заказов',
ROUND(t1.prdm_sum_acc-t1.podr_sebist) AS 'доход с заказов',
ROUND((t1.prdm_sum_acc - t1.podr_sebist)*0.1) AS '% менегера по заказам',


ROUND(t2.prdm_sum_acc) AS 'выручка магазин',
t2.num AS 'заказов магазин',
ROUND(t2.prdm_sum_acc/t2.num) AS 'средний чек',
ROUND(t4.r_cost) AS 'с/с готовой продукции',
t3.transport_num AS 'доставок',
ROUND(t3.transport_cost) AS 'с/с доставок',
ROUND(t2.prdm_sum_acc-t3.transport_cost) AS 'выручка магазин за вычетом доставки',
ROUND(t2.prdm_sum_acc-t3.transport_cost-t4.r_cost) AS 'маржа магазин без учета налогов и %',
ROUND((t2.prdm_sum_acc-t3.transport_cost)*0.2*0.06) AS '% менеджера по магазину',


ROUND(t33.prdm_sum_acc) AS 'выручка магазин c ЛОГО',
t33.num AS 'заказов магазин с ЛОГО',
ROUND(t33.prdm_sum_acc/t33.num) AS 'средний чек маг. с ЛОГО',
ROUND(t33.podr_sebist) AS 'c/c гот. с ЛОГО',
ROUND(t33.prdm_sum_acc-t33.podr_sebist) AS 'маржа магазин с ЛОГО без учета налогов и %',
ROUND((t33.prdm_sum_acc-t33.podr_sebist)*0.1) AS '% менеджера по магазину с ЛОГО',


(CASE
WHEN (t33.prdm_sum_acc IS NULL) THEN
ROUND(t1.prdm_sum_acc + t2.prdm_sum_acc)
ELSE
ROUND(t1.prdm_sum_acc + t2.prdm_sum_acc + t33.prdm_sum_acc)
END) AS 'общая выручка',

Month(t2.date_query),

(CASE
WHEN (t33.prdm_sum_acc IS NULL) THEN
ROUND(t1.prdm_sum_acc + t2.prdm_sum_acc-t1.podr_sebist-(t1.prdm_sum_acc - t1.podr_sebist)*0.1 - t3.transport_cost - t4.r_cost - (t2.prdm_sum_acc-t3.transport_cost)*0.2*0.06)
ELSE
ROUND(t1.prdm_sum_acc + t2.prdm_sum_acc + t33.prdm_sum_acc - t1.podr_sebist - (t1.prdm_sum_acc - t1.podr_sebist)*0.1 - t3.transport_cost - t33.podr_sebist - (t33.prdm_sum_acc-t33.podr_sebist)*0.1 - t4.r_cost - (t2.prdm_sum_acc-t3.transport_cost)*0.2*0.06)
END) AS 'доход'


FROM

(SELECT date_query, typ_ord, COUNT(*) AS num, SUM(prdm_sum_acc) AS prdm_sum_acc, SUM(podr_sebist) AS podr_sebist FROM queries WHERE date_query BETWEEN $dates_between AND typ_ord='1' AND deleted <> '1' GROUP BY Year(date_query), Month(date_query))t1

LEFT JOIN

(SELECT date_query, typ_ord, COUNT(*) AS num, SUM(prdm_sum_acc) AS prdm_sum_acc FROM `queries` WHERE `date_query` BETWEEN $dates_between AND typ_ord='2' AND deleted <> '1' GROUP BY Year(date_query), Month(date_query))t2
ON Year(t1.date_query) =  Year(t2.date_query) AND Month(t1.date_query) = Month(t2.date_query)


LEFT JOIN
(SELECT date_query, typ_ord, COUNT(*) AS num, SUM(prdm_sum_acc) AS prdm_sum_acc, SUM(podr_sebist) AS podr_sebist FROM queries WHERE date_query BETWEEN $dates_between AND typ_ord='3' AND deleted <> '1' GROUP BY Year(date_query), Month(date_query))t33
ON Year(t1.date_query) =  Year(t33.date_query) AND Month(t1.date_query) = Month(t33.date_query)


LEFT JOIN
(SELECT q.date_query, YEAR(q.date_query), MONTH(q.date_query), SUM(c.num) AS transport_num, SUM(c.price*c.num) AS transport_cost FROM queries AS q, contractors_list AS c WHERE q.date_query BETWEEN $dates_between AND q.typ_ord='2' AND q.deleted <> '1' AND ((c.contr_id = '186' OR c.name = '1' OR c.name = 'д' OR c.name = 'Д' OR c.name LIKE '%Доставка%' OR c.contr_id = '183') AND num < '4') AND q.uid = c.query_id GROUP BY Year(q.date_query), Month(q.date_query))t3
ON Year(t1.date_query) =  Year(t3.date_query) AND Month(t1.date_query) = Month(t3.date_query)

LEFT JOIN
(SELECT q.date_query, YEAR(q.date_query), MONTH(q.date_query), ROUND(SUM(o.num*o.r_price_our)) AS r_cost FROM queries AS q, obj_accounts AS o WHERE q.date_query BETWEEN $dates_between AND q.typ_ord='2' AND q.deleted <> '1' AND o.art_num != 'd' AND q.uid = o.query_id GROUP BY Year(q.date_query), Month(q.date_query))t4
ON Year(t1.date_query) =  Year(t4.date_query) AND Month(t1.date_query) = Month(t4.date_query)
";

echo mysql_error();

echo $q1;



/*$filename = 'full_stat.csv';
if ( !(@unlink($filename)) ) die('Error Delete File.');
    */
/* Вывод результата выборки из MySQL в файл csv */

$result_select = mysql_query($q1);

/* Вывод результата выборки из MySQL в файл csv */

echo mysql_error();


$fp = fopen('full_stat.csv', 'w');
$titles[] = array("Год", "Месяц",
"Выручка по заказам", "Заказы с лого", "Средний заказ", "с/с заказов", "доход с заказов", "% менегера по заказам",
"Выручка магазин", "Заказов магазин", "Средний чек", "с/с готовой продукции", "Доставок", "С/с доставок", "Выручка магазин за вычетом доставки", "Маржа магазин без налогов и %", "% менегера по магазину",
"Выручка магазин c ЛОГО", "заказов магазин с ЛОГО", "средний чек", "c/c гот. с ЛОГО", "маржа магазин с ЛОГО без учета налогов и %", "% менеджера по магазину с ЛОГО",
"Общая выручка", "налог + банк 4%", "Доход");


foreach ($titles as $fields) {
    fputcsv($fp, $fields, ";");   /* записываем строку в csv-файл */
}

while ($line = mysql_fetch_assoc($result_select)) {

fputcsv($fp, $line, ';');

}

fclose($fp);
?><br><br>
<a href="full_stat.csv">скачать файл</a>
<?
}else{echo "Доступ запрещен!";}
?>