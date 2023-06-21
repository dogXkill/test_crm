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



// Класс для работы с форматами дат
class dat_fn
{
	var $basedat 		= '';				// формат базы DATE
	var $basetm 		= '';				// формат базы DATETIME
	var $tmstamp 		= 0;				// tamestamp
	var $stringdat	= '';				// формат 12.05.2009
	var $stringtm		= '';				// формат 12.05.2009 12:49


	function dat_fn($dat, $tp = 0) {
		if($tp ==0) {		// формат базы данных 0000-00-00 00:00:00

				if((!trim($dat)) || ($dat == '0000-00-00 00:00:00'))
					return 0;

				preg_match('/(\d{4})-(\d{1,2})-(\d{1,2})\s(\d{1,2}):(\d{1,2}):(\d{1,2})/', $dat, $t);

				// timestamp
				$this -> tmstamp 		= mktime($t[4],$t[5],$t[6],$t[2],$t[3],$t[1]);
				$this -> basedat 		= date("Y-m-d", $this -> tmstamp);
				$this -> basetm 		= $dat;
				$this -> stringdat	= date("d.m.Y", $this -> tmstamp);
				$this -> stringtm		= date("d.m.Y", $this -> tmstamp);

		} elseif($tp == 1) {	// формат базы данных 0000-00-00

				if((!trim($dat)) || ($dat == '0000-00-00'))
					return 0;

				preg_match('/(\d{4})-(\d{1,2})-(\d{1,2})/', $dat, $t);

				// timestamp
				$this -> tmstamp 		= mktime(0,0,0,$t[2],$t[3],$t[1]);
				$this -> basedat 		= $dat;
				$this -> basetm 		= date("Y-m-d 00:00:00", $this -> tmstamp);
				$this -> stringdat	= date("d.m.Y", $this -> tmstamp);
				$this -> stringtm		= date("d.m.Y H:i", $this -> tmstamp);

		} elseif($tp == 2) {	// формат 01.02.2009 16:54

				if((!trim($dat)) || ($dat == '00.00.0000 00:00'))
					return 0;

				preg_match('/(\d{1,2})\.(\d{1,2})\.(\d{4})\s(\d{1,2}):(\d{1,2})/', $dat, $t);

				$this -> tmstamp 		= mktime($t[4],$t[5],0,$t[2],$t[1],$t[3]);
				$this -> basedat 		= date("Y-m-d", $this -> tmstamp);
				$this -> basetm 		= date("Y-m-d H:i:00", $this -> tmstamp);
				$this -> stringdat	= date("d.m.Y", $this -> tmstamp);
				$this -> stringtm		= date("d.m.Y H:i", $this -> tmstamp);

		} else {		// формат 01.02.2009

				if((!trim($dat)) || ($dat == '00.00.0000'))
					return 0;

				preg_match('/(\d{1,2})\.(\d{1,2})\.(\d{4})/', $dat, $t);

				$this -> tmstamp 		= mktime(0,0,0,$t[2],$t[1],$t[3]);
				$this -> basedat 		= date("Y-m-d", $this -> tmstamp);
				$this -> basetm 		= date("Y-m-d 00:00:00", $this -> tmstamp);
				$this -> stringdat	= date("d.m.Y", $this -> tmstamp);
				$this -> stringtm		= date("d.m.Y H:i", $this -> tmstamp);

		}

		return $this -> tmstamp;
	}
}

$tpus = $user_type;		// тип пользователя

// ----- перейти на главную если доступ запрещен ---------
if(!$auth) {
	header("Location: /");
	exit;
}
// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;

// ------------------- Удаление нескольких запросов ---------------------
if( isset($_POST['subm']) && trim($_POST['subm']) && $_POST['subm'] == '1' ) {

	$ch_arr = $_POST['ch_arr'];

	for($i=0;$i<count($ch_arr);$i++) {
    mysql_query("DELETE FROM applications_shipping_list WHERE apl_id=".$ch_arr[$i]);
    mysql_query("DELETE FROM applications WHERE uid=".$ch_arr[$i]);
  }
  header("Location: ".$_SERVER['HTTP_REFERER']);
}



