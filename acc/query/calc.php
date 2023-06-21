<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");


require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");
require_once("../includes/hint.inc.php");

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");
require_once("../includes/hint.inc.php");

if(!$auth) {
	header("Location: /");
	exit;
}

$pereadr = '';
?>
<!-- скотч

Считаем расход скотча

(высота пакета + бок * 0,7 + подворот) * количество листов пакета

программа сама устанавливает тариф на сборку, после указания пользователем всех размеров пакета, указания операций линии, а также определения количества листов, из которых собирается пакет. При необходимости пользователь может ввести свой тариф вручную.



<input type=radio id="" name="" value=""><label for=""></label>

пример выплывающего окна http://divosite.com/plavnoe-izmenenie-cveta-fona-ili-bloka-s-hover-effektom/


//картон укрепление

укрепление дно боковины

(площадь дна + площадь боковин) * граммаж * тираж = вес
вес * стоимость картона

запечатка дна считается по принципу самой дешевой А2 печати
количество прокаток = тираж (берем площадь А2 листа / площадь дна)
стоимость печати = приладка А2 + прокатка А2 ЦМИК * количество прокаток



+++ ручки вырубные

+++ дизайнерская бумага по листам



+++ стороны разные / одинаковые

- если стороны разные, то к текущей стоимости печати, добавляется еще одна приладка по текущей стоимости



+++ допприладка конгрев и тиснение каждые 10 000 ударов



//считаем стоимость сборки

умножаем выбранный тариф на тираж


//считаем допрасходы

= поле допрасходы

//считаем упаковку

выбранный вариант упаковки * тираж

//считаем стоимость штампа

= поле штамп (пока что!)

//транспортные расходы

= фикс 2 рейса + тираж / 1000 * транспрасход на 1000 пакетов

//накладные расходы

тираж * вознаграждение



 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.1</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" media="all" href="../includes/js/calendar/calendar-blue.css" title="Aqua" />
</head>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="http://jackrugile.com/jrumble/js/jquery.jrumble.1.3.min.js">


<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="../includes/lib/JsHttpRequest/JsHttpRequest.js"></script>
<script type="text/javascript" src="../includes/js/calendar/calendar.js"></script>
<script type="text/javascript" src="../includes/js/calendar/calendar-ru_win_.js"></script>
<script type="text/javascript" src="../includes/js/calendar/calendar-setup.js"></script>

<script language="JavaScript" type="text/javascript">
	var user_id	=	<?=$user_id?>;					// ид пользователя
	var tpacc		=	<?=$tpacc?>;				// тип пользователя
	var reqsl_user_id = <?=(($tpacc)?0:$user_id)?>;	// ид пользователя для списка клиентов
	var ed_us_id = <?=((@$ed_us_id)?$ed_us_id:$user_id)?>;	// ид пользователя запроса

	// новый запрос или редактирование
	var edit = <?=($op=='edit') ? $_GET['show'] : "'new'"?>;

	var curr_date = '<?=date("d.m.Y")?>';			// текущая дата в формате '01.05.2007'
	var user_full_name = '<?=$full_name?>';
	var	req_fl_hd = 0;

// форматирование строки, удаляет в строке начальные и конечные пробелы
function replace_str(v) {
	var reg_sp = /^\s*|\s*$/g;
	v = v.replace(reg_sp, "");
	return v;
}


//-->
</script>

<style>
.debug_res {
font-weight: bold;
}
.debug_res_vip {
font-weight: bold;
text-decoration: underline;
}
.bold {
font-weight: bold;
text-decoration: underline;
}
.highlight_stamp {
border:1px solid black;
}
.un_highlight_stamp {
border:1px solid #ECECEC;
}
.attention-listy {
border:2px solid #FF0000;
background-color: #FFFFCC;
}

</style>

<body>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>
<? require_once("../templates/top.php"); ?>
 <table align=center width=100% border=0>

<tr>
<td width="100%">
<br>
<?
$name_curr_page = 'calc';
require_once("../templates/main_menu.php");?>


<table width="970" cellpadding="1" cellspacing="5" align=center><tr><td>
    <form name=calc id=calc action=post>
<table width=970 cellpadding="5" border=0 cellspacing="10">
<tr>
<td>Тираж:</td>
<td><input onkeyup="this.value = replace_num(this.value, 'celoe');" type="text" name=tiraj id=tiraj  size=8 value="1000" />

<input type="checkbox" name="line" id="line" onclick="on_line()" onchange="sborka_raschet()"/>
<label for="line">на линии</label>
<span id=line_options style="opacity: 0.2;">
<input disabled onchange="sborka_raschet()" onclick="glue_set_up()" type="checkbox" name="line_truba" id="line_truba"/> <label for="line_truba">труба</label>
<input disabled onchange="sborka_raschet()" onclick="glue_set_up()" type="checkbox" name="line_dno" id="line_dno"/> <label for="line_dno">дно</label>
</span>
<script>

//удаление буков и запятых из полей в зависимости от типа поля
function replace_num(v,type) {

//если поле допускает дробные числа
if (type == "drob"){
v = v.replace(',', '.');
var reg_sp = /^\s*|\s*$/g;
var reg_sp1 = /[^.\d]*/g;
}
//если поле допускает только целые числа
if (type == "celoe"){
var reg_sp = /^\s*|\s*$/g;
var reg_sp1 = /[^\d]*/g;
}
//регулярка, которая выдергивает и заменяет
v = v.replace(reg_sp, '');
v = v.replace(reg_sp1, '');
return v;
}

$('#line_options').animate({opacity: "0.2"});
function on_line()
{
if($("#line").prop("checked"))
{$('#line_options').animate({opacity: "1"}, 300);
$("#line_truba").prop("disabled", false)
$("#line_dno").prop("disabled", false)
}
else
{$('#line_options').animate({opacity: "0.2"}, 300);
$("#line_truba").prop("checked", false)
$("#line_dno").prop("checked", false)
$("#line_truba").prop("disabled", true)
$("#line_dno").prop("disabled", true)
glue_set_up()
}
}
function glue_set_up(){
if($("#line_truba").prop("checked")){
$("#truba_glue_hot").prop("checked", true)
$("#truba_glue_tape9mm").prop("checked", false)
$("#truba_glue_tape11mm").prop("checked", false)
$("#truba_glue_tape_yellow").prop("checked", false)
$("#truba_glue_tape9mm").prop("disabled", true)
$("#truba_glue_tape11mm").prop("disabled", true)
$("#truba_glue_tape_yellow").prop("disabled", true)
}else{
$("#truba_glue_tape11mm").prop("checked", true)
$("#truba_glue_tape9mm").prop("disabled", false)
$("#truba_glue_tape11mm").prop("disabled", false)
$("#truba_glue_tape_yellow").prop("disabled", false)
}

if($("#line_dno").prop("checked")){
$("#dno_glue_hot").prop("checked", true)
$("#dno_glue_tape9mm").prop("checked", false)
$("#dno_glue_tape11mm").prop("checked", false)
$("#dno_glue_tape_yellow").prop("checked", false)
$("#dno_glue_tape9mm").prop("disabled", true)
$("#dno_glue_tape11mm").prop("disabled", true)
$("#dno_glue_tape_yellow").prop("disabled", true)
}else{
$("#dno_glue_tape11mm").prop("checked", true)
$("#dno_glue_tape9mm").prop("disabled", false)
$("#dno_glue_tape11mm").prop("disabled", false)
$("#dno_glue_tape_yellow").prop("disabled", false)
}
}

//другой материал, который может задать сам пользователь

function other_material() {

if($("#mater").val() == "other") {
$('#select_other_material').animate({opacity: "1"}, 300);
$("#per_list").prop("disabled", false)
$("#per_tonn").prop("disabled", false)
$("#price_other_material").prop("disabled", false)
$("#grammaj_other_material").prop("disabled", false)

}
else {
$('#select_other_material').animate({opacity: "0.2"}, 300);
$("#per_list").prop("disabled", true)
$("#per_tonn").prop("disabled", true)
$("#price_other_material").prop("disabled", true)
$("#grammaj_other_material").prop("disabled", true)
$("#price_other_material").val("")
$("#grammaj_other_material").val("")
$("#per_list").prop("checked", false)
$("#per_tonn").prop("checked", false)
}
}

//подставляем в спан значение за лист или за тонну, в зависимости от выбранного параметра. Значение берем из value
function price_per_what(){

var other_material_value = $(":radio[name=other_material_per]").filter(":checked").val();
$("#price_per_what_span").html(other_material_value)

}
</script>

</td>
<td>Материал:</td>
<td width=250><select name=mater id=mater onchange="other_material()">
<option value="none">нет</option>
<option value="other">ни одна из списка ниже</option>
<option value="Меловка 170 гр;170;60000" selected>Меловка 170 гр</option>
<option value="Меловка 200 гр;200;60000">Меловка 200 гр</option>
<option value="Меловка 250 гр;250;60000">Меловка 250 гр</option>
<option value="Меловка 300 гр;300;60000">Меловка 300 гр</option>
<option value="Крафт (топ)лайнер 135 гр;135;42500">Крафт (топ)лайнер 135 гр</option>
<option value="Крафт лайнер 140 гр (как ламаре);140;42500">Крафт лайнер 140 гр (как ламаре)</option>
<option value="Фэнси (лен) 120 гр;120;250000">Фэнси (лен) 120 гр</option>
<option value="Имитлин,эфалин 125 гр;125;300000">Имитлин,эфалин 125 гр</option>
<option value="Там брайт, 210 гр;210;67000">Там брайт, 210 гр</option>
<option value="Белый ВД крафт, 120 гр;120;57000">Белый ВД крафт, 120 гр</option>
<option value="Финский крафт 90гр;90;67000">Финский крафт 90гр</option>
<option value="Финский крафт 100гр;90;67000">Финский крафт 100гр</option>
<option value="Финский крафт 110гр;90;67000">Финский крафт 110гр</option>
<option value="Финский крафт 120гр;90;67000">Финский крафт 120гр</option>
<option value="Плайк 140 гр;140;480000">Плайк 140 гр</option>
</select>
<br />
<span id=select_other_material style="opacity: 0.2;">
<b>У меня есть цена за:</b><br />
<input disabled type=radio id="per_list" name="other_material_per" value="за лист" onclick="price_per_what()">
<label for="per_list">за лист</label>
<input disabled type=radio id="per_tonn" name="other_material_per" value="за тонну" onclick="price_per_what()">
<label for="per_tonn">за тонну</label>
<br />цена за <span id=price_per_what_span></span>: <input disabled type=text size=5 name=price_other_material id="price_other_material"/> р. <input disabled type=text size=5 name=grammaj_other_material id="grammaj_other_material"/> гр.
</span>


</td>
</tr>
<tr>
<td colspan=2>Ширина: <input onkeyup="this.value = replace_num(this.value, 'drob');" type="text" name=shirina id=shirina onchange="sborka_raschet()" size=4 value="25"/> &nbsp;&nbsp;&nbsp;&nbsp;
Высота: <input onkeyup="this.value = replace_num(this.value, 'drob');" type="text" name=vysota id=vysota onchange="sborka_raschet()" size=4 value="36"/> &nbsp;&nbsp;&nbsp;&nbsp;
Бок: <input onkeyup="this.value = replace_num(this.value, 'drob');" type="text" name=bok id=bok size=4 value="10" onchange="sborka_raschet()"/>&nbsp;&nbsp;&nbsp;&nbsp;
Подворот: <input onkeyup="this.value = replace_num(this.value, 'drob');" type="text" name=podvorot id=podvorot onchange="sborka_raschet()" size=2 min=0 max=20 value="5"/>
<br />
<span id=razvorot_top style="opacity: 0; width: 150px;">
разворот: <span id=razvorot_width_top></span> х <span id=razvorot_height_top></span> см &nbsp;&nbsp;&nbsp;&nbsp;
<a href="#" onclick="optimizi_razvorot()"><img src="../../i/icons/optimize.gif" width="15" height="15" alt="" valign=middle/></a>
</span>
<!-- записываем размеры разворота в переменную, что бы не приходилось высчитывать эти величины дважды. Это значения потом берем из функции make_calc -->
<input type=hidden value="" id="razvorot_width_top_hidden" />
<input type=hidden value="" id="razvorot_height_top_hidden" />
</td>
<td colspan=2>Стоимость штампа: <input type="checkbox" name="shtamp_include_cost" id="shtamp_include" onclick="highlight_shtamp()"/>
<label for="shtamp_include">включить в стоимость</label>
<input type="text" name=shtamp id=shtamp size="12" style="opacity: 0.2;"/>
<script>
$('#shtamp').animate({opacity: "0.2"});
$('#razvorot_top').animate({opacity: "0"});
function highlight_shtamp()
{
if($("#shtamp_include").prop("checked"))
{$('#shtamp').animate({opacity: "1"}, 300);}
else
{$('#shtamp').animate({opacity: "0.2"}, 300);}
}

