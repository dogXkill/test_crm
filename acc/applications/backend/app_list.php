<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
$ap_access_edit = $user_access['proizv_access_edit'];
$month = array(
    'января',
    "февраля",
    "марта",
    "апреля",
    "мая",
    "июня",
    "июля",
    "августа",
    "сентября",
    "октября",
    "ноября",
    "декабря"
    );

$str = $_SERVER['QUERY_STRING'];
parse_str($str);

$tek_date = date("d-m-Y");
$tek_year = date('Y');

//функция должна подтягивать в массив все типы, которые хранятся в бд
function get_all_types(){
//список названий таблиц, которые нам надо подгрузить в массивы
$getting_types = "types,materials,lamination,ruchki";

$getting_types_arr = explode(",", $getting_types);

foreach ($getting_types_arr as $val) {

$get = mysql_query("SELECT * FROM ".$val);

while($g =  mysql_fetch_array($get)){
$id = $g["tid"];
$arr[$val][$id] .= $g["type"];

}}

//Приходится задавать вручную, т.к. такую таблицу нет смысла создавать

$arr[app_type][1] .= "заказ";
$arr[app_type][2] .= "серийка";
$arr[app_type][3] .= "внешний";
$arr[app_type][4] .= "шелкография";

return $arr;

}


function deadline_print($deadline, $month, $tek_date){
if($deadline !== "" and $deadline !== "0000-00-00" and $deadline !== "00-00-0000"){

// дата, время запроса

                $tmp = explode('-', $deadline);
                $tek_year = date('Y');
                $year = $tmp[0];
                $mnt = $tmp[1];
                $dat = $tmp[2];

               // echo $deadline."<br>";
                //дата и месяц
                $deadline_formatted = $dat.' '.$month[intval($mnt)-1];
                //добавляем год если необходимо
                if($tek_year !==  $year){
                $deadline_formatted = $deadline_formatted." ".$year;
                }

if(strtotime($tek_date) > strtotime($deadline))
        {echo "<span class=red_alert>$deadline_formatted</span>";}
    else
        {echo $deadline_formatted;}
    }else
    {echo "<span class=red_alert><b>не указан</b></span>";}
}


function date_comparison($dat){

$tek_date = date("Y-m-d");
//$tek_year = date('Y');

if($dat < $tek_date){$dat = nice_date($dat); return "<span class=red_alert>$dat</span>";}else{return nice_date($dat);}

}


function nice_date($dat){
$tek_year = date('Y');
$ord_year = date('Y', strtotime($dat));
if($tek_year !== $ord_year){$dat = date('d.m.Y', strtotime($dat));}else{$dat = date('d.m', strtotime($dat));}
return $dat;
}






$arr = get_all_types();


//цвета
$q = mysql_query("SELECT * FROM colours");
while($u =  mysql_fetch_array($q)){
$colours[$u[cid]][name] = $u[colour];
$colours[$u[cid]][html] = $u[html_colour];
}





$app_users = mysql_query("SELECT user_id FROM applications GROUP BY user_id");

while($au =  mysql_fetch_array($app_users)){
          $ids .= $au[0].",";

}
$ids = rtrim($ids, ',');

$where_in = "uid IN ($ids)";

//получаем список пользователей
$q = mysql_query("SELECT uid, name, surname FROM users WHERE $where_in ORDER BY archive ASC, surname ASC");
while($u =  mysql_fetch_array($q)){
$users[$u[0]] .= $u[1]." ".$u[2];
}



//если переменная не пустая, то формируем соответствующий запрос
function form_sql_vst(){


//// app_status_multi
$app_status_multi = $_GET["app_status_multi"];

$app_statuses = [];
if (count($app_status_multi)>=1){
foreach($app_status_multi as $val) {
	if ($val === "") continue;
	$app_statuses[] = abs((int)($val));
}
}
$app_statuses_as_string = implode(",", $app_statuses);
$app_statuses_sql = "";
if (strlen($app_statuses_as_string) > 0) {
	$app_statuses_sql = " AND app_status IN ($app_statuses_as_string)";
}

////////


//делаем здесь, потому что результат этой функции не является глобальным
$str = $_SERVER['QUERY_STRING'];
parse_str($str);
//список названий столбцов, которые мы хотим вставить в запрос
$flds = "num_ord,izd_type,izd_w,izd_v,izd_b,izd_material,izd_lami,izd_color,izd_color_inn,izd_ruchki,hand_color,izd_gramm,user_id";
$flds_arr = explode(",", $flds);

foreach ($flds_arr as $val) {
//получаем значение
$fld_val = ${$val};
//формируем строку запроса в мускуль
if($fld_val !== ""){$vst .= " AND $val = '$fld_val'";}

}



//обрабатываем тип заявки
if($search !== "everywhere" and $num_ord == ""){

if($app_type == '5'){$vst .= " AND (app_type = '1' OR app_type = '2')";}
else{if($app_type !== ''){$vst .= " AND app_type = '$app_type'";}}

if($app_statuses_sql !== "") {$vst .= $app_statuses_sql;}


if($plan_in == "1"){$vst .= " AND plan_in = '1' AND archive <> '1'";}
else if($plan_in == "0"){$vst .= " AND plan_in = '0' AND archive <> '1'";}
else if($plan_in == "3"){$vst .= "  AND archive = '1'";}
else{$vst .= " AND (plan_in = '0' OR plan_in = '1')";}
}


if($vip == "1"){$vst .= " AND vip = '1'";}


return $vst;
}

if(!$order){$order = "num_ord_desc";}

if($order == "deadline_desc"){$order_vst = " ORDER BY deadline DESC ";}
else if($order == "deadline_asc"){$order_vst = " ORDER BY deadline ASC ";}
else if($order == "size"){$order_vst = " ORDER BY izd_w, izd_b, izd_v ASC ";}
else if($order == "num_ord_desc"){$order_vst = " ORDER BY num_ord DESC ";}
else if($order == "num_ord_asc"){$order_vst = " ORDER BY num_ord ASC ";}

$vst = form_sql_vst();


if($ClientName !== "" or $art_id){
$ClientName = iconv('UTF-8', 'windows-1251', $ClientName);
$vst .= " AND (ClientName LIKE '%$ClientName%' OR text_on_izd LIKE '%$ClientName%' OR zakaz_id LIKE '$ClientName' OR art_id = '$ClientName') ";}


//отдельно формируем дату
if(is_numeric($month_num) && is_numeric($year_num)){
 $vst .= " AND dat_ord LIKE '$year_num-$month_num%' ";
}

//отдельно формируем год
if(!is_numeric($month_num) && is_numeric($year_num)){
 $vst .= " AND dat_ord LIKE '$year_num-%' ";
}

if ($_GET['access_type'] == '1') {
  $vst .= " AND user_id = " . $_GET['usid'];
} else if ($_GET['access_type'] == '0') {
  $vst .= " AND user_id = 0";
}

if($limit == ""){$limit = 250;}



$q = "SELECT * FROM applications WHERE 1 $vst $order_vst LIMIT $limit";
$q2 = "SELECT num_ord FROM applications WHERE 1 $vst $order_vst LIMIT $limit";
$app_ids = mysql_query($q2);

