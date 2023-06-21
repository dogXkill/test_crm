<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
//ob_start();

$auth = false;

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");
?>
<table id="goods_table" cellpadding=0 cellspacing=0 width=1650>
<colgroup></colgroup>
<colgroup></colgroup>
<colgroup name="col_type"></colgroup>
<colgroup name="col_color"></colgroup>
<colgroup name="col_material"></colgroup>
<colgroup name="col_mesto"></colgroup>
<colgroup name="col_sold"></colgroup>
<colgroup name="col_booked"></colgroup>
<colgroup></colgroup>
<colgroup></colgroup>
<colgroup></colgroup>
<colgroup></colgroup>
<colgroup></colgroup>
<colgroup></colgroup>
<colgroup></colgroup>
<colgroup></colgroup>
<colgroup></colgroup>
<colgroup></colgroup>
<colgroup></colgroup>
<colgroup></colgroup>
<tr>
<td class="tab_query_tit" style="width:50px">Артикул</td>
<td class="tab_query_tit" style="width:350px">Название
<span onclick="additional_fld('hide')" id="add_fld_hide" style="cursor:pointer;font-size:15px;color:red; font-weight:bold;"><strong>-</strong></span>
<span onclick="additional_fld('show')" id="add_fld_show" style="cursor:pointer;display:none;font-size:15px;color:green; font-weight:bold;"><strong>+</strong></span></td>
<td class="tab_query_tit" name="col_type">Тип</td>
<td class="tab_query_tit" name="col_color">Цвет</td>
<td class="tab_query_tit" name="col_material">Материал</td>
<td class="tab_query_tit" name="col_mesto">Место на складе</td>
<td class="tab_query_tit" name="col_sold">Продано</td>
<td class="tab_query_tit" name="col_booked">Забронировано</td>
<td class="tab_query_tit">На складе</td>
<td class="tab_query_tit">В работе</td>
<td class="tab_query_tit">Тек потребность</td>
<td class="tab_query_tit">Товаров в группе</td>
<td class="tab_query_tit">Дефицит</td>
<td class="tab_query_tit">Группа</td>
<td class="tab_query_tit">С/с</td>
<td class="tab_query_tit">Цена</td>
<td class="tab_query_tit">Мес. прибыль</td>

<td class="tab_query_tit">Месяцев продаж</td>
<td class="tab_query_tit">Среднемес потребность</td>
<td class="tab_query_tit">Действие</td>
</tr>
<?

//это массив возможных параметров, по которым будет идти вывод данных из БД
$ar = array("izd_color","izd_type","izd_w","izd_v","izd_b","izd_material", "manufacturer","izd_lami", "grup", "vis", "onn");
//формируем из них строку с условием запроса в БД
$i = 0;
foreach ($ar as $value) {
if($_GET[$value] !== "" && $_GET[$value]){

	if($i == 0){$and = "";}else{$and = " AND ";}
	$sql_str .= $and." ".$value." = '".$_GET[$value]."'";
	$i = $i+1;
}
}

if($_GET["sravn"] !== "" && $_GET["sravn_type"] !== "" && $_GET["sravn_val"] !== ""){
if($sql_str !==""){$and = " AND ";}else{$and = "";}
$sql_str .= $and." ".$_GET["sravn"]." ".$_GET["sravn_type"]." '".$_GET["sravn_val"]."'";
}

if($_GET["sravn2"] !== "" && $_GET["sravn2_type"] !== "" && $_GET["sravn2_val"] !== ""){
if($sql_str !==""){$and = " AND ";}else{$and = "";}
$sql_str .= $and." ".$_GET["sravn2"]." ".$_GET["sravn2_type"]." '".$_GET["sravn2_val"]."'";
}

if($_GET["art_id"] !== ""){

if($sql_str !==""){$and = " AND ";}else{$and = "";}
$sql_str .= $and." art_id IN(".$_GET["art_id"].")";
}

//формируем отчет без учета наличия аналогичных пакетов в этой группе
if($_GET["ready_statement"] == "1"){
if($sql_str !==""){$and = " AND ";}else{$and = "";}
$zapas_months = $_GET["zapas_months"];
//если поле пустое, то ставим по умолчанию три месяца
if($zapas_months == ""){$zapas_months = "3";}
if($_GET["ready_statement_type"] == "bez_ucheta_grupp"){
//делаем отчет исходя из необходимости делать запас на Н месячев
$sql_str .= $and."(sklad+in_work) < (monthly_sales*".$zapas_months.")";
}
if($_GET["ready_statement_type"] == "s_uchetom_grupp"){
$sql_str .= $and."(sklad+in_work+group_sum) < (monthly_sales*".$zapas_months.")";
}
}


//если строка не пустая, добавляем WHERE
if($sql_str){$sql_str = " WHERE ".$sql_str;}

//формируем строку, по которой может быть произведена сортировка
//$srt_ar = array("art_id","sklad","booked","in_work","sold","tek_potrebnost","months_of_sales","monthly_sales","marja_unit","monthly_profit","izd_w","izd_v","price_our","price");
//формируем из них строку с условием запроса в БД

if($_GET["sort"]){
$sql_str_srt = "ORDER BY ".$_GET["sort"]." ".$_GET["sort_type"];
}



//если строка не пустая, добавляем ORDER BY
//if($sql_str_srt){$sql_str_srt = "ORDER BY ".$sql_str_srt;}


