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
// ----- перейти на главную если доступ запрещен ---------
if(!$auth) {
	header("Location: /");
	exit;
}





$tpus = $user_type;		// тип пользователя

// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;

  $uid = $_GET["uid"];

//получаем название клиента
  $zakaz_id = $_GET["zakaz_id"];
  if(is_numeric($zakaz_id)){
  $res1 = mysql_fetch_array(mysql_query("SELECT client_id FROM queries WHERE uid='$zakaz_id'"));
  $client_id =  $res1["client_id"];
  $res2 = mysql_fetch_array(mysql_query("SELECT short FROM clients WHERE uid='$client_id'"));
  $client_name = $res2["short"];
  }
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.2</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<link href="apps.css" rel="stylesheet" type="text/css" />


</head>

<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
<script src="../includes/js/jquery.ui.datepicker-courier-ru.js"></script>
<script>
var user_type = '<?=$user_type;?>';
var user_id = '<?=$user_id;?>';

</script>
<link rel="stylesheet" href="../includes/js/jquery-ui.css">
<body>
  <span id="all">
<? require_once("../templates/top.php"); ?>
<table align="center" width="100%" border="0">
  <tr>
    <td>
      <?$name_curr_page = 'apl_list';
      require_once("../templates/main_menu.php");?>
      <table width=80% align=center border="0" cellpadding="0" cellspacing="0" bgcolor="#F6F6F6">
      	<tr>
      		<td valign="top" align="center">
      			<table width="90%" border=0 cellspacing="0" cellpadding="0">
      				<tr id="top_menu">
					<td width="300"><a href="index.php" class="sublink"><img src="../../i/manufacture.png" width="24" height="24" alt=""  style="vertical-align: middle;"/></a>
<a href="index.php" class="sublink">Список заявок</a></td>
      					<td width="300" align=center>
<a href="edit.php" class="sublink"><img src="../../i/manufacture.png" width="24" height="24" alt=""  style="vertical-align: middle;"/></a> <a href="edit.php" class="sublink">Добавить заявку</a>
</td>
</tr>
<tr><td colspan=2 class=inputs>
<form id=forma action="" method="post">
<span id="everything">
<span id="user_id_span">
Инициатор заявки:<br>
<?$res = mysql_query("SELECT uid,surname,name FROM users WHERE (type='mng' OR type='sup' OR type='adm' OR type='meg' OR type = 'acc') AND archive <> '1' ORDER BY surname");?>
<select <?=(($tpacc)?'':'disabled="disabled"')?> style="width:180px" name="user_id" id="user_id" size="1">
<?while($r = mysql_fetch_array($res)){
if($user_id == $r['uid'])
{$sel = 'selected="selected"';}else{$sel = "";};
?>
<option value="<?=$r['uid']?>" <?=$sel;?>><?=$r['name']?> <?=$r['surname']?></option>
<?}?>
</select></span>

<span id="app_type_span">
Тип заявки:<br>
<select id=app_type name=app_type style="width:180px" onchange="search_similar_art();show_hide_app_type_flds()" required>
<option value="">не выбран</option>
<option value="1">заказная продукция</option>
<option value="2">серийная продукция</option>
<option value="3" disabled="disabled">заказ у внешнего поставщика</option>
</select>
</span>

<span id="num_ord_span">
Номер заявки:<br>
<input type="text" disabled  style="width:100px" id=num_ord name=num_ord />
<input type="hidden" value="<?=$uid;?>" id=uid name=uid />
</span>
<span style="vertical-align:middle" id="plan_span"><input type="checkbox" value="1" name="plan_in" id="plan_in"/> <label for="plan_in" style="cursor:pointer">в план!</label></span>