// ------------------- Выполнение нескольких запросов ---------------------
if( isset($_POST['subm']) && trim($_POST['subm']) && $_POST['subm'] == '2' ) {
	$ch_arr = $_POST['ch_arr'];

  $dat_exec = new dat_fn(date("d.m.Y H:i"),2);
  $dat_exec = $dat_exec -> basetm;

	for($i=0;$i<count($ch_arr);$i++) {
    mysql_query("UPDATE applications SET exec_on=1,exec_dat='$dat_exec' WHERE uid=".$ch_arr[$i]);
  }
  header("Location: ".$_SERVER['HTTP_REFERER']);
}



// Выполнить заявку
if(isset($_REQUEST['set']) && is_numeric($_REQUEST['set'])) {
  $dat_exec = new dat_fn(date("d.m.Y H:i"),2);
  $dat_exec = $dat_exec -> basetm;

  $query = "UPDATE applications SET exec_on=1,exec_dat='$dat_exec' WHERE uid=".$_REQUEST['set'];
  mysql_query($query);
  header("Location: list.php");
}



// Отменить выполнение заявки
if(isset($_REQUEST['unset']) && is_numeric($_REQUEST['unset'])) {
  $query = "UPDATE applications SET exec_on=0 WHERE uid=".$_REQUEST['unset'];
  mysql_query($query);
  header("Location: list.php");
}




// Удаление заявки
if(isset($_REQUEST['del']) && is_numeric($_REQUEST['del'])) {
  mysql_query("DELETE FROM applications_shipping_list WHERE apl_id=".$_REQUEST['del']);
  mysql_query("DELETE FROM applications WHERE uid=".$_REQUEST['del']);
  header("Location: ".$_SERVER['HTTP_REFERER']);
}





//  ------------- разбиение по страницам -------------
$rows_onpage = 60; 		// запросов на странице
$all_pages = false; 	// истина при нажатии ссылки "показать все"
$page = 1;						// страница по умолчанию

if(isset($_GET['page'])) {

	if(is_numeric($_GET['page']))
		$page = ($_GET['page']) > 0 ? $_GET['page'] : 1;
	elseif($_GET['page'] = 'all')
		$all_pages = true;
}


if (!$_GET["act"]){$query_num_rows = "SELECT uid FROM applications WHERE 1=1";}
if ($_GET["act"] == "by_key"){$val = $_GET["val"]; $query_num_rows = "SELECT uid FROM applications WHERE 1=1 AND title LIKE '%$val%'";}
if ($_GET["act"] == "by_query_id"){$val = $_GET["val"]; $query_num_rows = "SELECT uid FROM applications WHERE 1=1 AND  zakaz_id = '$val'";}
if ($_GET["act"] == "just_ser"){$query_num_rows = "SELECT uid FROM applications WHERE 1=1 AND art_num > '0'";}
if ($_GET["act"] == "by_uid"){$val = $_GET["val"]; $query_num_rows = "SELECT uid FROM applications WHERE 1=1 AND num_ord = '$val'";}
if ($_GET["act"] == "by_art_num"){$val = $_GET["val"]; $query_num_rows = "SELECT uid FROM applications WHERE 1=1 AND  art_num = '$val'";}



if ($_GET["act"] == "by_size"){

$paper_width = $_GET["paper_width"];
$paper_height = $_GET["paper_height"];
$paper_side = $_GET["paper_side"];
$query_num_rows = "SELECT uid FROM applications WHERE 1=1 AND paper_width = '$paper_width' AND paper_height = '$paper_height' AND paper_side = '$paper_side'";}


$res_num_rows = mysql_query($query_num_rows);
$num_all_rows = mysql_num_rows($res_num_rows);

$num_pages = ceil($num_all_rows/$rows_onpage);

if($page > $num_pages)
  $page = $num_pages;

$limit_start = ($all_pages) ? 0 : ($page-1)*$rows_onpage;
$limit_num = ($all_pages) ? 10000 : $rows_onpage;

