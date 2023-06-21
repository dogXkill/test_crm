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
if ($user_access['proizv_access'] == '0' || empty($user_access['proizv_access'])) {
  header('Location: /');
}
// ----- перейти на главную если доступ запрещен ---------
if(!$auth) {
  header("Location: /");
  exit;
}
// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;

$zakaz_id = $_GET["zakaz_id"];
$art_num = $_GET["art_num"];
$search = $_GET["search"];

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Производство</title>
<link href="../style.css?cache=<?=rand (1,1000000)?>" rel="stylesheet" type="text/css"/>
<link href="../includes/new.css?cache=<?=rand (1,1000000)?>" rel="stylesheet" type="text/css"/>
<!--<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/fontawesome.min.js" integrity="sha512-36dJpwdEm9DCm3k6J0NZCbjf8iVMEP/uytbvaiOKECYnbCaGODuR4HSj9JFPpUqY98lc2Dpn7LpyPsuadLvTyw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>-->
<link rel="stylesheet" type="text/css" href="../includes/fonts/css/all.min.css">
</head>

<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
<script type="text/javascript" src="../includes/jquery.cookie.js"></script>
<script type="text/javascript" src="../includes/app_list.js?cache=<?=rand (1,1000000)?>"></script>


<script>
var user_type = '<?=$user_type;?>';
var user_name = '<?=$user_access['name'];?>';
var user_surname = '<?=$user_access['surname'];?>';
</script>
<body>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>
<?require_once("../templates/top.php");
$name_curr_page = 'apl_list';
require_once("../templates/main_menu.php");
?>



<div id="app_sum_div" class="app_sum_div">
 <div id="material_qty_div" class="app_sum"></div>
 <div id="tiraz_qty_div" class="app_sum"></div>
 <div id="material_w_qty_div" class="app_sum"></div>
</div>
<? if($auth) {  ?>
<table align="center" width="100%" border="0" cellpadding=0 bgcolor="#FBFBFB">
<tr>
<td valign="top" align="center" style='width: calc(100% - 30%);display: block;'>
<table border="0" cellspacing="0" cellpadding="5" width=1000>
<tr>
<td></td>

<td><a href="edit.php" class='icon_btn'><!--<img src="../../i/manufacture.png" width="24" height="24" alt="" align="middle" />--><i class="fa-solid fa-file-pen icon_btn_r21 icon_btn_blue"></i></a> 
<a href="edit.php" class="sublink">Создать заявку</a></td>
<?
if ($user_access['accounting_user'] == 1 && $user_access['jobs_access'] !== '0') {
  ?>
  <td>
  <a href="count/add.php" target=_blank class='icon_btn'><!--<img src="../../i/add_job.png" width="22" height="22" alt="" align="middle">--><i class="fa-solid fa-square-plus icon_btn_r21 icon_btn_blue"></i></a>
  <a href="count/add.php" class=sublink target=_blank>Добавить работу</a>
  </td>
  <?
}
?>
<?php if (($user_access['proizv_access']==1)){?>
<td>
  <a href="/acc/sprav/stamps/" target=_blank class='icon_btn'>
  <!--<img src="../../i/journal.png" width="22" height="22" alt="" align="middle">-->
  <i class="fa-solid fa-box-archive  icon_btn_r21 icon_btn_blue"></i>
  </a>
  <a href="/acc/sprav/stamps/" class=sublink target=_blank>Реестр штампов</a>
  </td>
<?php } ?>
<?
if ($user_access['jobs_access'] !== '0') {
  ?>
  <td>
  <a href="count/" target=_blank class='icon_btn'>
  <!--<img src="../../i/journal.png" width="22" height="22" alt="" align="middle">-->
  <i class="fa-solid fa-table-cells icon_btn_r21 icon_btn_blue"></i>
  </a>
  <a href="count/" class=sublink target=_blank>Просмотр журнала</a>
  </td>
  <?
}

if ($user_access['edit_shipments'] == '1') {
  ?>
  <td><a href="/acc/applications/sendings/" class='icon_btn'><!--<img src="/i/logistic.png" width="24" height="24" alt="" align="middle" />--><i class="fa-solid fa-truck icon_btn_r21 icon_btn_blue"></i></a> <a href="/acc/applications/sendings/" class="sublink">Отправки</a></td>
  <?
}

?>
<td>

</td>

</tr></table></td></tr>
<tr>
<td>

<form action="" id=forma method="post" onformchange="get_app_list('')" style='    margin-top: -35px;'>
<input type="hidden" id=usid name=usid value="<?=$user_id;?>" />
<input type="hidden" id="access_type" name="access_type" value="<?=$user_access['proizv_access_type']?>" />
<span id="app_type_span">
<br>
<select id=app_type name=app_type style="width:170px" onchange="get_app_list('');">
<option value="5">все, кроме шелкографии</option>
<option value="1">заказная продукция</option>
<option value="2">серийная продукция</option>
<option value="3" disabled>заказ у внешнего поставщика</option>
<option value="4">готовые с лого</option>
</select>
</span>

<span id="app_status_span">
<select  multiple id=app_status name=app_status_multi[] style="width:150px;display:none;" onchange="get_app_list('');">
<!--<option value="">статус заявки</option>-->
<option value="">- не выбрано - </option>
<option value="0">не принята</option>
<option value="1">заявка принята</option>
<option value="2">в работе</option>
<option value="3">требует внимания</option>
<option value="4">выполнена</option>
</select>
</span>
<!--Статус заявки-->

<style>
#filter_status .filter__button {
	margin-top: 3px;
	background-color: white;
	height:32px;
	padding: 0px;
	padding-left: 10px;
	border-radius: 8px;
	font-size: 18px;
	/*line-height: 14px;*/
}
#filter_status .filter__option, 
#filter_status .filter__option_multi {
	background-color: white;
	padding: 0px;
	padding-left: 10px;
	height:22px;
	font-size: 18px;	
