<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

ob_start();

$auth = false;

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");

if ($user_access['plans_access'] == '0' || empty($user_access['plans_access'])) {
  header('Location: /');
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Планировщик</title>
<link href="../style.css?cache=<?=rand(1,1000000);?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../includes/fonts/css/all.min.css">
<style type="text/css">
<!--
#goods_table tr:hover {
background-color: #E6E6E6;
}
#goods_table td  {
border: 1px solid #336699;
padding: 4px;
}
.small_text{
font-size: 9px;
color: #336699;
}

.cifry{
font-size: 11px;
color: #336699;
font-weight: bold;
}

.spans{
border: 1px solid #336699;
background-color:white;
padding: 10px;
display:none;
}
</style>


<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
</head>
<body>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>
<?require_once("../templates/top.php");
$name_curr_page = 'plan';
require_once("../templates/main_menu.php");
$typ_ord = $_GET["typ_ord"];
if ($typ_ord == ""){$typ_ord = "all";}
$part = $_GET["part"];?>
<table align="center" width="1100" border="0" cellpadding=0 bgcolor="#F6F6F6">
  <tr>
    <td>


<table cellpadding=10>
<tr>

<td>
<a href="?part=goods" class=sublink><img src="../../i/goods.png" width="16" height="16" alt="" style="vertical-align:middle"></a>
<a href="?part=goods" class=sublink <?if($part=="goods"){?>style="font-weight:bold;"<?}?>>товары</a>
</td>
<td>
<a href="?part=synch" class=sublink><img src="../../i/synch.png" width="16" height="16" alt="" style="vertical-align:middle"></a>
<a href="?part=synch" class=sublink <?if($part=="synch"){?>style="font-weight:bold;"<?}?>>синхронизация</a>
</td>
<td>
<a href="/analyt/update_rpr.php" target="_blank" class=sublink>проставить с/с</a>
</td>
</tr>
</table>