//массив с типами работ по каждому заказу если они выполнялись
while($app_id = mysql_fetch_assoc($app_ids)){
$num_ord = $app_id[num_ord];
if(is_numeric($num_ord)){
$j = mysql_query("SELECT job, SUM(num_of_work) AS num_of_work FROM job WHERE num_ord = '$num_ord' GROUP BY job");
while($job = mysql_fetch_assoc($j)){
$jobs[$num_ord][$job[job]] = $job[num_of_work];
}}
}

//массив с данными по складскому остатку артикулов
$art_ids = mysql_query("SELECT art_id, sklad, monthly_sales FROM plan_arts WHERE sklad > 0");
            while ( $row = mysql_fetch_array($art_ids) ) {
            $sklad_info[$row[0]][sklad] = $row[1];
            $sklad_info[$row[0]][sales] = $row[2];
            }


function workout_table($num_ord, $job, $per_list, $tiraz, $limit_per){
    $done_itog = 0;
    $list_total_itog = 0;
if(is_numeric($per_list)){$list_total = $tiraz/$per_list;}else{$list_total = $tiraz;}
global $jobs;
$list_total = round($list_total);
$done = $jobs[$num_ord][$job];
if($done == ""){$done = 0;}
if ($list_total>=1){
$workout = round($done/$list_total*100);
}else{$workout=0;}
$done = round($done);
//если сделано больше 100% от объема работ, то все равно пишем 100, чтобы не портить вид талицы
if($workout > 100){$workout_perc = "100";}else{$workout_perc = $workout;}

//если выполнено работ больше чем допустимый перекат, то отмечаем такие таблицы красным
if($workout > (100+$limit_per)){$cl = "workout_red_div";}else{$cl = "workout_green_div";}
$table = "<div style=\"width:".$workout_perc."px;\" class=".$cl."></div><div id=\"workout_div_".$num_ord."_".$job."\" class=workout_outer_div>".$done." из ".$list_total." <br> <span class=workout_perc_text>(".$workout."%)</span></div><input type=\"hidden\" value=\"".$workout."\" id=\"workout".$num_ord."-".$job."\" style=\"width:30px;\" />";
//$done_itog = $done_itog + $done;
//$list_total_itog = $list_total_itog + $list_total;

return array("$table","$done","$list_total");

}

$apps = mysql_query($q);

?>
<span style="display:none;font-style:italic" id=app_list_q><?=$q; echo "<br>".mysql_error();?><br><br></span>

<table border=1 cellpadding=0 cellspacing=0 class="apps_tbl">

<tbody>
<tr style='position: sticky;
    top: 0px;
    z-index: 9;
    background: white;    border-top: none !important;
    border-bottom: none !important;
    box-shadow: inset 0 0px 0 #ffffff, inset 0 -2px 0 #313030;'>
<th name="col_app_type">Тип</th>
<th name="col_num_ord" style="cursor:pointer;<?if($order=="num_ord_desc" or $order=="num_ord_asc"){echo "text-decoration:underline;color:#A0A0A0";}?>" onclick="get_app_list('<?if($order=="num_ord_desc"){echo "num_ord_asc";}else{echo "num_ord_desc";}?>')">Номер заявки <?if($order=="num_ord_desc"){?>&darr;<?}if($order=="num_ord_asc"){?>&uarr;<?}?></th>
<th name="col_tiraz">Тираж</th>
<th name="col_art_id" style="width:250px"><?if($app_type == "2"){?>Артикул<?}else{?>Наименование<?}?></th>
<th name="col_izd_type">Тип изделия</th>
<th name="col_izd_color">Цвет изделия</th>
<th name="col_izd_cinn">Цвет внутр</th>
<th name="col_size" style="cursor:pointer;<?if($order=="size")echo "text-decoration:underline;color:#A0A0A0"?>" onclick="get_app_list('size')">Ш х В х Б</th>
<th name="col_izd_material">Материал</th>
<th name="col_material_qty">Листов (материал)</th>
<th name="col_material_size">Формат материала (см)</th>
<th name="col_material_w">Вес материала (кг)</th>
<th name="col_material_postavka">Поставка материала</th>
<th name="col_izd_lami">Ламинация</th>
<th name="col_izd_ruchki">Ручки</th>
<th name="col_hand_length">Длина ручки</th>
<th name="col_hand_thick">Толщ. ручки</th>
<th name="col_hand_color">Цвет ручек</th>
<th name="col_shtamp">Штамп / клише</th>
<th name="col_izdlami_status">Ламинация</th>
<th name="col_virub_status">Вырубка</th>
<th name="col_tisnenie_status">Тиснение</th>
<th name="col_shelko_status">Шелкография</th>
<th name="col_sborka_status">Сборка</th>
<th name="col_upakovka_status">Упаковка</th>
<th name="col_user">Менеджер</th>
<th name="col_deadline" style="cursor:pointer;<?if($order=="deadline_desc" or $order=="deadline_asc"){echo "text-decoration:underline;color:#A0A0A0";}?>" onclick="get_app_list('<?if($order=="deadline_asc"){echo "deadline_desc";}else{echo "deadline_asc";}?>')">Дедлайн <?if($order=="deadline_desc"){?>&darr;<?}if($order=="deadline_asc"){?>&uarr;<?}?></th>
<th name="col_plan_in">План</th>
<th name="col_archive">Архив</th>
<th name="col_buttons">Действие</th>
</tr>