function sborka_raschet(){

//получаем размер разворота
klapan = '1.5'
var shirina =  $("#shirina").val();
var vysota  = $("#vysota").val();
var bok  = $("#bok").val();
var podvorot  = $("#podvorot").val();


if (shirina != "" && vysota != "" && bok != "")
{
shirina = shirina*1
vysota = vysota*1
bok = bok*1
podvorot = podvorot*1
klapan = klapan*1


//ширина разворота
var razvorot_width = (shirina * 2) + (bok * 2) + klapan;
razvorot_width = razvorot_width.toFixed(2)
//высота разворота
var razvorot_height  =  vysota + podvorot + bok * 0.75;
razvorot_height = razvorot_height.toFixed(2)

$('#razvorot_top').animate({opacity: "1"}, 300);
$("#razvorot_width_top").html(razvorot_width)
$("#razvorot_height_top").html(razvorot_height)
//пишем эти значения также в спрятанные поля, чтобы из нижней функции их использовать можно было

$("#razvorot_width_top_hidden").val(razvorot_width)
$("#razvorot_height_top_hidden").val(razvorot_height)


//проверяем, если разворот не вмещается в выбранный печатный формат, то автоматически назначаем сборку из двух листов и предлагаем сделать визуальную раскладку
if ($("#format_pechati_A2").prop("checked")) {
$("#format_pechati_other_width").val("72")
$("#format_pechati_other_height").val("52")
$("#bez_pachati_a1").prop('selected', true)
$("#pechat_A2").prop('disabled', false)
$("#pechat_A1").prop('disabled', false)
}
if ($("#format_pechati_A1").prop("checked"))
{
$("#format_pechati_other_width").val("104")
$("#format_pechati_other_height").val("72")
$("#bez_pachati_a2").prop('selected', true)
$("#pechat_A2").prop('disabled', false)
$("#pechat_A1").prop('disabled', false)
}
if ($("#format_pechati_other").prop("checked"))
{
$("#pechat_A2").prop('disabled', false)
$("#pechat_A1").prop('disabled', false)
}

//снимаем выделение с предыдущего формата печати, если формат изменился
//определяем площадь листа
var format_pechati_other_width = $("#format_pechati_other_width").val()
format_pechati_other_width = format_pechati_other_width*1
var format_pechati_other_height = $("#format_pechati_other_height").val()
format_pechati_other_height = format_pechati_other_height*1

var ploshad_lista = format_pechati_other_width * format_pechati_other_height / 10000;
ploshad_lista = ploshad_lista.toFixed(3)
$("#ploshad_lista_inp").val(ploshad_lista)

//ставим возможный тип печати в зависимости от размера печатного листа
if (ploshad_lista <= 0.3744) {
$("#pechat_A2_select").show();
$("#format_pech_div").html('A2');
$("#pechat_A1_select").hide();
}else{
$("#pechat_A1_select").show();
$("#format_pech_div").html('A1');
$("#pechat_A2_select").hide();
}

var size_A_lista = $("#format_pechati_other_width").val();
var size_B_lista = $("#format_pechati_other_height").val();

var size_max_A_lista = Math.max(size_A_lista,size_B_lista)
var size_min_B_lista = Math.min(size_A_lista,size_B_lista)

//определяем большую и меньшую сторону разворота
var size_max_A = Math.max(razvorot_width,razvorot_height)
var size_min_B = Math.min(razvorot_width,razvorot_height)

/* если большая сторона вмещается в 72 или 104 и если меньшая сторона вмещается 52 или 72 то из 1 листа */
if (size_max_A > size_max_A_lista || size_min_B > size_min_B_lista)
{
$("#iz_2_lista").css("border-bottom","2px dotted");
$("#iz_1_lista").css("border-bottom","none");
}else{
$("#iz_1_lista").css("border-bottom","2px dotted");
$("#iz_2_lista").css("border-bottom","none");
}

//если размеры любой из сторон меньше 72 см,&nbsp;но больше 45, то пакет - БОЛЬШОЙ и наценка соответствующая
if (shirina < "72" && vysota < "72") {var tarif_sborka = '3.5'; var dobavka = '1'}
if (shirina < "60" && vysota < "60") {var tarif_sborka = '3.0'; var dobavka = '0.8'}
if (shirina < "50" && vysota < "50") {var tarif_sborka = '2.8'; var dobavka = '0.7'}
if (shirina < "40" && vysota < "40") {var tarif_sborka = '2.6'; var dobavka = '0.6'}
if (shirina < "20" && vysota < "25") {var tarif_sborka = '2.3'; var dobavka = '0.2'}

//если стоит галочка что пакет из двух листов, то добавляем доп награду за сборку и показываем слой, где можно указать разные листы или одинаковые
if ($("#is_skolkih_listov_paket_2").prop("checked"))
{
dobavka = 1*dobavka
tarif_sborka = 1*tarif_sborka
tarif_sborka = tarif_sborka + dobavka
$('#storony_tr').animate({opacity: "1"}, 300);
$("#storony_odinakovie").prop("disabled", false)
$("#storony_raznie").prop("disabled", false)
}
else {
$('#storony_tr').animate({opacity: "0.2"}, 300);
$("#storony_odinakovie").prop("checked", false)
$("#storony_raznie").prop("checked", false)
$("#storony_odinakovie").prop("disabled", true)
$("#storony_raznie").prop("disabled", true)
dop_priladki_raznye()
}

if ($("#format_pechati_other").prop("checked"))
{
$('#format_pechati_other_span').animate({opacity: "1"}, 300);
}
else {
$('#format_pechati_other_span').animate({opacity: "0.2"}, 300);
}

if ($("#line").prop("checked")) {
//определяем себестоимость работы по формированию трубы и дна
var truba = 0.4
var dno = 0.27
//задаем К соотношения труда в сборке пакета
var truba_ruchnaya = 0.4
var dno_ruchnoe = 0.4
//объявляем переменные, которые потом будут использованы для вычета из базового тарифа, в том случае, если используется линия
var vychet_truba = 0
var vychet_dno = 0

//определяем сколько убавляем из базового тарифа и сколько в итоге получится с учетом оплаты работы людей на линии
if ($("#line_truba").prop("checked"))
{vychet_truba = tarif_sborka * k_line_truba
vychet_truba = vychet_truba.toFixed(2)}
if ($("#line_dno").prop("checked"))
{vychet_dno = tarif_sborka * k_line_dno
vychet_dno = vychet_dno.toFixed(2)}




if ($("#line_truba").prop("checked") || $("#line_dno").prop("checked")) {
//вычитаем из базовой цены только если галочки стоят
tarif_sborka = tarif_sborka - vychet_truba - vychet_dno
tarif_sborka = tarif_sborka.toFixed(2)

}
}
}

/*
работа по сборке пакета состоит из следующих этапов, каждый из которых занимает следующий процент от тарифа сборки

склейка трубы и вклейка боковины - 0,5

вставка дна и проклейка дна - 0,3

подворот - 0,2

в случае есть пакет полностью собирается вручную то тариф назначается 100%

в случае если частично собирается на линии

= базовый тариф - базовый тариф * К - базовый * К

с/с сборки пакета под ключ = стоимость сборки в цеху (если есть) + стоимость сборки на линии (если есть) + вознаграждение допников + вознаграждение начальника пр-ва

стоимость сборки в цеху (если есть) = К трубы * базовый тариф + К дна * базовый тариф
стоимость сборки на линии (если есть) = тариф трубы + тариф дно

вознаграждение допников = константа

вознаграждение начальника пр-ва = константа
*/
//ставим цену сборки вручную труба

//ставим цену сборки вручную дно

//ставим цену сборки линии труба

//ставим цену сборки линии дно

//ставим цену сборки общую

$("#cena_sborki").val(tarif_sborka)

if (shirina == "" || vysota == "" || bok == "")  {
$('#razvorot_top').animate({opacity: "0"}, 300);}

/* стоимость сборки меняется в зависимости от:

- размера пакета
- - - маленькие где ширина меньше 25, высота до 25 - 2,3 руб
- - - средние где ширина меньше 50, высота меньше 50 - 2,8 руб
- - - большие где ширина меньше 75 высота меньше 70 - 3,0 руб

- количества листов, из которых состоит пакет
если из одного, то +0
если из двух, то +0,70 коп

- если часть операции выполняет машина (труба, дно)

без трубы и дна остается лишь подгиб и укрепление верха =
20% от тарифа
труба - 40%
дно - 40%
эти переменные можно менять потом */

}

//расчитываем количество доприладок, в зависимости от того, разные стороны или нет
function dop_priladki_raznye()
{
dop_priladki_storony = $("#dop_priladki_storony").html()
dop_priladki_storony = dop_priladki_storony*1
//если указано, что стороны разные, кол-во допприладок равняется или больше 0

//если выбрано стороны разные и кол-во доприладок равно или больше нуля
if ($("#storony_raznie").prop("checked") && dop_priladki_storony >= 0)
{
//то прибавляем к текущему колву приладок единицу
plus_one_pril = dop_priladki_storony + 1
plus_one_pril = plus_one_pril*1
$("#dop_priladki_storony").html(plus_one_pril)

//показываем спан с допприладками, если их значение больше 0
if(plus_one_pril > 0)
{$('#dop_priladki_storony_span').animate({opacity: "1"}, 300);}
}
//если выбрано стороны одинаковые, а колво приладок больше нуля, то сам бог велел вычесть единицу
if ($("#storony_odinakovie").prop("checked") && dop_priladki_storony > 0 ) {
plus_one_pril = dop_priladki_storony - 1
plus_one_pril = plus_one_pril*1
$("#dop_priladki_storony").html(plus_one_pril)

//если кол-во приладок равно нулю - то слой прячем
if(plus_one_pril == 0)
{$('#dop_priladki_storony_span').animate({opacity: "0"}, 300);}

}

//если пользователь переключил на пакет собран из одного листа, забыв снять галочку с стороны разнные, мы обнуляем приладки тут
if ($("#is_skolkih_listov_paket_1").prop("checked") && dop_priladki_storony > 0 ) {
plus_one_pril = dop_priladki_storony - 1
plus_one_pril = plus_one_pril*1
$("#dop_priladki_storony").html(plus_one_pril)

if(plus_one_pril == 0)
{$('#dop_priladki_storony_span').animate({opacity: "0"}, 300);}

}
}
</script>
</td>
</tr>
<tr>
<td colspan=2>
<table border=0>
<tr>
<td>Пакет сделан из:</td>
<td>
<input type=radio onchange="sborka_raschet()" checked id=is_skolkih_listov_paket_1 name=is_skolkih_listov_paket value="1">
<span id=iz_1_lista><label for="is_skolkih_listov_paket_1">из одного листа</label></span>
</td>
<td>
<input type=radio onchange="sborka_raschet()" id=is_skolkih_listov_paket_2 name=is_skolkih_listov_paket value="2">
<span id=iz_2_lista><label for="is_skolkih_listov_paket_2">из двух листов</label></span>
</td>
<td></td>
<td></td>
</tr>
<tr id=storony_tr style="opacity: 0.2;">
<td>Стороны пакета:</td>
<td>
<span id=storony_paketa>
<input type=radio onclick="dop_priladki_raznye()"  id=storony_odinakovie name=storony value="1">
<label for="storony_odinakovie">одинаковые</label></span>
</td>
<td>
<span id=storony_paketa>
<input type=radio onchange="dop_priladki_raznye()" id=storony_raznie name=storony value="2">
<label for="storony_raznie">разные</label></span>
</td>
<td></td>
<td></td>
</tr>
<tr>
<td>Размер листа:</td>
<td>
<input type=radio id=format_pechati_A2 name=format_pechati value="A2" onclick="sborka_raschet()"> <label for="format_pechati_A2"><b>A2</b> 72x52</label>
</td>
<td>
<input type=radio id=format_pechati_A1 name=format_pechati value="A1" onclick="sborka_raschet()"> <label for="format_pechati_A1"><b>A1</b> 72x104</label>
</td>
<td><input type=radio id=format_pechati_other name=format_pechati value="other" onclick="sborka_raschet()"> <label for="format_pechati_other">иной</label></td>
<td>
<span id=format_pechati_other_span style="opacity: 0.2;">
<input type=text size=3 id="format_pechati_other_width"  onchange="sborka_raschet()" name="format_pechati_other_width" value="" /> x
<input type=text size=3 id="format_pechati_other_height" onchange="sborka_raschet()" name="format_pechati_other_height" value="" /></span>
<input type=hidden size=5 id=ploshad_lista_inp>