<div id=art_id_span>Артикул: <input type="text" id="art_id" name=art_id size=5  style="width:100px" onchange="get_art_info('get_data');get_art_info('check')" required/>
<span id=art_link></span>
<span id="new_art_but">
<?if(!$uid){?><input type="checkbox" id=art_id_new onchange="new_art_form();jump('art_id_new','1','izd_type');search_similar_art()"/> <label for="art_id_new" style="cursor:pointer">новый</label><?}?>
</span>
<span id=art_id_span_al class="span_al"></span>
<input type="hidden" id="art_uid" name="art_uid" value="" />
<span id=art_check></span>
</div>

<span id=old_title style="display:block"></span>

<div id=ClientName_span>Название клиента (или проекта): <input type="text" id="ClientName" name=ClientName size=5 value="<?=$client_name;?>"  style="width:350px"/ required> <a href="/acc/query/query_send.php?show=<?=$zakaz_id;?>" target="_blank"><img src="../i/lupa.gif" width="20" height="20" alt=""></a>
<input type="hidden" id="zakaz_id" name=zakaz_id value="<?=$zakaz_id;?>"/>
</div>



<div id="izd_type_span">
Тип продукции:
<select id="izd_type" name=izd_type style="width:250px" onchange="jump('izd_type','1','tiraz');search_similar_art();hide_izd_type_flds();" required >
<?$get_types = mysql_query("SELECT * FROM types ORDER BY seq DESC");
while($gg =  mysql_fetch_array($get_types)){
$id = $gg["tid"];
if($id == "0"){$id = "";}
?>
<option value="<?=$id;?>"><?=$gg["type"];?></option>
<?}?>
</select>
<a href="http://www.paketoff.ru/admin/shop/types/" target="_blank"><img src="../../i/sprav.png" width="16" height="16" alt=""></a>
<input type="checkbox" name="vip" id="vip" value="1"> <label for="vip">VIP</label>
</div>

<div id="tiraz_span">
Тираж:
<input type="text" id="tiraz" name="tiraz" onkeyup="this.value=replace_num(this.value);" style="width:100px" required/> <span style="opacity:0.7;font-size:12px;"> перекат max:<input type="text" id="limit_per" name="limit_per" onkeyup="this.value=replace_num(this.value);" style="width:35px"/>% от тиража</span><br>
</div>

<div id=size_span style="line-height: 25px;">
Размер: <span id="jump_span"><input type="checkbox" id=jumpoff /> <label for="jumpoff" style="font-size:7px; cursor:pointer;">отключить прыжки</label></span>
Ш: <input type="text" style="width:100px;" id="izd_w" name="izd_w" onkeyup="jump('izd_w','2','izd_v');this.value=replace_num(this.value);" onchange="search_similar_art()" size=3 required/>
<span id="izd_v_span">В: <input type="text" style="width:100px;" id="izd_v" name="izd_v" onkeyup="jump('izd_v','2','izd_b');this.value=replace_num(this.value);" onchange="search_similar_art()" size=3 required/></span>
<span id="izd_b_span">Б: <input type="text" style="width:100px;" id="izd_b" name="izd_b" onkeyup="jump('izd_b','2','izd_material');this.value=replace_num(this.value);" onchange="search_similar_art()" size=3 required/></span>

<span style="opacity:0.7;font-size:12px;padding-left:100px;" id="podvorot_klapan_span">
подворот (см): <input type="text" style="width:35px" id="podvorot" name="podvorot" onkeyup="this.value=replace_num(this.value);" value="5" size=3/>
клапан (мм): <input type="text" style="width:35px" id="klapan" name="klapan" onkeyup="this.value=replace_num(this.value);" value="20" size=3/>
<br>
<span style="font-size:12px;"><span style="font-style: italic;display:none;" id=razvorot>примерный разворот - </span>
<span id="razvorot_cely"></span>
<span id="razvorot_half"></span></span>
</span>
</div>