<?
while($ap = mysql_fetch_assoc($apps)){
$uid = $ap[uid];
$dat_ord = $ap[dat_ord];
$user_id = $ap[user_id];
$app_type = $ap[app_type];
$shelko_art = $ap[shelko_art];
$shelko_num_colors = $ap[shelko_num_colors];
$app_status = $ap[app_status];
$app_status_update = $ap[app_status_update];
$izd_type = $ap[izd_type];
$tiraz = $ap[tiraz];
$izd_color = $ap[izd_color];
$izd_color_inn = $ap[izd_color_inn];
$zakaz_id = $ap[zakaz_id];
$num_ord = $ap[num_ord];
$num_ord_arr .= ",".$num_ord;
$izd_material = $ap[izd_material];
$isdely_per_list = $ap[isdely_per_list];
$izd_gramm = $ap[izd_gramm];
$material_arrival_date = $ap[material_arrival_date];

$material_arrival_comment = $ap[material_arrival_comment];
$material_supplier_comment = $ap[material_supplier_comment];
$resperson_material = $ap[resperson_material];
$resperson_pechat = $ap[resperson_pechat];
if($resperson_pechat == ""){$resperson_pechat = 0;}
$deadline_stamp = $ap['deadline_stamp'];
$deadline_material = $ap[deadline_material];
$deadline_pechat = $ap[deadline_pechat];//печать 
$list_w = $ap[list_w];
$list_h = $ap[list_h];
$ClName = $ap[ClientName];
$color_pantone = $ap[color_pantone];
$ClientName = (strlen($ClName)>25)?substr($ClName,0,25).'...':$ClName;
$txt_on_izd = $ap['text_on_izd'];
$text_on_izd = (strlen($txt_on_izd)>15)?substr($txt_on_izd,0,15).'...':$txt_on_izd;
$lami_isdely_per_list = $ap[lami_isdely_per_list];
$virub_isdely_per_list = $ap[virub_isdely_per_list];
$col_ottiskov_izd = $ap[col_ottiskov_izd];
$limit_per = $ap[limit_per];
$tisnenie = $ap[tisnenie];
$izd_tisn_storon = $ap[izd_tisn_storon];
$shelko_prokatok = $ap[shelko_prokatok];
if($shelko_prokatok==0){$shelko_prokatok = 1;}
$shelko_storon = $ap[shelko_storon];
$izd_lami_storon = $ap[izd_lami_storon];
$izd_virub_storon = $ap[izd_virub_storon];
$izd_lami = $ap[izd_lami];
$izd_ruchki = $ap[izd_ruchki];
$hand_length = $ap[hand_length];
$hand_thick = $ap[hand_thick];
$sborka_cost = $ap[rate_4];
$deadline = $ap[deadline];

$shnur_order = $ap[shnur_order];
$shnur_order_status = $ap[shnur_order_status];

$stamp_num = $ap[stamp_num];
$stamp_order = $ap[stamp_order];
$stamp_order_status =  $ap['stamp_order_status'];
$stamp_arrival_date = $ap['stamp_arrival_date'];
$klishe_order = $ap[klishe_order];
$klishe_order_status = $ap[klishe_order_status];

$art_id = $ap[art_id];
$comment = $ap[comment];
$dressing=$ap[dressing];
$plan_in = $ap[plan_in];
$archive = $ap[archive];
$highlight_color = $ap["highlight_color"];
$result_files = 0;
$result_files_arr = array();
$result_files_path = $_SERVER['DOCUMENT_ROOT'] . '/acc/applications/result_files_img/' . $uid;
if (file_exists($result_files_path)) {
    $result_files_dir = scandir($result_files_path);
    foreach ($result_files_dir as $key => $file) {
        if ($file !== '.' && $file !== '..') {
            $result_files = $result_files + 1;
            array_push($result_files_arr, array('file' => $file));
        }    
    }
}
switch ($highlight_color){
	case "FFFFFF"://не принята
		$color_icon="icon_btn_black";
	case "CCFFFF"://заявка принята
		$color_icon="icon_btn_black";
	case "FFD4A8"://в работе
		$color_icon="icon_btn_black";
		$tip="light";
	break;
	case "3DF500"://выполнена
		$color_icon="icon_btn_black";
		$tip="solid";
	break;
	case "FF3300"://трубет внимания
		$color_icon="icon_btn_white";
		$tip="light";
	break;
	default:
		$color_icon="icon_btn_black";
		$tip="light";
	break;
}
?>
<script>
    $(window).attr('uid', '<?=$user_access['uid']?>');
    console.log(JSON.parse('<?print_r(json_encode($result_files_arr));?>'));
</script>
<?  
//берем складской остаток из ранее созданного массива
if($art_id){
$sklad_ostatok = $sklad_info[$art_id][sklad];
if($sklad_ostatok == ''){$sklad_ostatok = '0';}
//получаем средний расход в месяц
$sales = $sklad_info[$art_id][sales];
//если он не указан, то ставим 25шт
if($sales == '0'){$sales = '25';}
if($sales == ''){$sales = 'нет данных';}
//если запас меньше месячного, подсвечиваем оранжевым
if($sklad_ostatok < $sales){$class = 'orange_alert';}
elseif($sklad_ostatok == '0'){$class = 'red_alert';}
else{$class = 'sm_text_td';}


$sklad_ostatok = "<br><span class=$class onmouseover=\"Tip('ежемесячное потребление примерно <b>$sales</b> шт');\">склад: <b>".$sklad_ostatok."</b>шт</span> <input type=hidden id=sklad_ostatok_$num_ord value=$sklad_ostatok />";
}else{$sklad_ostatok = "";}

$date_from = date('Y-m-d', strtotime($dat_ord));

$app_statuses = array("не принята", "заявка принята", "в работе", "требует внимания", "выполнена");
$shelko_num_colors_arr = array("","1+1","2+2","3+3","1+0","2+0","3+0");


$tek_date_app = date('Y-m-d H:i:s', time());

if($app_status == 0){

if(strtotime(date('Y-m-d H:i:s', strtotime($dat_ord . ' +1 day'))) < strtotime($tek_date_app)){$app_status_upd_err = "<br><span class='red_alert_sm' id='app_status_upd_err_$uid'>не принята больше дня</span>";}
if(strtotime(date('Y-m-d H:i:s', strtotime($dat_ord . ' +3 days'))) < strtotime($tek_date_app)){$app_status_upd_err = "<br><span class='red_alert' id='app_status_upd_err_$uid'>не принята больше 3х дней</span>";}

}else{$app_status_upd_err = "";}
$dressings = mysql_query("SELECT SUM(num_of_work) FROM `job` WHERE job = '31' AND num_ord = '$num_ord'");
$dressings = mysql_fetch_array($dressings);
?>

<tr data-resultFiles="<?=$result_files?>" data-type="<?=$app_type?>" id="tr_<?=$num_ord?>" <?if($usid == $user_id){echo "class=myorder";} ?>>
<td name="col_app_type" class="sm_text_td"><?=$arr[app_type][$app_type]; if($app_type == '4' and is_Numeric($ap[shelko_art])){echo "<br>на арт. <b>".$ap[shelko_art]."</b>";}?></td>
<td name="col_num_ord">
<!--<img src="../../i/info_sm.png" width="16" height="16" alt="" style="cursor:pointer" onclick="show_app_info('<?=$uid?>')">-->
<i class="fa-duotone fa-file-circle-info icon_btn_r17 icon_file_info" alt="" style="cursor:pointer" onclick="show_app_info('<?=$uid?>')"></i>
<span id="app_info_<?=$uid?>"  style="display: none; cursor: move; position: fixed;margin-left:30%;top:10px;z-index:100;background-color: white; width: 720px;  height: auto; border: 1px; border-color: #CDCDCD; padding: 10px;
border: 1px solid #808080;color: #333;border-radius: 15px;font-weight: 600;"></span>

<a href="edit.php?uid=<?=$uid?>"><?=$num_ord?></a>
<a href="edit.php?uid=<?=$uid?>" target="_blank"><i class="fa-duotone fa-pen-to-square icon_editbut"></i><!--<img src="/i/editbut.png" align="absmiddle">--></a>
<span class="dat_ord"><?=nice_date($ap[dat_ord]);?></span>
</td>

<td name="col_tiraz" id="col_tiraz_qty_<?=$num_ord?>"><?if($plan_in == '1'){?><input type="text" value="<?=$tiraz;?>" id="tiraz_<?=$num_ord;?>" onchange="update_app('<?=$num_ord;?>','tiraz', '0')" style="width:50px"/><?}else{echo $tiraz;} $tiraz_total = $tiraz_total + $tiraz;?><input type=checkbox class="tiraz_qty_class" onclick="calc_sum('tiraz_qty', '<?=$num_ord?>', 'шт. (тираж)')" id=tiraz_qty_<?=$num_ord;?> value=<?=$tiraz;?> /></td>


<td name="col_art_id" id="td_art_id_<?=$uid?>" style="background-color: <?=$highlight_color?>">
<?if($art_id > 0){?>
<a href="edit.php?uid=<?=$uid?>" target="_blank" onmouseover="Tip('Редактировать заявку');" style="font-weight:bold;"><?=$art_id;?></a><br>
<a href="/acc/stat/stat_shop.php?tip=by_art_num&art_id=<?=$art_id;?>&date_from=<?=$date_from = date('Y-m-d', strtotime($dat_ord));;?>&type=shop_history" target="_blank">
<!--<img src="../../i/stat_sm.png" align="absmiddle" onmouseover="Tip('Посмотреть историю продаж');">-->
<i class="fa-<?=$tip;?> fa-signal icon_btn_r17 <?php echo $color_icon;?>" align="absmiddle" onmouseover="Tip('Посмотреть историю продаж');"></i></a>
<a href="http://www.paketoff.ru/shop/view/?id=<?=$ap[art_uid];?>" target="_blank" onmouseover="Tip('Открыть в интернет магазине');" class='icon_btn'>
<!--<img src="/i/pkf.gif" align="absmiddle">-->
<i class="fa-<?=$tip;?> fa-lock icon_btn_r17 <?php echo $color_icon;?>"></i>
</a>
<?}else{?>
<a href="edit.php?uid=<?=$uid?>" target="_blank" onmouseover="Tip('Редактировать заявку');" style="font-weight:bold;"><?=$ClientName;?></a> <?if($text_on_izd !== ""){echo"($text_on_izd)";}?> <br>
<a href="/acc/applications/count/?num_ord=<?=$num_ord?>" target="_blank" class='icon_btn'>
<!--<img src="../../../i/table.png" align="absmiddle" width="16" height="16" style="display: display: inline;">-->
<i class="fa-<?=$tip;?> fa-table-cells icon_btn_r17 <?php echo $color_icon;?>"></i></a>
<a href="/acc/query/query_send.php?show=<?=$zakaz_id?>" target="_blank" class='icon_btn'>
<!--<img src="../i/rm_icon.gif" width="16" height="16" alt="" align="absmiddle"/>-->
<i class="fa-<?=$tip;?> fa-file-circle-question icon_btn_r17 <?php echo $color_icon;?>"></i>
</a>

<?
$jpg = $_SERVER{'DOCUMENT_ROOT'}. '/acc/applications/preview_img/'.$uid.'/1.jpg';
$png = $_SERVER{'DOCUMENT_ROOT'}. '/acc/applications/preview_img/'.$uid.'/1.png';

if (file_exists($jpg)) {
    //echo "<img src=\"../i/image_preview.png\" style=\"vertical-align:middle;cursor:pointer;\" onmouseover=\"show_preview('preview_img/$uid/1.jpg','$uid','show')\" onmouseout=\"show_preview('','','')\">";
	echo "<i class='fa-regular fa-image icon_btn_r17 $color_icon' style=\"vertical-align:middle;cursor:pointer;\" onmouseover=\"show_preview('preview_img/$uid/1.jpg','$uid','show')\" onmouseout=\"show_preview('','','')\"></i>&nbsp;";
}
if (file_exists($png)) {
    //echo "<img src=\"../i/image_preview.png\" style=\"vertical-align:middle;cursor:pointer;\" onmouseover=\"show_preview('preview_img/$uid/1.png','$uid','show')\" onmouseout=\"show_preview('','','')\">";
	echo "<i class='fa-regular fa-image icon_btn_r17 $color_icon' style=\"vertical-align:middle;cursor:pointer;\" onmouseover=\"show_preview('preview_img/$uid/1.png','$uid','show')\" onmouseout=\"show_preview('','','')\"></i>&nbsp;";
}
if ($result_files > 0) {  
    ?>
        <!--<img 
        src="/acc/i/result_file_icon.png" 
        style="width: 16px; height: 16px; cursor: pointer;" 
        align="absmiddle" 
        id="result_files_<?=$uid?>"
        onmouseover="show_result_files('<?=$uid?>', 'show')"
        onmouseout="show_result_files('', '')"
        />-->
		<i class="fa-<?=$tip;?> fa-eye icon_btn_r17 <?php echo $color_icon;?>" style='cursor:pointer' align="absmiddle" 
        id="result_files_<?=$uid?>"
        onmouseover="show_result_files('<?=$uid?>', 'show')"
        onmouseout="show_result_files('', '')"></i>
        <div id="result_files_list_<?=$uid?>" style="display: none;">
        <?
        foreach ($result_files_arr as $key => $value) {
            ?>
                <img style="width: 100px; height: auto;" src="/acc/applications/result_files_img/<?=$uid?>/<?=$value['file']?>"/>
            <?
        }
        ?>
        </div>
    <?
}
}?>
<i class="fa-<?=$tip;?> fa-traffic-light icon_btn_r17 <?php echo $color_icon;?>" style="display: display: inline; cursor:pointer" onclick="highlight_app_dialog('open', '<?=$uid;?>')" id="svetofor_<?=$uid;?>"></i>
<!--<img src="../i/svetofor.png" align="absmiddle" width="16" height="16" style="display: display: inline; cursor:pointer" onclick="highlight_app_dialog('open', '<?=$uid;?>')" id="svetofor_<?=$uid;?>">-->


<?=$sklad_ostatok;?><br>

<span id="span_app_status_<?=$uid?>" style="font-size:10px; font-weight:bold;" onmouseover="Tip('<?if($app_status_update !== "0000-00-00 00:00:00"){echo $app_status_update;}else{echo "дата и время приема заявки не известны";}?>');"><?=$app_statuses[$app_status];?></span>
<?=$app_status_upd_err;?>  <br>
</td>
<td class="sm_text_td" name="col_izd_type"><?=$arr[types][$izd_type];?></td>
<td name="col_izd_color" class="sm_text_td" style="text-align:left;">
<?
if(is_Numeric($izd_color)){?>
<span style="background-color:<?=$colours[$izd_color][html];?>; width:10px;height:10px;display: inline-block; border-width:1px; border-color: grey;border-style: solid;"></span>
<?=$colours[$izd_color]['name']; if($color_pantone){?><br>(пантон <b><?=$color_pantone;?></b>)
<?if($app_type == '4'){echo "<br><b>".$shelko_num_colors_arr[$shelko_num_colors]."</b>";}?>

<?}?>
<?}else{?><span style="background-color:<?=$colours[$izd_color][html];?>; width:10px;height:10px;display: inline-block; border-width:1px; border-color: grey;border-style: solid;"></span> не задан<?}?>
</td>
<td name="col_izd_cinn" class="sm_text_td" style="text-align:left;">
<?if(is_Numeric($izd_color_inn)){?>
<span style="background-color:<?=$colours[$ap[izd_color_inn]][html];?>; width:10px;height:10px;display: inline-block; border-width:1px; border-color: grey;border-style: solid;"></span>
<?=$colours[$izd_color_inn]['name'];?>
<?}else{?><span style="background-color:<?=$colours[$izd_color_inn][html];?>; width:10px;height:10px;display: inline-block; border-width:1px; border-color: grey;border-style: solid;"></span> не задан<?}?>
</td>
<td name="col_size" class="sm_text_td"><?=$ap[izd_w]?>x<?=$ap[izd_v]?>x<?=$ap[izd_b]?></td>
<td name="col_izd_material" class="sm_text_td"><?=$arr['materials'][$izd_material]; if($izd_gramm !== "" and $izd_gramm !== "0"){echo " ".$izd_gramm."гр";}?></td>
<td name="col_material_qty" id="col_material_qty_<?=$num_ord?>" onclick="calc_sum('material_qty', '<?=$num_ord?>', 'листов', '1')" class="sm_text_td" style="cursor:pointer">
<?if($app_type !== "4") if($isdely_per_list > 0){$material_qty = round($tiraz/$isdely_per_list); echo "$material_qty листов <input type=checkbox class=material_qty_class id=material_qty_$num_ord value=$material_qty />";  $material_qty_total = $material_qty_total + $material_qty;}else{echo "<span class=red_alert>не указано!</span>";}?></td>

<td name="col_material_size" class="sm_text_td"><?if($app_type !== '4') if($list_w > 1 and $list_h > 1){echo "$list_w x $list_h"; $m2 = ($list_w * $list_h) / 10000;}else{echo "<span class=red_alert>не указано!</span>"; $m2 = 0;}?></td>

<td name="col_material_w" id="col_material_w_qty_<?=$num_ord?>" class="sm_text_td" onclick="calc_sum('material_w_qty', '<?=$num_ord?>', 'кг (вес материала)', '1')" style="cursor:pointer"><?if($app_type !== '4') if($m2 > 0 and $izd_gramm > 0){$material_w = round(($m2 * $izd_gramm)/1000 * $material_qty); echo "$material_w кг. <input type=checkbox class=material_w_qty_class id=material_w_qty_$num_ord value=$material_w />";}else{echo "<span class=red_alert>укажите размер и граммаж!</span>";}?></td>

<td name="col_material_postavka" class="sm_text_td" style="cursor:pointer" onclick="show_app_info('<?=$uid?>')">

<?
$resperson_material_arr = array(
"<spad class=red_alert>не указано!</span>",
"самостоятельно менеджер",
"производственный отдел",
"в наличии",
"нет необходимости");
if($app_type !== '4'){
if($deadline_material !== "" and $material_arrival_date == ""){
    $deadline_material_nice = date_comparison($deadline_material);
    //$deadline_material_nice = nice_date($deadline_material);
    echo "планируется $deadline_material_nice";}
if($deadline_material == "" and $material_arrival_date == ""){
	if($resperson_material == 3){
		echo "<span class=green_alert>материал уже имелся</span>";
	}else if ($resperson_material==0){
		echo "<span class=red_alert>не указано!</span>";
		
	}else{
		$text_res_mat=$resperson_material_arr[$resperson_material];
		echo "<span class=green_alert>{$text_res_mat}</span>";
	}
}

if($material_arrival_date !== ""){
	$material_arrival_date_nice = nice_date($material_arrival_date); 
	echo "<span class=green_alert>материал поставлен <b>$material_arrival_date_nice</b>
	</span>";
}
?> <!--<img src="../../i/info_sm.png" width="16" height="16" alt="">--> 
<?php 
//печать 

//if($deadline_pechat == "" ){
	echo "</br>";
	if($resperson_pechat == 3){
		echo "<span class=green_alert>печать: уже имеется</span>";
	}else if ($resperson_pechat==0){
		echo "<span class=red_alert>печать: не указано!</span>";
		
	}else{
		$text_res_pechat=$resperson_material_arr[$resperson_pechat];
		echo "<span class=green_alert>печать: {$text_res_pechat}</span>";
	}
//}
/*else{
	echo "</br>";
	echo "<span class=red_alert>Печать планируется {$deadline_pechat}</span>";
}*/

echo "&nbsp;";
?>
<i class="fa-duotone fa-file-circle-info icon_btn_r12 icon_file_info" alt="" style='    vertical-align: baseline;' ></i>
<?}?>

</td>


<td name="col_izd_lami" class="sm_text_td"><?=$arr[lamination][$izd_lami];?></td>
<td name="col_izd_ruchki" class="sm_text_td"><?
 $highligt_hands = "sm_text_td";
 if($shnur_order == "1"){
        if($shnur_order_status == "0" or $shnur_order_status == ""){echo "<span class=red_alert_p onclick=show_app_info('$uid')>нужно заказать!</span><br>"; $highligt_hands = "red_alert_sm";}
        if($shnur_order_status == "1"){echo "<span class=orange_alert_p onclick=show_app_info('$uid')>заказан, но не получен!</span><br>"; $highligt_hands = "orange_alert_sm";}
        if($shnur_order_status == "2"){echo "<span class=green_alert_p onclick=show_app_info('$uid')>получен</span><br>"; $highligt_hands = "green_alert_sm";}
        }
 if($shnur_order == "2"){echo "<span class=green_alert_p onclick=show_app_info('$uid')>имеется в запасах!</span><br>"; $highligt_hands = "green_alert_sm";}
echo $arr[ruchki][$izd_ruchki];?></td>
<td name="col_hand_length" class="<?=$highligt_hands;?>"><?if($hand_length !== ""){echo $hand_length."см";}?></td>
<td name="col_hand_thick" class="<?=$highligt_hands;?>"><?if($hand_thick !== ""){echo $hand_thick."мм";}?></td>
<td name="col_hand_color" class="<?=$highligt_hands;?>"><?=$colours[$ap[hand_color]][name];?></td>
<td name="col_shtamp"><?

if ($stamp_order === '2' or $stamp_order === '1') { echo "<span class=green_alert_p onclick=show_app_info('$uid')>штамп имеется $stamp_num</span><br>"; }

if ($stamp_order === '3') {
    if($stamp_order_status == 0) { echo "<span class=red_alert_p onclick=show_app_info('$uid')>штамп нужно заказать!</span>"; }
    if($stamp_order_status == 1) { echo "<span class=orange_alert_p onclick=show_app_info('$uid')>штамп заказан, но не получен!</span>"; }
    if($stamp_order_status == 2) { echo "<span class=green_alert_p onclick=show_app_info('$uid')>штамп получен</span>"; }
    echo '<br>';
}
if ($stamp_order === '4') {
    if ($stamp_order_status == 0) { echo "<span class=red_alert_p onclick=show_app_info('$uid')>штамп заказывает менеджер проекта!</span>"; }
    if ($stamp_order_status == 1) { echo "<span class=orange_alert_p onclick=show_app_info('$uid')>штамп заказан, но не получен!</span>"; }
    if ($stamp_order_status == 2) { echo "<span class=green_alert_p onclick=show_app_info('$uid')>штамп получен</span>"; }
    echo '<br>';
}

if ($stamp_order === '5') {
    if ($stamp_order_status == 0) { echo "<span class=red_alert_p onclick=show_app_info('$uid')>штамп заказывает производство!</span>"; }
    if ($stamp_order_status == 1) { echo "<span class=orange_alert_p onclick=show_app_info('$uid')>штамп заказан, но не получен!</span>"; }
    if ($stamp_order_status == 2) { echo "<span class=green_alert_p onclick=show_app_info('$uid')>штамп получен</span>"; }
    echo '<br>';
}
// штамп в наличии и заполнено поле с фактической датой доставки
if (($stamp_order == 2 || $stamp_order_status == 2) && $stamp_arrival_date != "") {
    echo '<span class=green_alert_p onclick=\'show_app_info("'.$uid.'")\'> (поставлен ' . nice_date($stamp_arrival_date) . ')</span><br>';
}

// штамп не заказан или на стадии заказа кем-то - указывается планируемая дата поставки
if (($stamp_order == 3 || $stamp_order == 4 || $stamp_order == 5) && $stamp_order_status != 2 && $deadline_stamp != "") {
    echo '<span class=green_alert_p onclick=\'show_app_info("'.$uid.'")\'>(поставка планируется ' . date_comparison($deadline_stamp) . ')</span><br>';
}
// штамп не заказан или на стадии заказа кем-то и не заполнено поле с планируемой датой доставки, пусть заполняют
if (($stamp_order == 3 || $stamp_order == 4 || $stamp_order == 5) && $stamp_order_status != 2 && $deadline_stamp == "") {
    echo '<span class="red_alert_p" onclick=\'show_app_info("'.$uid.'")\'>планируемая дата поставки штампа не указана!</span><br>';
}
if ($stamp_order_status == 2 && $stamp_arrival_date == "") {
    echo '<span class="red_alert_p" onclick=\'show_app_info("'.$uid.'")\'>дата получения штампа не указана!</span><br>';
}

if($klishe_order == 2){echo "<span class=green_alert_p onclick=show_app_info('$uid')>клише уже имеется</span><br>";}
if($klishe_order == 1){
    if($klishe_order_status == 0){echo "<span class=red_alert_p onclick=show_app_info('$uid')>клише нужно заказать!</span><br>";}
    if($klishe_order_status == 1){echo "<span class=orange_alert_p onclick=show_app_info('$uid')>клише заказано, но не получен!</span><br>";}
    if($klishe_order_status == 2){echo "<span class=green_alert_p onclick=show_app_info('$uid')>клише получено</span><br>";}
}

?></td>
<td name="col_izdlami_status" class="workout_td"><?if($izd_lami !== "3" && $izd_lami !== "0"){if($izd_lami_storon == "0"){list($table,$done,$list_total) =  workout_table($num_ord, 1, $lami_isdely_per_list, $tiraz, $limit_per);  echo $table;  $lami_done_itog = $done + $lami_done_itog; $lami_list_total_itog = $list_total + $lami_list_total_itog; }else{echo "на стороне";}} if($izd_lami == "3"){echo "без ламинации";}?></td>
<td name="col_virub_status" class="workout_td"><?if($izd_virub_storon == "0"){ list($table,$done,$list_total) = workout_table($num_ord, 2, $virub_isdely_per_list, $tiraz, $limit_per); echo $table; $virub_done_itog = $done + $virub_done_itog; $virub_list_total_itog = $list_total + $virub_list_total_itog; }else{echo "на стороне";}?><input type="hidden" id="virub_isdely_per_list_<?=$num_ord;?>" value="<?=$virub_isdely_per_list;?>" /></td>
<td name="col_tisnenie_status" class="workout_td"><?if($tisnenie and $izd_tisn_storon !== "1"){list($table,$done,$list_total) = workout_table($num_ord, 3, 1/$col_ottiskov_izd, $tiraz, $limit_per);  echo $table; $tisnenie_done_itog = $done + $tisnenie_done_itog; $tisnenie_list_total_itog = $list_total + $tisnenie_list_total_itog; }if($izd_tisn_storon == "1"){echo "на стороне";} if($tisnenie == ""){echo "без тиснения";}?></td>
<td name="col_shelko_status" class="workout_td">
<?if($app_type==4 and $shelko_storon !=='1'){
	list($table,$done,$list_total) = workout_table($num_ord, 26, '', $tiraz*$shelko_prokatok, $limit_per); 
echo $table; $shelko_done_itog = $done + $shelko_done_itog; $shelko_list_total_itog = $list_total + $shelko_list_total_itog;
if ($dressing==1 && $dressings[0]>0 && $dressings[0]<$tiraz){
	echo "<p class='black_alert_p'>перевязано {$dressings[0]} из {$tiraz}шт";
}else if($dressing==1 && $dressings[0]>=$tiraz){
	echo "<p class='green_alert_p'>перевязано {$dressings[0]} из {$tiraz}шт";
}else if ($dressings[0]==0 && $dressing==1){
	echo "<p class='red_alert_p'>требуется перевязка</p>";
}
}
if($shelko_num_colors == '' or $shelko_num_colors == 0){echo "без шелкографии";}
if($shelko_storon == '1'){echo "на стороне";}?>

</td>
<td name="col_sborka_status" class="workout_td"><?list($table,$done,$list_total) = workout_table($num_ord, 4, 1, $tiraz, $limit_per); echo $table; $sborka_done_itog = $done + $sborka_done_itog; $sborka_list_total_itog = $list_total + $sborka_list_total_itog;?><span class="sm_text_td">тариф: <b><?if($sborka_cost == "" or $sborka_cost == "NaN"){echo "<span class=red_alert>ошибка</span>";}else{echo $sborka_cost;}?></b></span></td>
<td name="col_upakovka_status" class="workout_td"><?list($table,$done,$list_total) = workout_table($num_ord, 11, 1, $tiraz, $limit_per); echo $table; $upakovka_done_itog = $done + $upakovka_done_itog; $upakovka_list_total_itog = $list_total + $upakovka_list_total_itog;?></td>

<td name="col_user" class="sm_text_td"><?=$users[$user_id];?></td>
<td name="col_deadline" class="sm_text_td" >
    <?
    if ($user_access['proizv_access_edit'] == '2' || ($user_access['proizv_access_edit'] == '1' && $user_access['uid'] == $ap['user_id'])) {
       ?>
        <div class="app_deadline_text_cont" id="deadline_text_<?=$ap['num_ord']?>">
            <p id="app_deadline_date_<?=$ap['num_ord']?>"><?deadline_print($deadline, $month, $tek_date);?></p>
            &nbsp;<div class='icon_btn'>
			<!--<img data-numord="<?=$ap['num_ord']?>" class="app_deadline_img" id="deadline_img_<?=$ap['num_ord']?>" src="/acc/i/date.png"/>-->
			<i id="deadline_img_<?=$ap['num_ord']?>" data-numord="<?=$ap['num_ord']?>" class="fa-light fa-calendar-days app_deadline_img icon_btn_r17 "></i>
			</div>
        </div> 
        <div id="deadline_info_<?=$ap['num_ord']?>" class="green_alert_p" style="display: none;">Дедлайн <br />обновлен</div>
        <div id="deadline_edit_<?=$ap['num_ord']?>" style="display: none;" class="some_deadline_class">
            <input type="date" name="app_deadline" value="<?=$ap['deadline']?>" data-numord="<?=$ap['num_ord']?>" class="app_deadline_input" style="font-size: 11px;" id="deadline_<?=$ap['num_ord']?>">
            <!--<i class="fa fa-check app_deadline_save" data-numord="<?=$ap['num_ord']?>" title="Сохранить изменения" style="color: green; font-size: 15px;"></i>-->
			<i class="fa-solid fa-check app_deadline_save <?php echo $color_icon;?>" data-numord="<?=$ap['num_ord']?>" title="Сохранить изменения" style="color: green; font-size: 15px;"></i>
            <img class="app_deadline_undo" data-numord="<?=$ap['num_ord']?>" src="/acc/i/del.gif" title="Отменить изменения" data-deadline="<?=$ap['deadline']?>" id="app_deadline_del_<?=$ap['num_ord']?>" style="display: none;"/></img>
            <div class="app_deadline_actions">
                <!--<img class="app_deadline_save" data-numord="<?=$ap['num_ord']?>" src="/acc/i/pr_ok.gif" title="Сохранить изменения"/>
                <i class="fa fa-check app_deadline_save" data-numord="<?=$ap['num_ord']?>" title="Сохранить изменения" style="color: green; font-size: 15px;"></i>

                -->
            </div>
        </div>
       <? 
    } else {
        ?>
            <p id="app_deadline_date_<?=$ap['num_ord']?>"><?deadline_print($deadline, $month, $tek_date);?></p>
        <?
    }
    ?>

</td>
<td name="col_plan_in" class="sm_text_td">
<!--<?deadline_print($deadline, $month, $tek_date);?>-->
<input type="checkbox" <?if($ap_access_edit !== '2'){?>disabled<?}?> style="width:20px;height:20px; cursor:pointer;" name="plan_in_<?=$num_ord;?>" id="plan_in_<?=$num_ord;?>" value="" <?if($plan_in == '1'){echo "checked";}?> onchange="update_app('<?=$num_ord;?>','plan_in')">

</td>
<!--
<td name="col_plan_in">
<input type="checkbox" <?if($ap_access_edit !== '2'){?>disabled<?}?> style="width:20px;height:20px; cursor:pointer;" name="plan_in_<?=$num_ord;?>" id="plan_in_<?=$num_ord;?>" value="" <?if($plan_in == '1'){echo "checked";}?> onchange="update_app('<?=$num_ord;?>','plan_in')">
</td>
<td name="col_archive"><input type="checkbox" <?if($ap_access_edit !== '2'){?>disabled<?}?> style="width:20px;height:20px; cursor:pointer;" name="archive_<?=$num_ord;?>" id="archive_<?=$num_ord;?>" data-numord="<?=$num_ord;?>" value="" <?if($archive == '1'){echo "checked";}?> onchange="update_app('<?=$num_ord;?>','archive')"></td>
-->
<td name="col_archive">
<input type="checkbox" <?if($ap_access_edit !== '2'){?>disabled<?}?> style="width:20px;height:20px; cursor:pointer;" name="archive_<?=$num_ord;?>" id="archive_<?=$num_ord;?>" data-numord="<?=$num_ord;?>" value="" <?if($archive == '1'){echo "checked";}?> onchange="update_app('<?=$num_ord;?>','archive')">
</td>
<!--
<td name="col_buttons">
<input type="checkbox" <?if($ap_access_edit !== '2'){?>disabled<?}?> style="width:20px;height:20px; cursor:pointer;" name="plan_in_<?=$num_ord;?>" id="plan_in_<?=$num_ord;?>" value="" <?if($plan_in == '1'){echo "checked";}?> onchange="update_app('<?=$num_ord;?>','plan_in')">
</td>
-->
<td name="col_buttons" id="td_buttons_<?=$num_ord;?>">

<!--<img src="../../i/icons/print.png" width="16" height="16" onmouseover="Tip('Наклейки');" onclick="set_nakl('<?=$num_ord;?>')" style="cursor:pointer;">-->
<i class="fa-light fa-print icon_btn_r17" onmouseover="Tip('Наклейки');" onclick="set_nakl('<?=$num_ord;?>')" style="cursor:pointer;"></i><br><br>
<?if($comment!==""){
	//желтым
	?>
	<i class='fa-duotone fa-message-middle icon_btn_r17 icon_message_plus'  id='comment_but_<?=$num_ord;?>' onmouseover="Tip('Комментарий к заявке');" onclick="comment_app_dialog('<?=$num_ord;?>')" style="cursor:pointer;"></i>
	<?php
}else{
	?>
	<i class='fa-light fa-message-middle icon_btn_r17 '  id='comment_but_<?=$num_ord;?>' onmouseover="Tip('Комментарий к заявке');" onclick="comment_app_dialog('<?=$num_ord;?>')" style="cursor:pointer;"></i>
	<?php
}
?>
<!--<img src="../i/comment<?if($comment!==""){?>_is<?}?>.png" width="16" height="16" id="comment_but_<?=$num_ord;?>" onmouseover="Tip('Комментарий к заявке');" onclick="comment_app_dialog('<?=$num_ord;?>')" style="cursor:pointer;">-->
<?
if ($_GET['plan_in'] == '3') {
  ?>
  <img src="/acc/i/del.gif" width="20" height="20" onclick="del_app('<?=$num_ord;?>')" style="cursor:pointer;" onmouseover="Tip('Удалить заявку!');">

  <?
}
?>
</td>
</tr>
<?
$lami_isdely_per_list = NULL;
$lami_isdely_per_list_total = NULL;
$virub_isdely_per_list = NULL;
$limit_per = NULL;
$col_ottiskov_izd = NULL;
$deadline_frmt = "";
}?>
<tr>
<td name="col_app_type"></td>
<td name="col_num_ord"></td>
<td name="col_deadline"></td>
<td name="col_tiraz"><b><?=$tiraz_total;?></b></td>
<td name="col_art_id"></td>
<td name="col_izd_type"></td>
<td name="col_izd_color"></td>
<td name="col_izd_cinn"></td>
<td name="col_size"></td>
<td name="col_izd_material"></td>
<td name="col_material_qty"><b><?if($material_qty_total>0)echo "$material_qty_total листов";?></b></td>
<td name="col_material_size"></td>
<td name="col_material_w"></td>
<td name="col_material_postavka"></td>
<td name="col_izd_lami"></td>
<td name="col_izd_ruchki"></td>
<td name="col_hand_length"></td>
<td name="col_hand_thick"></td>
<td name="col_hand_color"></td>
<td name="col_shtamp"></td>
<td name="col_izdlami_status"><b><?if($lami_list_total_itog>0)echo "ламинация: $lami_done_itog из $lami_list_total_itog";?></b></td>
<td name="col_virub_status"><b><?if($virub_list_total_itog>0)echo "вырубка: $virub_done_itog из $virub_list_total_itog";?></b></td>
<td name="col_tisnenie_status"><b><?if($tisnenie_list_total_itog>0)echo "тиснение: $tisnenie_done_itog из $tisnenie_list_total_itog";?></b></td>
<td name="col_shelko_status"><b><?if($shelko_list_total_itog>0)echo "шелкография: $shelko_done_itog из $shelko_list_total_itog";?></b></td>
<td name="col_sborka_status"><b><?if($sborka_list_total_itog>0)echo "сборка: $sborka_done_itog из $sborka_list_total_itog";?></b></td>
<td name="col_upakovka_status"><b><?if($upakovka_list_total_itog>0)echo "упаковка: $upakovka_done_itog из $upakovka_list_total_itog";?></b></td>
<td name="col_user"></td>
<td name="col_plan_in"></td>
<td name="col_archive"></td>
<td name="col_buttons"></td>
</tr>
</tbody>
</table>