height:22px;
}
#filter_status .filter__button_label {
	line-height: 24px;

}
#filter_status .filter__options{
	padding-top:5px;
}

#filter_status .filter__option_multi {
	line-height: 14px;
}

</style>
<span id="filter_status" data-filter-id="tip_status" class="filter filterType_select active opened" style='display: inline-block;'>
	<div class="filter__input">
		<div class="filter__button clear">
<span class="filter__button_label" data-placeholder="Статус">- - - - -</span>
<span class="filter__button_arrow"><i class="fa fa-arrow-down"></i></span>
<span class="filter__button_reset"><i class="fa fa-times"></i></span>
		</div>
		<div class="filter__options">
<div class="filter__option_multi" data-value="0">не принята</div>
<div class="filter__option_multi" data-value="1">заявка принята</div>
<div class="filter__option_multi" data-value="2">в работе</div>
<div class="filter__option_multi" data-value="3">требует внимания</div>
<div class="filter__option_multi" data-value="4">выполнена</div>
		</div>
	</div>
</span>
<!-- /// -->
<span id="num_ord_span">
<input type="text" style="width:100px" id=num_ord name=num_ord value="" onchange="get_app_list('');" placeholder="Номер заявки"/>
</span>


<span id=ClientName_span><input type="text" id="ClientName" name=ClientName size=5 value="<?=$zakaz_id."".$art_num;?>"  placeholder="Артикул или бренд" onchange="get_app_list('');" style="width:170px"/></span>

<span id="izd_type_span">
<select id="izd_type" name=izd_type style="width:150px" onchange="get_app_list('');">
<option value="">тип продукции</option>
<?$get_types = mysql_query("SELECT * FROM types WHERE vis = '1' ORDER BY seq DESC");
while($gg = mysql_fetch_array($get_types)){
$id = $gg["tid"];
if($id == "0"){$id = "";}
?>
<option value="<?=$id;?>"><?=$gg["type"];?></option>
<?}?>
</select>
<input type="checkbox" name="vip" id="vip" value="1" onchange="get_app_list('');"> <label for="vip">VIP</label>
</span>


<span id=size_span style="line-height: 25px;">

<input type="text" style="width:50px;" id="izd_w" name="izd_w" onkeyup="this.value=replace_num(this.value);jump('izd_w', '2', 'izd_v')"  onchange="get_app_list('');" size=3  placeholder="Ширина"/>
<input type="text" style="width:50px;" id="izd_v" name="izd_v" onkeyup="this.value=replace_num(this.value);jump('izd_v', '2', 'izd_b')"  onchange="get_app_list('');" size=3  placeholder="Высота"/>
<input type="text" style="width:50px;" id="izd_b" name="izd_b" onkeyup="this.value=replace_num(this.value);jump('izd_b', '2', 'plan_in')"  onchange="get_app_list('');" size=3  placeholder="Бок"/>
</span>



