<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //���� � �������
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
<!-- �����

������� ������ ������

(������ ������ + ��� * 0,7 + ��������) * ���������� ������ ������

��������� ���� ������������� ����� �� ������, ����� �������� ������������� ���� �������� ������, �������� �������� �����, � ����� ����������� ���������� ������, �� ������� ���������� �����. ��� ������������� ������������ ����� ������ ���� ����� �������.



<input type=radio id="" name="" value=""><label for=""></label>

������ ������������ ���� http://divosite.com/plavnoe-izmenenie-cveta-fona-ili-bloka-s-hover-effektom/


//������ ����������

���������� ��� ��������

(������� ��� + ������� �������) * ������� * ����� = ���
��� * ��������� �������

��������� ��� ��������� �� �������� ����� ������� �2 ������
���������� �������� = ����� (����� ������� �2 ����� / ������� ���)
��������� ������ = �������� �2 + �������� �2 ���� * ���������� ��������



+++ ����� ��������

+++ ������������ ������ �� ������



+++ ������� ������ / ����������

- ���� ������� ������, �� � ������� ��������� ������, ����������� ��� ���� �������� �� ������� ���������



+++ ����������� ������� � �������� ������ 10 000 ������



//������� ��������� ������

�������� ��������� ����� �� �����


//������� ����������

= ���� ����������

//������� ��������

��������� ������� �������� * �����

//������� ��������� ������

= ���� ����� (���� ���!)

//������������ �������

= ���� 2 ����� + ����� / 1000 * ������������ �� 1000 �������

//��������� �������

����� * ��������������



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
	var user_id	=	<?=$user_id?>;					// �� ������������
	var tpacc		=	<?=$tpacc?>;				// ��� ������������
	var reqsl_user_id = <?=(($tpacc)?0:$user_id)?>;	// �� ������������ ��� ������ ��������
	var ed_us_id = <?=((@$ed_us_id)?$ed_us_id:$user_id)?>;	// �� ������������ �������

	// ����� ������ ��� ��������������
	var edit = <?=($op=='edit') ? $_GET['show'] : "'new'"?>;

	var curr_date = '<?=date("d.m.Y")?>';			// ������� ���� � ������� '01.05.2007'
	var user_full_name = '<?=$full_name?>';
	var	req_fl_hd = 0;

// �������������� ������, ������� � ������ ��������� � �������� �������
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
<td>�����:</td>
<td><input onkeyup="this.value = replace_num(this.value, 'celoe');" type="text" name=tiraj id=tiraj  size=8 value="1000" />

<input type="checkbox" name="line" id="line" onclick="on_line()" onchange="sborka_raschet()"/>
<label for="line">�� �����</label>
<span id=line_options style="opacity: 0.2;">
<input disabled onchange="sborka_raschet()" onclick="glue_set_up()" type="checkbox" name="line_truba" id="line_truba"/> <label for="line_truba">�����</label>
<input disabled onchange="sborka_raschet()" onclick="glue_set_up()" type="checkbox" name="line_dno" id="line_dno"/> <label for="line_dno">���</label>
</span>
<script>

//�������� ����� � ������� �� ����� � ����������� �� ���� ����
function replace_num(v,type) {

//���� ���� ��������� ������� �����
if (type == "drob"){
v = v.replace(',', '.');
var reg_sp = /^\s*|\s*$/g;
var reg_sp1 = /[^.\d]*/g;
}
//���� ���� ��������� ������ ����� �����
if (type == "celoe"){
var reg_sp = /^\s*|\s*$/g;
var reg_sp1 = /[^\d]*/g;
}
//���������, ������� ����������� � ��������
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

//������ ��������, ������� ����� ������ ��� ������������

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

//����������� � ���� �������� �� ���� ��� �� �����, � ����������� �� ���������� ���������. �������� ����� �� value
function price_per_what(){

var other_material_value = $(":radio[name=other_material_per]").filter(":checked").val();
$("#price_per_what_span").html(other_material_value)

}
</script>

</td>
<td>��������:</td>
<td width=250><select name=mater id=mater onchange="other_material()">
<option value="none">���</option>
<option value="other">�� ���� �� ������ ����</option>
<option value="������� 170 ��;170;60000" selected>������� 170 ��</option>
<option value="������� 200 ��;200;60000">������� 200 ��</option>
<option value="������� 250 ��;250;60000">������� 250 ��</option>
<option value="������� 300 ��;300;60000">������� 300 ��</option>
<option value="����� (���)������ 135 ��;135;42500">����� (���)������ 135 ��</option>
<option value="����� ������ 140 �� (��� ������);140;42500">����� ������ 140 �� (��� ������)</option>
<option value="����� (���) 120 ��;120;250000">����� (���) 120 ��</option>
<option value="�������,������ 125 ��;125;300000">�������,������ 125 ��</option>
<option value="��� �����, 210 ��;210;67000">��� �����, 210 ��</option>
<option value="����� �� �����, 120 ��;120;57000">����� �� �����, 120 ��</option>
<option value="������� ����� 90��;90;67000">������� ����� 90��</option>
<option value="������� ����� 100��;90;67000">������� ����� 100��</option>
<option value="������� ����� 110��;90;67000">������� ����� 110��</option>
<option value="������� ����� 120��;90;67000">������� ����� 120��</option>
<option value="����� 140 ��;140;480000">����� 140 ��</option>
</select>
<br />
<span id=select_other_material style="opacity: 0.2;">
<b>� ���� ���� ���� ��:</b><br />
<input disabled type=radio id="per_list" name="other_material_per" value="�� ����" onclick="price_per_what()">
<label for="per_list">�� ����</label>
<input disabled type=radio id="per_tonn" name="other_material_per" value="�� �����" onclick="price_per_what()">
<label for="per_tonn">�� �����</label>
<br />���� �� <span id=price_per_what_span></span>: <input disabled type=text size=5 name=price_other_material id="price_other_material"/> �. <input disabled type=text size=5 name=grammaj_other_material id="grammaj_other_material"/> ��.
</span>


</td>
</tr>
<tr>
<td colspan=2>������: <input onkeyup="this.value = replace_num(this.value, 'drob');" type="text" name=shirina id=shirina onchange="sborka_raschet()" size=4 value="25"/> &nbsp;&nbsp;&nbsp;&nbsp;
������: <input onkeyup="this.value = replace_num(this.value, 'drob');" type="text" name=vysota id=vysota onchange="sborka_raschet()" size=4 value="36"/> &nbsp;&nbsp;&nbsp;&nbsp;
���: <input onkeyup="this.value = replace_num(this.value, 'drob');" type="text" name=bok id=bok size=4 value="10" onchange="sborka_raschet()"/>&nbsp;&nbsp;&nbsp;&nbsp;
��������: <input onkeyup="this.value = replace_num(this.value, 'drob');" type="text" name=podvorot id=podvorot onchange="sborka_raschet()" size=2 min=0 max=20 value="5"/>
<br />
<span id=razvorot_top style="opacity: 0; width: 150px;">
��������: <span id=razvorot_width_top></span> � <span id=razvorot_height_top></span> �� &nbsp;&nbsp;&nbsp;&nbsp;
<a href="#" onclick="optimizi_razvorot()"><img src="../../i/icons/optimize.gif" width="15" height="15" alt="" valign=middle/></a>
</span>
<!-- ���������� ������� ��������� � ����������, ��� �� �� ����������� ����������� ��� �������� ������. ��� �������� ����� ����� �� ������� make_calc -->
<input type=hidden value="" id="razvorot_width_top_hidden" />
<input type=hidden value="" id="razvorot_height_top_hidden" />
</td>
<td colspan=2>��������� ������: <input type="checkbox" name="shtamp_include_cost" id="shtamp_include" onclick="highlight_shtamp()"/>
<label for="shtamp_include">�������� � ���������</label>
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

//�������� ������ ���������
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


//������ ���������
var razvorot_width = (shirina * 2) + (bok * 2) + klapan;
razvorot_width = razvorot_width.toFixed(2)
//������ ���������
var razvorot_height  =  vysota + podvorot + bok * 0.75;
razvorot_height = razvorot_height.toFixed(2)

$('#razvorot_top').animate({opacity: "1"}, 300);
$("#razvorot_width_top").html(razvorot_width)
$("#razvorot_height_top").html(razvorot_height)
//����� ��� �������� ����� � ���������� ����, ����� �� ������ ������� �� ������������ ����� ����

$("#razvorot_width_top_hidden").val(razvorot_width)
$("#razvorot_height_top_hidden").val(razvorot_height)


//���������, ���� �������� �� ��������� � ��������� �������� ������, �� ������������� ��������� ������ �� ���� ������ � ���������� ������� ���������� ���������
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

//������� ��������� � ����������� ������� ������, ���� ������ ���������
//���������� ������� �����
var format_pechati_other_width = $("#format_pechati_other_width").val()
format_pechati_other_width = format_pechati_other_width*1
var format_pechati_other_height = $("#format_pechati_other_height").val()
format_pechati_other_height = format_pechati_other_height*1

var ploshad_lista = format_pechati_other_width * format_pechati_other_height / 10000;
ploshad_lista = ploshad_lista.toFixed(3)
$("#ploshad_lista_inp").val(ploshad_lista)

//������ ��������� ��� ������ � ����������� �� ������� ��������� �����
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

//���������� ������� � ������� ������� ���������
var size_max_A = Math.max(razvorot_width,razvorot_height)
var size_min_B = Math.min(razvorot_width,razvorot_height)

/* ���� ������� ������� ��������� � 72 ��� 104 � ���� ������� ������� ��������� 52 ��� 72 �� �� 1 ����� */
if (size_max_A > size_max_A_lista || size_min_B > size_min_B_lista)
{
$("#iz_2_lista").css("border-bottom","2px dotted");
$("#iz_1_lista").css("border-bottom","none");
}else{
$("#iz_1_lista").css("border-bottom","2px dotted");
$("#iz_2_lista").css("border-bottom","none");
}

//���� ������� ����� �� ������ ������ 72 ��,&nbsp;�� ������ 45, �� ����� - ������� � ������� ���������������
if (shirina < "72" && vysota < "72") {var tarif_sborka = '3.5'; var dobavka = '1'}
if (shirina < "60" && vysota < "60") {var tarif_sborka = '3.0'; var dobavka = '0.8'}
if (shirina < "50" && vysota < "50") {var tarif_sborka = '2.8'; var dobavka = '0.7'}
if (shirina < "40" && vysota < "40") {var tarif_sborka = '2.6'; var dobavka = '0.6'}
if (shirina < "20" && vysota < "25") {var tarif_sborka = '2.3'; var dobavka = '0.2'}

//���� ����� ������� ��� ����� �� ���� ������, �� ��������� ��� ������� �� ������ � ���������� ����, ��� ����� ������� ������ ����� ��� ����������
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
//���������� ������������� ������ �� ������������ ����� � ���
var truba = 0.4
var dno = 0.27
//������ � ����������� ����� � ������ ������
var truba_ruchnaya = 0.4
var dno_ruchnoe = 0.4
//��������� ����������, ������� ����� ����� ������������ ��� ������ �� �������� ������, � ��� ������, ���� ������������ �����
var vychet_truba = 0
var vychet_dno = 0

//���������� ������� �������� �� �������� ������ � ������� � ����� ��������� � ������ ������ ������ ����� �� �����
if ($("#line_truba").prop("checked"))
{vychet_truba = tarif_sborka * k_line_truba
vychet_truba = vychet_truba.toFixed(2)}
if ($("#line_dno").prop("checked"))
{vychet_dno = tarif_sborka * k_line_dno
vychet_dno = vychet_dno.toFixed(2)}




if ($("#line_truba").prop("checked") || $("#line_dno").prop("checked")) {
//�������� �� ������� ���� ������ ���� ������� �����
tarif_sborka = tarif_sborka - vychet_truba - vychet_dno
tarif_sborka = tarif_sborka.toFixed(2)

}
}
}