<script>


col_vis()
num_ord_arr = '<?=$num_ord_arr;?>';

check_workout(num_ord_arr);

$(".app_deadline_img").each(function() {
    $(this).on('click', function() {
        var num_ord = $(this).attr('data-numord');
        $("#deadline_edit_" + num_ord).css('display', 'block');
        $("#deadline_text_" + num_ord).css('display', 'none');
        $("#deadline_info_" + num_ord).css('display', 'none');

        setTimeout(hideDeadlineInput(num_ord), 1500);

    });
});


function months(month) {
    months = {1: 'января', 2: 'февраля', 3: 'марта', 4: 'апреля', 5: 'мая', 6: 'июня', 7: 'июля', 8: 'августа', 9: 'сентября', 10: 'октября', 11: 'ноября', 12: 'декабря'};
    return months;
}
months = months(); 

$(".app_deadline_save").each(function () {
    $(this).on('click', function() {
        var num_ord = $(this).attr('data-numord');
        var prev_deadline = $("#app_deadline_del_" + num_ord).attr('data-deadline');
        var new_deadline = $('#deadline_' + num_ord).val();
        if (new_deadline != prev_deadline) {
            $("#deadline_info_" + num_ord).css('display', 'block');
            date_arr = new_deadline.split('-');
            day = Number(date_arr[2]);
            month = Number(date_arr[1]);
            change_deadline(num_ord, prev_deadline, new_deadline);
            $("#app_deadline_date_" + num_ord).html(day + ' ' + months[month]);
            $("#app_deadline_del_" + num_ord).attr('data-deadline', new_deadline);
        }
        $("#deadline_text_" + num_ord).css('display', 'flex');
        $("#deadline_edit_" + num_ord).css('display', 'none');
        
    });
    
})
$(".app_deadline_undo").each(function () {
    $(this).on('click', function() {
        var num_ord = $(this).attr('data-numord');
        var prev_deadline = $(this).attr('data-deadline');
        var new_deadline = $("#deadline_" + num_ord).val();
        var date_arr = prev_deadline.split('-');
        day = Number(date_arr[2]);
        month = Number(date_arr[1]);
        $("#deadline_" + num_ord).val(prev_deadline);
        $("#deadline_text_" + num_ord).css('display', 'flex');
        $("#deadline_edit_" + num_ord).css('display', 'none');
        $("#deadline_info_" + num_ord).css('display', 'none');
        $("#app_deadline_date_" + num_ord).html(day + ' ' + months[month]);
        if (new_deadline != prev_deadline) {
            change_deadline(num_ord, new_deadline, prev_deadline);
        }
    });
     
})