//ищем по ключевику
if ($_GET["act"] == "by_key"){
$val =$_GET["val"];
//echo $str;
$query = "SELECT a.*,b.surname FROM applications AS a LEFT JOIN users as b ON(a.user_id=b.uid) WHERE 1=1 AND title LIKE '%$val%' ORDER BY a.exec_on,a.dat_ord DESC LIMIT $limit_start,$limit_num";
}

//ищем по номеру заказа
if ($_GET["act"] == "by_uid"){
$val =$_GET["val"];
$query = "SELECT a.*,b.surname FROM applications AS a LEFT JOIN users as b ON(a.user_id=b.uid) WHERE 1=1 AND num_ord = '$val' ORDER BY a.exec_on,a.dat_ord DESC LIMIT $limit_start,$limit_num";
}

//ищем по номеру заказа-счета
if ($_GET["act"] == "by_query_id"){
$val =$_GET["val"];
$query = "SELECT a.*,b.surname FROM applications AS a LEFT JOIN users as b ON(a.user_id=b.uid) WHERE 1=1 AND zakaz_id = '$val' ORDER BY a.exec_on,a.dat_ord DESC LIMIT $limit_start,$limit_num";
}

//ищем по номеру артикула
if ($_GET["act"] == "by_art_num"){
$val =$_GET["val"];
$query = "SELECT a.*,b.surname FROM applications AS a LEFT JOIN users as b ON(a.user_id=b.uid) WHERE 1=1 AND art_num = '$val' ORDER BY a.exec_on,a.dat_ord DESC LIMIT $limit_start,$limit_num";
}

//ищем по размеру пакета
if ($_GET["act"] == "by_size"){
$query = "SELECT a.*,b.surname FROM applications AS a LEFT JOIN users as b ON(a.user_id=b.uid) WHERE 1=1 AND paper_width = '$paper_width' AND paper_height = '$paper_height' AND paper_side = '$paper_side' ORDER BY a.exec_on,a.dat_ord DESC LIMIT $limit_start,$limit_num";
}

//выбираем только серийку
if ($_GET["act"] == "just_ser"){
$query = "SELECT a.*,b.surname FROM applications AS a LEFT JOIN users as b ON(a.user_id=b.uid) WHERE 1=1 AND art_num > '0' ORDER BY a.exec_on,a.dat_ord DESC LIMIT $limit_start,$limit_num";
}

if (!$_GET["act"]){
$query = "SELECT a.*,b.surname FROM applications as a LEFT JOIN users as b ON(a.user_id=b.uid) WHERE 1=1 ORDER BY a.exec_on,a.dat_ord DESC LIMIT $limit_start,$limit_num";
}




$res_apl = mysql_query($query);

$res_apl1 = mysql_query($query);
//собираем инфу из работы в массив
while(@$r2 = mysql_fetch_array($res_apl1)) {
$ord_num = $r2["num_ord"];
$select_ord = mysql_query("SELECT job, SUM(num_of_work) FROM job WHERE num_ord='$ord_num' GROUP BY job");
$ord_data[$ord_num] = mysql_fetch_row($select_ord);
//echo "<strong>".$ord_num."--></strong>";
//print_r ($ord_data[$ord_num][1])." ";
}
// ---------------------------------------------------------
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.2</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" media="all" href="../includes/js/calendar/calendar-blue.css" title="Aqua" />
</head>

<script src="../includes/lib/JsHttpRequest/JsHttpRequest.js"></script>
<script type="text/javascript" src="../includes/js/calendar/calendar.js"></script>
<script type="text/javascript" src="../includes/js/calendar/calendar-ru_win_.js"></script>
<script type="text/javascript" src="../includes/js/calendar/calendar-setup.js"></script>
<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>

<script language="JavaScript" type="text/javascript">
<!--
// координаты мыши
var xpos=0;
var ypos=0;

var txpos = 0;
var typos = 0;

var tpacc = <?=$tpacc?>;

var curr_date = '<?=date("d.m.Y")?>';		// текущая дата в формате '01.05.2007'

function delall_act() {
	document.getElementById('delall_im').src = '../i/delall_act.gif';
}

function delall_dis() {
	document.getElementById('delall_im').src = '../i/delall_dis.gif';
}

