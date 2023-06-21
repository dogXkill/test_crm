<?php
require_once("../../includes/db.inc.php");

$sel_tarif = "SELECT num_ord, rate_1, rate_2, rate_3, rate_4, rate_5, rate_6, rate_7, rate_8, rate_9, rate_10, rate_11, rate_12, rate_13, rate_14, rate_15, rate_16, rate_17, rate_18, rate_19, rate_20, rate_21, rate_22, rate_23, rate_24, rate_25, rate_26, rate_27, rate_28, rate_30 FROM applications WHERE dat_ord > '2019-01-01 00:00:00'";
$sel_tarif_q = mysql_query($sel_tarif);
while($rows = mysql_fetch_row($sel_tarif_q)){

$rate_arr[$rows[0]] = array('',$rows[1],$rows[2],$rows[3],$rows[4],$rows[5],$rows[6],$rows[7],$rows[8],$rows[9],$rows[10],$rows[11],$rows[12],$rows[13],$rows[14],$rows[15],,$rows[16],$rows[17],$rows[18],$rows[19],$rows[20],$rows[21],$rows[22],$rows[23],$rows[24],$rows[25],$rows[26],$rows[27],$rows[28],'',$rows[29]);


}

//print_r($rate_arr[4722]);

$sel_j = "SELECT CONCAT(u.surname, ' ',u.name) AS name,
udep.name AS dept, d.name AS doljnost, a.app_type AS app_type, m.type AS material, a.zakaz_id AS zakaz_id,
a.art_id AS art_id, j.job AS job, jn.name AS job_name, j.num_ord AS num_ord,
t.type AS type, j.job AS job, j.num_of_work AS num_of_work, j.cur_time AS cur_time
FROM job AS j, users AS u, job_names AS jn, types AS t,
user_departments AS udep, doljnost AS d, applications AS a, materials AS m
WHERE j.cur_time > '2020-09-01 00:00:00' AND j.num_sotr = u.job_id
AND a.izd_type = t.tid AND j.job = jn.id AND j.num_ord = a.num_ord
AND u.user_department = udep.id AND u.doljnost = d.id AND m.tid = a.izd_material GROUP BY j.uid;";


$sel_j_q = mysql_query($sel_j);
while($r = mysql_fetch_assoc($sel_j_q)){
$name = $r[name];
$dept = $r[dept];
$doljnost = $r[doljnost];

$type = $r[type];


$app_type = $r[app_type];

    if($app_type == 2){$app_type_name = "серийка";}
    if($app_type == 1){$app_type_name = "заказ";}

$material = $r[material];
$zakaz_id = $r[zakaz_id];
$art_id = $r[art_id];
$job_name = $r[job_name];
$job = $r[job];

$num_ord = $r[num_ord];

$tarif = str_replace(".",",",$rate_arr[$num_ord][$job]);



$num_of_work = $r[num_of_work];
$cur_time = $r[cur_time];
$ptext .= $num_ord.";".$name.";".$dept.";".$doljnost.";".$app_type_name.";".$material.";".$zakaz_id.";".$art_id.";".$type.";".$job_name.";".$tarif.";".$num_of_work.";".$cur_time.";".$job."\n";

$num_ord  = NULL;
$name = NULL;
$dept = NULL;
$doljnost = NULL;
$app_type_name = NULL;
$material = NULL;
$zakaz_id = NULL;
$art_id = NULL;
$type = NULL;
$job_name = NULL;
$tarif = NULL;
$num_of_work = NULL;
$cur_time = NULL;
$job = NULL;

}

$header = "номер заявки на производство;имя сотрудника;отдел;должность;тип заявки;материал;номер заказа;артикул;тип изделия;наименование операции;тариф;количество;время добавления;учетный номер операции\n";
$ptext = $header.$ptext;


//echo $ptext;
fopen("export.csv", "r");
header("Content-type: application/vnd.ms-excel; charset=cp1251; header=present");
header("Content-disposition: attachment; filename=export.csv");
echo mysql_error();
echo $ptext;

/*

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
*/


?>