</td>

</tr>
<tr>
<td>Пакетов на лист</td>
<td><span id=format_pech_div></span>:
</td>
<td>
<select name="izdely_na_list" id="izdely_na_list">
<option value="1">1</option>
<option value="0.5">0,5</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
</select></td>
<td></td>
<td></td>
</tr>
</table>

<br />


<br />


<br />


</td>
<td>
<table><tr>
<td>Шелкография:</td>
<td><select name=shelkograf id=shelkograf style="width: 70px;">
<option value="0;0;0">без шелкографии</option>
<option value="1+0;7.3;50">1+0</option>
<option value="2+0;10.7;75">2+0</option>
<option value="3+0;14.2;100">3+0</option>
<option value="4+0;17.6;125">4+0</option>
<option value="5+0;21.1;150">5+0</option>
<option value="1+1;14.6;50">1+1</option>
<option value="2+2;21.4;100">2+2</option>
<option value="4+4;28.4;150">3+3</option>
<option value="4+4;35.2;200">4+4</option>
<option value="5+5;42.2;250">5+5</option>
</select>
</td>
</tr>
<tr>
<td>УФ лак:</td>
<td><select name=uf id=uf style="width: 70px;">
<option value="0;0;0">без УФ лака</option>
<option value="1+0, до 30%;2.6;50">1+0, до 30%</option>
<option value="1+0, от 30 до 50%;2.92;50">1+0, от 30 до 50%</option>
<option value="1+0, от 50% до 80%;3.12;50">1+0, от 50% до 80%</option>
<option value="1+0, от 80% до 100%;3.5;50">1+0, от 80% до 100%</option>
<option value="1+1, до 30%;5.2;100">1+1, до 30%</option>
<option value="1+1, от 30 до 50%;5.84;100">1+1, от 30 до 50%</option>
<option value="1+1, от 50% до 80%;6.24;100">1+1, от 50% до 80%</option>
<option value="1+1, от 80% до 100%;7;100">1+1, от 80% до 100%</option>
</select>
</td>
</tr></table>




</td>
<td>
<script>
$('#tisn_block').animate({opacity: "0.2"});
$('#tisn_one_side_block').animate({opacity: "0.2"});
$('#tisn_two_side_block').animate({opacity: "0.2"});
function show_tisn()
{
//начинаем, если стоит хотя бы одна галочка
if($("#tisnenie").prop("checked") || $("#kongrev").prop("checked"))
{
$('#tisnenie_span').show();
$('#tisn_block').animate({opacity: "1"}, 300);
$("#tisn_one_side").prop("disabled", false)
$("#tisn_two_side").prop("disabled", false)
}
else
{
//прячем в дебаге
$('#tisnenie_span').hide();

$('#tisn_block').animate({opacity: "0.2"}, 300);
$('#tisn_one_side_block').animate({opacity: "0.2"}, 300);
$('#tisn_two_side_block').animate({opacity: "0.2"}, 300);

$("#tisn_one_side").prop("checked", false)
$("#tisn_one_side").prop("disabled", true)
$("#tisn_two_side").prop("checked", false)
$("#tisn_two_side").prop("disabled", true)

$("#shirina_tisn_1").val("");
$("#shirina_tisn_1").prop("disabled", true)

$("#vysota_tisn_1").val("");
$("#vysota_tisn_1").prop("disabled", true)

$("#tisn_sides_same").prop("checked", false)
$("#tisn_sides_same").prop("disabled", true)

$("#tisn_sides_diff").prop("checked", false)
$("#tisn_sides_diff").prop("disabled", true)

$("#shirina_tisn_2").val("");
$("#shirina_tisn_2").prop("disabled", true)

$("#vysota_tisn_2").val("");
$("#vysota_tisn_2").prop("disabled", true)
}

}

function show_side_1(){
$("#shirina_tisn_1").prop("disabled", false)
$("#vysota_tisn_1").prop("disabled", false)
$('#tisn_one_side_block').animate({opacity: "1"}, 300);
$('#tisn_two_side_block').animate({opacity: "0.2"}, 300);

$("#tisn_sides_same").prop("checked", false)
$("#tisn_sides_same").prop("disabled", true)

$("#tisn_sides_diff").prop("checked", false)
$("#tisn_sides_diff").prop("disabled", true)

$("#shirina_tisn_2").val("");
$("#vysota_tisn_2").val("");
$("#shirina_tisn_2").prop("disabled", true)
$("#vysota_tisn_2").prop("disabled", true)

}
function show_side_2(){
$('#tisn_one_side_block').animate({opacity: "1"}, 300);
$('#tisn_two_side_block').animate({opacity: "1"}, 300);
$("#shirina_tisn_1").prop("disabled", false)
$("#vysota_tisn_1").prop("disabled", false)
$("#shirina_tisn_2").prop("disabled", false)
$("#vysota_tisn_2").prop("disabled", false)
$("#tisn_sides_same").prop("disabled", false)
$("#tisn_sides_diff").prop("disabled", false)
}

function tisn_sides_sinchron() {
shirina_tisn_1 = $("#shirina_tisn_1").val();
vysota_tisn_1 = $("#vysota_tisn_1").val();
$("#shirina_tisn_2").val(shirina_tisn_1);
$("#vysota_tisn_2").val(vysota_tisn_1);
}

function tisn_sinchron() {
var shirina_tisn_1 = $("#shirina_tisn_1").val();
var vysota_tisn_1 = $("#vysota_tisn_1").val();
var shirina_tisn_2 = $("#shirina_tisn_2").val();
var vysota_tisn_2 = $("#vysota_tisn_2").val();
if (shirina_tisn_1 != shirina_tisn_2 | vysota_tisn_1 != vysota_tisn_2)
{
$("#tisn_sides_diff").prop("checked", true)}
else
{$("#tisn_sides_same").prop("checked", true)}
}
</script>
<input checked type=radio id=net_tisnenie name=tisn_radio value="net_tisn" onclick="show_tisn()">
<label for="net_tisnenie">нет</label>
<input type=radio id=tisnenie name=tisn_radio value="tisnenie" onclick="show_tisn()">
<label for="tisnenie">тиснение</label>
<input type=radio id=kongrev name=tisn_radio value="kongrev" onclick="show_tisn()">
<label for="kongrev">конгрев</label>

<div id=tisn_block style="opacity: 0.2;">
<input disabled type=radio id=tisn_one_side name=tisn_sides value="tisn_one_side" onclick="show_side_1()">
<label for="tisn_one_side">с одной стороны</label>
<input disabled type=radio id=tisn_two_side name=tisn_sides value="tisn_two_side" onclick="show_side_2()">
<label for="tisn_two_side">с двух сторон</label><br /></div>

<div id=tisn_one_side_block style="opacity: 0.2;">
<b>1.</b> Ширина: <input disabled type="text" name=shirina_tisn_1 id=shirina_tisn_1 size="3"/>
Высота: <input disabled type="text" name=vysota_tisn_1 id=vysota_tisn_1 size="3"/><br />
</div>
<div id=tisn_two_side_block style="opacity: 0.2;">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input disabled type=radio id=tisn_sides_same name=tisn_sides_di value="tisn_sides_same" onclick="tisn_sides_sinchron()">
<label for="tisn_sides_same">одинаковые</label>
<input disabled type=radio id=tisn_sides_diff name=tisn_sides_di value="tisn_sides_diff">
<label for="tisn_sides_diff">разные</label>
<br />
<b>2.</b> Ширина: <input disabled type="text" name=shirina_tisn_2 id=shirina_tisn_2 size="3"  onchange="tisn_sinchron()"/>
Высота: <input disabled type="text" name=vysota_tisn_2 id=vysota_tisn_2 size="3"  onchange="tisn_sinchron()"/>
</div>
</td>
</tr>
<tr>
<td>Печать:</td>
<td><script>
//прячем / показываем поле с допприладками, в зависимости от того, выбрана печать или нет
function show_doppriladki(format)
{
//снимаем выделение с предыдущего формата печати, если формат изменился
if (format == "a2"){
$("#bez_pachati_a1").prop('selected', true)
}
if (format == "a1"){
$("#bez_pachati_a2").prop('selected', true)
}

pechat_A2 = $("#pechat_A2").val();
pechat_A2 = pechat_A2.split(";")
pechat_A2 = pechat_A2[0]
pechat_A1 = $("#pechat_A1").val();
pechat_A1 = pechat_A1.split(";")
pechat_A1 = pechat_A1[0]

if (pechat_A2 != 0 || pechat_A1 != 0)
{
$('#doppriladky_span').animate({opacity: "1"}, 300);
$('#dop_priladki_inp').prop("disabled", false);
}
else {$('#doppriladky_span').animate({opacity: "0.2"}, 300);
$('#dop_priladki_inp').prop("disabled", true);}
}
</script>
<span id=pechat_A2_select>
<select disabled name=pechat_A2 id=pechat_A2 style="width: 180px;" onchange="show_doppriladki('a2')">
<option value="0;;0,0;0;0;0" id=bez_pachati_a2>без печати</option>
<!-- текстовое описание;площадь листа;стоимость прокатки;стоимость приладки;листов на приладку  -->
<option value="А2 CMYK лого;A2 (72x52см);0,3744;1,5;6000;150">А2 ЦМИК лого</option>
<option value="А2 CMYK фото;A2 (72x52см);0,3744;1,8;6500;150">А2 CMYK фото</option>
<option value="А2 pantone лого;A2 (72x52см);0,3744;2,5;7000;200">А2 pantone лого</option>
<option value="А2 pantone заливка;A2 (72x52см);0,3744;5;9000;200">А2 pantone заливка</option>
<option value="А2 CMYK лого + внутр. заливка;A2 (72x52см);0,3744;6,5;12000;350">А2 CMYK лого + внутр. заливка</option>
<option value="А2 CMYK фото + внутр. заливка;A2 (72x52см);0,3744;6,8;15000;400">А2 CMYK фото + внутр. заливка</option>
<option value="А2 pantone лого + внутр. заливка;A2 (72x52см);0,3744;8,5;15000;400">А2 pantone лого + внутр. заливка</option>
<option value="А2 pantone заливка + внутр. заливка;A2 (72x52см);0,3744;10;18000;500">А2 pantone заливка + внутр. заливка</option>