// кнопка выполнить запросы
function setall_act() {
	document.getElementById('setall_im').src = '../i/setall_act.gif';
}

function setall_dis() {
	document.getElementById('setall_im').src = '../i/setall_dis.gif';
}



function show_app_info(ord_num){

if(ord_num){
	$('#app_info_'+ord_num).toggle(250);
var show_info;
  show_info = $.ajax({
    type: "GET",
    url: 'count/show_app_info.php',
	data : '&code=fdsfds8fu883832ije99089fs&ord_num='+ord_num,
    success: function () {
var resp1 = show_info.responseText
if (resp1){
button = "<br><a href=\"/acc/applications/count/exp_html.php?act=all&num_ord="+ord_num+"&num_sotr=&items_on_page=300\" target=\"_blank\"><img src=\"../../i/journal.png\" align=\"middle\"></a> <a href=\"/acc/applications/count/exp_html.php?act=all&num_ord="+ord_num+"&num_sotr=&items_on_page=300\" class=sublink target=\"_blank\">посмотреть в журнале</a>";
$('#app_info_'+ord_num).html(resp1+" "+button);
}else{alert("Возникла ошибка!")}
}})
}

}

function get_csv(){
year = $('#year').val();
month = $('#month').val();
window.location.href = 'count/exp_csv.php?year='+year+'&month='+month;
}
function export_div(){
$('#export_div').toggle(250)
}
function set_y_m(){
var date = new Date(),
year = date.getFullYear(),
month = date.getMonth()+1;
if (month<10) {month='0'+month;}
$("select#year").val(year)
$("select#month").val(month)
}
function get_timetable(){
year = $('#year').val();
month = $('#month').val();
window.location.href = 'timetable/index.php?year='+year+'&month='+month;
}
function timetable_div(){
$('#timetable_div').toggle(250)
}

function search(){

if($('#by_art_num').prop("checked") || $('#by_key').prop("checked") || $("#by_uid").prop("checked"))
{
val = $('#val').val()
if(val == ""){$('#val').focus()}else{$('#search_form').submit()  }
}

if($("#by_size").prop("checked"))
{
paper_width	= $('#paper_width').val()
paper_height	= $('#paper_height').val()
paper_side	= $('#paper_side').val()
if (paper_width == ""){$('#paper_width').focus()
return false;}
else if (paper_height == ""){$('#paper_height').focus()
return false;}
else if (paper_side == ""){$('#paper_side').focus()
return false;}
else{
$('#search_form').submit()
}
}


}


function by_size_form(){
$('#by_num_input').hide();
$('#by_size_input').show();
$("#paper_width").focus();
}

function by_size_form_hide(){

$('#by_num_input').show();
$('#by_size_input').hide();
$("#val").focus();
}

function jfocus(){
$("#val").focus();
//ставим текущий месяц и год в формах
set_y_m()
}
function jump(jumpfrom, maxsize, jumpto){
if($("#jumpoff").is(":not(:checked)")){
maxsize = maxsize-1
if ($('#'+jumpfrom).val().length > maxsize){$('#'+jumpto).focus()}
}
}
//-->
</script>

<body>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>
<? require_once("../templates/top.php");
$name_curr_page = 'apl_list';
require_once("../templates/main_menu.php");?>
<table align="center" width="1100" border="0" cellpadding=0 bgcolor="#F6F6F6">
      	<tr>