<div id="izd_material_span">
Материал: 
<select id="izd_material" name="izd_material" style="width:250px" onchange="jump('izd_material','1','izd_gramm');search_similar_art()" required>
<option value="">не выбран</option>
<?
$materials = mysql_query("SELECT * FROM materials ORDER BY seq DESC");
while($r =  mysql_fetch_array($materials)){
$id = $gg["tid"];
if($id == "0"){$id = "";}
?>
<option value="<?=$r["tid"];?>"><?=$r["type"];?></option>
<?}?>
</select>
<a href="http://www.paketoff.ru/admin/shop/material/" target="_blank"><img src="../../i/sprav.png" width="16" height="16" alt=""></a>

<span id="izd_gramm_span">
грамм: <input type="text" style="width:50px" id="izd_gramm" name="izd_gramm" maxlength=4 onkeyup="jump('izd_gramm','3','paper_suppl');this.value=replace_num(this.value);" required/></span>
<br>
комментарий:
<input type="text" style="width:180px;height:30px;font-size:10px;" id="material_comment" name="material_comment" />

поставщик:
<?//выбираем из таблицы всех поставщиков бумаги (в будущем будет не только бумага но и дерево и нити)
$get_vendors_type = mysql_query("SELECT id FROM vendor_gid WHERE gid = '1'");
while($vv =  mysql_fetch_array($get_vendors_type)){
$vendor_ids .= "'".$vv[id]."',";
}
//удаляем запятую в конце
$vendor_ids = chop($vendor_ids, ',');
 ?>
<select id="material_suppl" name="material_suppl" style="width:150px" onchange="jump('material_suppl','1','izd_lami')">
<option value="">не выбран</option>
<?$get_vendors = mysql_query("SELECT * FROM vendors WHERE id IN ($vendor_ids)");
while($pp =  mysql_fetch_array($get_vendors)){
$id = $pp["id"];?>
<option value="<?=$id;?>"><?=$pp["name"];?></option>
<?}?>
</select>
<a href="/acc/sprav/vendors/?part=vendors" target="_blank"><img src="../../i/sprav.png" width="16" height="16" alt=""></a>
</div>

<div id="izd_lami_span">
Ламинация:
<select id="izd_lami" name="izd_lami" style="width:250px" onchange="search_similar_art();jump('izd_lami','1','izd_color')" required>
<option value="">не выбран</option>
<?
$get = mysql_query("SELECT * FROM lamination ORDER BY id ASC");
while($gg =  mysql_fetch_array($get)){
$id = $gg["id"];?>
<option value="<?=$id;?>"><?=$gg["name"];?></option>
<?$lami_arr .= "lami_arr[".$gg["id"]."] = ".$gg["cost"]."\n";}?>
</select> <input type="checkbox" name="izd_lami_storon" id="izd_lami_storon" value="1"> <label for="izd_lami_storon">ламинация на стороне подрядчика</label>
</div>

<div id="tisnenie_span">
<span>
Тиснение:
<select id="tisnenie" name="tisnenie" style="width:250px" onchange="jump('tisnenie','1','col_ottiskov_izd');">
<option value="">без тиснения</option>
<option value="2">1+1</option>
<option value="4">2+2</option>
<option value="6">3+3</option>
<option value="1">1+0</option>
<option value="2">2+0</option>
<option value="3">3+0</option>
</select></span>
<span id="tisnenie_dop_polya">
оттисков на 1 изд.: <input type="text" id="col_ottiskov_izd" name="col_ottiskov_izd"  style="width:50px"/>
коммент:
<input type="text" style="width:200px;height:30px;font-size:10px;" id="tisn_comment" name="tisn_comment"/></span>
</div>

<div id="paper_col_ext_span">
Цвет изделия:
<select id="izd_color" name="izd_color" style="width:150px" onchange="jump('izd_color','1','izd_color_inn');search_similar_art();hlp('same','izd_color','izd_color_inn')">
<option value="">не выбран</option>
<?
$get_colors = mysql_query("SELECT * FROM colours WHERE cid <> '0' ORDER BY seq DESC");
while($gg =  mysql_fetch_array($get_colors)){
if($gg["cid"] !== "0")?>
<option value="<?=$gg["cid"];?>"><?=$gg["colour"];?></option>
<?}?>
</select>