</select></span>

<span id=pechat_A1_select style="display: none;">
<select disabled name=pechat_A1 id=pechat_A1 style="width: 180px;" onchange="show_doppriladki('a1')">
<!-- текстовое описание;площадь листа;стоимость прокатки;стоимость приладки;листов на приладку  -->
<option value="0;;0,0;0;0;0" id=bez_pachati_a1>без печати</option>
<option value="А1 CMYK лого;A1 (72x104см);0,7488;3;10000;250">А1 CMYK лого</option>
<option value="А1 CMYK фото;A1 (72x104см);0,7488;3,6;12000;300">А1 CMYK фото</option>
<option value="А1 pantone лого;A1 (72x104см);0,7488;5;13000;300">А1 pantone лого</option>
<option value="А1 pantone заливка;A1 (72x104см);0,7488;10;15000;400">А1 pantone заливка</option>
<option value="А1 CMYK лого + внутр. заливка;A1 (72x104см);0,7488;13;25000;600">А1 CMYK лого + внутр. заливка</option>
<option value="А1 CMYK фото + внутр. заливка;A1 (72x104см);0,7488;14;27000;600">А1 CMYK фото + внутр. заливка</option>
<option value="А1 pantone лого + внутр. заливка;A1 (72x104см);0,7488;15;28000;600">А1 pantone лого + внутр. заливка</option>
<option value="А1 pantone заливка + внутр. заливка;A1 (72x104см);0,7488;17;30000;600">А1 pantone заливка + внутр. заливка</option>

</select></span> <span id=dop_priladki_storony_span style="opacity: 0;">уже включена <span id=dop_priladki_storony class=bold>0</span> доп.приладка</span>
<br />
<span id=doppriladky_span style="opacity: 0.2;">
вы можете добавить еще доп.приладки: <input disabled type="number" maxlength=2 min=0 max=10 value="0" name=dop_priladki_inp id=dop_priladki_inp size="2"/></span>

</td>
<td>Ламинация:</td>
<td><select name=lami id=lami>
<!-- название / стоимость пленки за м2 / стоимость работы -->
<option value="">без ламинации</option>
<option value="матовая;6.24;0.4">матовая</option>
<option value="глянцевая;4.8;0.4">глянцевая</option>
<option value="матовая;4.8;6">матовая на стороне</option>
<option value="глянцевая;6.24;6">глянцевая на стороне</option>
</select></td>
</tr>
<tr>
<td>Ручки:</td>
<td>
<script>
//функция, которая устанавливает увеличенный подворот пакета, если выбрана прорубная ручка
function set_podvorot(){
var prorubn = $("#ruchki").val()
prorubn = prorubn.split(";")
if (prorubn[0] == "прорубные"){
$("#podvorot").val('6');
$("#podvorot").focus();
}


}
</script>
<select name=ruchki id=ruchki onchange="set_podvorot()">
<!-- есть ручки,&nbsp;где цена определяется ценой за погонный метр, а есть там,&nbsp;где цена заранее известна, т.е. fixed -->
<option id=bez_ruchek value="без ручек;0;0">без ручек</option>
<option value="шнурок 5 мм;0.90;notfixed">шнурок 5 мм</option>
<option value="шнурок 5 мм (с клипсой);1.19;fixed">шнурок 5 мм (с клипсой)</option>
<option value="шнурок 6 мм;1.50;notfixed">шнурок 6 мм</option>	1.12
<option value="шнурок 6 мм (с клипсой);2.10;fixed">шнурок 6 мм (с клипсой)</option>
<option value="прорубные;0.6;fixed">прорубные</option>
<option value="атласная лента, 2 см шир;3.00;notfixed">атласная лента, 2 см шир</option>
<option value="шпагат бумажный коричн;3.00;fixed">шпагат бумажный коричн</option>
<option value="шпагат белый;3.00;fixed">шпагат белый</option>
<option value="лента кож зам;7.00;notfixed">лента кож зам</option>
<option value="лента черная хб;2.80;notfixed">лента черная хб</option>
<option value="лента кож зам протос;14.00;notfixed">лента кож зам протос</option>
<option value="лента греция протос;7.00;notfixed">лента греция протос</option>

</select>
<br />
длина ручки: <input type="text" value="35" name=dlina_ruchki id=dlina_ruchki size=3/> см
<input type=checkbox id=ruch_ne_podhodit name=ruch_ne_podhodit value="ruch_ne_podhodit" onclick="ruchki_svoy_var()">
<label for="ruch_ne_podhodit">свой вариант</label>
<br />
<span id=ruchki_svoy_var style="opacity: 0;">введите цену за метр: <input type="text" value="" name=cena_za_metr id=cena_za_metr size=4/></span>
<script>
$("#ruchki_svoy_var").prop("disabled", true)
$('#ruchki_svoy_var').animate({opacity: "0"});

function ruchki_svoy_var(){

if($("#ruch_ne_podhodit").prop("checked"))  {
$("#ruchki_svoy_var").prop("disabled", false)
$('#ruchki_svoy_var').animate({opacity: "1"}, 300);
$('#ruchki').animate({opacity: "0.2"}, 300);
$("#ruchki").prop("disabled", true)
$("#bez_ruchek").prop('selected', true)
}
else {
 //обнуляем и прячем
$("#ruchki_svoy_var").prop("disabled", true)
$("#cena_za_metr").val("");
$('#ruchki_svoy_var').animate({opacity: "0"}, 300);

//а селект наоборот показываем и активируем
$('#ruchki').animate({opacity: "1"}, 300);
$("#ruchki").prop("disabled", false)

}
  }
</script>



</td>
<td>Пиккало:</td>
<td>
<select name=piccolo id=piccolo>
<!-- текстовое описание / стоимость люверсов / наценка за установку -->
<option value="без пикколо;0;0">без пикколо</option>
<option value="серебр/зол. 5 мм.;0.40;0.35">серебр/зол. 5 мм.</option>
<option value="цветные;1.50;0.50">цветные 5 мм.</option>
</select>
</td>
</tr>

<tr>
<td colspan=2 rowspan=4>Клеящие материалы:<br />
<table id=ugolkrug><tr><td>
<b>труба:</b>
<br />
<!-- зашиваем в value  название / тип / стоимость 1 м проклейки данным материалом /  -->
<input type=radio id=truba_glue_tape9mm name=glue_truba value="скотч 9 мм;tape;0.55">
<label for="truba_glue_tape9mm">скотч 9 мм</label>
<input type=radio checked id=truba_glue_tape11mm name=glue_truba value="скотч 11 мм;tape;0.72">
<label for="truba_glue_tape11mm">скотч 11 мм</label>
<input type=radio id=truba_glue_tape_yellow name=glue_truba value="жел/скотч 11 мм;tape;1.65">
<label for="truba_glue_tape_yellow">жел/скотч 11 мм</label>
<input type=radio id=truba_glue_hot name=glue_truba value="гор/клей;glue;0.5">
<label for="truba_glue_hot">гор/клей</label>
</table>

<br />
<table id=ugolkrug><tr><td>
<b>дно:</b>
<br />
<input type=radio id=dno_glue_tape9mm name=glue_dno value="скотч 9 мм;tape;0.54">
<label for="dno_glue_tape9mm">скотч 9 мм</label>
<input type=radio checked id=dno_glue_tape11mm name=glue_dno value="скотч 11 мм;tape;0.72">
<label for="dno_glue_tape11mm">скотч 11 мм</label>
<input type=radio id=dno_glue_tape_yellow name=glue_dno value="жел/скотч 11 мм;tape;1.65">
<label for="dno_glue_tape_yellow">жел/скотч 11 мм</label>
<input type=radio id=dno_glue_hot name=glue_dno value="гор/клей;glue;0.5">
<label for="dno_glue_hot">гор/клей</label>

</table>

<br />
<style>
#ugolkrug{
color: #0000; /* цвет текста */
background:white; /* фон блока */
border: 1px #D4D0C8 solid; /* стили рамки */
-moz-border-radius: 5px; /* закругление для старых Mozilla Firefox */
-webkit-border-radius: 5px; /* закругление для старых Chrome и Safari */
-khtml-border-radius:5px; /* закругл. для браузера Konquerer системы Linux */
border-radius: 5px; /* закругление углов для всех, кто понимает */
}
</style>
<table id=ugolkrug><tr><td>
<b>Укрепление:</b>
<br />
<input type=radio checked id=ukr_glue_tape9mm name=glue_ukr value="скотч 9 мм;tape;0.55">
<label for="ukr_glue_tape9mm">скотч 9 мм</label>
<input type=radio id=ukr_glue_tape_yellow name=glue_ukr value="жел/скотч 11 мм;tape;1.65">
<label for="ukr_glue_tape_yellow">жел/скотч 11 мм</label>
<input type=radio id=ukr_glue_no name=glue_ukr value="нет;0;0">
<label for="ukr_glue_no">нет</label>
</td></tr>
</table>


</td>
<td>Сборка:</td>
<td>
<input disabled type="text" id="cena_sborki" size=4 />

</td>
</tr>


<tr>
<td>Картон для укрепления:</td>
<td>
<input checked type=checkbox id=ukrepl_dno name=ukrepl_dno value="" onclick="show_dno_s_zapechatkoy()">
<label for="ukrepl_dno">дно</label>
<input checked type=checkbox id=ukrepl_bok name=ukrepl_bok value="">
<label for="ukrepl_bok">боковины</label>
<span id=dno_s_zapechatkoy>
<input type=checkbox id=print_on_dne name=print_on_dne value="">
<label for="print_on_dne">дно с запечаткой</label></span>
<script>
function show_dno_s_zapechatkoy(){
if($("#ukrepl_dno").prop("checked"))
{
$('#dno_s_zapechatkoy').animate({opacity: "1"}, 300);
$('#print_on_dne').prop("disabled", false);
}
else
{
$('#dno_s_zapechatkoy').animate({opacity: "0.2"}, 300);
$('#print_on_dne').prop("disabled", true);
$('#print_on_dne').prop("checked", false);
}

}
</script>
</td>
</tr>
<tr>
<td>Доп. расходы:</td>
<td><input type="text" name=dop_rash id=dop_rash size=12/></td>
</tr>
<tr>
<td>Упаковка:</td>
<td>
<input checked type=radio id=upak_plenka name=upakovka value="стрейч-пленка;0.1">
<label for="upak_plenka">в пленку</label>
<input type=radio id=upak_box name=upakovka value="коробку;0.5">
<label for="upak_box">в коробки</label></td>
</tr>

</table>




<script>