<td valign="top" align="center">
<table border="0" cellspacing="0" cellpadding="5" width=1050>
<tr>
<td><input name="subm" type="hidden" value="1" />
<? if ($_GET["act"]  == "just_ser"){?>
&rarr; <a href="?act=" class="sublink">все заявки</a>
<?} else{ ?>
&rarr; <a href="?act=just_ser" class="sublink">только серийники</a>
<? } ?></td>
<td><a href="edit.php"><img src="../../i/manufacture.png" width="24" height="24" alt="" align="middle" /></a> <a href="edit.php" class="sublink">Создать заявку</a></td>
<td><img src="../../i/export.png" width="24" height="24" alt="" align="middle" onclick="" onclick=export_div(2)>
<a href="#" class=sublink onclick=export_div()>Экспорт в CSV</a>
<div id=export_div style="top: 200px; left: 300px;background-color:white; z-index:100; position:absolute;display:none; border: 1px black; border: 1px solid black;">
<table align=center cellpadding=10>
<tr>
<td>
Год: <select name=year id=year>
<option value="2014">2014</option>
<option value="2015">2015</option>
<option value="2016">2016</option>
<option value="2017">2017</option>
<option value="2018">2018</option>
<option value="2019">2019</option>
<option value="2020">2020</option>
</select>
Месяц:
<select name=month id=month>
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
</td>
<td><input type=button value=OK onclick="get_csv()"></td>
<td>
<img src="../../i/del.gif" width="20" height="20" alt="" style="cursor:pointer" onclick=export_div()>
</td>
</tr>
</table>
</div>
</td>
<td>
<a href="count/exp_html.php" target=_blank><img src="../../i/journal.png" width="22" height="22" alt="" align="middle"></a>
<a href="count/exp_html.php" class=sublink target=_blank>Просмотр журнала</a>
</td>
<td>
<a href="count/" target=_blank><img src="../../i/add_job.png" width="22" height="22" alt="" align="middle"></a>
<a href="count/" class=sublink target=_blank>Добавить работу</a>
</td>
<td>
<?if ($user_type == "sup" || $user_type == "acc" || $user_type == "adm"){
include("timetable/timetable_form.php");
}
if ($user_type == "sup" || $user_type == "acc"){
include("timetable/report_form.php");
}
?>
</td>
</tr></table></td></tr>
<tr>
<td>
<form action="list.php" id="search_form" name="search_form" method="get"  onsubmit="search(); return false">
<table width="1100" border="0" cellpadding="0" cellspacing="0" onload=jfocus()>
<tr>

<td width="370" align=center valign=top>
<span id=what>поиск:</span>
<span id=by_num_input  style="display:<? if ($_GET["act"] !== "by_size") {echo "yes";}else{echo "none";} ?> "><input type=text value="<?=$_GET["val"]?>" name=val id=val size=35/></span>
<span id=by_size_input style="display:<? if ($_GET["act"] == "by_size") {echo "yes";}else{echo "none";} ?> ">
Ш: <input type=text value="<?=$_GET["paper_width"]?>" onkeyup="jump('paper_width','2','paper_height')" name=paper_width id=paper_width size=3/>
В: <input type=text value="<?=$_GET["paper_height"]?>" onkeyup="jump('paper_height','2','paper_side')" name=paper_height id=paper_height size=3/>
Б: <input type=text value="<?=$_GET["paper_side"]?>" name=paper_side id=paper_side size=3/>
<br><input type="checkbox" id=jumpoff /> <label for="jumpoff" style="font-size:7px; cursor:pointer; vertical-align: middle">отключить прыжки</label>
</span>
</td>
<td width=530 valign=top>
<input type=radio id="by_key" name="act" onchange="by_size_form_hide()" value="by_key" <?if ($_GET["act"] == "by_key" || !$_GET["act"]){echo "checked";}?>><label for="by_key" style="cursor: pointer; font-size: 14px;">по названию</label>
<input type=radio id="by_uid" name="act" onchange="by_size_form_hide()" value="by_uid" <?if ($_GET["act"] == "by_uid"){echo "checked";}?>><label for="by_uid" style="cursor: pointer; pointer; font-size: 14px;">по номеру заявки</label>
<input type=radio id="by_art_num" onchange="by_size_form_hide()" name="act" value="by_art_num" <?if ($_GET["act"] == "by_art_num"){echo "checked";}?>><label for="by_art_num" style="cursor: pointer; pointer; font-size: 14px;">по номеру артикула</label>
<input type=radio id="by_size" onchange="by_size_form()" name="act" value="by_size" <?if ($_GET["act"] == "by_size"){echo "checked";}?>><label for="by_size" style="cursor: pointer; pointer; font-size: 14px;">по размеру</label>