<span id="plan_in_span">
заявки
<select name="plan_in" id="plan_in" style="width:160px;" onchange="get_app_list('');">
  <option value="0">действущие</option>
  <option value="1">планируемые</option>
  <option value="2">все не архивные</option>
  <option value="3">архивные</option>
  <option value="4">все</option>
</select>
</span>

<span id="izd_material_span">
<select id="izd_material" name="izd_material" style="width:120px" onchange="jump('izd_material','1','izd_gramm');get_app_list('');" >
<option value="">материал</option>
<?$materials = mysql_query("SELECT * FROM materials ORDER BY seq DESC");
while($r =  mysql_fetch_array($materials)){
$id = $gg["tid"];
if($id == "0"){$id = "";}
?>
<option value="<?=$r["tid"];?>"><?=$r["type"];?></option>
<?}?>
</select>

<input type="text" style="width:70px;" id="izd_gramm" name="izd_gramm" onkeyup="this.value=replace_num(this.value);" size=3 onchange="get_app_list('');"  placeholder="Грамм"/>
</span>


<span id=show_xtra_flds_span onclick="show_xtra_flds()" onmouseover="Tip('Показать дополнительные поля');" class="show_xtra_flds_span">+</span>
<i class="fa-duotone fa-gear icon_btn_r21 icon_setting" style="vertical-align:middle; cursor:pointer" onclick="fill_cols_chk()" onmouseover="Tip('Управление колонками');"></i>
<!--<img src="../../i/settings.png" width="22" height="22" alt="" style="vertical-align:middle; cursor:pointer" onclick="fill_cols_chk()" onmouseover="Tip('Управление колонками');">-->
<!--<img src="../../i/planner.png" width="16" height="16" alt="" onmouseover="Tip('Посмотреть запрос sql');" style="vertical-align:middle; cursor:pointer;" onclick="show_query()">-->
<i class="fa-duotone fa-square-terminal icon_btn_r21 icon_planner" onmouseover="Tip('Посмотреть запрос sql');" style="vertical-align:middle; cursor:pointer;" onclick="show_query()"></i>
<button onclick="get_app_list('');return false;" style="width: 150px; height: 25px;">обновить поиск</button>


<br>


<span id="xtra_flds" style="display:none">
<br>



<span id="izd_color">
<select id="izd_color" name="izd_color" style="width:130px" onchange="jump('izd_color','1','izd_lami');get_app_list('');" >
<option value="">Цвет изделия</option>
<?
$get_colors = mysql_query("SELECT * FROM colours WHERE cid <> '0' ORDER BY seq DESC");
while($gg =  mysql_fetch_array($get_colors)){
if($gg["cid"] !== "0")?>
<option value="<?=$gg["cid"];?>"><?=$gg["colour"];?></option>
<?}?>
</select>
</span>

<span id="paper_col_ext_span">
<select id="izd_color_inn" name="izd_color_inn" style="width:130px" onchange="get_app_list('');">
<option value="">Цвет внутр</option>
<?
$get_colors = mysql_query("SELECT * FROM colours WHERE cid <> '0' ORDER BY seq DESC");
while($gg =  mysql_fetch_array($get_colors)){
if($gg["cid"] !== "0")?>
<option value="<?=$gg["cid"];?>"><?=$gg["colour"];?></option>
<?}?>
</select>
</span>

<span id="izd_lami_span">
<select id="izd_lami" name="izd_lami" style="width:120px" onchange="get_app_list('');">
<option value="">Ламинация</option>
<?$get = mysql_query("SELECT * FROM lamination ORDER BY id ASC");
while($gg =  mysql_fetch_array($get)){
$id = $gg["id"];?>
<option value="<?=$id;?>"><?=$gg["name"];?></option>
<?$lami_arr .= "lami_arr[".$gg["id"]."] = ".$gg["cost"]."\n";}?>
</select>
</span>