/*
������ �� ������ ������ ������� �� ��������� ������, ������ �� ������� �������� ��������� ������� �� ������ ������

������� ����� � ������� �������� - 0,5

������� ��� � ��������� ��� - 0,3

�������� - 0,2

� ������ ���� ����� ��������� ���������� ������� �� ����� ����������� 100%

� ������ ���� �������� ���������� �� �����

= ������� ����� - ������� ����� * � - ������� * �

�/� ������ ������ ��� ���� = ��������� ������ � ���� (���� ����) + ��������� ������ �� ����� (���� ����) + �������������� �������� + �������������� ���������� ��-��

��������� ������ � ���� (���� ����) = � ����� * ������� ����� + � ��� * ������� �����
��������� ������ �� ����� (���� ����) = ����� ����� + ����� ���

�������������� �������� = ���������

�������������� ���������� ��-�� = ���������
*/
//������ ���� ������ ������� �����

//������ ���� ������ ������� ���

//������ ���� ������ ����� �����

//������ ���� ������ ����� ���

//������ ���� ������ �����

$("#cena_sborki").val(tarif_sborka)

if (shirina == "" || vysota == "" || bok == "")  {
$('#razvorot_top').animate({opacity: "0"}, 300);}

/* ��������� ������ �������� � ����������� ��:

- ������� ������
- - - ��������� ��� ������ ������ 25, ������ �� 25 - 2,3 ���
- - - ������� ��� ������ ������ 50, ������ ������ 50 - 2,8 ���
- - - ������� ��� ������ ������ 75 ������ ������ 70 - 3,0 ���

- ���������� ������, �� ������� ������� �����
���� �� ������, �� +0
���� �� ����, �� +0,70 ���

- ���� ����� �������� ��������� ������ (�����, ���)

��� ����� � ��� �������� ���� ������ � ���������� ����� =
20% �� ������
����� - 40%
��� - 40%
��� ���������� ����� ������ ����� */

}