<?if($part=="goods"){?>

<script>

function goods_ajax(){
izd_type = $('#type_inp').val();
izd_color = $('#color_inp').val();
izd_w = $('#izd_w').val();
izd_v = $('#izd_v').val();
izd_b = $('#izd_b').val();
group = $('#group_inp').val();
materials = $('#materials_inp').val();
manufacturer = $('#manufacturer_inp').val();

sort = $('#sort_inp').val();
sort_type = $('#sort_type_inp').val();
sravn = $('#sravn_inp').val();
sravn_type = $('#sravn_type_inp').val();
sravn_val = $('#sravn_val_inp').val();
sravn2 = $('#sravn2_inp').val();
sravn2_type = $('#sravn2_type_inp').val();
sravn2_val = $('#sravn2_val_inp').val();
sravn2_val = $('#sravn2_val_inp').val();
art_id = $('#art_id').val();

if($("#vis_inp").is(":checked")){vis = '1'} else{vis = '0'}
if($("#onn_inp").is(":checked")){onn = '1'} else{onn = '0'}


var ready_statement
var ready_statement_type
var zapas_months

if($("#ready_statement_chk").is(":checked")){
ready_statement = '1'
if($("#uchet_grupp").is(":checked")){ready_statement_type = 's_uchetom_grupp'} else{ready_statement_type = 'bez_ucheta_grupp'}
zapas_months = $('#zapas_months').val();
} else{ready_statement = '0'}



var geturl;
  geturl = $.ajax({
    type: "GET",
    url: 'goods_ajax.php',
  data : '&izd_type='+izd_type+'&izd_color='+izd_color+'&izd_w='+izd_w+'&izd_v='+izd_v+'&izd_b='+izd_b+'&grup='+group+'&izd_material='+materials+'&manufacturer='+manufacturer+'&vis='+vis+'&onn='+onn+'&sort='+sort+'&sort_type='+sort_type+'&sravn='+sravn+'&sravn_type='+sravn_type+'&sravn_val='+sravn_val+'&sravn2='+sravn2+'&sravn2_type='+sravn2_type+'&sravn2_val='+sravn2_val+'&ready_statement='+ready_statement+'&ready_statement_type='+ready_statement_type+'&zapas_months='+zapas_months+'&art_id='+art_id,
    beforeSend: function () {
 $("#goods_resp").html("<img src=\"../../i/load.gif\">");
    },
    complete: function () {

    },
  success: function () {
var resp = geturl.responseText

$("#goods_resp").html(resp)
additional_fld('hide')

}})

}



function hide_show_goods(uid){

if(uid) {
var geturl;
  geturl = $.ajax({
    type: "GET",
    url: 'hide_show_goods.php',
  data : '&uid='+uid,

    success: function () {
var resp = geturl.responseText

if(resp == '0'){opac = '0.5'}
else if(resp == '1'){opac = '1.0'}
else{alert('ошибка '+resp)}

$('#tr_id_'+uid).animate({opacity:opac},300);
}

})}
}

function statement_span(){
if($("#ready_statement_chk").is(":checked")){
$('#ready_statement_span').animate({opacity: "1"}, 300);
$("#type_inp [value='4']").attr("selected", "selected");
$("#sort_inp [value='monthly_profit']").attr("selected", "selected");
$("#sort_type_inp [value='DESC']").attr("selected", "selected");
$("#sravn_inp [value='months_of_sales']").attr("selected", "selected");
$("#sravn_type_inp [value='>']").attr("selected", "selected");
$("#sravn_val_inp").val("4")
} else{
$('#ready_statement_span').animate({opacity: "0.2"}, 300);
}

}

function show_query(){
  $("#test_query").toggle();
}



</script>
<input type="checkbox" id="ready_statement_chk" onchange="statement_span()"/> <label for="ready_statement_chk" style="cursor:pointer; border-bottom: 1px solid #CCCCCC;">готовый отчет</label>

<span id="ready_statement_span" style="opacity:0.2; border:1px solid #CCCCCC; padding: 7px;border-radius: 4px;">исходя из поддержания <input type="text" id="zapas_months" size=3  onchange="goods_ajax()"/> месячного запаса. <label for="uchet_grupp"  style="cursor:pointer; border-bottom: 1px solid #CCCCCC;">Учесть альтернативные товары из этой же группы</label> <input type="checkbox" value="1" id="uchet_grupp" checked onchange="goods_ajax()"/></span>
<br>
<br>
<select id="type_inp" style="width:150px" onchange="goods_ajax()">
<option value="">тип</option>
<?
$get_types = mysql_query("SELECT * FROM types");
while($gg =  mysql_fetch_array($get_types)){?>
<option value="<?=$gg["tid"];?>"><?=$gg["type"];?></option>
<?}?>
</select>

<select id="color_inp" style="width:150px" onchange="goods_ajax()">
<option value="">цвет</option>
<?
$get_colors = mysql_query("SELECT * FROM colours ORDER BY colour ASC");
while($gg =  mysql_fetch_array($get_colors)){?>
<option value="<?=$gg["cid"];?>"><?=$gg["colour"];?></option>
<?}?>
</select>

Ш: <input type="text" id="izd_w" size=3 name="" onchange="goods_ajax()"/>
В: <input type="text" id="izd_v" size=3 name="" onchange="goods_ajax()"/>
Б: <input type="text" id="izd_b" size=3 name="" onchange="goods_ajax()"/>

группа:
<select id="group_inp" style="width:150px" onchange="goods_ajax()">
<option value="">группа не задана</option>
<?
$groups = mysql_query("SELECT * FROM plan_groups ORDER BY gname ASC");
while($r =  mysql_fetch_array($groups)){?>
<option value="<?=$r["id"];?>"><?=$r["gname"];?></option>
<?}?>
</select>

материал:
<select id="materials_inp" style="width:150px" onchange="goods_ajax()">
<option value="">материал не задан</option>
<?
$materials = mysql_query("SELECT * FROM materials ORDER BY type ASC");
while($r =  mysql_fetch_array($materials)){?>
<option value="<?=$r["tid"];?>"><?=$r["type"];?></option>
<?}?>
</select>

производитель:
<select id="manufacturer_inp" style="width:150px" onchange="goods_ajax()">
<option value="">производитель не задан</option>
<?
$manufacturer = mysql_query("SELECT * FROM manufacturer ORDER BY type ASC");
while($r =  mysql_fetch_array($manufacturer)){?>
<option value="<?=$r["tid"];?>"><?=$r["type"];?></option>
<?}?>
</select>


<label for="vis_inp" style="cursor:pointer;">видимые (интран)</label> <input type="checkbox" value="1" id="vis_inp" checked onchange="goods_ajax()"/>
<label for="onn_inp" style="cursor:pointer;">видимые (сайт)</label> <input type="checkbox" value="1" id="onn_inp" checked onchange="goods_ajax()"/>


 <br>
 Сравнение #1:
<select id="sravn_inp" style="width:150px" onchange="goods_ajax()">
<option value="">без сравнения</option>
<option value="art_id">по номеру артикула</option>
<option value="sklad">складской остаток</option>
<option value="booked">забронировано</option>
<option value="in_work">в производстве</option>
<option value="sold">продано</option>
<option value="sold">продано</option>
<option value="tek_potrebnost">текущая потребность</option>
<option value="months_of_sales">месяцев продаж</option>
<option value="monthly_sales">среднемесячное потребление</option>
<option value="marja_unit">маржа единица изд</option>
<option value="monthly_profit">месячная прибыль при наличии изд</option>
<option value="izd_w">ширина изделия</option>
<option value="izd_v">высота изделия</option>
<option value="izd_b">бок изделия</option>
<option value="price_our">с/с</option>
<option value="price">отпускная цена</option>
</select>

<select id="sravn_type_inp" style="width:150px" onchange="goods_ajax()">
<option value="">тип сравнения</option>
<option value=">">больше</option>
<option value="<">меньше</option>
<option value="=">равно</option>
</select>

<input type="text" id="sravn_val_inp" size=3 name="" onchange="goods_ajax()"/>


Сравнение #2:
<select id="sravn2_inp" style="width:150px" onchange="goods_ajax()">
<option value="">без сравнения</option>
<option value="art_id">по номеру артикула</option>
<option value="sklad">складской остаток</option>
<option value="booked">забронировано</option>
<option value="in_work">в производстве</option>
<option value="sold">продано</option>
<option value="sold">продано</option>
<option value="tek_potrebnost">текущая потребность</option>
<option value="months_of_sales">месяцев продаж</option>
<option value="monthly_sales">среднемесячное потребление</option>
<option value="marja_unit">маржа единица изд</option>
<option value="monthly_profit">месячная прибыль при наличии изд</option>
<option value="izd_w">ширина изделия</option>
<option value="izd_v">высота изделия</option>
<option value="izd_b">бок изделия</option>
<option value="price_our">с/с</option>
<option value="price">отпускная цена</option>
</select>

<select id="sravn2_type_inp" style="width:150px" onchange="goods_ajax()">
<option value="">тип сравнения</option>
<option value=">">больше</option>
<option value="<">меньше</option>
<option value="=">равно</option>
</select>


<input type="text" id="sravn2_val_inp" size=3 name="" onchange="goods_ajax()"/>


сортировка: <select id="sort_inp" style="width:150px" onchange="goods_ajax()">
<option value="">без сортировки</option>
<option value="art_id">по номеру артикула</option>
<option value="sklad">складской остаток</option>
<option value="booked">забронировано</option>
<option value="in_work">в производстве</option>
<option value="sold">продано</option>
<option value="sold">продано</option>
<option value="tek_potrebnost">текущая потребность</option>
<option value="months_of_sales">месяцев продаж</option>
<option value="monthly_sales">среднемесячное потребление</option>
<option value="marja_unit">маржа единица изд</option>
<option value="monthly_profit">месячная прибыль при наличии изд</option>
<option value="izd_w">ширина изделия</option>
<option value="izd_v">высота изделия</option>
<option value="izd_b">бок изделия</option>
<option value="price_our">с/с</option>
<option value="price">отпускная цена</option>
</select>

<select id="sort_type_inp" style="width:150px" onchange="goods_ajax()">
<option value="">тип сортировки</option>
<option value="ASC">по возрастанию</option>
<option value="DESC">по убыванию</option>
</select>


<input type="text" id="art_id" style="width:150px"/>

<img src="../../i/refresh.png" width="24" height="24" alt="" style="vertical-align: middle; cursor:pointer;" onclick="goods_ajax()">
<!--<img src="../../i/planner.png" width="16" height="16" alt="" style="vertical-align:middle; cursor:pointer;" onclick="show_query()">-->
<i class="fa-duotone fa-square-terminal icon_btn_r21 icon_planner" onmouseover="Tip('Посмотреть запрос sql');" style="vertical-align:middle; cursor:pointer;" onclick="show_query()"></i>
<div id="goods_resp"></div>


<span id="span_change_group" class="spans"></span>
<span id="span_change_sklad_id" class="spans"></span>

<script>
function additional_fld(act){

var coloumns_to_hide = ["col_type", "col_color", "col_material", "col_mesto", "col_sold", "col_booked"];

jQuery.each(coloumns_to_hide, function() {
coloumn_name = this

if(act == "hide"){
$('td[name^='+coloumn_name+']').fadeOut(500);
$('colgroup[name^='+coloumn_name+']').fadeOut(500);
$('#add_fld_show').show();
$('#add_fld_hide').hide();

}else{
$('td[name^='+coloumn_name+']').fadeIn(500);
$('colgroup[name^='+coloumn_name+']').fadeIn(500);
$('#add_fld_show').hide();
$('#add_fld_hide').show();

}
     });

}

</script>
<?}?>