<span id="izd_ruchki_span" style="padding-top:10px;">
<select id="izd_ruchki" name="izd_ruchki" style="width:120px" onchange="jump('izd_ruchki','1','hand_length');get_app_list('');">
<option value="">Ручки</option>
<?
$get = mysql_query("SELECT * FROM ruchki");
while($gg =  mysql_fetch_array($get)){?>
<option value="<?=$gg["tid"];?>"><?=$gg["type"];?></option>
<?}?>
</select>
<select id="hand_color" name="hand_color" style="width:150px" onchange="get_app_list('');">
<option value="">Цвет ручек</option>
<?
$get_colors = mysql_query("SELECT * FROM colours WHERE cid <> '0' ORDER BY seq DESC");
while($gg =  mysql_fetch_array($get_colors)){?>
<option value="<?=$gg["cid"];?>"><?=$gg["colour"];?></option>
<?}?>
</select>
</span>


<span id="ord_dat_span">
<select id="month_num" name="month_num" style="width:100px" onchange="get_app_list('');">
<option value="">месяц</option>
<option value="01">январь</option>
<option value="02">февраль</option>
<option value="03">март</option>
<option value="04">апрель</option>
<option value="05">май</option>
<option value="06">июнь</option>
<option value="07">июль</option>
<option value="08">август</option>
<option value="09">сентябрь</option>
<option value="10">октябрь</option>
<option value="11">ноябрь</option>
<option value="12">декабрь</option>
</select>

<select id="year_num" name="year_num" style="width:70px" onchange="get_app_list('');">
<option value="">год</option>
<option value="2010">2010</option>
<option value="2011">2011</option>
<option value="2012">2012</option>
<option value="2013">2013</option>
<option value="2014">2014</option>
<option value="2015">2015</option>
<option value="2016">2016</option>
<option value="2017">2017</option>
<option value="2018">2018</option>
<option value="2019">2019</option>
<option value="2020">2020</option>
<option value="2021">2021</option>
<option value="2022">2022</option>
<option value="2023">2023</option>
<option value="2024">2024</option>
<option value="2025">2025</option>
</select>
</span>

<span id=user_id_span">
<select id="user_id" name="user_id" style="width:150px" onchange="get_app_list('');">
<option value="">Менеджер</option>
<?
$app_users = mysql_query("SELECT user_id FROM applications GROUP BY user_id");

while($au =  mysql_fetch_array($app_users)){
          $ids .= $au[0].",";

}
$ids = rtrim($ids, ',');

$where_in = "uid IN ($ids)";
$users = mysql_query("SELECT uid, surname, name FROM users WHERE $where_in ORDER BY archive ASC, surname ASC");
while($gg =  mysql_fetch_array($users)){
?>
<option value="<?=$gg["uid"];?>"><?=$gg["surname"]." ".$gg["name"];?></option>
<?}?>
</select>
 <?//echo "SELECT uid, surname, name FROM users WHERE $where_in ORDER BY surname ASC"; ?>
</span>
<br>
<span id="workout_filters_span">
показать только те заявки, где:

<select id="compar_job" name="compar_job" style="width:130px" onchange="jump('compar_job','1','compar');workout_filters();">
<option value="1">ламинация</option>
<option value="2">вырубка</option>
<option value="3">тиснение</option>
<option value="4">сборка</option>
<option value="11">упаковка</option>
</select>
выполнено
<select id="compar" name="compar" style="width:130px" onchange="jump('compar','1','compar_qty');workout_filters();">
<option value="more">больше</option>
<option value="less">меньше</option>
<option value="equal">равно</option>
</select>
чем на
<input type="text" value="" style="width:50px;" id="compar_perc" name="compar_perc" onchange="this.value=replace_num(this.value);workout_filters();" size=3 /> %

<br>
<span onclick="sklad_art_filter(0)" style="cursor:pointer;">склад равен 0</span> | <span onclick="sklad_art_filter(1)">склад равен 0 + листы вырублены</span>

&nbsp; <span style="display:none;color:red;font-weight:bold;cursor:pointer;" id="sbros_filters" onclick="show_filtered_apps();">x</span>



</span>

выборка из последних
<select id="limit" name="limit" style="width:130px" onchange="get_app_list('');">
<option value="100">100</option>
<option value="250" selected="selected">250</option>
<option value="500">500</option>
<option value="1000">1000</option>
<option value="2000">2000</option>
</select>
заявок