function make_calc(tisn_listov_na_priladku)
{
<?require_once("calc_vars.php");?>

//объявляем переменные
var dop_priladki_cost = "0";
dop_priladki_cost = parseInt(dop_priladki_cost)

var pechat_format =  "не задан";
var pechat_prokatka_cost =  "0";
var pechat_priladka_cost = "0";
var pechat_priladka_listov =  "0";
var pechat_text = "без печати";
var pechat_priladka_listov = "0";
var material_cost_per_tonn = "0";

pechat_priladka_listov = pechat_priladka_listov*1


//достаем переменные из формы
var tiraj =  parseInt($("#tiraj").val());
var shirina =  $("#shirina").val();
shirina = shirina*1
var vysota =  $("#vysota").val();
vysota = vysota*1
var bok =  $("#bok").val();
bok = bok*1
var podvorot = $("#podvorot").val()
podvorot = podvorot*1
var lami =  $("#lami").val();

//получаем размер разворота из input type hidden
var razvorot_width = $("#razvorot_width_top_hidden").val()
var razvorot_height = $("#razvorot_height_top_hidden").val()

$("#razvorot_width").html(razvorot_width)
$("#razvorot_height").html(razvorot_height)

var izdely_na_list =  $("#izdely_na_list").val();

//достаем параметры из selecta
var select_a2 = $("#pechat_A2").val();
select_a2 = select_a2.split(";")
var select_a1 = $("#pechat_A1").val();
select_a1 = select_a1.split(";")
select_a2a =  select_a2["0"];
select_a1a =  select_a1["0"];

//проверяем какой формат выбран и в зависимости от него делаем выборку из соответствующего селекта
if (select_a2a != 0){
//0-текстовое описание;площадь листа;стоимость прокатки;стоимость приладки;5-листов на приладку  -->
pechat_text =  select_a2["0"];
pechat_format =  select_a2["1"];
pechat_prokatka_cost =  select_a2["3"];
pechat_priladka_cost =  select_a2["4"];
pechat_priladka_listov =  select_a2["5"];
//$("#pechat_lami_format").html("A2")
}

if (select_a1a != 0){
//0-текстовое описание;площадь листа;стоимость прокатки;стоимость приладки;5-листов на приладку  -->
pechat_text =  select_a1["0"];
pechat_format =  select_a1["1"];
pechat_prokatka_cost =  select_a1["3"];
pechat_priladka_cost =  select_a1["4"];
pechat_priladka_listov =  select_a1["5"];
//$("#pechat_lami_format").html("A1")
}


//нужна ли допприладка
var dop_priladki_inp = $("#dop_priladki_inp").val();
dop_priladki_inp = dop_priladki_inp*1
var dop_priladki_storony = $("#dop_priladki_storony").html()
dop_priladki_storony = dop_priladki_storony*1

//если приладок на печать больше чем одна (есть допприладки), то увеличиваем количество приладочных листов
dop_priladki_inp = dop_priladki_inp + dop_priladki_storony

//если допприладок всего одна, то прибавляем еще такое же колво листов на приладку. Почему прибавляем? Потому что умножать на 1 не имеет смысла :-)
if (dop_priladki_inp == 1)
{
pechat_priladka_listov = pechat_priladka_listov*1
pechat_priladka_listov = pechat_priladka_listov + pechat_priladka_listov
}
//если допприладок больше чем одна, то уже умножаем
if (dop_priladki_inp > 1)
{
pechat_priladka_listov = pechat_priladka_listov*1
pechat_priladka_listov = pechat_priladka_listov + pechat_priladka_listov * dop_priladki_inp
}

//если доприладок не равно пустоте, определяем их стоимость исходя из текущего тарифа
if (dop_priladki_inp != "") {
dop_priladki_cost = pechat_priladka_cost * dop_priladki_inp
$("#dop_priladki_span").html(dop_priladki_inp)
$("#dop_priladki_cost_span").html(dop_priladki_cost)
}
else {
$("#dop_priladki_span").html("нет")
$("#dop_priladki_cost_span").html("нет")
}
//пишем переменные в дебаг
$("#pechat_text").html(pechat_text)
$("#pechat_prokatka_cost").html(pechat_prokatka_cost)
$("#pechat_priladka_cost").html(pechat_priladka_cost)

//переводим переменные в цифры
pechat_prokatka_cost = parseInt(pechat_prokatka_cost)
pechat_priladka_cost = parseInt(pechat_priladka_cost)
//всего 4 вида приладок 1. офсет 2. шелкография 3. 4. тиснение уф. Они могут быть все вместе, так и каждая по отдельности. Каждая "жрет" листы.
//делаем функцию, которая прибавляет в переменную pechat_priladka_listov то количество листов, которое необходимо на приладку каждого вида печати
if($("#tisnenie").prop("checked") || $("#kongrev").prop("checked")) {
var tisn_listov_na_priladku = "0"
//определяем количество листов на приладку в зависимости от того, сколько сторон тиснения задействовано
if($("#tisn_one_side").prop("checked")) {
var tisn_listov_na_priladku = "50";
}
//если тиснение с двух сторон
if($("#tisn_two_side").prop("checked")) {
var tisn_listov_na_priladku = "100";
}

tisn_listov_na_priladku = parseInt(tisn_listov_na_priladku)
}else{
var tisn_listov_na_priladku = "0";
tisn_listov_na_priladku = parseInt(tisn_listov_na_priladku)
}
var shelkograf = $("#shelkograf").val();

shelkograf = shelkograf.split(";")
shelkograf_priladka_listov = shelkograf["2"]
shelkograf_priladka_listov = parseInt(shelkograf_priladka_listov)

var uf = $("#uf").val();
uf = uf.split(";")
uf_priladka_listov = uf["2"]
uf_priladka_listov = parseInt(uf_priladka_listov)

pechat_priladka_listov = parseInt(pechat_priladka_listov)
pechat_priladka_listov = pechat_priladka_listov  + shelkograf_priladka_listov + uf_priladka_listov + tisn_listov_na_priladku

$("#pechat_priladka_listov").html(pechat_priladka_listov)



//достаем параметры бумаги, втч граммаж
var mater = $("#mater").val();
//если выбрана другая бумага
if (mater == "other") {
	//если указана цена за лист
if ($("#per_list").prop("checked")){
var material_cost_per_list = $("#price_other_material").val();
$("#material_cost_per_tonn").html(material_cost_per_tonn)

}
//если указана цена за тонну
if ($("#per_tonn").prop("checked")){
var material_cost_per_tonn = $("#price_other_material").val();

}
//граммаж в любом случае в одинаковом поле
var grammaj_bum = $("#grammaj_other_material").val();
var name_bum = "пользовательская";
$("#material_cost_per_tonn").html(material_cost_per_tonn)
}
//если выбрана бумага из стандартного списка
else {
mater = mater.split(";")
//название материала
name_bum =  mater["0"];
//граммаж бумаги
grammaj_bum =  mater["1"];
//преобразуем в цифры
grammaj_bum = parseInt(grammaj_bum)
//$("#grammaj_bum").html(grammaj_bum)
//достаем стоимость за тонну
material_cost_per_tonn =  mater["2"];
material_cost_per_tonn = parseInt(material_cost_per_tonn)
$("#material_cost_per_tonn").html(material_cost_per_tonn)

}
//пишем название бумаги после выполнения всех проверок
$("#name_bum").html(name_bum)


var ploshad_lista = $("#ploshad_lista_inp").val()
//пишем площадь листа в дебаг
$("#ploshad_lista").html(ploshad_lista)

//вычисляем вес листа
var ves_lista = (ploshad_lista * grammaj_bum);

//округляем до целого числа
ves_lista = ves_lista.toFixed(0);

//граммаж бумаги

//преобразуем в цифры
grammaj_bum = parseInt(grammaj_bum)
$("#grammaj_bum").html(grammaj_bum)

//получаем количество листов необходимое для печати без приладки
var listov_na_pechat_bez_usadki =  tiraj / izdely_na_list;
listov_na_pechat_bez_usadki = listov_na_pechat_bez_usadki.toFixed(0)
$("#listov_na_pechat_bez_usadki").html(listov_na_pechat_bez_usadki)

//умножаем на К листов усадки
var listov_na_pechat_s_usadkoy = listov_na_pechat_bez_usadki * pechat_bum_brak;
listov_na_pechat_s_usadkoy = listov_na_pechat_s_usadkoy.toFixed(0)
listov_na_pechat_s_usadkoy = listov_na_pechat_s_usadkoy*1
pechat_priladka_listov = pechat_priladka_listov*1
//прибавляем приладочные листы и получаем общее кол-во листов с приладкой
var vsego_listov = listov_na_pechat_s_usadkoy + pechat_priladka_listov;
vsego_listov = vsego_listov*1

$("#listov_na_pechat_s_usadkoy").html(listov_na_pechat_s_usadkoy)

//ves_materiala считаем вес материала
var ves_materiala = vsego_listov *  ves_lista / 1000000;
ves_materiala = ves_materiala.toFixed(3);
$("#ves_materiala").html(ves_materiala)

//определяем порядок расчета цены бумаги,
//если указана цена за лист
if ($("#per_list").prop("checked")){
material_cost_per_list = material_cost_per_list*1
var material_cost = material_cost_per_list *  vsego_listov;
}
//если цена указана за тонну
else{
//вычисляем стоимость материала
var material_cost = material_cost_per_tonn *  ves_materiala;
}

material_cost = material_cost.toFixed(1)
material_cost = parseInt(material_cost)

$("#vsego_listov").html(vsego_listov)
//пишем вес листа в дебаг
$("#ves_lista").html(ves_lista);
$("#material_cost").html(material_cost)


//вычисляем стоимость печати без материала
var pechat_cost = pechat_priladka_cost + (vsego_listov * pechat_prokatka_cost) + dop_priladki_cost
$("#pechat_cost").html(pechat_cost)
pechat_cost = pechat_cost.toFixed("0")
pechat_cost = parseInt(pechat_cost)

//общая стоимость печати
var pechat_cost_total = pechat_cost + material_cost;
$("#pechat_cost_total").html(pechat_cost_total)
pechat_cost_total = pechat_cost_total.toFixed("0")
pechat_cost_total = parseInt(pechat_cost_total)

if (lami != "")
{
//показываем слой в дебаге
$('#lamination_span').show();

//получаем данные из селекта
select_lami = lami.split(";")
lami_name =  select_lami["0"];
lami_plenka_cost =  select_lami["1"];
lami_rab_cena =  select_lami["2"];
//всегда считаем ламинацию исходя из количества листов, которое получается при печати
var lami_na_list =  parseInt($("#lami_na_list").val())
var lami_listov = tiraj / izdely_na_list
$("#lami_listov_span").html(lami_listov)
$("#lami_name_span").html(lami_name)
$("#lami_plenka_cost_span").html(lami_plenka_cost)


//стоимость работы по ламинированию
lami_rab_cost = lami_listov * lami_rab_cena
$("#lami_rab_cost_span").html(lami_rab_cost)
$("#lami_rab_span").html(lami_rab_cena)
//площадь пленки
var lami_ploshad = ploshad_lista *  lami_listov * pogr_lami
lami_ploshad =  lami_ploshad.toFixed(0)
$("#lami_ploshad_span").html(lami_ploshad)
//стоимость пленки по текущему тарифу
var lami_plenka_total_cost = lami_ploshad *  lami_plenka_cost
lami_plenka_total_cost =  lami_plenka_total_cost.toFixed(0)
$("#lami_plenka_total_cost_span").html(lami_plenka_total_cost)
lami_priladka = parseInt(lami_priladka)
lami_rab_cost = parseInt(lami_rab_cost)
lami_plenka_total_cost = parseInt(lami_plenka_total_cost)

$("#lami_priladka_span").html(lami_priladka)
//получаем общую стоимость ламинации
var lami_total_cost = lami_priladka + lami_rab_cost + lami_plenka_total_cost
$("#lami_total_cost_span").html(lami_total_cost)
}

//передаем стоимость шатмпа в дебаг
var shtamp =  $("#shtamp").val();
$("#stamp_cost_span").html(shtamp)
//указываем новый он или нет
if($("#shtamp_include").prop("checked"))
{$('#stamp_new_span').html("новый");}
else
{$('#stamp_new_span').html("старый");}

//считаем стоимость вырубки
var virubka_udarov = tiraj / izdely_na_list
virubka_udarov = virubka_udarov.toFixed(0)
$("#virubka_udarov_span").html(virubka_udarov)
$("#virubka_priladka_span").html(tigel_pril)
$("#virubka_tarif_span").html(tigel_udar_cost)

virubka_udarov = 1*virubka_udarov
tigel_udar_cost = 1*tigel_udar_cost
tigel_pril = 1*tigel_pril
var virubka_tarif_total = virubka_udarov * tigel_udar_cost
virubka_tarif_total = virubka_tarif_total.toFixed(0)
virubka_tarif_total = virubka_tarif_total*1
var virubka_total_cost = virubka_tarif_total + tigel_pril
virubka_total_cost = virubka_total_cost.toFixed(0)
$("#virubka_total_cost_span").html(virubka_total_cost)
$("#virubka_tarif_total_span").html(virubka_tarif_total)

//расчет тиснение
if($("#tisnenie").prop("checked") || $("#kongrev").prop("checked"))  {
//считаем тиснение
if($("#tisnenie").prop("checked")){var type_of_tisn = "тиснение"; var type_of_tisn_var = "tisn";}
if($("#kongrev").prop("checked")){var type_of_tisn = "конгрев"; var type_of_tisn_var = "kongr";}
//объявляем переменнуюб когда тиснение на одной стороне, она умножает тираж на эту цифру и мы получаем количество ударов
var K_udarov = "1"
$('#type_of_tisn_span').html(type_of_tisn);
var shirina_tisn_1 = $("#shirina_tisn_1").val();
var vysota_tisn_1 = $("#vysota_tisn_1").val();
$('#tisn_shirina_1_span').html(shirina_tisn_1);
$('#tisn_vysota_1_span').html(vysota_tisn_1);
tisn_ploshad_1  = shirina_tisn_1 * vysota_tisn_1;
$('#tisn_ploshad_1_span').html(tisn_ploshad_1);

//если у нас только одна сторона, площадь второй стороны все равно нужно определить, иначе будет баг
var tisn_ploshad_2 = "0"
tisn_ploshad_2 = parseInt(tisn_ploshad_2)
if($("#tisn_two_side").prop("checked"))  {
K_udarov = "2"
var shirina_tisn_2 = $("#shirina_tisn_2").val();
var vysota_tisn_2 = $("#vysota_tisn_2").val();
$('#tisn_shirina_2_span').html(shirina_tisn_2);
$('#tisn_vysota_2_span').html(vysota_tisn_2);
tisn_ploshad_2  = shirina_tisn_2 * vysota_tisn_2;
$('#tisn_ploshad_2_span').html(tisn_ploshad_2);
 }

if($("#tisn_sides_diff").prop("checked"))  {
var tisn_klishe_cost = (tisn_ploshad_1 + tisn_ploshad_2) * cost_klishe
$('#tisn_ottiski_dif_span').html("разные");
}
else {
var tisn_klishe_cost = tisn_ploshad_1 * cost_klishe
$('#tisn_ottiski_dif_span').html("одинаковые");
}

tisn_udarov = tiraj * K_udarov
$('#tisn_udarov_span').html(tisn_udarov);
$('#tisn_tarif_span').html(tisn_udar);
$('#tisn_folga_cost_span').html(cost_folga);
tisn_pril = parseInt(tisn_pril)

$('#tisn_priladka_cost_span').html(tisn_pril);

if (type_of_tisn_var == "tisn") {
var tisn_folga_total = (tisn_ploshad_1 + tisn_ploshad_2) * tisn_udarov * pogr_other
tisn_folga_total = tisn_folga_total.toFixed("0")
$('#tisn_folga_total_span').html(tisn_folga_total);
var tisn_folga_total_cost = tisn_folga_total * cost_folga
tisn_folga_total_cost = tisn_folga_total_cost.toFixed("0")
$('#tisn_folga_total_cost_span').html(tisn_folga_total_cost);

}else {
tisn_folga_total_cost = "0";

}
tisn_folga_total_cost = parseInt(tisn_folga_total_cost)
$('#tisn_klishe_cost_span').html(tisn_klishe_cost);
tisn_klishe_cost = parseInt(tisn_klishe_cost)

var tisn_rabota_cost =  tisn_udarov * tisn_udar + tisn_pril
$('#tisn_rabota_cost_span').html(tisn_rabota_cost);


var tisn_total_cost = tisn_klishe_cost + tisn_folga_total_cost + tisn_rabota_cost
$('#tisn_total_cost_span').html(tisn_total_cost);

}

//расчет шелкографии
var shelkograf = $("#shelkograf").val();
shelkograf = shelkograf.split(";")
shelk_text =  shelkograf["1"];
if (shelk_text != "0") {
shelk_prokatka_cena =  shelkograf["1"];
shelk_prokatka_cena = parseInt(shelk_prokatka_cena)
$("#shelk_span").show();
$('#shelk_priladka_span').html(shelk_priladka);
shelk_priladka = parseInt (shelk_priladka)
$('#shelk_prokatka_span').html(shelk_prokatka_cena);
$('#shelk_skolko_prokatok_span').html(tiraj);
var shelk_cost_prokatok = tiraj * shelk_prokatka_cena
$('#shelk_cost_prokatok_span').html(shelk_cost_prokatok);
shelk_total_cost =  shelk_priladka + shelk_cost_prokatok
$('#shelk_total_cost_span').html(shelk_total_cost);
} else {$("#shelk_span").hide(); }


//расчет УФ лака
var uf = $("#uf").val();
uf = uf.split(";")
uf_text =  uf["1"];
if (uf_text != "0") {
uf_prokatka_cena =  uf["1"];
uf_prokatka_cena = parseInt(uf_prokatka_cena)
$("#uf_span").show();
$('#uf_priladka_span').html(uf_priladka);
uf_priladka = parseInt (uf_priladka)
$('#uf_prokatka_span').html(uf_prokatka_cena);
$('#uf_skolko_prokatok_span').html(tiraj);
var uf_cost_prokatok = tiraj * uf_prokatka_cena
$('#uf_cost_prokatok_span').html(uf_cost_prokatok);
uf_total_cost =  uf_priladka + uf_cost_prokatok
$('#uf_total_cost_span').html(uf_total_cost);
}  else {$("#uf_span").hide(); }


//расчет ручек
var ruchki = $("#ruchki").val();
ruchki = ruchki.split(";")
ruchki_text =  ruchki["0"];
ruchki_price =  ruchki["1"];
ruchki_type =  ruchki["2"];

var kolvo_ruchek = tiraj * 2
kolvo_ruchek = parseInt(kolvo_ruchek)
var kolvo_ruchek_s_pogr =  kolvo_ruchek * pogr_other

if (ruchki_price != "0") {

$('#kolvo_ruchek_span').html(kolvo_ruchek);
var kolvo_ruchek_s_pogr =  kolvo_ruchek * pogr_other
$('#kolvo_ruchek_s_pogr_span').html(kolvo_ruchek_s_pogr);

if (ruchki_type == "notfixed") {
//определяем длину ручки
var dlina_ruchki = parseInt($('#dlina_ruchki').val());
$('#dlina_ruchki_span').html(dlina_ruchki);
var vsego_metrov = (dlina_ruchki * kolvo_ruchek_s_pogr) / 100
vsego_metrov = parseInt(vsego_metrov)
$('#vsego_metrov_span').html(vsego_metrov);
$('#ruchki_meter_cost_span').html(ruchki_price);
var ruchki_total_cost =  vsego_metrov * ruchki_price
$('#ruchki_total_cost_span').html(ruchki_total_cost);
}

//если в селекте установлен параметр fixed, то считаем ручки по фиксированной цене, за 1 шт
if (ruchki_type == "fixed") {

$('#ruchki_fixed_price_span').html(ruchki_price);
//колво ручек делим на 2, т.к. ручки по фикс цене продаются покомплектно
var ruchki_total_cost =  kolvo_ruchek_s_pogr / 2 * ruchki_price
$('#ruchki_total_cost_span').html(ruchki_total_cost);

}else{$('#ruchki_fixed_price_span').html("");}
}

//если пользователь кликнул и хочет ввести свою цену на ручки за метр
if($("#ruch_ne_podhodit").prop("checked")) {
ruchki_text = "свой вариант";

$('#kolvo_ruchek_span').html(kolvo_ruchek);
var kolvo_ruchek_s_pogr =  kolvo_ruchek * pogr_other
$('#kolvo_ruchek_s_pogr_span').html(kolvo_ruchek_s_pogr);
var dlina_ruchki = parseInt($('#dlina_ruchki').val());
$('#dlina_ruchki_span').html(dlina_ruchki);
var vsego_metrov = (dlina_ruchki * kolvo_ruchek_s_pogr) / 100
vsego_metrov = parseInt(vsego_metrov)
$('#vsego_metrov_span').html(vsego_metrov);
//добываем переменную из поля своя цена за метр
var cena_za_metr = parseInt($('#cena_za_metr').val());
$('#ruchki_meter_cost_span').html(cena_za_metr);
var ruchki_total_cost =  vsego_metrov * cena_za_metr
$('#ruchki_total_cost_span').html(ruchki_total_cost);
}
$('#ruchki_text_span').html(ruchki_text);

//считаем пикколо
var piccolo = $("#piccolo").val();
piccolo = piccolo.split(";")
piccolo_text =  piccolo["0"];
piccolo_cost_4 =  piccolo["1"];
piccolo_job_cost =  piccolo["2"];

if (piccolo_cost_4 != "0") {

//если выбрано, показываем в дебаге
$("#piccolo_span").show();
$('#piccolo_text_span').html(piccolo_text);

//цена за комплект из 4 шт
$('#piccolo_cost_4_span').html(piccolo_cost_4);
piccolo_tiraj = 4 * tiraj
piccolo_tiraj_s_pogr = piccolo_tiraj * pogr_other
piccolo_tiraj = piccolo_tiraj.toFixed("0")
piccolo_tiraj_s_pogr = piccolo_tiraj_s_pogr.toFixed("0")

$('#piccolo_tiraj_span').html(piccolo_tiraj);
$('#piccolo_tiraj_s_pogr_span').html(piccolo_tiraj_s_pogr);

//piccolo_quantity = piccolo_tiraj_s_pogr * 4
//$('#piccolo_quantity_span ').html(piccolo_quantity);
piccolo_cost_all =  piccolo_tiraj_s_pogr * piccolo_cost_4 / 4
$('#piccolo_cost_all_span ').html(piccolo_cost_all);

piccolo_job_cost =  piccolo_tiraj * piccolo_job_cost
$('#piccolo_job_cost_span ').html(piccolo_job_cost);

piccolo_total_cost =  piccolo_cost_all + piccolo_job_cost
$('#piccolo_total_cost_span ').html(piccolo_total_cost);

}  else {$("#piccolo_span").hide(); }

//считаем клеевые материалы

//получаем значения radio по выбранным этапам
var glue_truba = $(":radio[name=glue_truba]").filter(":checked").val();
//var glue_truba = $("#glue_truba").val();
glue_truba = glue_truba.split(";")
truba_text =  glue_truba["0"];
$('#truba_text_span').html(truba_text);
truba_type_glue =  glue_truba["1"];
truba_glue_cost_meter =  glue_truba["2"];
$('#truba_glue_cost_meter_span').html(truba_glue_cost_meter);

//если пакет из двух листов, умножаем метры скотча на два
if ($("#is_skolkih_listov_paket_2").prop("checked")) {var iz_listov = "2"}else{var iz_listov = "1"}

//для упрощения, берем переменную razvorot_heightс самого начала
//не забываем про погрешность pogr_other
var truba_meters_gluing = razvorot_height / 100 * iz_listov * pogr_other
truba_meters_gluing = truba_meters_gluing.toFixed(2)
$('#truba_meters_gluing_span').html(truba_meters_gluing);
//вычисляем стоимость клеевого материала на трубу
var truba_glue_cost_total = truba_meters_gluing * truba_glue_cost_meter
truba_glue_cost_total = truba_glue_cost_total.toFixed(2)
$('#truba_glue_cost_total_span').html(truba_glue_cost_total);




var glue_dno = $(":radio[name=glue_dno]").filter(":checked").val();
glue_dno = glue_dno.split(";")
dno_text =  glue_dno["0"];
$('#dno_text_span').html(dno_text);
dno_type_glue =  glue_dno["1"];
dno_glue_cost_meter =  glue_dno["2"];
$('#dno_glue_cost_meter_span').html(dno_glue_cost_meter);
//вычисляем донный подгиб
sq_side = 0.75*bok
//донный подгиб в квадрате
sq_side = sq_side * sq_side
//теорема пифагора в действии
var katet = Math.sqrt(2*sq_side)
katet = katet.toFixed(0)
shirina = 1 * shirina
var dno_meters_gluing = ((4 * katet) + shirina - (bok * 1.5)) / 100 * pogr_other
dno_meters_gluing = dno_meters_gluing.toFixed(2)
$('#dno_meters_gluing_span').html(dno_meters_gluing);
//вычисляем стоимость клеевого материала на трубу
var dno_glue_cost_total = dno_meters_gluing * dno_glue_cost_meter
dno_glue_cost_total = dno_glue_cost_total.toFixed(2)
$('#dno_glue_cost_total_span').html(dno_glue_cost_total);

//расчет клея на дно
var glue_ukr = $(":radio[name=glue_ukr]").filter(":checked").val();
glue_ukr = glue_ukr.split(";")
ukreplenie_text =  glue_ukr["0"];
$('#ukreplenie_text_span').html(ukreplenie_text);
ukreplenie_type_glue =  glue_ukr["1"];
ukreplenie_glue_cost_meter =  glue_ukr["2"];
$('#ukreplenie_glue_cost_meter_span').html(ukreplenie_glue_cost_meter);
//допустим, на укрепление боковины используется скотча 0,75 от ширины пакета, т.е. полторы ширины пакета
var ukreplenie_meters_gluing = shirina * 0.015 * pogr_other
ukreplenie_meters_gluing = ukreplenie_meters_gluing.toFixed(2)
$('#ukreplenie_meters_gluing_span').html(ukreplenie_meters_gluing);
var ukreplenie_glue_cost_total = ukreplenie_meters_gluing * ukreplenie_glue_cost_meter
ukreplenie_glue_cost_total = ukreplenie_glue_cost_total.toFixed(2)
$('#ukreplenie_glue_cost_total_span').html(ukreplenie_glue_cost_total);

//вычисляем общую стоимость клеевых материалов
truba_glue_cost_total = truba_glue_cost_total*1
dno_glue_cost_total = dno_glue_cost_total*1
ukreplenie_glue_cost_total = ukreplenie_glue_cost_total*1
var total_gluing_material_cost = (truba_glue_cost_total + dno_glue_cost_total + ukreplenie_glue_cost_total) * tiraj
total_gluing_material_cost = total_gluing_material_cost.toFixed(2)
$('#total_gluing_material_cost_span').html(total_gluing_material_cost);

/*
Существует 2вида задействования клеящих материалов
1. только двухсторонний скотч (когда линия не выбрана)

2. смешанный когда выбрана одна или все из операций на линии выбрана
в этом случае из первоначального двухстороннего скотча, вычитаем те операции, которые
клеятся клеем, но оставляем скотч для проклейки боковин

Скотч считается по формуле

труба = высота пакета + клапан + бок * 0,75

var truba_meters_gluing = vysota + klapan + bok * 0.75

shirina
vysota
bok
klapan

боковины = ширина пакета * 0,75
 */

//картон укрепление
var ploshad_dna = shirina * bok
if ($("#ukrepl_dno").prop("checked")) {
$('#dno_ukrepl_span').html("да");
$('#ploshad_dna_span').html(ploshad_dna);
}
else {
$('#dno_ukrepl_span').html("нет");
var ploshad_dna = "0";
ploshad_dna = ploshad_dna*1
$('#ploshad_dna_span').html("");
}

if ($("#ukrepl_bok").prop("checked")) {

$('#bok_ukrepl_span').html("да");
var ploshad_bokovin = shirina * podvorot * 2
$('#ploshad_bokovin_span').html(ploshad_bokovin);
}
else {$('#bok_ukrepl_span').html("нет");
var ploshad_bokovin = "0";
ploshad_bokovin = ploshad_bokovin*1
$('#ploshad_bokovin_span').html("");
}

if ($("#ukrepl_dno").prop("checked") || $("#ukrepl_bok").prop("checked")) {
$("#ukreplenie_span").show();
var obshaya_ploshad_ukrepl = ploshad_dna + ploshad_bokovin
var obshaya_ploshad_total = obshaya_ploshad_ukrepl * tiraj / 10000
var weight_karton_ukrepl = obshaya_ploshad_total * karton_ukrepl_grammaj / 1000
weight_karton_ukrepl = weight_karton_ukrepl.toFixed(0)
var weight_karton_ukrepl_span_s_pogr = weight_karton_ukrepl * pogr_bumaga
weight_karton_ukrepl_span_s_pogr = weight_karton_ukrepl_span_s_pogr.toFixed(0)
var karton_ukrepl_total_cost = weight_karton_ukrepl_span_s_pogr * karton_ukrepl_cost

$('#obshaya_ploshad_ukrepl_span').html(obshaya_ploshad_ukrepl);
$('#obshaya_ploshad_total_span').html(obshaya_ploshad_total);
$('#weight_karton_ukrepl_span').html(weight_karton_ukrepl);
$('#weight_karton_ukrepl_span_s_pogr_span').html(weight_karton_ukrepl_span_s_pogr);
$('#karton_ukrepl_total_cost_span').html(karton_ukrepl_total_cost);

}else {$("#ukreplenie_span").hide();}

if ($("#print_on_dne").prop("checked")) {
$("#zapechatka_dna_span").show();
$('#karton_ukrepl_zapechatka_span').html("да");
var ploshad_lista_a2 = "7488"
var dno_on_a2 = ploshad_lista_a2 / ploshad_dna
dno_on_a2 = dno_on_a2.toFixed(0)
var kolvo_prokatok_pechat_dno = tiraj / dno_on_a2
kolvo_prokatok_pechat_dno = kolvo_prokatok_pechat_dno.toFixed(0)
priladka_pechat_dno_cost = priladka_pechat_dno_cost*1
var pechat_dno_cost = priladka_pechat_dno_cost + prokatka_pechat_dno_cost * kolvo_prokatok_pechat_dno
//pechat_dno_cost = pechat_dno_cost.toFixed(0)
$('#dno_on_a2_span').html(dno_on_a2);
$('#kolvo_prokatok_pechat_dno_span').html(kolvo_prokatok_pechat_dno);
$('#priladka_pechat_dno_cost_span').html(priladka_pechat_dno_cost);
$('#pechat_dno_cost_span').html(pechat_dno_cost);
}
else {
$('#karton_ukrepl_zapechatka_span').html("нет");
$("#zapechatka_dna_span").hide();
}


/* <span id=ukreplenie_span style="display: none">
<br /> <b>Картон на укрепление с возможной запечаткой:</b><br />
укрепление дна: <span id=dno_ukrepl_span class=debug_res></span><br>
площадь дна: <span id=ploshad_dna_span class=debug_res></span> см 2<br>
укрепление боковин: <span id=bok_ukrepl_span class=debug_res></span><br>
площадь 2 боковин: <span id=ploshad_bokovin_span class=debug_res></span> см 2<br>
общая площадь укрепления:   <span id=obshaya_ploshad_ukrepl_span class=debug_res></span> см 2
общая площадь всех укреплений:   <span id=obshaya_ploshad_total_span class=debug_res></span> м2<br>
общий вес картона для укрепления:   <span id=weight_karton_ukrepl_span class=debug_res></span> тн &nbsp; &nbsp; &nbsp;
с погрешностью:  <span id=weight_karton_ukrepl_span_s_pogr_span class=debug_res></span> тн<br>
стоимость картона для укрепления:  <span id=karton_ukrepl_total_cost_span class=debug_res_vip></span> р.<br>
</span>
<span id=zapechatka_dna_span style="display: none">
с запечаткой?   <span id=karton_ukrepl_zapechatka_span class=debug_res></span><br>
количество донышек на 1 лист А2:   <span id=dno_on_a2_span class=debug_res></span><br>
количество прокаток на печать дна:   <span id=kolvo_prokatok_pechat_dno_span class=debug_res></span><br>
стоимость приладки А2 на печать дна:   <span id=priladka_pechat_dno_cost_span class=debug_res></span><br>
общая стоимость на печати на дне:   <span id=pechat_dno_cost_span class=debug_res_vip></span><br>
</span> */


//считаем стоимость сборки
cena_sborki_tarif = $("#cena_sborki").val()

//все таки разделить что получает автомат, а что ручники

//считаем допрасходы


//считаем упаковку

//считаем стоимость штампа



}

