<?php
require_once("../../includes/db.inc.php");

//получаем имена сотрудников в массив
$sotr = "SELECT job_id, surname  FROM users WHERE job_id > '0'";
$sotr = mysql_query($sotr);
$sotr_arr = array();
while($rows = mysql_fetch_row($sotr)){
    $sotr_arr[$rows[0]] = $rows[1];
}

$year = $_GET["year"];
$month = $_GET["month"];
if ($year){
$vstavka = "WHERE cur_time LIKE '".$year."-".$month."-%'";
}

$num_ord = $_GET['num_ord'];
if (is_numeric($num_ord)){
$vstavka = "WHERE num_ord='".$num_ord."'";
}

$num_sotr = $_GET['num_sotr'];
//echo $num_sotr;
if (is_numeric($num_sotr)){
$vstavka = "WHERE num_sotr='".$num_sotr."'";
}


//получаем стоимость каждого вида работ в массив
$select_ord = "SELECT num_ord FROM job ".$vstavka." GROUP BY num_ord";
$select_ord = mysql_query($select_ord);
$rate_arr = array();
//получаем все номера заказов в данной выборке в массив
while($rows = mysql_fetch_row($select_ord)){

$nmord =$rows[0];
$select_job = "SELECT
num_ord,
rate,
rate_lamin,
rate_tigel_pril,
rate_tigel_udar,
rate_tisn_pril,
rate_tisn_udar,
rate_vstavka_dna_bok,
rate_line_truba_pril,
rate_line_truba_prokat,
rate_line_dno_pril,
rate_line_dno_prokat,
rate_upak,
rate_podgotovka_truby,
rate_drugoe,
title
FROM applications WHERE num_ord = '$nmord'";
$select_job = mysql_query($select_job);
$select_job = mysql_fetch_array($select_job);
//создаем массив, в котором по номеру заказа храним данные о стоимости работы
$rate_arr[$select_job[0]] = array($select_job[1],$select_job[2],$select_job[3],$select_job[4],$select_job[5],$select_job[6],$select_job[7],$select_job[8],$select_job[9],$select_job[10],$select_job[11],$select_job[12],$select_job[13],$select_job[14],$select_job[15]);
}

//print_r($rate_arr);
$select = "SELECT uid, num_sotr, num_ord, job, num_of_work, cur_time, nadomn FROM job ".$vstavka."";
$select = mysql_query($select);

while($r = mysql_fetch_array($select)){

$num_ord = $r[2];
$job = $r[3];
if ($job == "1"){$job_name = "ламинация"; $price = $rate_arr[$r[2]][1];}
if ($job == "2"){$job_name = "вырубка";  $price = $rate_arr[$r[2]][3];}
if ($job == "3"){$job_name = "тиснение"; $price = $rate_arr[$r[2]][5];}
if ($job == "4"){$job_name = "сборка"; $price = $rate_arr[$r[2]][0];}
if ($job == "5"){$job_name = "труба на линии"; $price = $rate_arr[$r[2]][8];}
if ($job == "6"){$job_name = "дно на линии"; $price = $rate_arr[$r[2]][10];}
if ($job == "7"){$job_name = "приладка вырубки"; $price = $rate_arr[$r[2]][2];}
if ($job == "8"){$job_name = "приладка тиснения"; $price = $rate_arr[$r[2]][4];}
if ($job == "9"){$job_name = "приладка на линии (труба)"; $price = $rate_arr[$r[2]][7];}
if ($job == "10"){$job_name = "приладка на линии (дно)"; $price = $rate_arr[$r[2]][9];}
if ($job == "11"){$job_name = "упаковка"; $price = $rate_arr[$r[2]][11];}
if ($job == "12"){$job_name = "вставка дна и боковин"; $price = $rate_arr[$r[2]][6];}
if ($job == "13"){$job_name = "ручная подготовка трубы"; $price = $rate_arr[$r[2]][12];}
if ($job == "14"){$job_name = "выдача надомнику"; $price = "0.50";}
if ($job == "15"){$job_name = "ручки с клипсами (комплект)"; $price = "0.17";}
if ($job == "16"){$job_name = "другое"; $price = $rate_arr[$r[2]][13];}

$price = str_replace(',','.',$price);
$cost = $r[4]*$price;
$price = str_replace('.',',',$price);
$price = str_replace('?',',',$price);
$cost = str_replace('.',',',$cost);
if ($r[6] == "1"){$nadomn = "нд";}else{$nadomn = "";}

$title = substr($rate_arr[$r[2]][14],0,25)."...";
//echo $r[2];
$text = $r[0].";".$r[1].";".$sotr_arr[$r[1]].";".$title.";".$r[2].";".$r[3].";".$job_name.";".$r[4].";".$price.";".$cost.";".$nadomn.";".$r[5]."\n";
$ptext = $ptext.$text;

$job_name = "";
}

$header = "id;id сотр.;имя сотрудника;заказ;номер заявки;id этапа;название этапа;количество;цена;стоимость;надому;время;\n";
$ptext = $header.$ptext;

//echo $ptext;
//fopen("export.csv", "r");
header("Content-type: application/vnd.ms-excel; charset=cp1251; header=present");
header("Content-disposition: attachment; filename=export.csv");
echo $ptext;

?>