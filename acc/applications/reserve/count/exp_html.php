<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");
//задаем умолчания, если не означено иное
if(!$items_on_page){$items_on_page = "300";}
$str = $_SERVER['QUERY_STRING'];
parse_str($str);
// ----- перейти на главную если доступ запрещен ---------
if(!$auth) {
  header("Location: /");
  exit;
}
// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;
?>

<html>
<script type="text/javascript" src="../../includes/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="../../includes/js/jquery-ui.min.js"></script>
<style type="text/css">
<!--
.apps_tbl {
    border: #C0C0C0 solid 1px;
    border-collapse: collapse;
    font-size: 10px;
}

.red {
 color:red;
}
.bold {
 font-weight:bold;
}
-->
</style>
<link href="../../style.css" rel="stylesheet" type="text/css" />
<script>
$(document).ready(function(){
$("#forma").change(function(){
get_table()});});


function del(j_uid){
if(j_uid){
pass=prompt('Введите пароль на удаление','');

var del;
  del = $.ajax({
    type: "GET",
    url: 'del.php',
	data : '&j_uid='+j_uid+'&pass='+pass,
    success: function () {
var resp1 = del.responseText
if (resp1 == "ok"){
$('#td_'+j_uid).animate({opacity: "0.3"}, 300);
}else{alert("Возникла ошибка! "+resp1)}
}})
}else{alert("Пароль введен не верно")}

}



function edit(j_uid){

num_sotr = $('#num_sotr_'+j_uid).html();
num_ord = $('#num_ord_'+j_uid+'_span').html();
num_of_work = $('#num_of_work_'+j_uid).html();
cur_time = $('#cur_time_'+j_uid).html();
job = $('#job_'+j_uid+'_span').html();

$('#num_sotr_'+j_uid).html("<input type=text size=4 id=fld_num_sotr_"+j_uid+" value="+num_sotr+">");
$('#num_ord_'+j_uid).html("<input type=text size=4 id=fld_num_ord_"+j_uid+" value="+num_ord+">");
$('#num_of_work_'+j_uid).html("<input type=text size=4 id=fld_num_of_work_"+j_uid+" value="+num_of_work+">");
$('#job_'+j_uid).html("<input type=text size=4 id=fld_job_"+j_uid+" value="+job+">");
$('#cur_time_'+j_uid).html("<input type=text size=15 id=fld_cur_time_"+j_uid+" value=\""+cur_time+"\">");

$('#edit_but_'+j_uid).html("<img src=\"../../../i/save.png\" style=\"cursor:pointer\" onclick=\"save('"+j_uid+"')\">").animate({opacity: "1"}, 300);

}

function save(j_uid){

pass=prompt('Введите пароль на изменение','');

num_sotr = $('#fld_num_sotr_'+j_uid).val();
num_ord = $('#fld_num_ord_'+j_uid).val();
num_of_work = $('#fld_num_of_work_'+j_uid).val();
job = $('#fld_job_'+j_uid).val();
cur_time = $('#fld_cur_time_'+j_uid).val();

var save;
  save = $.ajax({
    type: "GET",
    url: 'save.php',
    data : '&j_uid='+j_uid+'&num_sotr='+num_sotr+'&num_ord='+num_ord+'&num_of_work='+num_of_work+'&job='+job+"&cur_time="+cur_time+'&pass='+pass,
    success: function () {
var resp1 = save.responseText
if (resp1 == "ok"){
$('#num_sotr_'+j_uid).html(num_sotr);
$('#num_ord_'+j_uid).html(num_ord);
$('#num_of_work_'+j_uid).html(num_of_work);
$('#job_'+j_uid).html(job);
$('#cur_time_'+j_uid).html(cur_time);

$('#edit_but_'+j_uid).html("<img src=\"../../../i/edit_bg.png\" style=\"cursor:pointer\" onclick=\"edit('"+j_uid+"')\"> <img src=\"../../i/del.gif\" style=\"cursor:pointer\" onclick=\"del('"+j_uid+"')\">").animate({opacity: "1"}, 300); ;

//$('#td_'+j_uid).css("opacity", 0.8);
}else{alert("Возникла ошибка!"+resp1)}
}})


/*
num_sotr = $('#fld_num_sotr_'+j_uid).val();
num_ord = $('#fld_num_ord_'+j_uid).val();
num_of_work = $('#fld_num_of_work_'+j_uid).val();
job = $('#fld_job_'+j_uid).val();
cur_time = $('#cur_time_'+j_uid).val();*/




}