</td>
<td valign=top><input type=submit value="искать!"/> <input type=button onclick="window.location = 'list.php';" value="очистить фильтры"> </form>
<script>
 $(document).ready(function() {

     $('#val').AddXbutton({ img: '/acc/i/x.gif' });

 });
</script>
</td>
</tr></table></td>
      </tr>
      <tr>
      	<td align="center">
      		<? if($auth) {  ?>
<table width="1100" border="0" cellpadding="0" cellspacing="0" id="app_list">
<tr class="tab_query_tit">
    <td align="center" class="tab_query_tit">№</td>
    <td align="center" class="tab_query_tit">Название</td>
	<td align="center" class="tab_query_tit">Граммаж</td>
	<td align="center" class="tab_query_tit">Длина ручек</td>
	<td align="center" class="tab_query_tit">Толщина мм</td>
	<td align="center" class="tab_query_tit">Упаковка по</td>
	<td align="center" class="tab_query_tit">Тираж</td>
  	<td align="center" class="tab_query_tit">Тариф сборка</td>
	<td align="center" class="tab_query_tit">Артикул</td>
    <td align="center" class="tab_query_tit">Дата</td>
    <td align="center" class="tab_query_tit" >Менеджер</td>
    <td align="center" class="tab_query_tit">Операция</td>
</tr>
<?$nm = 1;while(@$r = mysql_fetch_array($res_apl)) {?>
<tr onmouseover="this.style.background='#BDCDFF';" onmouseout="this.style.background='';">
<td align="center" class="tab_td_norm"><a href="/acc/applications/count/exp_html.php?act=all&num_ord=<?=$r['num_ord']?>&num_sotr=&items_on_page=300" class=sublink target="_blank"><?=$r['num_ord']?></a></td>
<td align="left" class="tab_td_norm">
<img src="../../i/info_sm.png" width="16" height="16" alt="" style="cursor:pointer" onclick="show_app_info(<?=$r['num_ord']?>)">
<span id="app_info_<?=$r['num_ord']?>"   style="display: none; cursor: move; position: absolute;z-index:100;background-color: white; width: 520px; height:auto; border: 1px; border-color: #CDCDCD; padding: 10px;
border: 1px solid #808080;border-top-left-radius: 10px;border-top-right-radius: 10px;border-bottom-right-radius: 10px;border-bottom-left-radius: 10px;"></span>
<script>
$("#app_info_<?=$r['num_ord']?>").draggable();
</script>
<a href="edit.php?id=<?=$r['uid']?>"><?$tit = (strlen($r['title'])>50)?substr($r['title'],0,50).'...':$r['title'];?><?=$tit?></a>

<a href="edit1.php?uid=<?=$r['uid']?>">>>></a></td>
<td align="center" class="tab_td_norm"><?=$r['paper_density']?></td>
<td align="center" class="tab_td_norm"><?=$r['hand_length']?></td>
<td align="center" class="tab_td_norm"><?=$r['hand_thick']?></td>
<td align="center" class="tab_td_norm"><?=$r['packing_other']?></td>
<td align="center" class="tab_td_norm"><?=$r['tiraz']?></td>
<td align="center" class="tab_td_norm"><?=$r['rate']?></td>
<td align="left" class="tab_td_norm">
<?if ($r['art_num'] !== "0"){?>
<a href="/acc/stat/stat_shop.php?tip=by_art_num&art_num=<?=$r['art_num'];?>&date_from=<?=date('Y-m-d', strtotime($r['dat_ord']));?>&date_to=&type=shop_history" target="_blank"><img src="../../i/stat_sm.png" align="absmiddle" onmouseover="Tip('Посмотреть историю продаж');"></a>
<a href="/acc/applications/count/exp_html.php?act=all&num_ord=<?=$r['num_ord']?>&num_sotr=&items_on_page=1000" target="_blank"><img src="../../../i/table.png" align="absmiddle" width="16" height="16" alt="" id="table_sdelka_link_200" style="display: display: inline;"></a>
<a href="http://www.paketoff.ru/admin/shop/goods_list/edit/?id=<?=$r['art_uid']?>" target=_blank onmouseover="Tip('Редактировать в интернет магазине');"><img src="/i/editbut.png" align=absmiddle></a>
<a href="http://www.paketoff.ru/shop/view/?id=<?=$r['art_uid']?>" target=_blank  onmouseover="Tip('Открыть в интернет магазине');"><img src="/i/pkf.gif" align=absmiddle><?=$r['art_num']?></a>
<?}else{?>
<a href="http://192.168.1.100/acc/query/query_send.php?show=<?=$r['zakaz_id'];?>" target=_blank><img src="../../i/invoice16.png" width="16" height="16" align="absmiddle"></a>
<a href="http://192.168.1.100/acc/query/query_send.php?show=<?=$r['zakaz_id'];?>" target=_blank>заказ</a>
 <?}?>
 </td>
 <td align="center" class="tab_td_norm"><?$dat_ord = new dat_fn(@$r['dat_ord']);  $dat_ord = $dat_ord -> stringtm;?> <?=$dat_ord;?></td>
<td align="center" class="tab_td_norm_nobr"><?=$r['surname']?></td>
<td align="left" class="tab_td_norm">
<a href="apl_word.php?id=<?=$r['uid']?>" target=_blank onmouseover="Tip('Распечатать заявку');"><img src="../../i/icons/print.png" style="opacity:<?if($r['printed'] == "0"){echo "0.5";}else{echo "1";}?>" width="16" height="16" alt="" /> <sup>&nbsp;<?=$r['printed'];?></sup></a>
<a href="count/exp_csv.php?num_ord=<?=$r['num_ord'];?>"><img src="../../i/export_sm.png" width="16" height="16" alt=""></a>
<? if($tpacc || $user_id==$r['user_id']) { ?>
<a onclick="if(confirm('Удалить заявку?')){ document.location='?del=<?=$r['uid']?>'};return false;" href="#" onmouseover="Tip('Удалить');"><img src="/acc/i/del.gif" width="20" height="20" alt="" /></a><? } ?>
</td>
</tr>
<? $nm++; } ?>
</table><?}?></td>
</tr></table>