function ochistit(){

$("#pechat_A2").prop('disabled', true)
$("#pechat_A1").prop('disabled', true)
}

</script>

<table width="" border="0" cellpadding="1" cellspacing="1">
<tr>
<td>
<b>Срок исполнения:</b>
<input checked type=radio id="standartny" name="srok" value="standartny"><label for="standartny">стандартный</label>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=radio id="srochny" name="srok" value="srochny"><label for="srochny">срочный</label>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=radio id="supersrochny" name="srok" value="supersrochny"><label for="supersrochny">суперсрочный</label>
</td>
<td>
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="Расчет!" onclick="make_calc()"/>  <input type="reset" onclick="ochistit()" value="Очистить"/></td>
</tr>
</table>

    </form>
       <br />
<table width="900" border="0" cellpadding="1" cellspacing="1" align=center>
<tr>
<td>
Общая себестоимость: <span id=ss>110 000,00</span> руб. или <span id=ss>58,00</span> руб. за пакет<br />
</td>
<td>
</td>
</tr>

<tr>
<td>
Вся партия клиенту: <span id=ss>160 000,00</span> руб. или <span id=ss>78,00</span> руб. за пакет<br />
</td>
<td>Наценка: 1,5
</td>
</tr>

 <tr>
<td>
Скидка: <input type="text" name=skidka id=skidka size=2/>% &mdash; С учетом скидки партия клиенту: <span id=ss>160 000,00</span> руб. или <span id=ss>78,00</span> руб. за пакет<br />
</td>
<td>
</td>
</tr>


