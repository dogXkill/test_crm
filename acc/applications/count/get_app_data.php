<?
require_once("../../includes/db.inc.php");

//получаем из файла номер заявки, формируем json массив и отправляем его обратно для заполнения формы заявки
$num_ord = $_GET["num_ord"];
$error = '';
if (isset($_GET['sending'])) {
  $sending = $_GET['sending'];
  $q = "SELECT applications FROM shipments WHERE id = $sending";
  $r = mysql_fetch_assoc(mysql_query($q));
  $apps = explode('||', $r['applications']);
  if (!in_array($num_ord, $apps)) {
    $error = "error;<span class=\"result_err\" id=num_ord_err_span>Данный номер заявки отсутствует в отправке №".$sending;
    $error .= " Доступные номера: " . implode(", ", $apps);
    $error .= "</span>";
    echo $error;
    $ok = 0;
  }
}

$q = mysql_query("SELECT app_type, izd_type, izd_lami, izd_material, ClientName, izd_color, izd_w, izd_v, izd_b, tiraz, DATE_FORMAT(dat_ord,'%d.%m.%Y') AS dat_ord, plan_in, archive FROM applications WHERE num_ord = '$num_ord'");

if (mysql_num_rows($q) == 0){
  if (empty($error)) {
    $error = "error;<span class=\"result_err\" id=num_ord_err_span>Заявка с номером <b>$num_ord</b> не найдена!</span>".mysql_error();
  }


echo $error;
}else{

$r =mysql_fetch_assoc($q);

$izd_w = $r[izd_w];
$izd_v = $r[izd_v];
$izd_b = $r[izd_b];

$ClientName = $r[ClientName];

$arr = get_all_types();

$arr[app_type][1] .= "заказ";
$arr[app_type][2] .= "серийка";

$app_type=$arr[app_type][$r[app_type]];
$izd_type=$arr[types][$r[izd_type]];
$izd_lami=$arr[lamination][$r[izd_lami]];
$izd_material=$arr[materials][$r[izd_material]];
$izd_color=$arr[colours][$r[izd_color]];


$tiraz = $r[tiraz];
$dat_ord = $r[dat_ord];
$plan_in = $r[plan_in];
$archive = $r[archive];

if($plan_in == "1"){$error = "error"; $error_mes = "<span class=result_err>Нельзя добавить работу по заявке, которая в плане!</span>";}else{$error = "ok"; $error_mes="";}
if($archive == "1"){$error = "error"; $error_mes = "<span class=result_err>Нельзя добавить работу по заявке, которая в уже архиве!</span>";}else{$error = "ok"; $error_mes="";}


$app_data = "$app_type $ClientName $izd_type $izd_w x $izd_v x $izd_b $izd_lami $izd_material $izd_color - $tiraz шт. заказ от $dat_ord. $error_mes";


if ($ok !== 0) {
  echo $error.";".$app_data;
}

}




//функция должна подтягивать в массив все типы, которые хранятся в бд
function get_all_types(){
//список названий таблиц, которые нам надо подгрузить в массивы
$getting_types = "types,materials,lamination,colours";
$getting_types_arr = explode(",", $getting_types);

foreach ($getting_types_arr as $val) {

$get = mysql_query("SELECT * FROM ".$val);

while($g =  mysql_fetch_array($get)){
$id = $g["0"];
$arr[$val][$id] .= $g["1"];
}} return $arr;
}

?>