<?	if ($_GET["act"] == ""){$act="";}
	if ($_GET["act"] == "by_key"){$act="by_key";}
	if ($_GET["act"] == "by_uid"){$act="by_uid";}
	if ($_GET["act"] == "by_art_num"){$act="by_art_num";}
	if ($_GET["act"] == "just_ser"){$act="just_ser";}
	if ($_GET["act"] == "by_size"){$act="by_size";}
$paper_width = $_GET["paper_width"];
$paper_height = $_GET["paper_height"];
$paper_side = $_GET["paper_side"];
$val = $_GET["val"];
if( $rows_onpage < $num_all_rows ) { ?>
<table border="0" align="center" cellpadding="0" cellspacing="0" width=900>
	<tr>
		<? if(!$all_pages) { ?>
		<td>Страница</td>
		<? for($i=1;$i<=10;$i++) { ?>
		<td width=30>
			<?$lnk ='<strong>'.$i.'</strong>';
			if($page != $i)
				$lnk = '<a href="?act='.$act.'&page='.$i.'&paper_width='.$paper_width.'&paper_height='.$paper_height.'&paper_side='.$paper_side.'&val='.$val.'">'.$lnk.'</a>';
			echo $lnk;
			?>
		</td>
		<? } } else {?>
		<td>
		<? if($all_pages) {?>
		<a href="?act=<?=$act?>&paper_width=<?=$paper_width?>&paper_height=<?=$paper_height?>&paper_side=<?=$paper_side?>&val=<?=$val?>">Постранично</a>
		<? } else {?>
		Постранично
		<? }?>
		</td>
		<? } ?>
		<td width=150>
			<? if(!$all_pages) {?>
			<a href="?act=<?=$act?>&paper_width=<?=$paper_width?>&paper_height=<?=$paper_height?>&paper_side=<?=$paper_side?>&page=all&val=<?=$val?>" >Показать все</a>
			<? } else {?>
			Показать все
			<? }?>
		</td>
	</tr>
</table>
<?}?>

 <script>
jfocus()
 </script>
</body>
</html>
<? ob_end_flush(); ?>