<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

ob_start();

$auth = false;

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");
if ($user_access['sprav_access'] == '0' || empty($user_access['sprav_access'])) {
  header('Location: /');
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.2</title>
<link href="../../style.css?cache=<?=rand(1,1000000);?>" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../../includes/js/jquery-ui.js"></script>
<script src="../../includes/js/jquery.cookie.js"></script>
<link rel="stylesheet" type="text/css" href="../../includes/fonts/css/all.min.css">
<style type="text/css">
<!--
#vendors_table td, #tbl td  {
border: 1px solid #336699;
padding: 4px;
}
.spans{
border: 1px solid #336699;
background-color:white;
padding: 10px;
display:none;
}
#vendors_table tr:hover, #tbl tr:hover{
background-color: #E6E6E6;
}
</style>
</head>


<body>

<script type="text/javascript" src="../../includes/js/wz_tooltip.js"></script>
<? require_once("../../templates/top.php"); ?>
   <?
      $name_curr_page = 'sprav';
      require_once("../../templates/main_menu.php");

	   $part = $_GET["part"];
       ?>
<table align="center" width="1700" border="0" cellpadding=10 bgcolor="#F6F6F6">
  <tr>
    <td>

<?require_once("../../templates/spravmenu.php");  ?>

<? if($part=="vendor_types"){?>
<script>
function add_type(act, id){

name = $("#name_"+id).val();

if(name == ""){
alert ("Это поле не должно быть пустым!")
$("#name_"+id).focus();
return false
}

if(act == "del"){
if(!confirm("Вы уверены что хотите удалить данный тип?")){return false}
}


var geturl;
  geturl = $.ajax({
    type: "GET",
    url: 'vendor_types.php',
	data : '&act='+act+'&id='+id+'&name='+name,
    success: function () {
var resp = geturl.responseText
if($.isNumeric(resp)){
$('#start_tr').clone().fadeIn(1000).attr('id', 'tr_id_'+resp).prependTo("#tbl");
$('#tr_id_' + resp).html("<td class='font-weight:bold;' id='id_"+resp+"'>"+resp+"</td><td id='td_name_"+resp+"'>"+name+"</td><td align=center>0</td><td></td>");
}else if(resp == "EDIT_OK"){
edit(id, act)
}else if(resp == "DEL_OK"){
$("#tr_id_"+id).fadeOut(1000)();
}else{alert(resp)}

}})

if(act == "add"){
$("#name_").val("");
}
}

function edit(id, act){
if(act!=="save"){
name = $("#td_name_"+id).html();
$("#td_name_"+id).html("<input type=text id='name_"+id+"' style='width:150px;' value='"+name+"'>");
$("#edit_but_"+id).hide();
$("#save_but_"+id).show();
$("#name_"+id).focus();
}else{
gname = $("#name_"+id).val();
$("#td_name_"+id).html(name);
$("#edit_but_"+id).show();
$("#save_but_"+id).hide();
}
}


function show_add_group(){
$('#span_add_type').fadeIn(1000);
$('#name_').focus();

}
</script>
<table cellpadding=10>
<tr>
<td>
<!--<img src="../../../i/add_job.png" width="22" height="22"  onclick="show_add_group()" align="middle"> -->
<i class="fa-solid fa-square-plus icon_btn_r21 icon_btn_blue" onclick="show_add_group()"></i>
<span class=sublink onclick="show_add_group()">добавить тип поставщика</span>

<span id="span_add_type" style="display:none;">
название: <input type="text" style="width:200px" id="name_" />
<button onclick="add_type('add', '')">OK!</button>
</span>
<table id="tbl" cellpadding=0 cellspacing=0 width=800>
<tr id="start_tr" style="display:none;"></tr>

<?

//получаем количества товаров в каждой группе
$get_types = mysql_query("SELECT * FROM vendor_types");
while($r =  mysql_fetch_array($get_types)){
$id = $r['id'];
$get_qty = mysql_query("SELECT COUNT(*) AS qty FROM vendor_gid WHERE gid = '$id'");
while($gg =  mysql_fetch_array($get_qty)){
$vendor_qty[$id] .= $gg["qty"];
}}


$get_types = mysql_query("SELECT * FROM vendor_types ORDER BY name ASC");
while($r =  mysql_fetch_array($get_types)){
?>
<tr id="tr_id_<?=$r['id'];?>">
<td width=20 class="font-weight:bold;"><?=$r['id'];?></td>
<td width=250 id="td_name_<?=$r['id'];?>"><?=$r['name'];?></td>
<td align=center><?=$vendor_qty[$r['id']];?></td>
<td>
<img src="../../../i/save.png" width="24" height="24" style="cursor:pointer;display:none;" align="absmiddle" onclick="add_type('save', '<?=$r['id'];?>')" id="save_but_<?=$r['id'];?>">
<img src="../../../i/edit_bg.png" width="22" height="22" onclick="edit('<?=$r['id'];?>')" align="absmiddle" style="cursor:pointer" id="edit_but_<?=$r['id'];?>">
<img src="/acc/i/del.gif" width="20" height="20"  align="absmiddle" style="cursor:pointer;"  onclick="add_type('del', '<?=$r['id'];?>')">
</td>
</tr>

<?}?>

</table>
</td>
</tr>
</table>
<?}?>