function change_deadline(num_ord, from, to) {
    var uid = $(window).attr('uid');
    console.log(uid, num_ord, from, to);
	//отправить всем или нет
	var result_email = confirm("Отправить всем?");
    $.ajax({
      url: 'change_deadline.php',
      method: 'POST',
      data: {num_ord: num_ord, uid: uid, from: from, to: to,email_check:result_email},
      dataType: 'html', 
      async: false,
      success: function(data){
        console.log(data);
        $("#deadline_info_" + num_ord).css('display', 'block');
      }
    });
}

function hideDeadlineInput(num_ord) {
    $(document).on('click', function(e) {
        if (e.target.id != 'deadline_' + num_ord && e.target.id != 'deadline_img_' + num_ord) {
            $('#deadline_edit_' + num_ord).css('display', 'none');

            $('#deadline_text_' + num_ord).css('display', 'flex');
            $(document).off('click');
        }
        
    });
}


</script>
<input type="hidden" id="num_ord_arr" value="<?=$num_ord_arr;?>'"/>


<div id="div_comment" onmouseup="end_drag()" onmousemove="dragIt(this,event)" style="background-color: rgb(255, 255, 255); padding: 5px; width:550px; border: 1px solid rgb(0, 153, 204); position: absolute; display: none; z-index:10000">
<span style="cursor:move;" onmousedown="start_drag(document.getElementById('div_comment'),event)"><b>Комментарий к заявке №<span id="num_ord_comment_span"></span></b></span>
<div id="comment_div_text" style="width:530px; max-height:200; overflow:auto; padding: 3px; background-color: #F2F2F2"></div>
<textarea name="" id="comment" style="width:530px;height:50px"></textarea>
<input type="hidden" value="" id="num_ord_comment"/><br>
<p id="email_mas_otp" style="display: block;"><input type="checkbox" id="email_mas_otp_check"><label for="email_mas_otp_check">Отправить уведомления на почту?</label></p>
<input type="button" class="btn_big" onclick="comment_save()" value="Сохранить"/> <input type="button" class="btn_big" onclick="comment_close()" value="Закрыть"/>
</div>