$test ="SELECT * FROM plan_arts ".$sql_str." ".$sql_str_srt;
echo  "<div id=test_query style=\"display:none; border:1px solid #CCCCCC; padding: 7px;border-radius: 4px; width:700px; margin: 10px 10px 10px 0px; \">".$test."</div>";

//собираем типы в массив
$get_types = mysql_query("SELECT * FROM types");
while($gg =  mysql_fetch_array($get_types)){
$types[$gg["tid"]] .= $gg["type"];
}

//собираем цвета в массив
$get_colors = mysql_query("SELECT * FROM colours ORDER BY colour ASC");
while($gg =  mysql_fetch_array($get_colors)){
$clrs[$gg["cid"]] .= $gg["colour"];
}

//собираем материалы в массив
$get_materials = mysql_query("SELECT * FROM materials");
while($gg =  mysql_fetch_array($get_materials)){
$materials[$gg["tid"]] .= $gg["type"];
}

//собираем названия групп в массив
$get_colors = mysql_query("SELECT * FROM plan_groups");
while($gg =  mysql_fetch_array($get_colors)){
$plan_groups[$gg["id"]] .= $gg["gname"];
}

$get_goods = mysql_query("SELECT * FROM plan_arts  ".$sql_str." ".$sql_str_srt);
echo mysql_error();
while($r =  mysql_fetch_array($get_goods)){
?>
<tr id="tr_id_<?=$r['uid'];?>"  style="opacity:<?if($r['vis'] == '0'){echo "0.5";}else{echo "1.0";}?>;cursor:pointer;">
<td><a href="http://www.paketoff.ru/admin/shop/goods_list/edit/?id=<?=$r['uid'];?>" target="_blank"><?=$r['art_id'];?></a></td>
<td style="width:350px"><a href="http://www.paketoff.ru/admin/shop/goods_list/edit/?id=<?=$r['uid'];?>" target="_blank"><?=$r['title'];?></a></td>
<td class="small_text" name="col_type"><?=$types[$r['izd_type']];?></td>
<td class="small_text" name="col_color"><?=$clrs[$r['izd_color']];?></td>
<td class="small_text" name="col_material"><?=$materials[$r['izd_material']];?></td>
<td align=center style="width:50px" id="td_sklad_<?=$r['uid'];?>" name="col_mesto"><?if($r['sklad_id'] == "0" or $r['sklad_id'] ==""){$sklad_id = "выбрать";}else{$sklad_id = $r['sklad_id'];}?><span class=sublink onclick="show_sklad('<?=$r['uid'];?>', 'show')"><?=$sklad_id;?></span></td>
<td align=center class="cifry" name="col_sold"><?=$r['sold'];?></td>
<td align=center class="cifry" name="col_booked"><?=$r['booked'];?></td>
<td align=center class="cifry"><?=$r['sklad'];?></td>

<td align=center class="cifry"><?=$r['in_work'];?> <a href="http://192.168.1.100/acc/applications/list.php?val=<?=$r['art_id'];?>&act=by_art_num" target="_blank">
<!--<img width="20" height="20" src="../i/../../i/manufacture.png" onmouseover="Tip('Просмотреть заявки на производство')">-->
<i class="fa-solid fa-file-pen icon_btn_r21 icon_btn_blue" onmouseover="Tip('Просмотреть заявки на производство')"></i>
</a></td>
<td align=center class="cifry"><?$potrebnost=$zapas_months*$r['monthly_sales']; echo $potrebnost;?></td>
<td align=center class="cifry"><?=$r['group_sum'];?></td>
<td align=center class="cifry"><?
if($_GET["ready_statement_type"] == "bez_ucheta_grupp"){
$deifcit = $r['sklad']+$r['in_work']-$potrebnost;
}
if($_GET["ready_statement_type"] == "s_uchetom_grupp"){
$deifcit =  $r['sklad']+$r['in_work']+$r['group_sum']-$potrebnost;
}

if($deifcit < 0){echo "<span style=color:red>".$deifcit."</span>";}else{echo "<span style=color:green>ok</span>";}

?></td>
<td align=center id="td_<?=$r['uid'];?>"><?if($r['grup'] == "0" or $r['grup'] ==""){$group = "выбрать";}else{$group = $plan_groups[$r['grup']];}?><span class=sublink onclick="show_groups('<?=$r['uid'];?>', 'show')" id="span_<?=$r['uid'];?>"><?=$group;?></span></td>

<td align=center class="cifry"><?=$r['price_our'];?></td>
<td align=center class="cifry"><?=$r['price'];?></td>
<td align=center class="cifry"><?=$r['monthly_profit'];?></td>
<td align=center class="cifry"><?=$r['months_of_sales'];?></td>
<td align=center class="cifry"><?=$r['monthly_sales'];?></td>
<td><img src="../../i/show.png" width="24" height="24" alt="эта видимость задается только в интранете, на сайте видимость товара задается отдельно" style="cursor:pointer;" id="show_<?=$r['uid'];?>" onclick="hide_show_goods('<?=$r['uid'];?>')">
<a href="#">
<!--<img width="20" height="20" src="../i/../../i/manufacture.png" onmouseover="Tip('Создать заявку')">-->
<i class="fa-solid fa-file-pen icon_btn_r21 icon_btn_blue" onmouseover="Tip('Создать заявку')"></i>
</a></td>
</tr>
<?}
if(mysql_num_rows($get_goods) == "0"){
?>
<tr><td colspan=20>Поиск не дал результатов</td></tr>
<?}?>
</table>