function jump(jumpfrom, maxsize, jumpto){
if($("#jumpoff").is(":not(:checked)")){
maxsize = maxsize-1
if ($('#'+jumpfrom).val().length > maxsize){$('#'+jumpto).select();
$('#'+jumpto).focus();
}}}

function replace_num(v) {
  var reg_sp = /[^\d^.]*/g;		// вырезание всех символов кроме цифр и точки
  v = v.replace(reg_sp, '');
  return v;
}


function hide_show_query(){
    $('#mysql_q').toggle();
}

function get_table(){
var str = $("#forma").serialize();
$("#table_div").html("<img src=\"../../../i/load2.gif\" style=\"align:middle;padding-bottom:30px;\">");
$.post("table.php?act=table&"+str, function(table) {}).done(function(table) {$("#table_div").html(table);  });
}


</script>



<body>
<?if($auth){?>
<script type="text/javascript" src="../../includes/js/wz_tooltip.js"></script>
<form id=forma method="post">
<table width="1300" border="1" class="apps_tbl" cellpadding="5" cellspacing="0">
<tr>
<td>
<select name=year_num id=year_num style="width:60px;">
<option value="" <?if($year_num== ""){echo "selected";}?>>год</option>
<option value="2014" <?if($year_num == "2014"){echo "selected";}?>>2014</option>
<option value="2015" <?if($year_num == "2015"){echo "selected";}?>>2015</option>
<option value="2016" <?if($year_num == "2016"){echo "selected";}?>>2016</option>
<option value="2017" <?if($year_num == "2017"){echo "selected";}?>>2017</option>
<option value="2018" <?if($year_num == "2018"){echo "selected";}?>>2018</option>
<option value="2019" <?if($year_num == "2019"){echo "selected";}?>>2019</option>
<option value="2020" <?if($year_num == "2020"){echo "selected";}?>>2020</option>
</select>

<select name=month_num id=month_num style="width:80px;">
<option value="" <?if($month_num == ""){echo "selected";}?>>месяц</option>
<option value="01" <?if($month_num == "01"){echo "selected";}?>>январь</option>
<option value="02" <?if($month_num == "02"){echo "selected";}?>>февраль</option>
<option value="03" <?if($month_num == "03"){echo "selected";}?>>март</option>
<option value="04" <?if($month_num == "04"){echo "selected";}?>>апрель</option>
<option value="05" <?if($month_num == "05"){echo "selected";}?>>май</option>
<option value="06" <?if($month_num == "06"){echo "selected";}?>>июнь</option>
<option value="07" <?if($month_num == "07"){echo "selected";}?>>июль</option>
<option value="08" <?if($month_num == "08"){echo "selected";}?>>август</option>
<option value="09" <?if($month_num == "09"){echo "selected";}?>>сентябрь</option>
<option value="10" <?if($month_num == "10"){echo "selected";}?>>октябрь</option>
<option value="11" <?if($month_num == "11"){echo "selected";}?>>ноябрь</option>
<option value="12" <?if($month_num == "12"){echo "selected";}?>>декабрь</option>
</select>
</td>
<td>артикул: <input type="text" size=4 maxlength=5 name=art_id onkeydown="replace_num(this.value)" value="<?=$art_id;?>"></td>
<td>заявка: <input type="text" size=4 maxlength=5 name=num_ord onkeydown="replace_num(this.value)" value="<?=$num_ord;?>"></td>
<td>
<select name="num_sotr" id="num_sotr" style="width:100px;">
<option value="">сотрудник</option>
<?$users = "SELECT `uid`, `job_id`, `surname`, `name`, archive FROM `users` WHERE (proizv = '1' OR nadomn = '1') AND archive != '1' ORDER BY surname ASC";
$users = mysql_query($users);
while ($r = mysql_fetch_array($users)){
?>
<option value="<?=$r["1"];?>" <?if($num_sotr == $r["1"]){echo "selected";}?>><?=$r["2"]." ".$r["3"];?></option>
<?}?>


<option value="">*---- АРХИВ ----*</option>
<?$users = "SELECT `uid`, `job_id`, `surname`, `name`, archive FROM `users` WHERE (proizv = '1' OR nadomn = '1') AND archive = '1' ORDER BY surname ASC";
$users = mysql_query($users);
while ($r = mysql_fetch_array($users)){
?>
<option value="<?=$r["1"];?>" <?if($num_sotr == $r["1"]){echo "selected";}?>><?=$r["2"]." ".$r["3"];?></option>
<?}?>


<option value="">*---- ОФИС ----*</option>
<?$users = "SELECT `uid`, `job_id`, `surname`, `name`, archive FROM `users` WHERE (administration = '1') ORDER BY surname ASC";
$users = mysql_query($users);
while ($r = mysql_fetch_array($users)){
?>
<option value="<?=$r["1"];?>" <?if($num_sotr == $r["1"]){echo "selected";}?>><?=$r["2"]." ".$r["3"];?></option>
<?}?>
</select>


</td>
<td>
<select name="izd_type" id="izd_type" style="width:80px;">
<option value="">Тип изделия</option>
<?$types = "SELECT tid, type FROM types ORDER BY seq DESC";
$types = mysql_query($types);
while ($r = mysql_fetch_array($types)){
?>
<option value="<?=$r["tid"];?>" <?if($izd_type == $r["tid"]){echo "selected";}?>><?=$r["type"];?></option>
<?}?>
</select>
</td>
<td>
<select id="izd_material" name="izd_material" style="width:80px" onchange="" >
<option value="">Материал</option>
<?$materials = mysql_query("SELECT * FROM materials ORDER BY seq DESC");
while($r =  mysql_fetch_array($materials)){
$id = $gg["tid"];
if($id == "0"){$id = "";}
?>
<option value="<?=$r["tid"];?>" <?if($izd_material == $r["1"]){echo "selected";}?>><?=$r["type"];?></option>
<?}?>
</select>
</td>
<td>
<select id="izd_color" name="izd_color" style="width:80px" onchange="" >
<option value="">Цвет</option>
<?
$get_colors = mysql_query("SELECT * FROM colours WHERE cid <> '0' ORDER BY seq DESC");
while($gg =  mysql_fetch_assoc($get_colors)){
if($gg["cid"] !== "0")?>
<option value="<?=$gg["cid"];?>" <?if($izd_color == $gg["cid"]){echo "selected";}?>><?=$gg["colour"];?></option>
<?}?>
</select>
</td>
<td>
<input type="text" style="width:30px;" id="izd_w" name="izd_w" value="<?=$izd_w;?>" onkeyup="this.value=replace_num(this.value);" onchange="" size=3  placeholder="Ширина"/>
<input type="text" style="width:30px;" id="izd_v" name="izd_v" value="<?=$izd_v;?>" onkeyup="this.value=replace_num(this.value);" onchange="" size=3  placeholder="Высота"/>
<input type="text" style="width:30px;" id="izd_b" name="izd_b" value="<?=$izd_b;?>" onkeyup="this.value=replace_num(this.value);" onchange="" size=3  placeholder="Бок"/>
</td>
<td>
<select name="job" id="job" style="width:100px;">
<option value="">Этап</option>
<?
$job_names = "SELECT id, name FROM job_names ORDER BY id ASC";
$job_names = mysql_query($job_names);
while ($r = mysql_fetch_array($job_names)){
?>
<option value="<?=$r["id"];?>" <?if($job == $r["id"]){echo "selected";}?>><?=$r["name"];?></option>
<?}?>
</select>
</td>
<td>
  <input type="checkbox" name="nadomn" id="nadomn" value="1" <?if($nadomn == "1"){echo "checked";}?>> <label for="nadomn" style="font-size:8px;">только <img src="../../../i/house.png"></label>
</td>
<td>по: <select name="items_on_page" id="items_on_page">
<option value="20" <?if($items_on_page == "20") {echo "selected";}?>>20</option>
<option value="50" <?if($items_on_page == "50") {echo "selected";}?>>50</option>
<option value="100" <?if($items_on_page == "100") {echo "selected";}?>>100</option>
<option value="300" <?if($items_on_page == "300") {echo "selected";}?>>300</option>
<option value="500" <?if($items_on_page == "500") {echo "selected";}?>>500</option>
<option value="1000" <?if($items_on_page == "1000") {echo "selected";}?>>1000</option>
<option value="10000" <?if($items_on_page == "10000") {echo "selected";}?>>10000</option>
</select>
 <img src="../../i/refresh.png" onmouseover="Tip('Обновить данные');" style="vertical-align: middle; cursor:pointer;" onclick="get_table()">

</td>
<td><a href="exp_html.php">сбросить</a></td>
</tr></table>
</form>

<form id=forma2>
<div id="table_div"></div>
</form>
<script>
get_table()
</script>
<?}?>
</body></html>