<?
require_once("../../includes/db.inc.php");
$str = $_SERVER['QUERY_STRING'];
parse_str($str);



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


return $arr;

}



$arr = get_all_types();








//если переменная не пустая, то формируем соответствующий запрос
function form_sql_vst(){
//делаем здесь, потому что результат этой функции не является глобальным
$str = $_SERVER['QUERY_STRING'];
parse_str($str);
//список названий столбцов, которые мы хотим вставить в запрос
$flds = "num_ord,art_id,izd_type,izd_w,izd_v,izd_b,izd_material,izd_lami,izd_color,izd_color_inn,izd_ruchki,hand_color,izd_gramm,user_id";
$flds_arr = explode(",", $flds);

foreach ($flds_arr as $val) {
//получаем значение
$fld_val = ${$val};
//формируем строку запроса в мускуль
if($fld_val !== ""){$vst .= " AND $val = '$fld_val'";}

}


if($app_type == '5'){$vst .= " AND (app_type = '1' OR app_type = '2')";}else{if($app_type !== ''){$vst .= " AND app_type = '$app_type'";}}
if($vip == "1"){$vst .= " AND vip = '1'";}
if($plan_in == "1"){$vst .= " AND plan_in = '1'";}else if($plan_in == "0"){$vst .= " AND plan_in = '0'";}else{$vst .= " AND (plan_in = '0' OR plan_in = '1')";}
if($archive == "1"){$vst .= " AND archive = '1'";}else{$vst .= " AND archive = '0'";}
return $vst;
}



$vst = form_sql_vst();



if($limit == ""){$limit = 50;}



$q = "SELECT * FROM applications WHERE 1 $vst ORDER BY dat_ord DESC LIMIT $limit";

$app_ids = mysql_query($q2);



?>
<span style="display:none;font-style:italic" id=app_list_q><?=$q; echo "<br>".mysql_error(); //print_r ($arr[types])?><br><br></span>

<table border=1 cellpadding=0 cellspacing=0 class="apps_tbl">

<tbody>
<tr>
<th>id</th>
<th>ФИО</th>
<th>Дата добавления</th>
<th>Должность</th>
<th>База</th>
<th>Телефон</th>
<th>Логин/пароль</th>
<th>Тип доступа</th>
<th>Действие</th>
</tr>


<?//получаем список пользователей
$q = mysql_query("SELECT * FROM users WHERE administration='1' AND archive <> '1'");
while($u =  mysql_fetch_array($q)){

 ?>


<tr>
<td><?=$u["job_id"];?></td>
<td><?=$u["surname"];?> <?=$u["name"];?> <?=$u["father"];?></td>
<td>Дата добавления</td>
<td>Должность</td>
<td>База</td>
<td>Телефон</td>
<td>Логин/пароль</td>
<td>Тип доступа</td>
<td>Действие</td>
</tr>

<? } ?>

</tbody>
</table>

<script>


col_vis()
num_ord_arr = '<?=$num_ord_arr;?>';
check_workout(num_ord_arr);
</script>

<div id="div_comment" onmouseup="end_drag()" onmousemove="dragIt(this,event)" style="background-color: rgb(255, 255, 255); padding: 5px; width:450px; border: 1px solid rgb(0, 153, 204); position: absolute; display: none; z-index:10000">
<span style="cursor:move;" onmousedown="start_drag(document.getElementById('div_comment'),event)"><b>Комментарий к заявке №<span id="num_ord_comment_span"></span></b></span>

<textarea name="" id="comment" style="width:430px;height:150px"></textarea>
<input type="hidden" value="" id="num_ord_comment"/><br>
<input type="button" class="btn_big" onclick="comment_save()" value="Сохранить"/> <input type="button" class="btn_big" onclick="comment_close()" value="Закрыть"/>
</div>

<div  id="div_nakl"  style="background-color:#FFFFFF; width: 150px; height: 60px; padding:10px; border:1px #0099CC solid; display:none;z-index:10000">
<span onmouseover="Tip('Закрыть')" onclick="close_nakl()" style="font-weight:bold; color:#FF0000;position:absolute;right:15px;top:15px; cursor:pointer; font-size: 15px;">X</span>
<strong>Наклейка:</strong><br>
<select id=qty onchange="show_nakl()">
<option value="#">выбрать</option>
<option value="16">16</option>
<option value="9">9</option>
<option value="4">4</option>
<option value="2">2</option>
<option value="1">1</option>
</select>
<br>шт. на лист
<input type="hidden" id="nakl_num_ord" value=""/>
</div>