<span id="color_inn_span">
<span id="hlp_izd_color_span">
внутри: <span onclick="hlp('same','izd_color','izd_color_inn')" style="font-size:8px; cursor:pointer;">такой же</span> |
<span onclick="hlp('15','izd_color','izd_color_inn')" style="font-size:8px; cursor:pointer;">белый</span> |
<span onclick="hlp('23','izd_color','izd_color_inn')" style="font-size:8px; cursor:pointer;">коричневый</span>
<b>&rsaquo;</b></span>
<select id="izd_color_inn" name="izd_color_inn" style="width:150px">
<option value="">не выбран</option>
<?
$get_colors1 = mysql_query("SELECT * FROM colours WHERE cid <> '0' ORDER BY seq DESC");
while($gg1 =  mysql_fetch_array($get_colors1)){
if($gg1["cid"] !== "0")?>
<option value="<?=$gg1["cid"];?>"><?=$gg1["colour"];?></option>
<?}?>
</select>
<a href="http://www.paketoff.ru/admin/shop/colours/" target="_blank"><img src="../../i/sprav.png" width="16" height="16" alt=""></a>
</span>
</div>


<div id="paper_num_list_span">
Из скольких листов собирается:
<select id="paper_num_list" name="paper_num_list" style="width:250px" required>
<option value="">выбрать</option>
<option value="1">из одного</option>
<option value="2">из двух</option>
</select>
<span id=paper_list_typ_span>
листы:
<select id="paper_list_typ" name="paper_list_typ" style="width:250px">
<option value="1">одинаковые</option>
<option value="2">разные</option>
</select></span>
</div>

<div id="luve_span">
Люверсы:
<select id="luve" name="luve" style="width:250px" onchange="jump('luve','1','izd_ruchki');">
<option value="">без люверсов</option>
<option value="1">серебро</option>
<option value="2">золото</option>
<option value="3">черные</option>
<option value="4">красные</option>
<option value="5">синие</option>
<option value="6">другие (укажите в коментариях)</option>
</select>
</div>


<div id="izd_ruchki_span" style="padding-top:10px;">
<span>
Ручки:
<select id="izd_ruchki" name="izd_ruchki" style="width:150px" onchange="jump('izd_ruchki','1','hand_length');" required>
<option value="">выбрать</option>
<?
$get = mysql_query("SELECT * FROM ruchki");
while($gg =  mysql_fetch_array($get)){?>
<option value="<?=$gg["tid"];?>"><?=$gg["type"];?></option>
<?}?>
</select></span>


<span id="ruchki_dop_polya">
Длина 1 ручки (вкл. узел):
<input type="text" style="width:50px" value="40" onkeyup="this.value=replace_num(this.value);" id="hand_length" name="hand_length" maxlength=4 required/>
Толщина шнура (ленты) мм:
<input type="text" style="width:50px" onkeyup="this.value=replace_num(this.value);" id="hand_thick" name="hand_thick" maxlength=4 required/>
<br>
Цвет ручек:
<span id="hlp_hand_color_span">
<span onclick="hlp('same','izd_color','hand_color')" style="font-size:8px; cursor:pointer;">как пакет</span> |
<span onclick="hlp('15','','hand_color')" style="font-size:8px; cursor:pointer;">белый</span> |
<span onclick="hlp('16','','hand_color')" style="font-size:8px; cursor:pointer;">черный</span> |
<span onclick="hlp('23','','hand_color')" style="font-size:8px; cursor:pointer;">коричневый</span> |
<span onclick="hlp('18','','hand_color')" style="font-size:8px; cursor:pointer;">синий</span>
<b>&rsaquo;</b></span>
<select id="hand_color" name="hand_color" style="width:150px" onchange="jump('hand_color','','gluing_material');" required>
<option value="">не выбран</option>
<?
$get_colors = mysql_query("SELECT * FROM colours WHERE cid <> '0' ORDER BY seq DESC");
while($gg =  mysql_fetch_array($get_colors)){?>
<option value="<?=$gg["cid"];?>"><?=$gg["colour"];?></option>
<?}?>
</select>
<br>
Крепление ручек:
<select id="hands_krepl" name="hands_krepl" style="width:250px" onchange="jump('hands_krepl','','hand_color');" required>
<option value="">выбрать</option>
<?$get = mysql_query("SELECT * FROM hands_krepl ORDER BY seq DESC");
while($gg =  mysql_fetch_array($get)){?>
<option value="<?=$gg["id"];?>"><?=$gg["name"];?></option>
<?}?>
</select>