<?if($part=="synch"){  ?>
<script>
function synch(type){

var geturl;
  geturl = $.ajax({
    type: "GET",
    url: 'synch.php',
  data : 'actus=1&type='+type,
 beforeSend: function () {
 $("#status_"+type).html("<img src=\"../../i/load.gif\">");
    },
    complete: function () { },
    success: function () {
var resp1 = geturl.responseText
$("#status_"+type).html(resp1);
}
})

}
</script>
<span onclick="synch('date_format')">
<strong>0.</strong>
<a href="#" style="font-size: 20px;">подготовка базы</a></span>
<span id="status_date_format"><?renewal_date("date_format");?></span>
<br>
<span onclick="synch('web_goods')">
<strong>1.</strong>
<a href="#" style="font-size: 20px;">артикулы, типы изделий, цвета, производители, материалы, складской остаток с сайтом</a></span>
<span id="status_web_goods"><?renewal_date("web_goods");?></span>

<br>
<span onclick="synch('intra_sales')">
<strong>2.</strong>
<a href="#" style="font-size: 20px;">статистика продаж (посл. 2 года, сколько продано и за сколько месяцев продаж)</a></span>
<span id="status_intra_sales"><?renewal_date("intra_sales");?></span>

<br>
<span onclick="synch('app_stat')">
<strong>3.</strong>
<a href="#" style="font-size: 20px;">заявки на производство (незакрытые заявки за посл. 6 мес, недоупакованные)</a></span>
<span id="status_app_stat"><?renewal_date("app_stat");?></span>


<br>
<span onclick="synch('calc_rent')">
<strong>4.</strong>
<a href="#" style="font-size: 20px;">посчитать рентабельность (monthly_profit, monthly_sales)</a></span>
<span id="status_calc_rent"><?renewal_date("calc_rent");?></span>

<br>


<span onclick="synch('booked')">
<strong>5.</strong>
<a href="#" style="font-size: 20px;">обновить информацию о брони в таблице plan_arts</a></span>
<span id="status_booked"></span>

<br>
<strong>6.</strong>
<a href="http://crm.upak.me/analyt/export.php" style="font-size: 20px;" target="blank">выгрузить данные о заявках, продажах на сайт</a>

<br>


<b>7.</b>
<a href="http://test.paketoff.ru/content/controllers/AnyController.php" style="font-size: 20px;" target="blank">импортировать в БД сайта выгруженные выше таблицы</a>
<br>




<br>
<? } ?>

</td></tr>
</table>

<script>

</script>

</body>
</html>
<? ob_end_flush(); ?>