<div id="div_highlight" onmouseup="end_drag()" onmousemove="dragIt(this,event)" style="background-color: rgb(255, 255, 255); padding: 5px; width:180px; border: 1px solid rgb(0, 153, 204); position: absolute; display: none; z-index:10000">

<input type="hidden" id="uid_highlight_inp" val=""/>
<div style="background-color:#FFFFFF;cursor:pointer;height:30px;text-align: center; padding: 5px;" onclick="highlight_app('FFFFFF', '0')">заявка не принята</div>
<div style="background-color:#CCFFFF;cursor:pointer;height:25px;text-align: center; padding: 5px;" onclick="highlight_app('CCFFFF', '1')">заявка принята</div>
<div style="background-color:#FFD4A8;cursor:pointer;height:25px;text-align: center; padding: 5px;" onclick="highlight_app('FFD4A8', '2')">в работе</div>
<div style="background-color:#FF3300;cursor:pointer;height:25px;text-align: center; padding: 5px;" onclick="highlight_app('FF3300', '3')">требует внимания</div>
<div style="background-color:#3DF500;cursor:pointer;height:25px;text-align: center; padding: 5px;" onclick="highlight_app('3DF500', '4')">выполнена</div>



</div>


<div id="div_preview"></div>
<div id="div_result_files"></div>

<div  id="div_nakl"  style="background-color:#FFFFFF; width: 150px; height: 60px; padding:10px; border:1px #0099CC solid; display:none;z-index:10000">
<span onmouseover="Tip('Закрыть')" onclick="close_nakl()" style="font-weight:bold; color:#FF0000;position:absolute;right:15px;top:15px; cursor:pointer; font-size: 15px;">X</span>
<strong>Наклейка:</strong><br>
<select id=qty onchange="show_nakl()">
<option value="#">выбрать</option>
<option value="30">30</option>
<option value="21">21</option>
<option value="15">15</option>
<option value="9">9</option>
<option value="4">4</option>
<option value="2">2</option>
<option value="1">1</option>
</select>
<br>шт. на лист
<input type="hidden" id="nakl_num_ord" value=""/>
</div>