<tr>
<td>
<a href="#" onclick="show_smeta()" id=smeta_span>Смета</a> / <a href="#" onclick="show_debug_smeta()" id=debug_smeta_span>Дебаг</a><br />
<script>
function show_smeta() {
$("#smeta").show();
$("#debug_smeta").hide();

$("#debug_smeta_span").html('Дебаг');
$("#smeta_span").html('<b>Смета</b>');

}
function show_debug_smeta() {
$("#debug_smeta").show();
$("#smeta_span").html('Смета');
$("#debug_smeta_span").html('<b>Дебаг</b>');
$("#smeta").hide();
}
</script>

<div id=debug_smeta style="display: block">
<b>Бумага:</b><br />
Граммаж бумаги м2: <span id=grammaj_bum class=debug_res></span> гр  <br />
Площадь выбранного листа: <span id=ploshad_lista class=debug_res></span> м2  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; печать: <span id=pechat_text class=debug_res></span> <br />
Вес выбранного листа:   <span id=ves_lista class=debug_res></span> гр<br />
Листов на приладку:   <span id=pechat_priladka_listov class=debug_res></span> шт<br />
Листов на печать без усадки:   <span id=listov_na_pechat_bez_usadki class=debug_res></span> шт  <br />
Листов на печать с усадкой:   <span id=listov_na_pechat_s_usadkoy class=debug_res></span> шт   <br />
Всего листов:  <span id=vsego_listov class=debug_res_vip></span>  шт.&nbsp;&nbsp;&nbsp;&nbsp;
Вес всего материала: <span id=ves_materiala class=debug_res_vip></span> тн<br />
Стоимость материала (бумаги) за тонну: <span id=material_cost_per_tonn class=debug_res></span> р.  &nbsp;&nbsp;&nbsp;&nbsp;
Название бумаги: <span id=name_bum class=debug_res></span>    <br />
Общая стоимость бумаги: <span id=material_cost class=debug_res_vip></span> р. <br />

<br /><b>Печать:</b><br />