//����������� ���������� ����������, � ����������� �� ����, ������ ������� ��� ���
function dop_priladki_raznye()
{
dop_priladki_storony = $("#dop_priladki_storony").html()
dop_priladki_storony = dop_priladki_storony*1
//���� �������, ��� ������� ������, ���-�� ����������� ��������� ��� ������ 0

//���� ������� ������� ������ � ���-�� ���������� ����� ��� ������ ����
if ($("#storony_raznie").prop("checked") && dop_priladki_storony >= 0)
{
//�� ���������� � �������� ����� �������� �������
plus_one_pril = dop_priladki_storony + 1
plus_one_pril = plus_one_pril*1
$("#dop_priladki_storony").html(plus_one_pril)

//���������� ���� � �������������, ���� �� �������� ������ 0
if(plus_one_pril > 0)
{$('#dop_priladki_storony_span').animate({opacity: "1"}, 300);}
}
//���� ������� ������� ����������, � ����� �������� ������ ����, �� ��� ��� ����� ������� �������
if ($("#storony_odinakovie").prop("checked") && dop_priladki_storony > 0 ) {
plus_one_pril = dop_priladki_storony - 1
plus_one_pril = plus_one_pril*1
$("#dop_priladki_storony").html(plus_one_pril)

//���� ���-�� �������� ����� ���� - �� ���� ������
if(plus_one_pril == 0)
{$('#dop_priladki_storony_span').animate({opacity: "0"}, 300);}

}

//���� ������������ ���������� �� ����� ������ �� ������ �����, ����� ����� ������� � ������� �������, �� �������� �������� ���
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
<td>����� ������ ��:</td>
<td>
<input type=radio onchange="sborka_raschet()" checked id=is_skolkih_listov_paket_1 name=is_skolkih_listov_paket value="1">
<span id=iz_1_lista><label for="is_skolkih_listov_paket_1">�� ������ �����</label></span>
</td>
<td>
<input type=radio onchange="sborka_raschet()" id=is_skolkih_listov_paket_2 name=is_skolkih_listov_paket value="2">
<span id=iz_2_lista><label for="is_skolkih_listov_paket_2">�� ���� ������</label></span>
</td>
<td></td>
<td></td>
</tr>
<tr id=storony_tr style="opacity: 0.2;">
<td>������� ������:</td>
<td>
<span id=storony_paketa>
<input type=radio onclick="dop_priladki_raznye()"  id=storony_odinakovie name=storony value="1">
<label for="storony_odinakovie">����������</label></span>
</td>
<td>
<span id=storony_paketa>
<input type=radio onchange="dop_priladki_raznye()" id=storony_raznie name=storony value="2">
<label for="storony_raznie">������</label></span>
</td>
<td></td>
<td></td>
</tr>
<tr>
<td>������ �����:</td>
<td>
<input type=radio id=format_pechati_A2 name=format_pechati value="A2" onclick="sborka_raschet()"> <label for="format_pechati_A2"><b>A2</b> 72x52</label>
</td>
<td>
<input type=radio id=format_pechati_A1 name=format_pechati value="A1" onclick="sborka_raschet()"> <label for="format_pechati_A1"><b>A1</b> 72x104</label>
</td>
<td><input type=radio id=format_pechati_other name=format_pechati value="other" onclick="sborka_raschet()"> <label for="format_pechati_other">����</label></td>
<td>
<span id=format_pechati_other_span style="opacity: 0.2;">
<input type=text size=3 id="format_pechati_other_width"  onchange="sborka_raschet()" name="format_pechati_other_width" value="" /> x
<input type=text size=3 id="format_pechati_other_height" onchange="sborka_raschet()" name="format_pechati_other_height" value="" /></span>
<input type=hidden size=5 id=ploshad_lista_inp>



</td>

</tr>
<tr>
<td>������� �� ����</td>
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
<td>�����������:</td>
<td><select name=shelkograf id=shelkograf style="width: 70px;">
<option value="0;0;0">��� �����������</option>
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
<td>�� ���:</td>
<td><select name=uf id=uf style="width: 70px;">
<option value="0;0;0">��� �� ����</option>
<option value="1+0, �� 30%;2.6;50">1+0, �� 30%</option>
<option value="1+0, �� 30 �� 50%;2.92;50">1+0, �� 30 �� 50%</option>
<option value="1+0, �� 50% �� 80%;3.12;50">1+0, �� 50% �� 80%</option>
<option value="1+0, �� 80% �� 100%;3.5;50">1+0, �� 80% �� 100%</option>
<option value="1+1, �� 30%;5.2;100">1+1, �� 30%</option>
<option value="1+1, �� 30 �� 50%;5.84;100">1+1, �� 30 �� 50%</option>
<option value="1+1, �� 50% �� 80%;6.24;100">1+1, �� 50% �� 80%</option>
<option value="1+1, �� 80% �� 100%;7;100">1+1, �� 80% �� 100%</option>
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
//��������, ���� ����� ���� �� ���� �������
if($("#tisnenie").prop("checked") || $("#kongrev").prop("checked"))
{
$('#tisnenie_span').show();
$('#tisn_block').animate({opacity: "1"}, 300);
$("#tisn_one_side").prop("disabled", false)
$("#tisn_two_side").prop("disabled", false)
}
else
{
//������ � ������
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
<label for="net_tisnenie">���</label>
<input type=radio id=tisnenie name=tisn_radio value="tisnenie" onclick="show_tisn()">
<label for="tisnenie">��������</label>
<input type=radio id=kongrev name=tisn_radio value="kongrev" onclick="show_tisn()">
<label for="kongrev">�������</label>

<div id=tisn_block style="opacity: 0.2;">
<input disabled type=radio id=tisn_one_side name=tisn_sides value="tisn_one_side" onclick="show_side_1()">
<label for="tisn_one_side">� ����� �������</label>
<input disabled type=radio id=tisn_two_side name=tisn_sides value="tisn_two_side" onclick="show_side_2()">
<label for="tisn_two_side">� ���� ������</label><br /></div>

<div id=tisn_one_side_block style="opacity: 0.2;">
<b>1.</b> ������: <input disabled type="text" name=shirina_tisn_1 id=shirina_tisn_1 size="3"/>
������: <input disabled type="text" name=vysota_tisn_1 id=vysota_tisn_1 size="3"/><br />
</div>
<div id=tisn_two_side_block style="opacity: 0.2;">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input disabled type=radio id=tisn_sides_same name=tisn_sides_di value="tisn_sides_same" onclick="tisn_sides_sinchron()">
<label for="tisn_sides_same">����������</label>
<input disabled type=radio id=tisn_sides_diff name=tisn_sides_di value="tisn_sides_diff">
<label for="tisn_sides_diff">������</label>
<br />
<b>2.</b> ������: <input disabled type="text" name=shirina_tisn_2 id=shirina_tisn_2 size="3"  onchange="tisn_sinchron()"/>
������: <input disabled type="text" name=vysota_tisn_2 id=vysota_tisn_2 size="3"  onchange="tisn_sinchron()"/>
</div>
</td>
</tr>
<tr>
<td>������:</td>
<td><script>
//������ / ���������� ���� � �������������, � ����������� �� ����, ������� ������ ��� ���
function show_doppriladki(format)
{
//������� ��������� � ����������� ������� ������, ���� ������ ���������
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
<option value="0;;0,0;0;0;0" id=bez_pachati_a2>��� ������</option>
<!-- ��������� ��������;������� �����;��������� ��������;��������� ��������;������ �� ��������  -->
<option value="�2 CMYK ����;A2 (72x52��);0,3744;1,5;6000;150">�2 ���� ����</option>
<option value="�2 CMYK ����;A2 (72x52��);0,3744;1,8;6500;150">�2 CMYK ����</option>
<option value="�2 pantone ����;A2 (72x52��);0,3744;2,5;7000;200">�2 pantone ����</option>
<option value="�2 pantone �������;A2 (72x52��);0,3744;5;9000;200">�2 pantone �������</option>
<option value="�2 CMYK ���� + �����. �������;A2 (72x52��);0,3744;6,5;12000;350">�2 CMYK ���� + �����. �������</option>
<option value="�2 CMYK ���� + �����. �������;A2 (72x52��);0,3744;6,8;15000;400">�2 CMYK ���� + �����. �������</option>
<option value="�2 pantone ���� + �����. �������;A2 (72x52��);0,3744;8,5;15000;400">�2 pantone ���� + �����. �������</option>
<option value="�2 pantone ������� + �����. �������;A2 (72x52��);0,3744;10;18000;500">�2 pantone ������� + �����. �������</option>

</select></span>

<span id=pechat_A1_select style="display: none;">
<select disabled name=pechat_A1 id=pechat_A1 style="width: 180px;" onchange="show_doppriladki('a1')">
<!-- ��������� ��������;������� �����;��������� ��������;��������� ��������;������ �� ��������  -->
<option value="0;;0,0;0;0;0" id=bez_pachati_a1>��� ������</option>
<option value="�1 CMYK ����;A1 (72x104��);0,7488;3;10000;250">�1 CMYK ����</option>
<option value="�1 CMYK ����;A1 (72x104��);0,7488;3,6;12000;300">�1 CMYK ����</option>
<option value="�1 pantone ����;A1 (72x104��);0,7488;5;13000;300">�1 pantone ����</option>
<option value="�1 pantone �������;A1 (72x104��);0,7488;10;15000;400">�1 pantone �������</option>
<option value="�1 CMYK ���� + �����. �������;A1 (72x104��);0,7488;13;25000;600">�1 CMYK ���� + �����. �������</option>
<option value="�1 CMYK ���� + �����. �������;A1 (72x104��);0,7488;14;27000;600">�1 CMYK ���� + �����. �������</option>
<option value="�1 pantone ���� + �����. �������;A1 (72x104��);0,7488;15;28000;600">�1 pantone ���� + �����. �������</option>
<option value="�1 pantone ������� + �����. �������;A1 (72x104��);0,7488;17;30000;600">�1 pantone ������� + �����. �������</option>

</select></span> <span id=dop_priladki_storony_span style="opacity: 0;">��� �������� <span id=dop_priladki_storony class=bold>0</span> ���.��������</span>
<br />
<span id=doppriladky_span style="opacity: 0.2;">
�� ������ �������� ��� ���.��������: <input disabled type="number" maxlength=2 min=0 max=10 value="0" name=dop_priladki_inp id=dop_priladki_inp size="2"/></span>

</td>
<td>���������:</td>
<td><select name=lami id=lami>
<!-- �������� / ��������� ������ �� �2 / ��������� ������ -->
<option value="">��� ���������</option>
<option value="�������;6.24;0.4">�������</option>
<option value="���������;4.8;0.4">���������</option>
<option value="�������;4.8;6">������� �� �������</option>
<option value="���������;6.24;6">��������� �� �������</option>
</select></td>
</tr>
<tr>
<td>�����:</td>
<td>
<script>
//�������, ������� ������������� ����������� �������� ������, ���� ������� ��������� �����
function set_podvorot(){
var prorubn = $("#ruchki").val()
prorubn = prorubn.split(";")
if (prorubn[0] == "���������"){
$("#podvorot").val('6');
$("#podvorot").focus();
}


}
</script>
<select name=ruchki id=ruchki onchange="set_podvorot()">
<!-- ���� �����,&nbsp;��� ���� ������������ ����� �� �������� ����, � ���� ���,&nbsp;��� ���� ������� ��������, �.�. fixed -->
<option id=bez_ruchek value="��� �����;0;0">��� �����</option>
<option value="������ 5 ��;0.90;notfixed">������ 5 ��</option>
<option value="������ 5 �� (� �������);1.19;fixed">������ 5 �� (� �������)</option>
<option value="������ 6 ��;1.50;notfixed">������ 6 ��</option>	1.12
<option value="������ 6 �� (� �������);2.10;fixed">������ 6 �� (� �������)</option>
<option value="���������;0.6;fixed">���������</option>
<option value="�������� �����, 2 �� ���;3.00;notfixed">�������� �����, 2 �� ���</option>
<option value="������ �������� ������;3.00;fixed">������ �������� ������</option>
<option value="������ �����;3.00;fixed">������ �����</option>
<option value="����� ��� ���;7.00;notfixed">����� ��� ���</option>
<option value="����� ������ ��;2.80;notfixed">����� ������ ��</option>
<option value="����� ��� ��� ������;14.00;notfixed">����� ��� ��� ������</option>
<option value="����� ������ ������;7.00;notfixed">����� ������ ������</option>

</select>
<br />
����� �����: <input type="text" value="35" name=dlina_ruchki id=dlina_ruchki size=3/> ��
<input type=checkbox id=ruch_ne_podhodit name=ruch_ne_podhodit value="ruch_ne_podhodit" onclick="ruchki_svoy_var()">
<label for="ruch_ne_podhodit">���� �������</label>
<br />
<span id=ruchki_svoy_var style="opacity: 0;">������� ���� �� ����: <input type="text" value="" name=cena_za_metr id=cena_za_metr size=4/></span>
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
 //�������� � ������
$("#ruchki_svoy_var").prop("disabled", true)
$("#cena_za_metr").val("");
$('#ruchki_svoy_var').animate({opacity: "0"}, 300);

//� ������ �������� ���������� � ����������
$('#ruchki').animate({opacity: "1"}, 300);
$("#ruchki").prop("disabled", false)

}
  }
</script>



</td>
<td>�������:</td>
<td>
<select name=piccolo id=piccolo>
<!-- ��������� �������� / ��������� �������� / ������� �� ��������� -->
<option value="��� �������;0;0">��� �������</option>
<option value="������/���. 5 ��.;0.40;0.35">������/���. 5 ��.</option>
<option value="�������;1.50;0.50">������� 5 ��.</option>
</select>
</td>
</tr>

<tr>
<td colspan=2 rowspan=4>������� ���������:<br />
<table id=ugolkrug><tr><td>
<b>�����:</b>
<br />
<!-- �������� � value  �������� / ��� / ��������� 1 � ��������� ������ ���������� /  -->
<input type=radio id=truba_glue_tape9mm name=glue_truba value="����� 9 ��;tape;0.55">
<label for="truba_glue_tape9mm">����� 9 ��</label>
<input type=radio checked id=truba_glue_tape11mm name=glue_truba value="����� 11 ��;tape;0.72">
<label for="truba_glue_tape11mm">����� 11 ��</label>
<input type=radio id=truba_glue_tape_yellow name=glue_truba value="���/����� 11 ��;tape;1.65">
<label for="truba_glue_tape_yellow">���/����� 11 ��</label>
<input type=radio id=truba_glue_hot name=glue_truba value="���/����;glue;0.5">
<label for="truba_glue_hot">���/����</label>
</table>

<br />
<table id=ugolkrug><tr><td>
<b>���:</b>
<br />
<input type=radio id=dno_glue_tape9mm name=glue_dno value="����� 9 ��;tape;0.54">
<label for="dno_glue_tape9mm">����� 9 ��</label>
<input type=radio checked id=dno_glue_tape11mm name=glue_dno value="����� 11 ��;tape;0.72">
<label for="dno_glue_tape11mm">����� 11 ��</label>
<input type=radio id=dno_glue_tape_yellow name=glue_dno value="���/����� 11 ��;tape;1.65">
<label for="dno_glue_tape_yellow">���/����� 11 ��</label>
<input type=radio id=dno_glue_hot name=glue_dno value="���/����;glue;0.5">
<label for="dno_glue_hot">���/����</label>

</table>

<br />
<style>
#ugolkrug{
color: #0000; /* ���� ������ */
background:white; /* ��� ����� */
border: 1px #D4D0C8 solid; /* ����� ����� */
-moz-border-radius: 5px; /* ����������� ��� ������ Mozilla Firefox */
-webkit-border-radius: 5px; /* ����������� ��� ������ Chrome � Safari */
-khtml-border-radius:5px; /* �������. ��� �������� Konquerer ������� Linux */
border-radius: 5px; /* ����������� ����� ��� ����, ��� �������� */
}
</style>
<table id=ugolkrug><tr><td>
<b>����������:</b>
<br />
<input type=radio checked id=ukr_glue_tape9mm name=glue_ukr value="����� 9 ��;tape;0.55">
<label for="ukr_glue_tape9mm">����� 9 ��</label>
<input type=radio id=ukr_glue_tape_yellow name=glue_ukr value="���/����� 11 ��;tape;1.65">
<label for="ukr_glue_tape_yellow">���/����� 11 ��</label>
<input type=radio id=ukr_glue_no name=glue_ukr value="���;0;0">
<label for="ukr_glue_no">���</label>
</td></tr>
</table>