</span>
</div>

<div id="strengt_bot_span">
Укрепление пакета:
<input type="checkbox" id="strengt_bot" name="strengt_bot" value="1" checked="checked"> <label for="strengt_bot" style="cursor:pointer;">дно</label>
<input type="checkbox" id="strengt_side" name="strengt_side" value="1" checked="checked"> <label for="strengt_side" style="cursor:pointer;">бок</label>
</div>
<span id="gluing_material_span">
Клеим на: <select id="gluing_material" name="gluing_material" style="width:250px" onchange="jump('gluing_material','1','pack');" required>
<option value="">не выбран</option>
<?$get = mysql_query("SELECT * FROM gluing_materials ORDER BY seq DESC");
while($gg =  mysql_fetch_array($get)){?>
<option value="<?=$gg["id"];?>"><?=$gg["name"];?></option>
<?$glue_arr .= "glue_arr[".$gg["id"]."] = ".$gg["cost"]."\n";}?>
</select>
</span>
<br>
<span id="pack_span">
Упаковка: <select id="pack" name="pack" style="width:250px" onchange="jump('pack','1','col_in_pack');">
<?$get = mysql_query("SELECT * FROM upakovka ORDER BY seq DESC");
while($gg =  mysql_fetch_array($get)){?>
<option value="<?=$gg["id"];?>"><?=$gg["name"];?></option>
<?}?>
</select> по <input type="text" style="width:50px"  onkeyup="this.value=replace_num(this.value);" value="25" id="col_in_pack" name="col_in_pack" maxlength=4/> шт
</span>

<span id="deadline_span" style="padding-left:20px;">
Дедлайн: <input type="text" style="width:150px" value="" id="deadline" name="deadline" onchange="jump('deadline','1','spec_req');"/>
</span>

<div id="spec_req_span">
Комментарии:<br>
<textarea id="spec_req" name="spec_req" style="width:800px;height:50px;font-size:10px;"></textarea>
<br></div>

<br><br>
<div id=add_art_add_flds style="border: 1px #3399CC solid; border-radius: 5px; width:1000px;">
<b>Форма добавления на сайт:</b><br>
Отпускная цена: <input type="text" style="width:80px"  onkeyup="this.value=replace_num(this.value);" value="" id="price" name="price" maxlength=8 required/>
Себестоимость: <input type="text" style="width:80px"  onkeyup="this.value=replace_num(this.value);" value="" id="price_our" name="price_our" maxlength=8 required/><br>

<input type="checkbox" value="1" name="onn" id="onn" checked/> <label for="onn">отображать на сайте</label>
<input type="checkbox" name="print" id="print" value="type2"  checked> <label for="print">шелкография</label>
<input type="checkbox" name="show_when_zero" id="show_when_zero" value="1" > <label for="show_when_zero">отображать если 0</label>

<br>


<span id="additional_flds"></span>

<br>
Примечание к товару: <input style="width:450px" name="primechanie" id="primechanie" type="text" value="" maxlength="255">
<br>

<input onclick="add_art_site();" type="button" id="add_art_site_but" value="Добавить артикул на сайт!" style="width: 400px; cursor:pointer; height: 45px; font-size: 25px;">
<input type="button" value="Показать похожие" onclick="search_similar_art('1')" style="width: 300px; left:10px; cursor:pointer; height: 45px; font-size: 25px;"/>
<span id="new_art_span"></span>
</div>