<br>
<span class="colors__block" style="display: inline-block; vertical-align: middle; border: .0625rem solid #f0f0f0; background: #66CC00; width: 1.25rem; height: 1.25rem;"></span> - этап выполняется корректно
<span class="colors__block" style="display: inline-block; vertical-align: middle; border: .0625rem solid #f0f0f0; background: #FF3300; width: 1.25rem; height: 1.25rem;"></span> - сделано больше чем необходимо
<span class="colors__block" style="display: inline-block; vertical-align: middle; border: .0625rem solid #f0f0f0; background: #FFFF66; width: 1.25rem; height: 1.25rem;"></span> - сделано больше, чем в предыдущем этапе
</span>
<input type="hidden" id=search name=search value="<?=$search?>"/>
<input type="hidden" id=tpacc name=tpacc value="<?=$tpacc?>"/>

</form>

<div style="z-index: 100; position: fixed;margin-left:30%;top:0px;  border-width: 1px; border-color: grey; border-style: solid; padding: 10px; background-color: white; border-radius: 5px; width: 500px; display:none;" id="col_checker">
<div style="cursor:move">Убрать галочки у тех колонок, которые не хотите видеть:</div><br>
<form action="" id="cols_vis_form" name="cols_vis_form">
<input type="checkbox" value="col_app_type" id="col_app_type_chk" class="col_vis_chk" /><label for="col_app_type_chk" style="white-space: nowrap;cursor:pointer"> тип заявки</label><br>
<input type="checkbox" value="col_num_ord" id="col_num_ord_chk" class="col_vis_chk" /><label for="col_num_ord_chk" style="white-space: nowrap;cursor:pointer"> номер заявки</label><br>
<input type="checkbox" value="col_tiraz" id="col_tiraz_chk" class="col_vis_chk" /><label for="col_tiraz_chk" style="white-space: nowrap;cursor:pointer"> тираж</label><br>
<input type="checkbox" value="col_art_id" id="col_art_id_chk" class="col_vis_chk" /><label for="col_art_id_chk" style="white-space: nowrap;cursor:pointer"> артикул / клиент</label><br>

<input type="checkbox" value="col_izd_type" id="col_izd_type_chk" class="col_vis_chk" /><label for="col_izd_type_chk" style="white-space: nowrap;cursor:pointer"> тип изделия</label><br>
<input type="checkbox" value="col_izd_color" id="col_izd_color_chk" class="col_vis_chk" /><label for="col_izd_color_chk" style="white-space: nowrap;cursor:pointer"> цвет изделия</label><br>
<input type="checkbox" value="col_izd_cinn" id="col_izd_cinn_chk" class="col_vis_chk" /><label for="col_izd_cinn_chk" style="white-space: nowrap;cursor:pointer"> цвет изделия внутри</label><br>
<input type="checkbox" value="col_size" id="col_size_chk" class="col_vis_chk" /><label for="col_size_chk" style="white-space: nowrap;cursor:pointer"> размер изделия</label><br>
<input type="checkbox" value="col_izd_material" id="col_izd_material_chk" class="col_vis_chk" /><label for="col_izd_material_chk" style="white-space: nowrap;cursor:pointer"> материал</label><br>
<input type="checkbox" value="col_material_qty" id="col_material_qty_chk" class="col_vis_chk" /><label for="col_material_qty_chk" style="white-space: nowrap;cursor:pointer"> листов (материал)</label><br>
<input type="checkbox" value="col_material_size" id="col_material_size_chk" class="col_vis_chk" /><label for="col_material_size_chk" style="white-space: nowrap;cursor:pointer"> формат материала</label><br>
<input type="checkbox" value="col_material_w" id="col_material_w_chk" class="col_vis_chk" /><label for="col_material_w_chk" style="white-space: nowrap;cursor:pointer"> вес материала</label><br>

<input type="checkbox" value="col_material_postavka" id="col_material_postavka_chk" class="col_vis_chk" /><label for="col_material_postavka_chk" style="white-space: nowrap;cursor:pointer"> поставка материала</label><br>

<input type="checkbox" value="col_izd_lami" id="col_izd_lami_chk" class="col_vis_chk" /><label for="col_izd_lami_chk" style="white-space: nowrap;cursor:pointer"> ламинация</label><br>
<input type="checkbox" value="col_izd_ruchki" id="col_izd_ruchki_chk" class="col_vis_chk" /><label for="col_izd_ruchki_chk" style="white-space: nowrap;cursor:pointer"> ручки</label><br>
<input type="checkbox" value="col_hand_length" id="col_hand_length_chk" class="col_vis_chk" /><label for="col_hand_length_chk" style="white-space: nowrap;cursor:pointer"> длина ручек</label><br>
<input type="checkbox" value="col_hand_thick" id="col_hand_thick_chk" class="col_vis_chk" /><label for="col_hand_thick_chk" style="white-space: nowrap;cursor:pointer"> толщина ручек</label><br>
<input type="checkbox" value="col_hand_color" id="col_hand_color_chk" class="col_vis_chk" /><label for="col_hand_color_chk" style="white-space: nowrap;cursor:pointer"> цвет ручек</label><br>