</td>
<td>������:</td>
<td>
<input disabled type="text" id="cena_sborki" size=4 />

</td>
</tr>


<tr>
<td>������ ��� ����������:</td>
<td>
<input checked type=checkbox id=ukrepl_dno name=ukrepl_dno value="" onclick="show_dno_s_zapechatkoy()">
<label for="ukrepl_dno">���</label>
<input checked type=checkbox id=ukrepl_bok name=ukrepl_bok value="">
<label for="ukrepl_bok">��������</label>
<span id=dno_s_zapechatkoy>
<input type=checkbox id=print_on_dne name=print_on_dne value="">
<label for="print_on_dne">��� � ����������</label></span>
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
<td>���. �������:</td>
<td><input type="text" name=dop_rash id=dop_rash size=12/></td>
</tr>
<tr>
<td>��������:</td>
<td>
<input checked type=radio id=upak_plenka name=upakovka value="������-������;0.1">
<label for="upak_plenka">� ������</label>
<input type=radio id=upak_box name=upakovka value="�������;0.5">
<label for="upak_box">� �������</label></td>
</tr>

</table>




<script>

function make_calc(tisn_listov_na_priladku)
{
<?require_once("calc_vars.php");?>

//��������� ����������
var dop_priladki_cost = "0";
dop_priladki_cost = parseInt(dop_priladki_cost)

var pechat_format =  "�� �����";
var pechat_prokatka_cost =  "0";
var pechat_priladka_cost = "0";
var pechat_priladka_listov =  "0";
var pechat_text = "��� ������";
var pechat_priladka_listov = "0";
var material_cost_per_tonn = "0";

pechat_priladka_listov = pechat_priladka_listov*1


//������� ���������� �� �����
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

//�������� ������ ��������� �� input type hidden
var razvorot_width = $("#razvorot_width_top_hidden").val()
var razvorot_height = $("#razvorot_height_top_hidden").val()

$("#razvorot_width").html(razvorot_width)
$("#razvorot_height").html(razvorot_height)

var izdely_na_list =  $("#izdely_na_list").val();

//������� ��������� �� selecta
var select_a2 = $("#pechat_A2").val();
select_a2 = select_a2.split(";")
var select_a1 = $("#pechat_A1").val();
select_a1 = select_a1.split(";")
select_a2a =  select_a2["0"];
select_a1a =  select_a1["0"];

//��������� ����� ������ ������ � � ����������� �� ���� ������ ������� �� ���������������� �������
if (select_a2a != 0){
//0-��������� ��������;������� �����;��������� ��������;��������� ��������;5-������ �� ��������  -->
pechat_text =  select_a2["0"];
pechat_format =  select_a2["1"];
pechat_prokatka_cost =  select_a2["3"];
pechat_priladka_cost =  select_a2["4"];
pechat_priladka_listov =  select_a2["5"];
//$("#pechat_lami_format").html("A2")
}

if (select_a1a != 0){
//0-��������� ��������;������� �����;��������� ��������;��������� ��������;5-������ �� ��������  -->
pechat_text =  select_a1["0"];
pechat_format =  select_a1["1"];
pechat_prokatka_cost =  select_a1["3"];
pechat_priladka_cost =  select_a1["4"];
pechat_priladka_listov =  select_a1["5"];
//$("#pechat_lami_format").html("A1")
}


//����� �� �����������
var dop_priladki_inp = $("#dop_priladki_inp").val();
dop_priladki_inp = dop_priladki_inp*1
var dop_priladki_storony = $("#dop_priladki_storony").html()
dop_priladki_storony = dop_priladki_storony*1

//���� �������� �� ������ ������ ��� ���� (���� �����������), �� ����������� ���������� ����������� ������
dop_priladki_inp = dop_priladki_inp + dop_priladki_storony

//���� ����������� ����� ����, �� ���������� ��� ����� �� ����� ������ �� ��������. ������ ����������? ������ ��� �������� �� 1 �� ����� ������ :-)
if (dop_priladki_inp == 1)
{
pechat_priladka_listov = pechat_priladka_listov*1
pechat_priladka_listov = pechat_priladka_listov + pechat_priladka_listov
}
//���� ����������� ������ ��� ����, �� ��� ��������
if (dop_priladki_inp > 1)
{
pechat_priladka_listov = pechat_priladka_listov*1
pechat_priladka_listov = pechat_priladka_listov + pechat_priladka_listov * dop_priladki_inp
}

//���� ���������� �� ����� �������, ���������� �� ��������� ������ �� �������� ������
if (dop_priladki_inp != "") {
dop_priladki_cost = pechat_priladka_cost * dop_priladki_inp
$("#dop_priladki_span").html(dop_priladki_inp)
$("#dop_priladki_cost_span").html(dop_priladki_cost)
}
else {
$("#dop_priladki_span").html("���")
$("#dop_priladki_cost_span").html("���")
}
//����� ���������� � �����
$("#pechat_text").html(pechat_text)
$("#pechat_prokatka_cost").html(pechat_prokatka_cost)
$("#pechat_priladka_cost").html(pechat_priladka_cost)

//��������� ���������� � �����
pechat_prokatka_cost = parseInt(pechat_prokatka_cost)
pechat_priladka_cost = parseInt(pechat_priladka_cost)
//����� 4 ���� �������� 1. ����� 2. ����������� 3. 4. �������� ��. ��� ����� ���� ��� ������, ��� � ������ �� �����������. ������ "����" �����.
//������ �������, ������� ���������� � ���������� pechat_priladka_listov �� ���������� ������, ������� ���������� �� �������� ������� ���� ������
if($("#tisnenie").prop("checked") || $("#kongrev").prop("checked")) {
var tisn_listov_na_priladku = "0"
//���������� ���������� ������ �� �������� � ����������� �� ����, ������� ������ �������� �������������
if($("#tisn_one_side").prop("checked")) {
var tisn_listov_na_priladku = "50";
}
//���� �������� � ���� ������
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



//������� ��������� ������, ��� �������
var mater = $("#mater").val();
//���� ������� ������ ������
if (mater == "other") {
	//���� ������� ���� �� ����
if ($("#per_list").prop("checked")){
var material_cost_per_list = $("#price_other_material").val();
$("#material_cost_per_tonn").html(material_cost_per_tonn)

}
//���� ������� ���� �� �����
if ($("#per_tonn").prop("checked")){
var material_cost_per_tonn = $("#price_other_material").val();

}
//������� � ����� ������ � ���������� ����
var grammaj_bum = $("#grammaj_other_material").val();
var name_bum = "����������������";
$("#material_cost_per_tonn").html(material_cost_per_tonn)
}
//���� ������� ������ �� ������������ ������
else {
mater = mater.split(";")
//�������� ���������
name_bum =  mater["0"];
//������� ������
grammaj_bum =  mater["1"];
//����������� � �����
grammaj_bum = parseInt(grammaj_bum)
//$("#grammaj_bum").html(grammaj_bum)
//������� ��������� �� �����
material_cost_per_tonn =  mater["2"];
material_cost_per_tonn = parseInt(material_cost_per_tonn)
$("#material_cost_per_tonn").html(material_cost_per_tonn)

}
//����� �������� ������ ����� ���������� ���� ��������
$("#name_bum").html(name_bum)


var ploshad_lista = $("#ploshad_lista_inp").val()
//����� ������� ����� � �����
$("#ploshad_lista").html(ploshad_lista)

//��������� ��� �����
var ves_lista = (ploshad_lista * grammaj_bum);

//��������� �� ������ �����
ves_lista = ves_lista.toFixed(0);

//������� ������

//����������� � �����
grammaj_bum = parseInt(grammaj_bum)
$("#grammaj_bum").html(grammaj_bum)

//�������� ���������� ������ ����������� ��� ������ ��� ��������
var listov_na_pechat_bez_usadki =  tiraj / izdely_na_list;
listov_na_pechat_bez_usadki = listov_na_pechat_bez_usadki.toFixed(0)
$("#listov_na_pechat_bez_usadki").html(listov_na_pechat_bez_usadki)

//�������� �� � ������ ������
var listov_na_pechat_s_usadkoy = listov_na_pechat_bez_usadki * pechat_bum_brak;
listov_na_pechat_s_usadkoy = listov_na_pechat_s_usadkoy.toFixed(0)
listov_na_pechat_s_usadkoy = listov_na_pechat_s_usadkoy*1
pechat_priladka_listov = pechat_priladka_listov*1
//���������� ����������� ����� � �������� ����� ���-�� ������ � ���������
var vsego_listov = listov_na_pechat_s_usadkoy + pechat_priladka_listov;
vsego_listov = vsego_listov*1

$("#listov_na_pechat_s_usadkoy").html(listov_na_pechat_s_usadkoy)

//ves_materiala ������� ��� ���������
var ves_materiala = vsego_listov *  ves_lista / 1000000;
ves_materiala = ves_materiala.toFixed(3);
$("#ves_materiala").html(ves_materiala)

//���������� ������� ������� ���� ������,
//���� ������� ���� �� ����
if ($("#per_list").prop("checked")){
material_cost_per_list = material_cost_per_list*1
var material_cost = material_cost_per_list *  vsego_listov;
}
//���� ���� ������� �� �����
else{
//��������� ��������� ���������
var material_cost = material_cost_per_tonn *  ves_materiala;
}

material_cost = material_cost.toFixed(1)
material_cost = parseInt(material_cost)

$("#vsego_listov").html(vsego_listov)
//����� ��� ����� � �����
$("#ves_lista").html(ves_lista);
$("#material_cost").html(material_cost)


//��������� ��������� ������ ��� ���������
var pechat_cost = pechat_priladka_cost + (vsego_listov * pechat_prokatka_cost) + dop_priladki_cost
$("#pechat_cost").html(pechat_cost)
pechat_cost = pechat_cost.toFixed("0")
pechat_cost = parseInt(pechat_cost)

//����� ��������� ������
var pechat_cost_total = pechat_cost + material_cost;
$("#pechat_cost_total").html(pechat_cost_total)
pechat_cost_total = pechat_cost_total.toFixed("0")
pechat_cost_total = parseInt(pechat_cost_total)

if (lami != "")
{
//���������� ���� � ������
$('#lamination_span').show();

//�������� ������ �� �������
select_lami = lami.split(";")
lami_name =  select_lami["0"];
lami_plenka_cost =  select_lami["1"];
lami_rab_cena =  select_lami["2"];
//������ ������� ��������� ������ �� ���������� ������, ������� ���������� ��� ������
var lami_na_list =  parseInt($("#lami_na_list").val())
var lami_listov = tiraj / izdely_na_list
$("#lami_listov_span").html(lami_listov)
$("#lami_name_span").html(lami_name)
$("#lami_plenka_cost_span").html(lami_plenka_cost)


//��������� ������ �� �������������
lami_rab_cost = lami_listov * lami_rab_cena
$("#lami_rab_cost_span").html(lami_rab_cost)
$("#lami_rab_span").html(lami_rab_cena)
//������� ������
var lami_ploshad = ploshad_lista *  lami_listov * pogr_lami
lami_ploshad =  lami_ploshad.toFixed(0)
$("#lami_ploshad_span").html(lami_ploshad)
//��������� ������ �� �������� ������
var lami_plenka_total_cost = lami_ploshad *  lami_plenka_cost
lami_plenka_total_cost =  lami_plenka_total_cost.toFixed(0)
$("#lami_plenka_total_cost_span").html(lami_plenka_total_cost)
lami_priladka = parseInt(lami_priladka)
lami_rab_cost = parseInt(lami_rab_cost)
lami_plenka_total_cost = parseInt(lami_plenka_total_cost)

$("#lami_priladka_span").html(lami_priladka)
//�������� ����� ��������� ���������
var lami_total_cost = lami_priladka + lami_rab_cost + lami_plenka_total_cost
$("#lami_total_cost_span").html(lami_total_cost)
}

//�������� ��������� ������ � �����
var shtamp =  $("#shtamp").val();
$("#stamp_cost_span").html(shtamp)
//��������� ����� �� ��� ���
if($("#shtamp_include").prop("checked"))
{$('#stamp_new_span').html("�����");}
else
{$('#stamp_new_span').html("������");}

//������� ��������� �������
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

//������ ��������
if($("#tisnenie").prop("checked") || $("#kongrev").prop("checked"))  {
//������� ��������
if($("#tisnenie").prop("checked")){var type_of_tisn = "��������"; var type_of_tisn_var = "tisn";}
if($("#kongrev").prop("checked")){var type_of_tisn = "�������"; var type_of_tisn_var = "kongr";}
//��������� ����������� ����� �������� �� ����� �������, ��� �������� ����� �� ��� ����� � �� �������� ���������� ������
var K_udarov = "1"
$('#type_of_tisn_span').html(type_of_tisn);
var shirina_tisn_1 = $("#shirina_tisn_1").val();
var vysota_tisn_1 = $("#vysota_tisn_1").val();
$('#tisn_shirina_1_span').html(shirina_tisn_1);
$('#tisn_vysota_1_span').html(vysota_tisn_1);
tisn_ploshad_1  = shirina_tisn_1 * vysota_tisn_1;
$('#tisn_ploshad_1_span').html(tisn_ploshad_1);

//���� � ��� ������ ���� �������, ������� ������ ������� ��� ����� ����� ����������, ����� ����� ���
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
$('#tisn_ottiski_dif_span').html("������");
}
else {
var tisn_klishe_cost = tisn_ploshad_1 * cost_klishe
$('#tisn_ottiski_dif_span').html("����������");
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

//������ �����������
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


//������ �� ����
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


//������ �����
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
//���������� ����� �����
var dlina_ruchki = parseInt($('#dlina_ruchki').val());
$('#dlina_ruchki_span').html(dlina_ruchki);
var vsego_metrov = (dlina_ruchki * kolvo_ruchek_s_pogr) / 100
vsego_metrov = parseInt(vsego_metrov)
$('#vsego_metrov_span').html(vsego_metrov);
$('#ruchki_meter_cost_span').html(ruchki_price);
var ruchki_total_cost =  vsego_metrov * ruchki_price
$('#ruchki_total_cost_span').html(ruchki_total_cost);
}

//���� � ������� ���������� �������� fixed, �� ������� ����� �� ������������� ����, �� 1 ��
if (ruchki_type == "fixed") {

$('#ruchki_fixed_price_span').html(ruchki_price);
//����� ����� ����� �� 2, �.�. ����� �� ���� ���� ��������� ������������
var ruchki_total_cost =  kolvo_ruchek_s_pogr / 2 * ruchki_price
$('#ruchki_total_cost_span').html(ruchki_total_cost);

}else{$('#ruchki_fixed_price_span').html("");}
}

//���� ������������ ������� � ����� ������ ���� ���� �� ����� �� ����
if($("#ruch_ne_podhodit").prop("checked")) {
ruchki_text = "���� �������";

$('#kolvo_ruchek_span').html(kolvo_ruchek);
var kolvo_ruchek_s_pogr =  kolvo_ruchek * pogr_other
$('#kolvo_ruchek_s_pogr_span').html(kolvo_ruchek_s_pogr);
var dlina_ruchki = parseInt($('#dlina_ruchki').val());
$('#dlina_ruchki_span').html(dlina_ruchki);
var vsego_metrov = (dlina_ruchki * kolvo_ruchek_s_pogr) / 100
vsego_metrov = parseInt(vsego_metrov)
$('#vsego_metrov_span').html(vsego_metrov);
//�������� ���������� �� ���� ���� ���� �� ����
var cena_za_metr = parseInt($('#cena_za_metr').val());
$('#ruchki_meter_cost_span').html(cena_za_metr);
var ruchki_total_cost =  vsego_metrov * cena_za_metr
$('#ruchki_total_cost_span').html(ruchki_total_cost);
}
$('#ruchki_text_span').html(ruchki_text);

//������� �������
var piccolo = $("#piccolo").val();
piccolo = piccolo.split(";")
piccolo_text =  piccolo["0"];
piccolo_cost_4 =  piccolo["1"];
piccolo_job_cost =  piccolo["2"];

if (piccolo_cost_4 != "0") {

//���� �������, ���������� � ������
$("#piccolo_span").show();
$('#piccolo_text_span').html(piccolo_text);

//���� �� �������� �� 4 ��
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

//������� ������� ���������

//�������� �������� radio �� ��������� ������
var glue_truba = $(":radio[name=glue_truba]").filter(":checked").val();
//var glue_truba = $("#glue_truba").val();
glue_truba = glue_truba.split(";")
truba_text =  glue_truba["0"];
$('#truba_text_span').html(truba_text);
truba_type_glue =  glue_truba["1"];
truba_glue_cost_meter =  glue_truba["2"];
$('#truba_glue_cost_meter_span').html(truba_glue_cost_meter);

//���� ����� �� ���� ������, �������� ����� ������ �� ���
if ($("#is_skolkih_listov_paket_2").prop("checked")) {var iz_listov = "2"}else{var iz_listov = "1"}

//��� ���������, ����� ���������� razvorot_height� ������ ������
//�� �������� ��� ����������� pogr_other
var truba_meters_gluing = razvorot_height / 100 * iz_listov * pogr_other
truba_meters_gluing = truba_meters_gluing.toFixed(2)
$('#truba_meters_gluing_span').html(truba_meters_gluing);
//��������� ��������� �������� ��������� �� �����
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
//��������� ������ ������
sq_side = 0.75*bok
//������ ������ � ��������
sq_side = sq_side * sq_side
//������� �������� � ��������
var katet = Math.sqrt(2*sq_side)
katet = katet.toFixed(0)
shirina = 1 * shirina
var dno_meters_gluing = ((4 * katet) + shirina - (bok * 1.5)) / 100 * pogr_other
dno_meters_gluing = dno_meters_gluing.toFixed(2)
$('#dno_meters_gluing_span').html(dno_meters_gluing);
//��������� ��������� �������� ��������� �� �����
var dno_glue_cost_total = dno_meters_gluing * dno_glue_cost_meter
dno_glue_cost_total = dno_glue_cost_total.toFixed(2)
$('#dno_glue_cost_total_span').html(dno_glue_cost_total);

//������ ���� �� ���
var glue_ukr = $(":radio[name=glue_ukr]").filter(":checked").val();
glue_ukr = glue_ukr.split(";")
ukreplenie_text =  glue_ukr["0"];
$('#ukreplenie_text_span').html(ukreplenie_text);
ukreplenie_type_glue =  glue_ukr["1"];
ukreplenie_glue_cost_meter =  glue_ukr["2"];
$('#ukreplenie_glue_cost_meter_span').html(ukreplenie_glue_cost_meter);
//��������, �� ���������� �������� ������������ ������ 0,75 �� ������ ������, �.�. ������� ������ ������
var ukreplenie_meters_gluing = shirina * 0.015 * pogr_other
ukreplenie_meters_gluing = ukreplenie_meters_gluing.toFixed(2)
$('#ukreplenie_meters_gluing_span').html(ukreplenie_meters_gluing);
var ukreplenie_glue_cost_total = ukreplenie_meters_gluing * ukreplenie_glue_cost_meter
ukreplenie_glue_cost_total = ukreplenie_glue_cost_total.toFixed(2)
$('#ukreplenie_glue_cost_total_span').html(ukreplenie_glue_cost_total);

//��������� ����� ��������� ������� ����������
truba_glue_cost_total = truba_glue_cost_total*1
dno_glue_cost_total = dno_glue_cost_total*1
ukreplenie_glue_cost_total = ukreplenie_glue_cost_total*1
var total_gluing_material_cost = (truba_glue_cost_total + dno_glue_cost_total + ukreplenie_glue_cost_total) * tiraj
total_gluing_material_cost = total_gluing_material_cost.toFixed(2)
$('#total_gluing_material_cost_span').html(total_gluing_material_cost);

/*
���������� 2���� �������������� ������� ����������
1. ������ ������������� ����� (����� ����� �� �������)

2. ��������� ����� ������� ���� ��� ��� �� �������� �� ����� �������
� ���� ������ �� ��������������� �������������� ������, �������� �� ��������, �������
������� �����, �� ��������� ����� ��� ��������� �������

����� ��������� �� �������

����� = ������ ������ + ������ + ��� * 0,75

var truba_meters_gluing = vysota + klapan + bok * 0.75

shirina
vysota
bok
klapan

�������� = ������ ������ * 0,75
 */

//������ ����������
var ploshad_dna = shirina * bok
if ($("#ukrepl_dno").prop("checked")) {
$('#dno_ukrepl_span').html("��");
$('#ploshad_dna_span').html(ploshad_dna);
}
else {
$('#dno_ukrepl_span').html("���");
var ploshad_dna = "0";
ploshad_dna = ploshad_dna*1
$('#ploshad_dna_span').html("");
}

if ($("#ukrepl_bok").prop("checked")) {

$('#bok_ukrepl_span').html("��");
var ploshad_bokovin = shirina * podvorot * 2
$('#ploshad_bokovin_span').html(ploshad_bokovin);
}
else {$('#bok_ukrepl_span').html("���");
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
$('#karton_ukrepl_zapechatka_span').html("��");
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
$('#karton_ukrepl_zapechatka_span').html("���");
$("#zapechatka_dna_span").hide();
}


/* <span id=ukreplenie_span style="display: none">
<br /> <b>������ �� ���������� � ��������� ����������:</b><br />
���������� ���: <span id=dno_ukrepl_span class=debug_res></span><br>
������� ���: <span id=ploshad_dna_span class=debug_res></span> �� 2<br>
���������� �������: <span id=bok_ukrepl_span class=debug_res></span><br>
������� 2 �������: <span id=ploshad_bokovin_span class=debug_res></span> �� 2<br>
����� ������� ����������:   <span id=obshaya_ploshad_ukrepl_span class=debug_res></span> �� 2
����� ������� ���� ����������:   <span id=obshaya_ploshad_total_span class=debug_res></span> �2<br>
����� ��� ������� ��� ����������:   <span id=weight_karton_ukrepl_span class=debug_res></span> �� &nbsp; &nbsp; &nbsp;
� ������������:  <span id=weight_karton_ukrepl_span_s_pogr_span class=debug_res></span> ��<br>
��������� ������� ��� ����������:  <span id=karton_ukrepl_total_cost_span class=debug_res_vip></span> �.<br>
</span>
<span id=zapechatka_dna_span style="display: none">
� ����������?   <span id=karton_ukrepl_zapechatka_span class=debug_res></span><br>
���������� ������� �� 1 ���� �2:   <span id=dno_on_a2_span class=debug_res></span><br>
���������� �������� �� ������ ���:   <span id=kolvo_prokatok_pechat_dno_span class=debug_res></span><br>
��������� �������� �2 �� ������ ���:   <span id=priladka_pechat_dno_cost_span class=debug_res></span><br>
����� ��������� �� ������ �� ���:   <span id=pechat_dno_cost_span class=debug_res_vip></span><br>
</span> */


//������� ��������� ������
cena_sborki_tarif = $("#cena_sborki").val()

//��� ���� ��������� ��� �������� �������, � ��� �������

//������� ����������


//������� ��������

//������� ��������� ������



}

function ochistit(){

$("#pechat_A2").prop('disabled', true)
$("#pechat_A1").prop('disabled', true)
}

</script>

<table width="" border="0" cellpadding="1" cellspacing="1">
<tr>
<td>
<b>���� ����������:</b>
<input checked type=radio id="standartny" name="srok" value="standartny"><label for="standartny">�����������</label>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=radio id="srochny" name="srok" value="srochny"><label for="srochny">�������</label>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type=radio id="supersrochny" name="srok" value="supersrochny"><label for="supersrochny">������������</label>
</td>
<td>
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="������!" onclick="make_calc()"/>  <input type="reset" onclick="ochistit()" value="��������"/></td>
</tr>
</table>

    </form>
       <br />
<table width="900" border="0" cellpadding="1" cellspacing="1" align=center>
<tr>
<td>
����� �������������: <span id=ss>110 000,00</span> ���. ��� <span id=ss>58,00</span> ���. �� �����<br />
</td>
<td>
</td>
</tr>

<tr>
<td>
��� ������ �������: <span id=ss>160 000,00</span> ���. ��� <span id=ss>78,00</span> ���. �� �����<br />
</td>
<td>�������: 1,5
</td>
</tr>

 <tr>
<td>
������: <input type="text" name=skidka id=skidka size=2/>% &mdash; � ������ ������ ������ �������: <span id=ss>160 000,00</span> ���. ��� <span id=ss>78,00</span> ���. �� �����<br />
</td>
<td>
</td>
</tr>


<tr>
<td>
<a href="#" onclick="show_smeta()" id=smeta_span>�����</a> / <a href="#" onclick="show_debug_smeta()" id=debug_smeta_span>�����</a><br />
<script>
function show_smeta() {
$("#smeta").show();
$("#debug_smeta").hide();

$("#debug_smeta_span").html('�����');
$("#smeta_span").html('<b>�����</b>');

}
function show_debug_smeta() {
$("#debug_smeta").show();
$("#smeta_span").html('�����');
$("#debug_smeta_span").html('<b>�����</b>');
$("#smeta").hide();
}
</script>

<div id=debug_smeta style="display: block">
<b>������:</b><br />
������� ������ �2: <span id=grammaj_bum class=debug_res></span> ��  <br />
������� ���������� �����: <span id=ploshad_lista class=debug_res></span> �2  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ������: <span id=pechat_text class=debug_res></span> <br />
��� ���������� �����:   <span id=ves_lista class=debug_res></span> ��<br />
������ �� ��������:   <span id=pechat_priladka_listov class=debug_res></span> ��<br />
������ �� ������ ��� ������:   <span id=listov_na_pechat_bez_usadki class=debug_res></span> ��  <br />
������ �� ������ � �������:   <span id=listov_na_pechat_s_usadkoy class=debug_res></span> ��   <br />
����� ������:  <span id=vsego_listov class=debug_res_vip></span>  ��.&nbsp;&nbsp;&nbsp;&nbsp;
��� ����� ���������: <span id=ves_materiala class=debug_res_vip></span> ��<br />
��������� ��������� (������) �� �����: <span id=material_cost_per_tonn class=debug_res></span> �.  &nbsp;&nbsp;&nbsp;&nbsp;
�������� ������: <span id=name_bum class=debug_res></span>    <br />
����� ��������� ������: <span id=material_cost class=debug_res_vip></span> �. <br />

<br /><b>������:</b><br />

��������� ��������: <span id=pechat_priladka_cost class=debug_res></span> �. <br />
����� ���������� <span id=dop_priladki_span class=debug_res></span> �� ����� ���������:  <span id=dop_priladki_cost_span class=debug_res></span> �. <br />
��������� ��������:  <span id=pechat_prokatka_cost class=debug_res></span> �.<br />
��������� ������ � ���������: <span id=pechat_cost class=debug_res></span> �.<br /><br />

��������� ������, ������ � �������: <span id=pechat_cost_total class=debug_res_vip></span> �.<br />


<br /><b>������� ���������:</b>
<br />������ &mdash; <span id=razvorot_width class=debug_res></span> �� x ������ &mdash; <span id=razvorot_height class=debug_res></span> �� <br />

<span id=lamination_span style="display: none">
<br />
<b>���������:</b><br />
����� ������ <span id=pechat_lami_format class=debug_res></span> ������������: <span id=lami_listov_span class=debug_res></span> ��,
������ <span id=lami_name_span class=debug_res></span>: <span id=lami_ploshad_span class=debug_res></span> �2   <br />
�� <span id=lami_plenka_cost_span class=debug_res></span> �� 1 ��. �2,
�������� ��������� ������: <span id=lami_plenka_total_cost_span class=debug_res></span> �.<br />
��������:   <span id=lami_priladka_span class=debug_res></span> �.       <br />
����� �� ���������:  <span id=lami_rab_span class=debug_res></span> �.          <br />
��������� ������:  <span id=lami_rab_cost_span class=debug_res></span> �. <br />
����� ��������� ���������: <span id=lami_total_cost_span class=debug_res_vip></span> �.<br /></span>


<span id=virub_span>
<br />
<b>�������:</b><br />
����� ������ <span id=virubka_udarov_span class=debug_res></span><br />
��������:   <span id=virubka_priladka_span class=debug_res></span> �.<br />
����� �� �������:  <span id=virubka_tarif_span class=debug_res></span> �.<br />
��������� ������:  <span id=virubka_tarif_total_span class=debug_res></span> �.<br />
����� ��������� �������: <span id=virubka_total_cost_span class=debug_res_vip></span> �.<br /></span>

<br />
<b>�����:</b><br />
����� �����?  <span id=stamp_new_span class=debug_res></span><br />
��������� ������: <span id=stamp_cost_span class=debug_res_vip></span> �.<br />

<span id=tisnenie_span style="display: none">
<br />
<b>�������� / �������</b><br />
��� ��������: <span id=type_of_tisn_span class=debug_res></span><br>
������� 1 ������� &mdash; <br>
������: <span id=tisn_shirina_1_span class=debug_res></span> ��2<br>
������: <span id=tisn_vysota_1_span class=debug_res></span> ��2  <br>
������� ��������: <span id=tisn_ploshad_1_span class=debug_res></span> ��2 <br>
������� 2 ������� &mdash;<br>
������: <span id=tisn_shirina_2_span class=debug_res></span> ��2 <br>
������: <span id=tisn_vysota_2_span class=debug_res></span> ��2  <br>
������� ��������: <span id=tisn_ploshad_2_span class=debug_res></span> ��2 <br>
������� ����������?  <span id=tisn_ottiski_dif_span class=debug_res></span>  <br>
����� ������:   <span id=tisn_udarov_span class=debug_res></span>   <br>
����� ��������:   <span id=tisn_tarif_span class=debug_res></span>     <br>
��������� ������:  <span id=tisn_folga_cost_span class=debug_res></span> �/��2,
�� ����� ����� <span id=tisn_folga_total_span class=debug_res></span> ��2 ������
� ��� ����� ������ <span id=tisn_folga_total_cost_span class=debug_res></span> �.<br>
��������� �����:  <span id=tisn_klishe_cost_span class=debug_res></span>       <br>
��������� ��������:  <span id=tisn_priladka_cost_span class=debug_res></span>      <br>
��������� ������:  <span id=tisn_rabota_cost_span class=debug_res></span>              <br>
����� ���������:  <span id=tisn_total_cost_span class=debug_res_vip></span>
<br /></span>


<span id=shelk_span style="display: none"><br />
<b>�����������: <span id=shelk_text_span class=debug_res></span></b><br>
��������: <span id=shelk_priladka_span class=debug_res></span> �.<br>
��������� ��������: <span id=shelk_prokatka_span class=debug_res></span> �.<br>
���������� ��������:  <span id=shelk_skolko_prokatok_span class=debug_res></span><br>
��������� ���� ��������:  <span id=shelk_cost_prokatok_span class=debug_res></span> �.<br>
����� ���������:   <span id=shelk_total_cost_span class=debug_res_vip></span> �.<br>
</span>

<span id=uf_span style="display: none"><br />
<b>�� ���: <span id=uf_text_span class=debug_res></span></b><br>
��������: <span id=uf_priladka_span class=debug_res></span> �.<br>
��������� ��������: <span id=uf_prokatka_span class=debug_res></span> �.<br>
���������� ��������:  <span id=uf_skolko_prokatok_span class=debug_res></span><br>
��������� ���� ��������:  <span id=uf_cost_prokatok_span class=debug_res></span> �.<br>
����� ���������:   <span id=uf_total_cost_span class=debug_res_vip></span> �.<br>
</span>

<span id=glue_span><br />
<b>������� ���������:</b><br />
<b><i>�����</i></b><br />
��� �������� ���������: <span id=truba_text_span class=debug_res></span><br>
������ �� �������: <span id=truba_meters_gluing_span class=debug_res></span> �.<br>
��������� ����� �������: <span id=truba_glue_cost_meter_span class=debug_res></span> �.<br>
����� ��������� ������� ���������� �� �����:  <span id=truba_glue_cost_total_span class=debug_res></span><br>
<b><i>���</i></b><br />
��� �������� ���������: <span id=dno_text_span class=debug_res></span><br>
������ �� �������: <span id=dno_meters_gluing_span class=debug_res></span> �.<br>
��������� ����� �������: <span id=dno_glue_cost_meter_span class=debug_res></span> �.<br>
����� ��������� ������� ���������� �� ���:  <span id=dno_glue_cost_total_span class=debug_res></span><br>
<b><i>����������</i></b><br />
��� �������� ���������: <span id=ukreplenie_text_span class=debug_res></span><br>
������ �� �������: <span id=ukreplenie_meters_gluing_span class=debug_res></span> �.<br>
��������� ����� �������: <span id=ukreplenie_glue_cost_meter_span class=debug_res></span> �.<br>
����� ��������� ������� ���������� �� ����������:  <span id=ukreplenie_glue_cost_total_span class=debug_res></span>
<br />
����� ��������� ������� ���������� �� �����:   <span id=total_gluing_material_cost_span class=debug_res_vip></span> �.<br>
</span>

<span id=ruchki_span><br />
<b>�����:</b><br />
��� �����: <span id=ruchki_text_span class=debug_res></span><br>
����� ����� �����:   <span id=dlina_ruchki_span class=debug_res></span> c�.<br>
���������� ����� �� �����:   <span id=kolvo_ruchek_span class=debug_res></span> ��. &nbsp; &nbsp; &nbsp; � ������������:  <span id=kolvo_ruchek_s_pogr_span class=debug_res></span> ��<br>
����� ������ �� �����:  <span id=vsego_metrov_span class=debug_res></span> �.<br>
��������� ��������� �����:   <span id=ruchki_meter_cost_span class=debug_res></span> �.<br>
����� �� ������������� ����:  <span id=ruchki_fixed_price_span class=debug_res></span> �.<br>
����� ��������� �����:   <span id=ruchki_total_cost_span class=debug_res_vip></span> �.<br>
<br /></span>


<span id=piccolo_span style="display: none">
<br /> <b>�������:</b><br />
��� �������: <span id=piccolo_text_span class=debug_res></span><br>
��������� 4 �������:   <span id=piccolo_cost_4_span class=debug_res></span> �.<br>
���������� ������� �� �����:   <span id=piccolo_tiraj_span class=debug_res></span> ��. &nbsp; &nbsp; &nbsp; � ������������:  <span id=piccolo_tiraj_s_pogr_span class=debug_res></span> ��<br>
��������� ������� �� �����:  <span id=piccolo_cost_all_span class=debug_res></span> �.<br>
��������� ������ �� ��������� �������:   <span id=piccolo_job_cost_span class=debug_res></span> �.<br>
����� ��������� ��������� �������:   <span id=piccolo_total_cost_span class=debug_res_vip></span> �.<br>
</span>

<span id=ukreplenie_span style="display: none">
<br /> <b>������ �� ���������� � ��������� ����������:</b><br />
���������� ���: <span id=dno_ukrepl_span class=debug_res></span><br>
������� ���: <span id=ploshad_dna_span class=debug_res></span> �� 2<br>
���������� �������: <span id=bok_ukrepl_span class=debug_res></span><br>
������� 2 �������: <span id=ploshad_bokovin_span class=debug_res></span> �� 2<br>
����� ������� ����������:   <span id=obshaya_ploshad_ukrepl_span class=debug_res></span> �� 2
����� ������� ���� ����������:   <span id=obshaya_ploshad_total_span class=debug_res></span> �2<br>
����� ��� ������� ��� ����������:   <span id=weight_karton_ukrepl_span class=debug_res></span> �� &nbsp; &nbsp; &nbsp;
� ������������:  <span id=weight_karton_ukrepl_span_s_pogr_span class=debug_res></span> ��<br>
��������� ������� ��� ����������:  <span id=karton_ukrepl_total_cost_span class=debug_res_vip></span> �.<br>
</span>
<span id=zapechatka_dna_span style="display: none">
� ����������?   <span id=karton_ukrepl_zapechatka_span class=debug_res></span><br>
���������� ������� �� 1 ���� �2:   <span id=dno_on_a2_span class=debug_res></span><br>
���������� �������� �� ������ ���:   <span id=kolvo_prokatok_pechat_dno_span class=debug_res></span><br>
��������� �������� �2 �� ������ ���:   <span id=priladka_pechat_dno_cost_span class=debug_res></span> �.<br>
����� ��������� �� ������ �� ���:   <span id=pechat_dno_cost_span class=debug_res_vip></span> �.<br>
</span>



</div>

<div id=smeta style="display: none">
��������: <span id=mater>123</span>�.   <br />
������:	<span id=pechat>123</span>�.   <br />
�����������: <span id=shelkograf>123</span>�.  <br />
��������� (������):	<span id=lami>123</span>�.  <br />
������������� (���):	<span id=mater>123</span>�.  <br />
�� ���:	<span id=uf>123</span>�.        <br />
������:	<span id=folga>123</span>�.      <br />
����� ��������:	<span id=klishe>123</span>�.    <br />
����� ��������:	<span id=udary_tisn>123</span>�. <br />
�����:	<span id=shtamp>123</span>�.        <br />
�������:	<span id=vyrubka>123</span>�. <br />
������ ������: <span id=karton_ukrepl>123</span>�.   <br />
�����:	<span id=tape>123</span>�.     <br />
������:	<span id=piccolo>123</span>�.    <br />
�����:	<span id=ruchki>123</span>�.  <br />
������:	<span id=sborka>123</span>�.    <br />
��������:	<span id=upak>123</span>�.  <br />
�������������� �������:	<span id=dop>123</span>�.   <br />
���������:	<span id=transport>123</span>�.        <br />
��������� �������:	<span id=nakladnie>123</span>�.   <br />
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