<div id="tech_param_span" style="border: 1px dotted; width: 800px">

<b>Технические параметры изделия:</b><br>
Формат листа, используемого для изготовления изделия:<br>
<input type=text id="list_h" name="list_h" value="" style="width:50px" required> x <input type=text id="list_w" name="list_w" value="" style="width:50px" required>
см вес 1 листа: <input type=text id="one_list_weight" value="" disabled style="width:100px">

<span style="font-size:12px;"><span style="font-style: italic;display:none;" id=razvorot_1>примерный разворот - </span>
<span id="razvorot_cely_1"></span>
<span id="razvorot_half_1"></span></span>
</span>

<br>

Сколько изделий получается из 1 листа:
<select id="isdely_per_list" name="isdely_per_list" style="width:100px" required>
<option value="">выбрать</option>
<option value="0.5">0.5</option>
<?
$i = 1;
while ($i < 100) {
?>
<option value="<?=$i;?>"><?=$i;?></option>
<?$i = $i + 1;}?>
</select>
<br>
всего необходимо листов: <input type=text id="list_total" value="" disabled style="width:100px"> шт

общий вес материала: <input type=text id="list_weight" value="" disabled style="width:100px"> кг

<br>
Сколько изделий ламинируется за 1 прогон:
<select id="lami_isdely_per_list" name="lami_isdely_per_list" style="width:100px" required>
<option value="">выбрать</option>
<option value="0.5">0.5</option>
<?
$i = 1;
while ($i < 100) {
?>
<option value="<?=$i;?>"><?=$i;?></option>
<?$i = $i + 1;}?>
</select> всего прогонов: <input type=text id="lamin_total" value="" disabled style="width:100px">

<br>

Сколько изделий вырубается за 1 удар:
<select id="virub_isdely_per_list" name="virub_isdely_per_list" style="width:100px" required>
<option value="">выбрать</option>
<option value="0.5">0.5</option>
<?
$i = 1;
while ($i < 100) {
?>
<option value="<?=$i;?>"><?=$i;?></option>
<?$i = $i + 1;}?>
</select> всего ударов: <input type=text id="virub_total" value="" disabled style="width:100px">
</div>

<?if($user_type == 'sup'){?>
<span id="ss_span" style="border: 1px dotted; width: 800px">
<b>С/с:</b>
<br>
Цена материала: <input type=text id="material_cost" name="material_cost" style="width:100px"  onkeyup="this.value=replace_zap(this.value);" required>

<select id="material_cost_type" name="material_cost_type" style="width:100px">
<option value="" selected="selected">выбрать</option>
<option value="per_list">за 1 лист</option>
<option value="per_tonn">за тонну</option>
</select> <span class="span_tip">может включать в себя печать</span>
<br>
Цена за материал на 1 изделие: <input type=text id="price_per_list" disabled style="width:100px"><br>
Стоимость печати на 1 изделие: <input type=text id="price_per_print" name="price_per_print" onkeyup="this.value=replace_zap(this.value);" style="width:100px">  <span class="span_tip">может включать в себя материал</span> <br>
Стоимость пленки для ламинирования (м2): <input type=text id="price_per_lami_film" disabled style="width:100px">
за 1 изделие включая работу: <input type=text id="price_per_lami" disabled style="width:100px"><br>
Стоимость вырубки (включая приладку): <input type=text id="price_per_virub" disabled style="width:100px"><br>
Стоимость сборки: <input type=text id="sborka_cost_oznak" disabled style="width:100px"><br>
Клеевой материал объем на изд: <input type=text id="gluing_material_per_izd" disabled style="width:100px"> цена на изд: <input type=text id="price_gluing_material_per_izd" disabled style="width:100px"><br>
Стоимость ручек на изделие: <input type=text id="price_per_ruchki" name="price_per_ruchki" onkeyup="this.value=replace_zap(this.value);" style="width:100px" value="3"> <span class="span_tip">вносится вручную исходи из текущих цен</span><br>
Организационные расходы: <input type=text id="orgrashodi_cost" name="orgrashodi_cost" value="1"  onkeyup="this.value=replace_zap(this.value);" style="width:100px"> <span class="span_tip">административные, складские и транспортные расходы</span><br>
Доп работы: <input type=text id="dopraboty_cost" value="" name="dopraboty_cost" onkeyup="this.value=replace_zap(this.value);" style="width:100px"> <span class="span_tip">упаковка, нарезка дна и боковин, вставка ручек, сверление</span><br>
Примерная с/с изд: <input type=text id="r_price_our" name="r_price_our" style="background-color:#DDDDDD;width:100px" value=""> <span id="ss_site"></span>
<input type="button" value="сверить с/с с сайтом >>>" style="height:30px" onclick="get_art_info('compare_ss')"  id="compare_ss_but"/>
<span id="compare_ss_span" class="span_tip"></span><br>
<input onclick="get_art_info('compare_flds')" type="button" value="сверить все поля с сайтом >>>" id="compare_flds_but"  style="height:30px" />
<br>
</span>
<?}?>