<?if($part=="vendors"){?>

<script>
function add_type(act, id){
//if(id == 'undefined'){id = '';}
name = $("#name_"+id).val();
//alert("#name_"+id)
//alert(act + ' ' + name + ' ' + id)
cont_person = $("#cont_person_"+id).val();
cont_phone = $("#cont_phone_"+id).val();

if(name == ""){
alert("Это поле не должно быть пустым!")
$("#name_"+id).focus();
return false
}

if(act == "del"){
if(!confirm("Вы уверены что хотите удалить данного поставщика?")){return false}
}


var geturl;
  geturl = $.ajax({
    type: "GET",
    url: 'vendors.php',
	data : '&act='+act+'&id='+id+'&name='+name+'&cont_person='+cont_person+'&cont_phone='+cont_phone,
    success: function () {
var resp = geturl.responseText
if($.isNumeric(resp)){
$('#start_tr').clone().fadeIn(1000).attr('id', 'tr_id_'+resp).prependTo("#vendors_table");
$('#tr_id_' + resp).html("<td class='font-weight:bold;' id='id_"+resp+"'>"+resp+"</td><td id='td_name_"+resp+"'>"+name+"</td><td id='td_cont_person_"+resp+"'>"+cont_person+"</td><td id='td_cont_phone_"+resp+"'>"+cont_phone+"</td><td></td>");
}else if(resp == "EDIT_OK"){
edit(id, act)
}else if(resp == "DEL_OK"){
$("#tr_id_"+id).fadeOut(1000)();
}else{alert(resp)}

}})

if(act == "add"){
$("#name_").val("");
$("#cont_person_").val("");
$("#cont_phone_").val("");
}
}

function edit(id, act){
if(act!=="save"){
name = $("#td_name_"+id).html();
$("#td_name_"+id).html("<input type=text id='name_"+id+"' style='width:150px;' value='"+name+"'>");
cont_person = $("#td_cont_person_"+id).html();
$("#td_cont_person_"+id).html("<input type=text id='cont_person_"+id+"' style='width:150px;' value='"+cont_person+"'>");
cont_phone = $("#td_cont_phone_"+id).html();
$("#td_cont_phone_"+id).html("<input type=text id='cont_phone_"+id+"' style='width:150px;' value='"+cont_phone+"'>");
$("#edit_but_"+id).hide();
$("#save_but_"+id).show();
$("#name_"+id).focus();
}else{
name = $("#name_"+id).val();
$("#td_name_"+id).html(name);
cont_person = $("#cont_person_"+id).val();
$("#td_cont_person_"+id).html(cont_person);
cont_phone = $("#cont_phone_"+id).val();
$("#td_cont_phone_"+id).html(cont_phone);

$("#edit_but_"+id).show();
$("#save_but_"+id).hide();
}
}


function show_add_group(){
$('#span_add_type').fadeIn(1000);
$('#name_').focus();
}


function show_groups(uid, act){


if(act == "save"){
$('input:checkbox[name=gid]:checkbox:checked').each(function(){
new_gid = $(this).val()
gid = new_gid + "," + gid

});

}else{gid = ""}



var geturl;
  geturl = $.ajax({
    type: "GET",
    url: 'set_groups.php',
	data : '&act='+act+'&uid='+uid+'&gid='+gid,
    success: function () {
var resp = geturl.responseText
//alert(resp)
if(act == "show"){
	hide_show_div(uid, 'show')
    $('#span_change_group').html(resp);
}
if(act == "save"){
	if(resp == ''){resp = "выбрать";}
	$("#span_"+uid).html(resp)
	hide_show_div(uid, 'hide')
	$('#span_change_group').html("");
	}
}})
}

function hide_show_div(uid, act){
var pos = $("#td_"+uid).position();

$('#span_change_group').css({
    position: 'absolute',
    left: pos.left,
    top: pos.top
});

if(act == "show"){
$('#span_change_group').fadeIn(200);
}else{$('#span_change_group').fadeOut(300);}
}
/*
$('#span_change_group').css({
    position: 'absolute',
    left: pos.left,
    top: pos.top
});   */

</script>

<!--<img src="../../../i/add_job.png" width="22" height="22"  onclick="show_add_group()" align="middle"> -->
<i class="fa-solid fa-square-plus icon_btn_r21 icon_btn_blue" onclick="show_add_group()"></i>
<span class=sublink onclick="show_add_group()">добавить поставщика</span>
<span id="span_change_group" class="spans"></span>
<span id="span_add_type" style="display:none;">
название: <input type="text" style="width:200px" id="name_" />
контактное лицо: <input type="text" style="width:200px" id="cont_person_" />
телефон: <input type="text" style="width:200px" id="cont_phone_" />
<button onclick="add_type('add','')">OK!</button>
</span>

<table id="vendors_table" cellpadding=0 cellspacing=0 width=1700>

<tr id="start_tr" style="display:none;"></tr>
<?
$get_types = mysql_query("SELECT * FROM vendors ORDER BY name ASC");
while($r =  mysql_fetch_array($get_types)){
?>
<tr id="tr_id_<?=$r['id'];?>">
<td width=20 class="font-weight:bold;"><?=$r['id'];?></td>
<td width=350 id="td_name_<?=$r['id'];?>"><?=$r['name'];?></td>
<td width=350 id="td_cont_person_<?=$r['id'];?>"><?=$r['cont_person'];?></td>
<td width=380 id="td_cont_phone_<?=$r['id'];?>"><?=$r['cont_phone'];?></td>
<td align=center id="td_<?=$r['id'];?>"><?if($r['grup'] == "0" or $r['grup'] ==""){$group = "выбрать";}else{$group = $plan_groups[$r['grup']];}?><span class=sublink onclick="show_groups('<?=$r['id'];?>', 'show')" id="span_<?=$r['id'];?>"><?=$group;?></span></td>

<td width=500>
<img src="../../../i/save.png" width="24" height="24" style="cursor:pointer;display:none;" align="absmiddle" onclick="add_type('save', '<?=$r['id'];?>')" id="save_but_<?=$r['id'];?>">
<img src="../../../i/edit_bg.png" width="22" height="22" onclick="edit('<?=$r['id'];?>')" align="absmiddle" style="cursor:pointer" id="edit_but_<?=$r['id'];?>">
<img src="/acc/i/del.gif" width="20" height="20"  align="absmiddle" style="cursor:pointer;"  onclick="add_type('del', '<?=$r['id'];?>')">
</td>
</tr>

<?}?>

</table>




<?}?>




</td></tr>
</table>


</body>
</html>
<? ob_end_flush(); ?>
