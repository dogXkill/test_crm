<?header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
ob_start();
$auth = false;

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");?>
<html>

<head>
  <title>Учет работы</title>
  <style type="text/css">
  <!--
.job-list{
	font-size: 20px;
	cursor: pointer;
}
.job-list:hover{
	font-size: 20px;
	cursor: pointer;
	background-color: #B6D6F1
}
.title{
	font-size: 30px;
	font-weight:bold;
}

.result{
	font-size: 23px;
	font-weight:bold;
}


.fld_num{
	width: 120px;
	height: 60px;
	font-size: 40px;

}

  -->
  </style>
</head>
<?if($auth){?>
<script type="text/javascript" src="../../includes/js/jquery-1.9.1.min.js"></script>

<script>

function get_sotr_name(){
num_sotr =$("#num_sotr").val();

var geturl;
  geturl = $.ajax({
  type: "POST",
  url: 'get_sotr_name.php',
  data : 'act=get_sotr_name&num_sotr='+num_sotr,
  success: function () {

var resp1 = geturl.responseText

if(resp1 == "no_sotr" || resp1 == ""){
$('#sotr_name_span').html("<span style=\"color: red; font-weight:bold;\">Сотрудника с таким номером не найдено. Уточните свой номер</span>");
$('#sotr_err').val("0")
$('#num_ord').prop("disabled", true)
$('#num_ord').val("")
	}
else{
$('#sotr_err').val("1")

$('#num_ord').prop("disabled", false)
$("#num_ord").focus();
$("#sotr_name_span").html(resp1);
$('#num_of_work').prop("disabled", true)
$('#num_of_work').prop("")
$('#num_of_work_err').val("0")
}
}});
unblock_but()
}

function get_nadomn_sotr_name(){
num_sotr =$("#num_nadomn_sotr").val();

var geturl;
  geturl = $.ajax({
  type: "POST",
  url: 'get_sotr_name.php',
  data : 'act=get_sotr_name&num_sotr='+num_sotr,
  success: function () {

var resp1 = geturl.responseText

if(resp1 == "no_sotr" || resp1 == ""){
$('#nadomn_name_span').html("<br>Надомника с таким номером не найдено. Уточните номер");
$('#nadomn_name_err').val("0")
unblock_but()
}
else{
$('#nadomn_name_err').val("1")
$("#nadomn_name_span").html("<br>Надомник: "+resp1);
unblock_but()
}
}});

}


function get_uid_name(){
num_ord = $('#num_ord').val();
var geturl;
  geturl = $.ajax({
  type: "POST",
  url: 'get_uid_name.php',
  data : 'act=get_uid_name&num_ord='+num_ord,
  success: function () {
var resp1 = geturl.responseText
resp1=resp1.split(";");
uid_err=resp1[0];
uid_name=resp1[1];
uid_tiraz=resp1[2];
uid_paper_num_list=resp1[3];

//alert(uid)
if(uid_err == "no_uid"){$('#uid_name_span').html("<br><span style=\"color: red; font-weight:bold;\">Такой заявки не найдено</span>");
$('#uid_err').val("0")

$('#num_of_work').prop("disabled", true)
$("#num_of_work").val("");
$("#uid_job_span").html("");
$('#num_of_work_err').val("0")
}
else if(uid_err == "uid_over"){$('#uid_name_span').html("<br><span style=\"color: red; font-weight:bold;\">Эта заявка уже выполнена</span>");
$('#uid_err').val("0")

$('#num_of_work').prop("disabled", true)
$("#num_of_work").val("");
$("#uid_job_span").html("");
$('#num_of_work_err').val("0")
}
else{
$('#uid_name_span').html(uid_name);
$('#uid_err').val("1")

$('#uid_tiraz').val(uid_tiraz)
$('#uid_paper_num_list').val(uid_paper_num_list)
$('#num_of_work').prop("disabled", false)
$("#1").focus();
$("#uid_job_span").html("");

}
unblock_but()
}});
}


function check_kol(){
job = $(":radio[name=job]").filter(":checked").val();

if(job > 0){
num_of_work = $('#num_of_work').val()
if (num_of_work > 0){
$('#num_of_work_err').val("1")
}else{
	//alert("Введите количество")
	$("#num_of_work").focus();
	$('#num_of_work_err').val("0")
}
}else{
	alert("Выберите вид работ")
    $("#1").focus();
	$('#num_of_work_err').val("0")
}
unblock_but()
}

function jfocus(){
$("#num_sotr").focus();
}

function kfocus(pril_num){
if ($('#2').prop("checked")==true){
$('#virub_pril').show(250)
}

if ($('#4').prop("checked")==true){
$('#sborka_nadomn').show(250)
}

if ($('#14').prop("checked")==true){
$('#nadomn_nomer').show(250)
$("#num_nadomn_sotr").focus();
$("#nadomn_name_err").val("0");
unblock_but()
return false;
}else{
$("#nadomn_name_err").val("1")
$('#num_nadomn_sotr').val("")
$('#nadomn_nomer').hide(250)
$('#num_nadomn_sotr').val("")
$('#nadomn_name_span').html("")
}

if ($('#3').prop("checked")==true){
$('#tisn_pril').show(250)
}

if ($('#5').prop("checked")==true){
$('#truba_pril').show(250)
}

if ($('#6').prop("checked")==true){
$('#dno_pril').show(250)
}

$("#num_of_work").val(pril_num);
$("#num_of_work").focus();

check_kol()
unblock_but()
}

function replace_num(v) {
	var reg_sp = /[^\d]*/g;		// вырезание всех символов кроме цифр
	v = v.replace(reg_sp, '');
	return v;
}

function replace_num_dots(v) {
	var reg_sp = /[^\d^\.]*/g;		// вырезание всех символов кроме цифр
	v = v.replace(reg_sp, '');
	return v;
}

function unblock_but(){

k1 = $("#sotr_err").val()*1
k2 = $("#uid_err").val()*1
k3 = $("#num_of_work_err").val()*1
k4 = $("#nadomn_name_err").val()*1
ball = k1 + k2 + k3 + k4
	//alert(ball)
if (ball == 4){$('#save_but').prop("disabled", false)}else{$('#save_but').prop("disabled", true)}
}

function add_job(){
num_sotr =$("#num_sotr").val();
num_ord = $('#num_ord').val();
job = $(":radio[name=job]").filter(":checked").val();
num_of_work = $("#num_of_work").val();


sotr_name = $("#sotr_name_span").html();
if (job == "1"){job_name = "ламинация";}
if (job == "2"){job_name = "вырубка";}
if (job == "3"){job_name = "тиснение";}
if (job == "4"){job_name = "сборка";}
if (job == "5"){job_name = "труба на линии";}
if (job == "6"){job_name = "сборка на линии";}
if (job == "7"){job_name = "приладка вырубки";}
if (job == "8"){job_name = "приладка тиснения";}
if (job == "9"){job_name = "приладка на линии (труба)";}
if (job == "10"){job_name = "приладка на линии (дно)";}
if (job == "11"){job_name = "упаковка";}
if (job == "12"){job_name = "вставка дна и боковин";}
if (job == "13"){job_name = "ручная подготовка трубы";}
if (job == "14"){
	job_name = "надомная работа";
	nadomn_name = $("#nadomn_name_span").html();
	nadomn_num = $("#num_nadomn_sotr").val();

	}else{nadomn_name = ""; nadomn_num = "";}
if (job == "15"){job_name = "ручки с клипсами (комплект)";}
if (job == "16"){job_name = "другое";}

if (job == "17"){job_name = "нарезка шнура на станке (2 шт)";}
if (job == "18"){job_name = "нарезка ленты/шнура вручную (2 шт)";}
if (job == "19"){job_name = "нарезка дна/боковин на 1 пакет";}
if (job == "20"){job_name = "привязка ленты с бантом 1 пакет";}
if (job == "21"){job_name = "привязка шнура узел 1 пакет";}
if (job == "22"){job_name = "вставка ручек с клипсами на 1 пакет";}
if (job == "23"){job_name = "сверление 1 пакет";}
if (job == "24"){job_name = "установка люверсов 4шт";}


 //alert(nadomn_num)
if($("#order").is(":checked")){
order_price = $("#order_price").val();
//alert(order_price)
//return false
} else{order_price="0"}

//if (confirm("Проверьте данные: "+job_name+" в количестве "+num_of_work+" записана на имя: "+sotr_name+" по заказу номер "+num_ord+" "+nadomn_name)){

var geturl;
  geturl = $.ajax({
  type: "POST",
  url: 'add_job.php',
  data : 'code=sfdfdsfsdfsdfsdf&num_sotr='+num_sotr+'&num_ord='+num_ord+'&job='+job+'&num_of_work='+num_of_work+"&nadomn_num="+nadomn_num+"&order_price="+order_price,
  success: function () {
//alert("&nadomn_num"+nadomn_num)
var resp1 = geturl.responseText
//alert(resp1)



if(resp1 == "ok"){
$("#ok").fadeIn(800).fadeOut(800);
clear_form()
}
else{
var resp = resp1.split(';');
if(resp[0] == "error"){alert (resp[1])}else{alert(resp1)}
}


}});
}
//}

function clear_form(){
$(':input','#insert_form')
 .not(':button, :submit, :reset, :hidden')
 //.val('')
 .removeAttr('checked')
 .removeAttr('selected');

$("#num_sotr").val("");
$("#num_ord").val("");
$("#num_of_work").val("");

$("#sotr_err").val("0");
$("#uid_err").val("0");
$("#num_of_work_err").val("0");

$("#sotr_name_span").html("");
$("#uid_name_span").html("");
$("#uid_job_span").html("");
$("#num_nadomn_sotr").val("");
$("#nadomn_name_err").val("0");
$("#nadomn_nomer").hide();

$("#nadomn_name_span").html("");
$('#num_ord').prop("disabled", true)
$('#num_of_work').prop("disabled", true)
$('#save_but').prop("disabled", true)

$('#order_price_div').hide();
$("#order_price").val("");

}

function show_type(type){
   if(type == "line"){$('#linia').toggle(250)}
   if(type == "dops"){$('#dops').toggle(250)}
   if(type == "vyaz"){$('#vyaz_raboty').toggle(250)}
$('#osn_raboty').toggle(250)
}


</script>


<body onload=jfocus()>

<form action="" id=insert_form name=insert_form>
<table width="1000" border="1" cellpadding="10" cellspacing="1">
<tr>
<td width=250>
<span class=title>Ваш номер:</span>
</td>
<td width=400 style="width:400px"><input type=text class=fld_num onchange="get_sotr_name()" maxlength=4 name="num_sotr" id="num_sotr" onkeyup="this.value=replace_num(this.value);">
<input type=hidden name=sotr_err id=sotr_err value="0"></td>
<td width=400 style="width:400px" id=sotr_name_span class=result></td>
</tr>
<tr>
<td height=200><span class=title>Номер заявки:</span></td>
<td><input type=text class=fld_num onchange="get_uid_name()" maxlength=4 name="num_ord" id="num_ord" disabled onkeyup="this.value=replace_num(this.value);">
<input type=hidden name=uid_err id=uid_err value="0"></td>
<td style="width:300px" id=uid_name_span class=result></td>
</tr>
<tr>
<td>
<span class=title>Выберите вид работы:</span>
</td>
<td>
<span id=osn_raboty>
<input type=radio id="1" name="job" value="1" onchange="kfocus()"> <label for="1" class=job-list>ламинирование</label><br>
<input type=radio id="2" name="job" value="2" onchange="kfocus()"> <label for="2" class=job-list>вырубка</label>
<span id=virub_pril style="display:none"><input type=radio id="7" name="job" value="7" onchange="kfocus(1)">
<label for="7" class=job-list>приладка</label></span><br>
<input type=radio id="3" name="job" value="3" onchange="kfocus()"> <label for="3" class=job-list>тиснение</label>
<span id=tisn_pril style="display:none"><input type=radio id="8" name="job" value="8" onchange="kfocus(1)"> <label for="8" class=job-list>приладка</label></span><br>
<input type=radio id="4" name="job" value="4" onchange="kfocus()"> <label for="4" class=job-list>сборка</label>
<span id=sborka_nadomn style="display:none"><input type=radio id="14" name="job" value="14" onchange="kfocus()"> <label for="14" class=job-list>надомная</label></span>
<span id=nadomn_nomer style="display:none"><br>номер надомника: <input type=text class=fld_num onchange="get_nadomn_sotr_name()" maxlength=4 name="num_nadomn_sotr" id="num_nadomn_sotr" onkeyup="this.value=replace_num(this.value);"> <span id=nadomn_name_span style="font-size: 12px; font-face:arial"></span><input type=hidden name=nadomn_name_err id=nadomn_name_err value="0"></span>
</span>


<br>
<input type=radio id="11" name="job" value="11" onchange="kfocus()"> <label for="11" class=job-list>упаковка</label><br>



<span style="border-bottom: 2px dotted; font-size: 25px;cursor:pointer" onclick="show_type('line')">линия</span><br>
<span id=linia style="display:none">
<table>
<tr><td><input type=radio id="12" name="job" value="12" onchange="kfocus()"> <label for="12" class=job-list>вставка дна и боковин</label></td></tr>
<tr><td><input type=radio id="13" name="job" value="13" onchange="kfocus()"> <label for="13" class=job-list>ручная подготовка трубы</label></td></tr>
<tr><td><input type=radio id="5" name="job" value="5" onchange="kfocus()"> <label for="5" class=job-list>труба на линии</label>
<span id=truba_pril style="display:none"><input type=radio id="9" name="job" value="9" onchange="kfocus(1)"> <label for="9" class=job-list>приладка</label></span>
</td></tr>
<tr><td><input type=radio id="6" name="job" value="6" onchange="kfocus()"> <label for="6" class=job-list>дно на линии</label>
<span id=dno_pril style="display:none"><input type=radio id="10" name="job" value="10" onchange="kfocus(1)"> <label for="10" class=job-list>приладка</label></span>
</td></tr>
</table>
<br></span>


<span style="border-bottom: 2px dotted; font-size: 25px;cursor:pointer" onclick="show_type('dops')">допработы</span><br>
<span id=dops style="display:none">
<table>
<tr><td><input type=radio id="17" name="job" value="17" onchange="kfocus()"> <label for="17" class=job-list>нарезка шнура на станке (2 шт)</label></td></tr>
<tr><td><input type=radio id="18" name="job" value="18" onchange="kfocus()"> <label for="18" class=job-list>нарезка ленты/шнура вручную (2 шт)</label></td></tr>
<tr><td><input type=radio id="19" name="job" value="19" onchange="kfocus()"> <label for="19" class=job-list>нарезка дна/боковин на 1 пакет</label></td></tr>
<tr><td><input type=radio id="20" name="job" value="20" onchange="kfocus()"> <label for="20" class=job-list>привязка ленты с бантом 1 пакет</label></td></tr>
<tr><td><input type=radio id="21" name="job" value="21" onchange="kfocus()"> <label for="21" class=job-list>привязка шнура узел 1 пакет</label></td></tr>
<tr><td><input type=radio id="22" name="job" value="22" onchange="kfocus()"> <label for="22" class=job-list>вставка ручек с клипсами на 1 пакет</label></td></tr>
<tr><td><input type=radio id="23" name="job" value="23" onchange="kfocus()"> <label for="23" class=job-list>сверление 1 пакет</label></td></tr>
<tr><td><input type=radio id="24" name="job" value="24" onchange="kfocus()"> <label for="24" class=job-list>установка люверсов 4шт</label></td></tr>
</table>
<br></span>



<span style="border-bottom: 2px dotted; font-size: 25px;cursor:pointer" onclick="show_type('vyaz')">вязальный участок</span><br>
<span id=vyaz_raboty style="display:none">
<table>
<tr><td><input type=radio id="15" name="job" value="15" onchange="kfocus()"> <label for="15" class=job-list>ручки с клипсами</label></td></tr>
<tr><td><input type=radio id="16" name="job" value="16" onchange="kfocus()"> <label for="16" class=job-list>другое</label></td></tr>
</table>
<br></span>



</td>
<td rowspan=3 valign=top>



</td>
</tr>
<tr>
<td><span class=title>Количество:</span></td>
<td><input type=text class=fld_num name="num_of_work" id="num_of_work" maxlength=5 disabled onkeyup="this.value=replace_num(this.value);check_kol()">
<input type=hidden name=num_of_work_err id=num_of_work_err value="0">
<span id=uid_job_span class=result></span>
</td>

</tr>
<tr>
<td></td>
<td><label class=job-list for="order">ордер?</label> <input type="checkbox" id="order" name="order" onclick="jquery:$('#order_price_div').toggle();$('#order_price').focus();$('#order_price').val('');" value="1" /><br>
<span style="display:none" id="order_price_div">тариф: <input type=text class=fld_num name="order_price" id="order_price" maxlength=5 onkeyup="this.value=replace_num_dots(this.value);"></span></td>
</tr>

<tr>
<td height="90" width=250></td>
<td><input type=button style="width: 250px; height: 40px; font-size: 30px;" value="Ввести" id="save_but" onclick="add_job()" disabled>

</td>
<td><span id=ok style="display: none; width: 250px; height:100px; border-color: #CDCDCD; padding: 10px; background-color: green; color:white; ">
<span style="font-size: 17px; align:center; vertical-align: middle; font-weight:bold;">Спасибо! Данные внесены.</span>
</span></td>
</tr>
</table></form>

<?}?>

</body>

</html><? ob_end_flush(); ?>   