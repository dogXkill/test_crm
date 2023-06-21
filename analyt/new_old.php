<html>

<head>
  <title>Соотношение старых и новых клиентов</title>


</head>

<body>
<a href="index.php">В главное меню</a>
<?$tek_year = date("Y");?>

<form action="new_old.php" method=get>
Получить данные за
<select id=year_num name=year_num>
<option value="2010" <?if($tek_year=="2010"){echo " selected";}?>>2010</option>
<option value="2011" <?if($tek_year=="2011"){echo " selected";}?>>2011</option>
<option value="2012" <?if($tek_year=="2012"){echo " selected";}?>>2012</option>
<option value="2013" <?if($tek_year=="2013"){echo " selected";}?>>2013</option>
<option value="2014" <?if($tek_year=="2014"){echo " selected";}?>>2014</option>
<option value="2015" <?if($tek_year=="2015"){echo " selected";}?>>2015</option>
<option value="2016" <?if($tek_year=="2016"){echo " selected";}?>>2016</option>
<option value="2017" <?if($tek_year=="2017"){echo " selected";}?>>2017</option>
<option value="2018" <?if($tek_year=="2018"){echo " selected";}?>>2018</option>
<option value="2019" <?if($tek_year=="2019"){echo " selected";}?>>2019</option>
<option value="2020" <?if($tek_year=="2020"){echo " selected";}?>>2020</option>
<option value="2021" <?if($tek_year=="2021"){echo " selected";}?>>2021</option>
<option value="2022" <?if($tek_year=="2022"){echo " selected";}?>>2022</option>
</select> год <input type="hidden" name="act" value="do" /><input type=submit value=">>>">

 </form>


<?


$act = $_GET["act"];

require_once("../acc/includes/db.inc.php");
if ($act == "do"){
$fp = fopen('new_old.csv', 'w');
$year_num = $_GET["year_num"];

$months = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");


$titles = array("Год, месяц", "Кол-во заказов СТАРЫХ", "Выручка заказов СТАРЫХ", "Кол-во магазин с лого СТАРЫХ", "Выручка магазин с лого СТАРЫХ", "Кол-во заказов магазин СТАРЫХ ","Выручка магазин СТАРЫХ","Итог кол-во СТАРЫХ","Итог выручка СТАРЫХ", "Кол-во заказов НОВЫХ","Выручка заказов НОВЫХ", "Выручка заказов НОВЫХ", "Кол-во магазин с лого НОВЫХ", "Кол-во заказов магазин НОВЫХ","Выручка магазин НОВЫХ", "Итого кол-во НОВЫХ", "Итого выручка НОВЫХ");
fputcsv($fp, $titles, ";");


foreach ($months as $m) {

$zak_old_num = "0";
$zak_old_sum = "0";
$mag_old_num = "0";
$mag_old_sum = "0";
$total_old_num = "0";
$total_old_sum = "0";
$zak_new_num = "0";
$zak_new_sum = "0";
$mag_new_num = "0";
$mag_new_sum = "0";
$total_new_num = "0";
$total_new_sum = "0";

$dt = "$year_num-$m";


$q = "SELECT  client_id, ROUND(prdm_sum_acc) AS prdm_sum_acc, date_query, typ_ord FROM queries WHERE date_query LIKE '$dt%' AND deleted <> '1' ORDER BY date_query DESC";
$get = mysql_query($q);


while($g =  mysql_fetch_assoc($get)){

$client_id = $g[client_id];
$prdm_sum_acc = $g[prdm_sum_acc];
$date_query = $g[date_query];
$typ_ord = $g[typ_ord];

$s = "SELECT client_id FROM queries WHERE client_id = '$client_id' AND date_query < '$date_query'";
$search = mysql_query($s);
$num_rows = mysql_num_rows($search);

//значит клиент старый
if($num_rows > 0){


//заказ, заказчик старый
if ($typ_ord == "1"){
$zak_old_num = $zak_old_num + 1;
$zak_old_sum = $zak_old_sum + $prdm_sum_acc;
}
//магазин, заказчик старый
if ($typ_ord == "2"){
$mag_old_num = $mag_old_num + 1;
$mag_old_sum = $mag_old_sum + $prdm_sum_acc;
}

//магазин c лого , заказчик старый
if ($typ_ord == "3"){
$mag_logo_old_num = $mag_logo_old_num + 1;
$mag_logo_old_sum = $mag_logo_old_sum + $prdm_sum_acc;
}

//итоговые данные по старым клиентам
$total_old_num = $total_old_num + 1;
$total_old_sum = $total_old_sum + $prdm_sum_acc;


}
//значит клиент новый
if($num_rows == 0){


//заказ, заказчик новый
if ($typ_ord == "1"){
$zak_new_num = $zak_new_num + 1;
$zak_new_sum = $zak_new_sum + $prdm_sum_acc;
}
//магазин, заказчик новый
if ($typ_ord == "2"){
$mag_new_num = $mag_new_num + 1;
$mag_new_sum = $mag_new_sum + $prdm_sum_acc;
}

//магазин c лого , заказчик новый
if ($typ_ord == "3"){
$mag_logo_new_num = $mag_logo_new_num + 1;
$mag_logo_new_sum = $mag_logo_new_sum + $prdm_sum_acc;
}

//итоговые данные по новым клиентам
$total_new_num = $total_new_num + 1;
$total_new_sum = $total_new_sum + $prdm_sum_acc;

}



}

$g[] .= $dt;
$g[] .= $zak_old_num;
$g[] .= $zak_old_sum;
$g[] .= $mag_logo_old_num;
$g[] .= $mag_logo_old_sum;
$g[] .= $mag_old_num;
$g[] .= $mag_old_sum;
$g[] .= $total_old_num;
$g[] .= $total_old_sum;
$g[] .= $zak_new_num;
$g[] .= $zak_new_sum;
$g[] .= $mag_logo_new_num;
$g[] .= $mag_logo_new_sum;
$g[] .= $mag_new_num;
$g[] .= $mag_new_sum;
$g[] .= $total_new_num;
$g[] .= $total_new_sum;

$zak_old_num = "";
$zak_old_sum = "";
$mag_logo_old_num = "";
$mag_logo_old_sum = "";
$mag_old_num = "";
$mag_old_sum = "";
$total_old_num = "";
$total_old_sum = "";
$zak_new_num = "";
$zak_new_sum = "";
$mag_logo_new_num = "";
$mag_logo_new_sum = "";
$mag_new_num = "";
$mag_new_sum = "";
$total_new_num = "";
$total_new_sum = "";

fputcsv($fp, $g, ';');

}

echo mysql_error();
fclose($fp);



?>
 <br><br><a href="new_old.csv">скачать файл</a>

 <? }
?>
 </body>

</html>
