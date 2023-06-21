<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
ob_start();
$auth = false;

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

$str = $_SERVER['QUERY_STRING'];
parse_str($str);
$err = "";

if($num_ord == "" or $job == "" or $num_sotr == "" or $num_of_work == ""){$err = "Обязательные поля не заполнены";}else{

$date_today = date("Y-m-d");
//проверяем на дубли, только если это не выдача надомнику
if($job !== '14'){
//проверка дублированной записи (смотрим, не добавлялось ли сегодня запись на этого сотрудника на эту же заявку)
$check_dubles = mysql_query("SELECT * FROM job WHERE job = '$job' AND job <> '14' AND num_sotr = '$num_sotr' AND num_ord = '$num_ord' AND num_of_work = '$num_of_work' AND cur_time LIKE '$date_today%'");
$obr = "сотруднику";
//$q = "SELECT * FROM job WHERE job = '$job' AND job <> '14' AND num_sotr = '$num_sotr' AND num_ord = '$num_ord' AND num_of_work = '$num_of_work' AND cur_time LIKE '$date_today%'";
}else{
$check_dubles = mysql_query("SELECT * FROM job WHERE nadomn = '1' AND num_sotr = '$num_sotrnadomn' AND num_ord = '$num_ord' AND num_of_work = '$num_of_work' AND cur_time LIKE '$date_today%'");
$obr = "надомнику";
}
$dubles = mysql_num_rows($check_dubles);


if($dubles > 0){$err = "Вы сегодня уже добавляли такую же запись данному $obr. Запись НЕ внесена. <br>";}

//выявляем тираж и максимальное количество действий (вырубка / ламинация / ручки )
$app = mysql_query("SELECT title, archive,dressing, tiraz, limit_per, paper_num_list, lami_isdely_per_list, isdely_per_list, izd_lami_storon, virub_isdely_per_list, col_ottiskov_izd,
tiraz/lami_isdely_per_list AS max_lami,
tiraz/virub_isdely_per_list AS max_virub,
tiraz*col_ottiskov_izd AS max_tisn,
dat_ord AS dat_ord,
plan_in AS plan_in
FROM applications WHERE num_ord = '$num_ord'");
$app = mysql_fetch_array($app);

$dat_ord = $app[dat_ord];
$izd_lami_storon = $app[izd_lami_storon];
$archive = $app[archive];
$dressing=$app[dressing];
$limit_per = $app[limit_per];
if($limit_per > 0){$limit = 1+$limit_per/100;}else{$limit = "1.01";}


$max_lami = round($app[max_lami]*$limit);
$max_virub = round($app[max_virub]*$limit);
$max_tisn = round($app[max_tisn]*$limit);
$max_sborka = round($app[tiraz]*$limit);
$max_upak = round($app[tiraz]*$limit);




$lami = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '1' AND num_ord = '$num_ord'");
$lami = mysql_fetch_array($lami);

$virub = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '2' AND num_ord = '$num_ord'");
$virub = mysql_fetch_array($virub);

$tisn = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '3' AND num_ord = '$num_ord'");
$tisn = mysql_fetch_array($tisn);
 /*
$hands = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '15' AND num_ord = '$num_ord'");
$hands = mysql_fetch_array($hands);
*/
$sborka = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '4' AND num_ord = '$num_ord'");
$sborka = mysql_fetch_array($sborka);

$upak = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '11' AND num_ord = '$num_ord'");
$upak = mysql_fetch_array($upak);

$dressings = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '31' AND num_ord = '$num_ord'");
$dressings = mysql_fetch_array($dressings);

//считаем % соотношение выполненной работы, чтобы не допускать ситуаций, когда вырублено больше чем отламинированно
//ламинирование
/*$proc_lami = round($lami[0]*100/$max_lami,2);
//вырубка после добавления
$proc_virub = round(($virub[0]+$num_of_work)*100/$max_virub,2);*/




//проверка нет ли превышения ламинации
if($izd_lami_storon == '1' and $job == '1'):
$err = "В данной заявке указано, что ламинирование будет производиться на стороне. Запись НЕ внесена. Для внесения этой записи требуется разрешение руководства.";
//заявка кот еще в плане - нельзя добавить работу
elseif($app[plan_in] == '1'):
$err = "Нельзя добавить работу по заявке, которая еще только в планах. Запись НЕ внесена. Для внесения этой записи необходимо убрать галочку в колонке &quot;план&quot; в списке заявок.";
elseif($app[dressing] == '0' and $job == '31'):
$err="перевязка ручек отключена";
elseif($job == '31' and ($dressings[0]+$num_of_work > $app[tiraz])):
$err="перевязка превышает тираж";
//проверка не делается ли ламинация на стороне
elseif((($lami[0] + $num_of_work) > $max_lami) and $job == '1'):
$err = "Если добавить эту запись, то общее кол-во отламинированных листов превысит необходимое для выполнения заказа (<b>$max_lami</b>). Запись НЕ внесена. Для внесения этой записи требуется разрешение руководства.";
//нельзя добавить приладок больше чем 2шт за 1 раз
elseif($num_of_work > 2 and ($job == '7' or $job == '8' or $job == '9' or $job == '10')):
$err = "Приладок нельзя добавить больше 2 за раз. Запись НЕ внесена.";
elseif($num_of_work > 4 and  $job == '25'):
$err = "Приладок на шелкографию нельзя добавить больше 4 за раз. Запись НЕ внесена.";
elseif((($virub[0] + $num_of_work) > $max_virub) and $job == '2'):
$err = "Если добавить эту запись, то общее кол-во вырубленных листов превысит необходимое для выполнения заказа (<b>$max_virub</b>). Запись НЕ внесена. Для внесения этой записи требуется разрешение руководства.";
/*elseif(($proc_virub>$proc_lami) and $job == '2'):
$err = "Если добавить эту запись, то общее кол-во вырубленных листов превысит количество отламинированных листов ($proc_virub>$proc_lami). Запись НЕ внесена. Для внесения этой записи требуется разрешение руководства.";
*/
//проверка нет ли превышения тиснения
elseif((($tisn[0] + $num_of_work) > $max_tisn) and $job == '3'):
$err = "Если добавить эту запись, то общее кол-во оттисненных листов превысит необходимое для выполнения заказа (<b>$max_tisn</b>). Запись НЕ внесена. Для внесения этой записи требуется разрешение руководства.";
//проверка не собрано ли больше чем тираж
elseif((($sborka[0] + $num_of_work) > $max_sborka) and ($job == '4' or $job == '14')):
$err = "Если добавить эту запись, то общее собранных изделий превысит максимум (<b>$max_sborka</b>). Запись НЕ внесена. Для внесения этой записи требуется разрешение руководства.";
//упаковано больше чем  тираж
elseif((($upak[0] + $num_of_work) > $max_upak) and $job == '11'):
$err = "Если добавить эту запись, то общее количество упакованных изделий превысит тираж (<b>$max_upak</b>). Запись НЕ внесена. Для внесения этой записи требуется разрешение руководства.";

elseif($archive == 1):
$err = "Данная заявка находится в архиве";

endif;




if($err == ""){
//если ошибок нет, то добавляем работу
if (empty($num_sending)) {
  $insert = mysql_query("INSERT INTO job(num_sotr, num_ord, job, num_of_work, order_price, who_entered)VALUES ('$num_sotr','$num_ord','$job','$num_of_work', '$order_price', '$who_entered')")or die("Ошибка MySql: " . mysql_error());
} else {
  $insert = mysql_query("INSERT INTO job(num_sotr, num_ord, job, num_of_work, order_price, who_entered, otpravka)VALUES ('$num_sotr','$num_ord','$job','$num_of_work', '$order_price', '$who_entered', '$num_sending')")or die("Ошибка MySql: " . mysql_error());

}
$last = mysql_insert_id();
echo "ok;".$last;
$related_to = mysql_insert_id();
//если добавляется надомная работа, то также добавляется и тот, кто выдал ее
if ($num_sotrnadomn > "0"){
$job = '4';
$insert_nadomn = mysql_query("INSERT INTO job(num_sotr, num_ord, job, num_of_work, nadomn, order_price, related_to, who_entered)VALUES ('$num_sotrnadomn','$num_ord','$job','$num_of_work', '1', '$order_price', '$related_to', '$who_entered')")or die("Ошибка MySql: " . mysql_error());
}}
else{
   echo "error;".$err;
  echo mysql_error();
  $error = mysql_query("INSERT INTO job_error_log(error, user, num_ord, num_sotr, job, num_of_work) VALUES ('$err','$who_entered', '$num_ord', '$num_sotr', '$job', '$num_of_work')");
  $last_error = mysql_insert_id();
 // echo "!$last_error!";
  echo mysql_error();
}}

?>