Стоимость приладки: <span id=pechat_priladka_cost class=debug_res></span> р. <br />
нужно доприладок <span id=dop_priladki_span class=debug_res></span> их общая стоимость:  <span id=dop_priladki_cost_span class=debug_res></span> р. <br />
Стоимость прокатки:  <span id=pechat_prokatka_cost class=debug_res></span> р.<br />
Стоимость печати с приладкой: <span id=pechat_cost class=debug_res></span> р.<br /><br />

Стоимость печати, ВМЕСТЕ с бумагой: <span id=pechat_cost_total class=debug_res_vip></span> р.<br />


<br /><b>Размеры разворота:</b>
<br />Ширина &mdash; <span id=razvorot_width class=debug_res></span> см x высота &mdash; <span id=razvorot_height class=debug_res></span> см <br />

<span id=lamination_span style="display: none">
<br />
<b>Ламинация:</b><br />
всего листов <span id=pechat_lami_format class=debug_res></span> ламинируется: <span id=lami_listov_span class=debug_res></span> шт,
пленка <span id=lami_name_span class=debug_res></span>: <span id=lami_ploshad_span class=debug_res></span> м2   <br />
по <span id=lami_plenka_cost_span class=debug_res></span> за 1 кв. м2,
итоговая стоимость пленки: <span id=lami_plenka_total_cost_span class=debug_res></span> р.<br />
приладка:   <span id=lami_priladka_span class=debug_res></span> р.       <br />
тариф на ламинацию:  <span id=lami_rab_span class=debug_res></span> р.          <br />
стоимость работы:  <span id=lami_rab_cost_span class=debug_res></span> р. <br />
общая стоимость ламинации: <span id=lami_total_cost_span class=debug_res_vip></span> р.<br /></span>


<span id=virub_span>
<br />
<b>Вырубка:</b><br />
всего ударов <span id=virubka_udarov_span class=debug_res></span><br />
приладка:   <span id=virubka_priladka_span class=debug_res></span> р.<br />
тариф на вырубку:  <span id=virubka_tarif_span class=debug_res></span> р.<br />
стоимость работы:  <span id=virubka_tarif_total_span class=debug_res></span> р.<br />
общая стоимость вырубки: <span id=virubka_total_cost_span class=debug_res_vip></span> р.<br /></span>

<br />
<b>Штамп:</b><br />
штамп новый?  <span id=stamp_new_span class=debug_res></span><br />
стоимость штампа: <span id=stamp_cost_span class=debug_res_vip></span> р.<br />

<span id=tisnenie_span style="display: none">
<br />
<b>Тиснение / конгрев</b><br />
Тип тиснения: <span id=type_of_tisn_span class=debug_res></span><br>
размеры 1 стороны &mdash; <br>
ширина: <span id=tisn_shirina_1_span class=debug_res></span> см2<br>
высота: <span id=tisn_vysota_1_span class=debug_res></span> см2  <br>
площадь тиснения: <span id=tisn_ploshad_1_span class=debug_res></span> см2 <br>
размеры 2 стороны &mdash;<br>
ширина: <span id=tisn_shirina_2_span class=debug_res></span> см2 <br>
высота: <span id=tisn_vysota_2_span class=debug_res></span> см2  <br>
площадь тиснения: <span id=tisn_ploshad_2_span class=debug_res></span> см2 <br>
стороны одинаковые?  <span id=tisn_ottiski_dif_span class=debug_res></span>  <br>
всего ударов:   <span id=tisn_udarov_span class=debug_res></span>   <br>
тариф тиснение:   <span id=tisn_tarif_span class=debug_res></span>     <br>
стоимость фольги:  <span id=tisn_folga_cost_span class=debug_res></span> р/см2,
на тираж нужно <span id=tisn_folga_total_span class=debug_res></span> см2 фольги
и это будет стоить <span id=tisn_folga_total_cost_span class=debug_res></span> р.<br>
стоимость клише:  <span id=tisn_klishe_cost_span class=debug_res></span>       <br>
стоимость приладки:  <span id=tisn_priladka_cost_span class=debug_res></span>      <br>
стоимость работы:  <span id=tisn_rabota_cost_span class=debug_res></span>              <br>
общая стоимость:  <span id=tisn_total_cost_span class=debug_res_vip></span>
<br /></span>


<span id=shelk_span style="display: none"><br />
<b>Шелкография: <span id=shelk_text_span class=debug_res></span></b><br>
приладка: <span id=shelk_priladka_span class=debug_res></span> р.<br>
стоимость прокатки: <span id=shelk_prokatka_span class=debug_res></span> р.<br>
количество прокаток:  <span id=shelk_skolko_prokatok_span class=debug_res></span><br>
стоимость всех прокаток:  <span id=shelk_cost_prokatok_span class=debug_res></span> р.<br>
общая стоимость:   <span id=shelk_total_cost_span class=debug_res_vip></span> р.<br>
</span>

<span id=uf_span style="display: none"><br />
<b>УФ лак: <span id=uf_text_span class=debug_res></span></b><br>
приладка: <span id=uf_priladka_span class=debug_res></span> р.<br>
стоимость прокатки: <span id=uf_prokatka_span class=debug_res></span> р.<br>
количество прокаток:  <span id=uf_skolko_prokatok_span class=debug_res></span><br>
стоимость всех прокаток:  <span id=uf_cost_prokatok_span class=debug_res></span> р.<br>
общая стоимость:   <span id=uf_total_cost_span class=debug_res_vip></span> р.<br>
</span>

<span id=glue_span><br />
<b>Клеевые материалы:</b><br />
<b><i>труба</i></b><br />
тип клеящего материала: <span id=truba_text_span class=debug_res></span><br>
метров на склейку: <span id=truba_meters_gluing_span class=debug_res></span> м.<br>
стоимость метра склейки: <span id=truba_glue_cost_meter_span class=debug_res></span> р.<br>
общая стоимость клеевых материалов на трубу:  <span id=truba_glue_cost_total_span class=debug_res></span><br>
<b><i>дно</i></b><br />
тип клеящего материала: <span id=dno_text_span class=debug_res></span><br>
метров на склейку: <span id=dno_meters_gluing_span class=debug_res></span> м.<br>
стоимость метра склейки: <span id=dno_glue_cost_meter_span class=debug_res></span> р.<br>
общая стоимость клеевых материалов на дно:  <span id=dno_glue_cost_total_span class=debug_res></span><br>
<b><i>укрепление</i></b><br />
тип клеящего материала: <span id=ukreplenie_text_span class=debug_res></span><br>
метров на склейку: <span id=ukreplenie_meters_gluing_span class=debug_res></span> м.<br>
стоимость метра склейки: <span id=ukreplenie_glue_cost_meter_span class=debug_res></span> р.<br>
общая стоимость клеевых материалов на укрепление:  <span id=ukreplenie_glue_cost_total_span class=debug_res></span>
<br />
общая стоимость клеевых материалов на пакет:   <span id=total_gluing_material_cost_span class=debug_res_vip></span> р.<br>
</span>

<span id=ruchki_span><br />
<b>Ручки:</b><br />
тип ручек: <span id=ruchki_text_span class=debug_res></span><br>
длина одной ручки:   <span id=dlina_ruchki_span class=debug_res></span> cм.<br>
количество ручек на тираж:   <span id=kolvo_ruchek_span class=debug_res></span> шт. &nbsp; &nbsp; &nbsp; с погрешностью:  <span id=kolvo_ruchek_s_pogr_span class=debug_res></span> шт<br>
всего метров на тираж:  <span id=vsego_metrov_span class=debug_res></span> м.<br>
стоимость погонного метра:   <span id=ruchki_meter_cost_span class=debug_res></span> р.<br>
ручки по фиксированной цене:  <span id=ruchki_fixed_price_span class=debug_res></span> р.<br>
общая стоимость ручек:   <span id=ruchki_total_cost_span class=debug_res_vip></span> р.<br>
<br /></span>


<span id=piccolo_span style="display: none">
<br /> <b>Пикколо:</b><br />
тип пикколо: <span id=piccolo_text_span class=debug_res></span><br>
стоимость 4 колечек:   <span id=piccolo_cost_4_span class=debug_res></span> р.<br>
количество пикколо на тираж:   <span id=piccolo_tiraj_span class=debug_res></span> шт. &nbsp; &nbsp; &nbsp; с погрешностью:  <span id=piccolo_tiraj_s_pogr_span class=debug_res></span> шт<br>
стоимость пикколо на тираж:  <span id=piccolo_cost_all_span class=debug_res></span> р.<br>
стоимость работы по установке пикколо:   <span id=piccolo_job_cost_span class=debug_res></span> р.<br>
общая стоимость установки пикколо:   <span id=piccolo_total_cost_span class=debug_res_vip></span> р.<br>
</span>

<span id=ukreplenie_span style="display: none">
<br /> <b>Картон на укрепление с возможной запечаткой:</b><br />
укрепление дна: <span id=dno_ukrepl_span class=debug_res></span><br>
площадь дна: <span id=ploshad_dna_span class=debug_res></span> см 2<br>
укрепление боковин: <span id=bok_ukrepl_span class=debug_res></span><br>
площадь 2 боковин: <span id=ploshad_bokovin_span class=debug_res></span> см 2<br>
общая площадь укрепления:   <span id=obshaya_ploshad_ukrepl_span class=debug_res></span> см 2
общая площадь всех укреплений:   <span id=obshaya_ploshad_total_span class=debug_res></span> м2<br>
общий вес картона для укрепления:   <span id=weight_karton_ukrepl_span class=debug_res></span> кг &nbsp; &nbsp; &nbsp;
с погрешностью:  <span id=weight_karton_ukrepl_span_s_pogr_span class=debug_res></span> кг<br>
стоимость картона для укрепления:  <span id=karton_ukrepl_total_cost_span class=debug_res_vip></span> р.<br>
</span>
<span id=zapechatka_dna_span style="display: none">
с запечаткой?   <span id=karton_ukrepl_zapechatka_span class=debug_res></span><br>
количество донышек на 1 лист А2:   <span id=dno_on_a2_span class=debug_res></span><br>
количество прокаток на печать дна:   <span id=kolvo_prokatok_pechat_dno_span class=debug_res></span><br>
стоимость приладки А2 на печать дна:   <span id=priladka_pechat_dno_cost_span class=debug_res></span> р.<br>
общая стоимость на печати на дне:   <span id=pechat_dno_cost_span class=debug_res_vip></span> р.<br>
</span>



</div>

<div id=smeta style="display: none">
Материал: <span id=mater>123</span>р.   <br />
Печать:	<span id=pechat>123</span>р.   <br />
Шелкография: <span id=shelkograf>123</span>р.  <br />
Ламинация (пленка):	<span id=lami>123</span>р.  <br />
Ламинирование (раб):	<span id=mater>123</span>р.  <br />
УФ лак:	<span id=uf>123</span>р.        <br />
Фольга:	<span id=folga>123</span>р.      <br />
Клише тиснение:	<span id=klishe>123</span>р.    <br />
Удары тиснение:	<span id=udary_tisn>123</span>р. <br />
Штамп:	<span id=shtamp>123</span>р.        <br />
Вырубка:	<span id=vyrubka>123</span>р. <br />
Картон укрепл: <span id=karton_ukrepl>123</span>р.   <br />
Скотч:	<span id=tape>123</span>р.     <br />
Пикало:	<span id=piccolo>123</span>р.    <br />
Ручки:	<span id=ruchki>123</span>р.  <br />
Сборка:	<span id=sborka>123</span>р.    <br />
Упаковка:	<span id=upak>123</span>р.  <br />
Дополнительные расходы:	<span id=dop>123</span>р.   <br />
Транспорт:	<span id=transport>123</span>р.        <br />
Накладные расходы:	<span id=nakladnie>123</span>р.   <br />
</div>
</td>
<td>
</td>
</tr>
</table>
</td>
</tr></table>
</td></tr></table>
</body>
</html>