<table style="border: 1px dotted; width: 800px" id="job_rate_box"><tr><td>
<?
$job_types = mysql_query("SELECT * FROM job_types ORDER BY seq ASC");
while($jt = mysql_fetch_array($job_types)){
$jt_id=$jt["id"];
?><span style="border-bottom: 2px dotted; font-size: 30px;cursor:pointer" onclick="show_tarif('<?=$jt_id;?>')"><?=$jt["name"];?></span><br>
<table width="450" border="1" cellpadding="1" cellspacing="1" id="<?=$jt_id;?>" style="display:none"><?
$job_names = mysql_query("SELECT * FROM job_names WHERE job_type = '$jt_id' ORDER BY seq ASC");
while($jn = mysql_fetch_array($job_names)){
$nums_jobs = $nums_jobs.";".$jn["id"];
$rate_id = "rate_".$jn["id"];
?>
<tr>
<td><?=$jn["name"];?></td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);"  name="<?=$rate_id;?>" id="<?=$rate_id;?>" type="text" size="8" value="<?=$jn["price"];?>" /></td>
</tr>
<?}?></td></tr>
</table>
<?}?>


</td></tr></table>


<br>


<span style="font-size:12px; text-decoration: underline; cursor:pointer" id="close_print_span" onclick="close_print()">закрыть вид для печати</span>

<input onclick="save_app();" type="button" id="save_but" value="Сохранить!" style="width: 400px; cursor:pointer; height: 70px; font-size: 30px;">

<?if(is_numeric($uid)){?>
<input onclick="print_app();" type="button" id="print_but" value="Распечатать!" style="width: 300px; cursor:pointer; height: 70px; font-size: 30px;">
<?}?>




</span>
</form>

</td></tr></table>
</td></tr></table>




<div id="similar_arts_div" style="position:fixed;top:20px;right:20px;width:500px;height:250px; background-color: white;display:none;"></div>
<input type="hidden" id="similar_arts_never_show" value="0" />
<div id="debug"></div>

<script src="../includes/application_edit.js"></script>


<script>
var lami_arr = new Array();
<?echo $lami_arr;?>
var glue_arr = new Array();
<?echo $glue_arr;?>

<?if(is_numeric($uid)){?>
//если указан Ид то делаем запрос и получаем данные заявки в массив
get_app_data(<?=$uid?>);
<?}?>

<?if(is_numeric($zakaz_id)){?>
show_hide_app_type_flds('1')
$("#app_type").val(1);
$("#app_type").prop("disabled", true);
<?}else if(!is_numeric($uid)){?>
show_hide_app_type_flds('start')
//hide_izd_type_flds()
<?}if($user_type !== "sup"){?>

<?}?>
</script>

<span id=res></span>

</body>
</html>
<? ob_end_flush();?>