<input type="checkbox" value="col_izdlami_status" id="col_izdlami_status_chk" class="col_vis_chk" /><label for="col_izdlami_status_chk" style="white-space: nowrap;cursor:pointer"> ламинация (статус)</label><br>
<input type="checkbox" value="col_virub_status" id="col_virub_status_chk" class="col_vis_chk" /><label for="col_virub_status_chk" style="white-space: nowrap;cursor:pointer"> вырубка (статус)</label><br>
<input type="checkbox" value="col_tisnenie_status" id="col_tisnenie_status_chk" class="col_vis_chk" /><label for="col_tisnenie_status_chk" style="white-space: nowrap;cursor:pointer"> тиснение (статус)</label><br>
<input type="checkbox" value="col_shelko_status" id="col_shelko_status_chk" class="col_vis_chk" /><label for="col_shelko_status_chk" style="white-space: nowrap;cursor:pointer"> шелкография (статус)</label><br>
<input type="checkbox" value="col_sborka_status" id="col_sborka_status_chk" class="col_vis_chk" /><label for="col_sborka_status_chk" style="white-space: nowrap;cursor:pointer"> сборка (статус)</label><br>
<input type="checkbox" value="col_upakovka_status" id="col_upakovka_status_chk" class="col_vis_chk" /><label for="col_upakovka_status_chk" style="white-space: nowrap;cursor:pointer"> упаковка (статус)</label><br>

<input type="checkbox" value="col_user" id="col_user_chk" class="col_vis_chk" /><label for="col_user_chk" style="white-space: nowrap;cursor:pointer"> менеджер</label><br>
<input type="checkbox" value="col_deadline" id="col_deadline_chk" class="col_vis_chk" /><label for="col_deadline_chk" style="white-space: nowrap;cursor:pointer"> дедлайн</label><br>
<input type="checkbox" value="col_plan_in" id="col_plan_in_chk" class="col_vis_chk" /><label for="col_plan_in_chk" style="white-space: nowrap;cursor:pointer"> план</label><br>
<input type="checkbox" value="col_archive" id="col_archive_chk" class="col_vis_chk" /><label for="col_archive_chk" style="white-space: nowrap;cursor:pointer"> архив</label><br>
<input type="checkbox" value="col_buttons" id="col_buttons_chk" class="col_vis_chk" /><label for="col_buttons_chk" style="white-space: nowrap;cursor:pointer"> кнопки</label><br>


<input type="button" value="Закрыть!" class="btn_big" onclick="fill_cols_chk('close')"/>
</form>
</div>
<br>
<div id="app_list_div" style="display: table-cell; horizontal-align: center;"></div>

</td></tr>
</table>




<script>

$("#izd_w").focus();
get_app_list('')

function show_query(){
  $("#app_list_q").toggle();
}
$(document).on("click","#filter_status",function(e){
	if ($(".filter__options").css("display")=="none"){
		$(".filter__button_arrow").hide();
		$(".filter__button_reset").show();
	}else{
		$(".filter__button_arrow").show();
		$(".filter__button_reset").hide();
	}
	$(".filter__options").toggle();
});
$(document).on("click",".filter__options div",function(e){
	//console.log("1");
	//при клике проверяем выбран ли этот элемент в select 
	//если да - отмена
	//если нет - выбираем
	var value_new=$(this).data('value');
	console.log($("#app_status option[value="+value_new+"]").prop('selected'));
	if ($("#app_status option[value="+value_new+"]").prop('selected')==false){
		//новый
		//меняем цвет
		$(this).addClass("active");
		$("#app_status option[value="+value_new+"]").prop('selected', true);
	}else{
		//повторный клик
		$(this).removeClass("active");
		$("#app_status option[value="+value_new+"]").prop('selected', false);
	}
	
	get_app_list_new(value_new);
	
});
 </script>
 <?}?>


</body>
</html>
<? ob_end_flush(); ?>
