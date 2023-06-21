<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

$uid = $_GET['uid'];
//длина ручки
$hand_length_u = "35";
//перекат / недокат
$apl_limit_per_u = "1";
//упаковка по
$packing_other_u = "25";
//диаметр отверстий
$pikalo_diam_hol_u = "5";

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
				$this -> stringtm		= date("d.m.Y H:i", $this -> tmstamp);

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

$id = 0;

if(!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
  $id = $_REQUEST['id'];
}
if($id != 0) {
  $query = "SELECT * FROM applications WHERE uid=$id";
  $res = mysql_query($query);
  $r_apl = mysql_fetch_array($res);

  $arr_otgr = array();
  $query = "SELECT * FROM applications_shipping_list WHERE apl_id=$id ORDER BY num";
  $res2 = mysql_query($query);
  while($r2 = mysql_fetch_array($res2)) {
    $arr_otgr[] = array($r2['val_nums'],$r2['val_tx']);
  }

/*  if( !($tpacc || $user_id==$r_apl['user_id']) ) {
    header("Location: list.php");
    exit;
  }  */
}

if ($r_apl['zakaz_id']){
$uid = $r_apl['zakaz_id'];
}
else{
$uid = $_GET["uid"];
}
  $query1 = "SELECT * FROM queries WHERE uid='$uid'";
  $res1 = mysql_query($query1);
  $res2 = mysql_fetch_array($res1);
  $cl_id =  $res2["client_id"];
  $query2 = "SELECT * FROM clients WHERE uid='$cl_id'";
  $res2 = mysql_query($query2);
  $res3 = mysql_fetch_array($res2);

if($r_apl['type']){$type=$r_apl['type'];}else{$type = $_GET['type'];}
if ($type == ""){$type = "2";}

// ---------------------------------------------------------
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.2</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" media="all" href="../includes/js/calendar/calendar-blue.css" title="Aqua" />
</head>
<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>   
<script src="../includes/lib/JsHttpRequest/JsHttpRequest.js"></script>
<script type="text/javascript" src="../includes/js/calendar/calendar.js"></script>
<script type="text/javascript" src="../includes/js/calendar/calendar-ru_win_.js"></script>
<script type="text/javascript" src="../includes/js/calendar/calendar-setup.js"></script>
<script language="JavaScript" type="text/javascript">
<!--
// координаты мыши
var xpos=0;
var ypos=0;

var txpos = 0;
var typos = 0;
var tpacc = <?=$tpacc?>;
var edit = <?=intval($id)?>;            // ид редактируемой заявки
var arr_data = new Array();                 // все данные для сохранения
var curr_date = '<?=date("d.m.Y")?>';		// текущая дата в формате '01.05.2007'

// после загрузки страницы
  $(
			function()
      {
        switch_paper_list_typ_txt();   // загрузить выбранные объект или последний
        switch_lami_tp();
        switch_tisn_tp();
        switch_hand_tp1();
        switch_hand_mount();
        ch_pikalo_opt();
        ch_packing_opt();
        packing_nameof_oth_switch();
        packing_sel_switch(0);
      }
	);


  // Функция AJAX параметры:

  function ajrun(query,arr,func) {
  	op = (arguments.length>3)?arguments[3]:'';
  	file = (arguments.length>4)?arguments[4]:[];
  	JsHttpRequest.query(
  	  query,    		// backend
  	  { op:op, arr: arr, file:file }, // Параметры
  	  func,					// обработка результатов
  	  true					// true - не кешировать данные
  	);
  }

// Плагин для JQuery для переключения checkbox флажков
jQuery.fn.check = function(mode) {
 // если mode не определен, используем 'on' по умолчанию
 var mode = mode || 'on';

 return this.each(function()
 {
   switch(mode) {
     case 'on':
       this.checked = true;
       break;
     case 'off':
       this.checked = false;
       break;
     case 'toggle':
       this.checked = !this.checked;
       break;
   }
 });
};


<!-- <<<<<<<<<<<< ******** КАЛЕНДАРЬ ДЛЯ ЗАПОЛНЕНИЯ ПОЛЕЙ С ДАТОЙ  *****************  //-->

var oldLink = null;
// code to change the active stylesheet
function setActiveStyleSheet(link, title) {
  var i, a, main;
  for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
    if(a.getAttribute("rel").indexOf("style") != -1 && a.getAttribute("title")) {
      a.disabled = true;
      if(a.getAttribute("title") == title) a.disabled = false;
    }
  }
  if (oldLink) oldLink.style.fontWeight = 'normal';
  oldLink = link;
  link.style.fontWeight = 'bold';
  return false;
}

// This function gets called when the end-user clicks on some date.
function selected(cal, date) {
  cal.sel.value = date; // just update the date in the input field.
  if (cal.dateClicked && (cal.sel.id == "sel1" || cal.sel.id == "sel3"))

    cal.callCloseHandler();
}


function closeHandler(cal) {
  cal.hide();                        // hide the calendar
  _dynarch_popupCalendar = null;
}


function showCalendar(id, format, showsTime, showsOtherMonths) {
  var el = document.getElementById(id);
  if (_dynarch_popupCalendar != null) {
    // we already have some calendar created
    _dynarch_popupCalendar.hide();                 // so we hide it first.
  } else {
    // first-time call, create the calendar.
    var cal = new Calendar(1, null, selected, closeHandler);
    // uncomment the following line to hide the week numbers
    // cal.weekNumbers = false;
    if (typeof showsTime == "string") {
      cal.showsTime = true;
      cal.time24 = (showsTime == "24");
    }
    if (showsOtherMonths) {
      cal.showsOtherMonths = true;
    }
    _dynarch_popupCalendar = cal;                  // remember it in the global var
    cal.setRange(1900, 2070);        // min/max year allowed.
    cal.create();
  }
  _dynarch_popupCalendar.setDateFormat(format);    // set the specified date format
  _dynarch_popupCalendar.parseDate(el.value);      // try to parse the text in field
  _dynarch_popupCalendar.sel = el;                 // inform it what input field we use


  _dynarch_popupCalendar.showAtElement(el.nextSibling, "Br");        // show the calendar

  return false;
}

var MINUTE = 60 * 1000;
var HOUR = 60 * MINUTE;
var DAY = 24 * HOUR;
var WEEK = 7 * DAY;


function isDisabled(date) {
  var today = new Date();
  return (Math.abs(date.getTime() - today.getTime()) / DAY) > 10;
}

function flatSelected(cal, date) {
  var el = document.getElementById("preview");
  el.innerHTML = date;
}

function showFlatCalendar() {
  var parent = document.getElementById("display");

  // construct a calendar giving only the "selected" handler.
  var cal = new Calendar(0, null, flatSelected);

  // hide week numbers
  cal.weekNumbers = false;

  // We want some dates to be disabled; see function isDisabled above
  cal.setDisabledHandler(isDisabled);
  cal.setDateFormat("%A, %B %e");

  cal.create(parent);

  // ... we can show it here.
  cal.show();
}
<!-- >>>>>>>>>>>> ******** КАЛЕНДАРЬ ДЛЯ ЗАПОЛНЕНИЯ ПОЛЕЙ С ДАТОЙ  *****************  //-->



// Показать / Скрыть  Бумага и размер пакета
function razd_paper_switch() {
	if($('#tab_razd_paper').css('display')=='none')
    razd_paper_show();
	else
    razd_paper_hide();
}

// Показать Бумага и размер пакета
function razd_paper_show() {
  $('#im_razd_paper').attr('src','/i/icons/b_minus.png');
  $('#tab_razd_paper').show();
}

// Скрыть Бумага и размер пакета
function razd_paper_hide() {
  $('#im_razd_paper').attr('src','/i/icons/b_plus.png');
  $('#tab_razd_paper').hide();
}

// скрыть показать раздел Ручки и укрепление
function razd_hand_switch() {
  if($('#tab_hand').css('display')=='none')
    razd_hand_show();
	else
    razd_hand_hide();
}

// Показать Ручки и укрепление
function razd_hand_show() {
  $('#im_hand').attr('src','/i/icons/b_minus.png');
  $('#tab_hand').show();
}

// Скрыть Ручки и укрепление
function razd_hand_hide() {
  $('#im_hand').attr('src','/i/icons/b_plus.png');
  $('#tab_hand').hide();
}


// Пределы перекат/недокат
function limit_per_check() {
  var apl_limit_per = replace_num_acc($("#apl_limit_per").val());
  if(apl_limit_per > 99)
    apl_limit_per = 99;
  $("#apl_limit_per").val(apl_limit_per);
}

// при выборе разных листов на пакете показать текстовое поле
function switch_paper_list_typ_txt() {
  if($('#rad_paper_list_typ_2').get(0).checked == true) {
    $('#td_paper_list_typ').show();
  } else {
    $('#td_paper_list_typ').hide();
  }
}


// при выборе ламиниции в спеске показать или скрыть галочки доп опц.
function switch_lami_tp() {
  if($('#lami_sel1').val() != '0') {
    $('#td_lami_tp').show();
  } else {
    $('#td_lami_tp').hide();
  }
}

// при выборе селекта тиснения - показать доп опции
function switch_tisn_tp() {
  if($('#tisn_sel1').val() == '2') {
    $('#td_tisn_tp').show();
  } else {
    $('#td_tisn_tp').hide();
  }
  if($('#tisn_sel1').val() != '0') {
    $('#td_tisn_opt').show();
  } else {
    $('#td_tisn_opt').hide();
  }
}


// при выборе материала ручек в списке показать или скрыть поле другой.
function switch_hand_tp1() {

  if($('#hand_sel1').val() == '4') {
    $('#hand_mount_oth').removeClass('inerr');
    $('#hand_color').removeClass('inerr');
    $('#hand_length').removeClass('inerr');
    $('#pikalo_on_color').removeClass('inerr');
    $('#pikalo_on_diam').removeClass('inerr');
    $('#hand_mount_color').removeClass('inerr');
  }

  if($('#hand_sel1').val() == '0') {
    $('#hand_mat_oth').show();
  } else {
    $('#hand_mat_oth').hide();
  }
  ch_pikalo_opt();
}


// при выборе крепления ручек в списке показать или скрыть поле другой.
function switch_hand_mount() {
  if($('#hand_mount_sel').val() == '0') {
    $('#hand_mount_oth').show();
    $('#div_hand_mount_color').hide();
  } else {
    $('#hand_mount_oth').hide();
    if($('#hand_mount_sel').val() == '2')
      $('#div_hand_mount_color').show();
    else
      $('#div_hand_mount_color').hide();
  }
}

// смена опций пикало
function ch_pikalo_opt() {
   if($('#ch_pikalo').get(0).checked == true) {
      $('#div_pikalo_opt2').show();
      $('#div_pikalo_opt1').hide();
   } else {
      $('#div_pikalo_opt2').hide();
      if($('#hand_sel1').val() == '2')
        $('#div_pikalo_opt1').hide();
      else
        $('#div_pikalo_opt1').show();
   }
}



// скрыть показать раздел Маркировка, упаковка, доставка, сборщики, сроки
function razd_packing_switch() {
  if($('#tab_packing').css('display')=='none')
    razd_packing_show();
	else
    razd_packing_hide();
}


// Показать Маркировка, упаковка, доставка, сборщики, сроки
function razd_packing_show() {
  $('#im_packing').attr('src','/i/icons/b_minus.png');
  $('#tab_packing').show();
}


// Скрыть Маркировка, упаковка, доставка, сборщики, сроки
function razd_packing_hide() {
  $('#im_packing').attr('src','/i/icons/b_plus.png');
  $('#tab_packing').hide();
}



// смена опций пикало
function ch_packing_opt() {
   if($('#ch_packing').get(0).checked == true) {
      $('#packing_opt').hide();
   } else {
      $('#packing_opt').show();
   }
}

// показать скрыть поле Маркировка и накладные от имени - другое
function packing_nameof_oth_switch() {
   if($('#packing_nameof_sel').val() == '0') {
     $('#div_packing_nameof_oth').show();
     //$('#packing_nameof_oth').val('');
   } else {
     $('#div_packing_nameof_oth').hide();
   }
}



// при выборе типа упаковки
function packing_sel_switch(init) {
   if($('#packing_sel').val() == '0') {
     $('#packing_other_div').hide();
   } else {
     $('#packing_other_div').show();
     if($('#packing_sel').val() == '3') {
       $('#packing_other_nam').html('другое<span class="err">*</span>&nbsp;');
       if(init == 1)
         $('#packing_oth').val('');
     } else {
       $('#packing_other_nam').html('по шт.<span class="err">*</span>&nbsp;');
       if(init == 1)
         $('#packing_oth').val('0');
     }
   }
}




// форматирование строки, удаляет в строке начальные и конечные пробелы
function replace_str(v) {
	var reg_sp = /^\s*|\s*$/g;
	v = v.replace(reg_sp, "");
	return v;
}


// форматирование номера счета
function replace_num_acc(v) {
	var reg_sp = /[^\d]*/g;		// вырезание всех символов кроме цифр
	v = v.replace(reg_sp, '');
	return v;
}


function replace_zap(v) {
	v = v.replace(',', '.');
	v = v.replace(' ', '');
	return v;
}


function create_art(){
$('#art_num_inp').val("");
$('#art_uid_inp').val("");

$('#art_span').show();

col_in_pack = $('#packing_oth').val();

if($("#onn_show").prop("checked")){onn = "1"}else{onn = "0"}

izd_w = $('#paper_wd').val();
izd_v = $('#paper_hg').val();
izd_b = $('#paper_side').val();
izd_color = $('#paper_col_ext').val();
izd_material = $('#paper_name').val();
izd_gramm = $('#paper_density').val();
izd_lami = $('#lami_sel1').val();
izd_ruchki = $('#hand_sel1').val();
izd_type = $('#izd_type').val();
min_part =  $('#packing_oth').val();

  if(izd_w=='') {
    $('#paper_wd').addClass('inerr');
	$('#paper_wd').focus();
	$('#art_new').prop('checked', false);
	$('#art_old').prop('checked', false);
  }
  else if(izd_v=='') {
    $('#paper_hg').addClass('inerr');
	$('#paper_hg').focus();
	$('#art_new').prop('checked', false);
	$('#art_old').prop('checked', false);
  }
  else if(izd_color=='') {
    $('#paper_col_ext').addClass('inerr');
	$('#paper_col_ext').focus();
	$('#art_new').prop('checked', false);
	$('#art_old').prop('checked', false);
  }
  else if(izd_material=='') {
    $('#paper_name').addClass('inerr');
	$('#paper_name').focus();
	$('#art_new').prop('checked', false);
	$('#art_old').prop('checked', false);
  }

  else{

var pack = $('#packing_sel').val();
var min_part = $('#min_part').val();
var price_our = $('#price_our').val();
var price = $('#price').val();

var base_url = "http://www.paketoff.ru/modules/admin/shop/backend/shop_query.php?"
var url = base_url+"&min_part="+col_in_pack+"&col_in_pack="+col_in_pack+"&onn_show="+onn+"&izd_w="+izd_w+"&izd_v="+izd_v+"&izd_b="+izd_b+"&izd_material="+izd_material+"&izd_gramm="+izd_gramm+"&izd_color="+izd_color+"&izd_lami="+izd_lami+"&izd_ruchki="+izd_ruchki+"&izd_type="+izd_type+"&price_our="+price_our+"&price="+price+"&pack="+pack
document.getElementById("save_iframe").innerHTML = '<iframe border=\"0\" height=\"600\" width=\"850\" id=new_art frameborder=0 scrollbar=\"auto\" src=\"'+url+'\" style=\"display:hidden;\"></iframe>';

}}

function get_uid(){
//если не пустой и номерной, разблокируем save_but
art_num_inserted = $('#art_num_inp').val();
if ($.isNumeric(art_num_inserted)){
$("#save_but").prop("disabled", false);
}else{$("#save_but").prop("disabled", true);}

var art_id = $('#art_num_inp').val();
var geturl;
  geturl = $.ajax({
    type: "POST",
    url: 'get_art_num.php',
	data : 'act=get_uid&art_id='+art_id,
    success: function () {
var resp1 = geturl.responseText
resp1=resp1.split(";");
uid=resp1[0];
title=resp1[1];
price=resp1[2];
if (uid){
$('#art_uid_span').html("<br><a href=\"http://www.paketoff.ru/shop/view/?id="+uid+"\" target=_blank><img src=\"/i/button_ok.png\"/></a> "+title+" "+price+"руб.");
$('#art_uid_inp').val(uid);

}else{$('#art_uid_span').html("<br><span style=\"color: red; font-weight:bold;\">Проверьте правильность артикула</span>");}
}});
}


function make_able_save_but()
{
$('#apl_tiraz').focus()
art_num = $('#art_num_inp').val()
if ($.isNumeric(art_num)){
$("#save_but").prop("disabled", false);
}}

function hide_new(){
$('#art_span').show();
$('#save_iframe').html("");
$('#art_num_inp').val("");
$('#art_uid_inp').val("");
$("#art_num_inp").prop("disabled", false);
$('#art_num_inp').focus();
}


function unblock_save_but(){
var art_num = $('#art_num_inp').val();
if (art_num !== ""){
$("#save_but").prop("disabled", false);}
else{
$("#save_but").prop("disabled", true);}
}
// проверить все поля на правильность
function apl_check_all_feld() {
type = document.getElementById("type").value;

//скрипт генерации конец
//цвет ручки окрашиваем в тон пакета
  var error = false;
  arr_data = new Array();

$('input,textarea,select').removeClass('inerr');  // скрыть все ошибки


arr_data['izd_type']   = $('#izd_type').val();

arr_data['apl_title'] = replace_str($('#apl_title').val());
  // Номер закакза
  if(edit!=0) {
    arr_data['apl_num_ord'] = replace_num_acc($('#apl_num_ord').val());
    $('#apl_num_ord').val(arr_data['apl_num_ord']);
    if( (arr_data['apl_num_ord']=='') || (arr_data['apl_num_ord']=='0') ) {
      error = true;
      $('#apl_num_ord').addClass('inerr');
    }
  }
  // Общий тираж
  arr_data['apl_tiraz'] = replace_num_acc($('#apl_tiraz').val());
  $('#apl_tiraz').val(arr_data['apl_tiraz']);
  if( (arr_data['apl_tiraz']=='') || (arr_data['apl_tiraz']=='0') ) {
    error = true;
    $('#apl_tiraz').addClass('inerr');
	$('#apl_tiraz').focus();
  }
//alert ($('#art_uid_inp').val())
  // Номер артикула

arr_data['art_num'] = $('#art_num_inp').val();
  //UID артикула на сайте
 arr_data['art_uid'] = $('#art_uid_inp').val();

  // Менеджер
  arr_data['manager'] = $('#manager').val();

  // Дата
  arr_data['apl_dat'] = $('#apl_dat').val();

  // Пределы перекат/недокат
  arr_data['limit_per_sign'] = $('#limit_per_sign').val();    // знак
  arr_data['apl_limit_per'] = replace_str($('#apl_limit_per').val());
  $('#apl_limit_per').val(arr_data['apl_limit_per']);
  if(arr_data['apl_limit_per']=='') {
    error = true;
    $('#apl_limit_per').addClass('inerr');
  }

  // Бумага и размер пакета
  var error_tmp = false;

  // Ширина
  arr_data['paper_wd'] = replace_num_acc($('#paper_wd').val());
  $('#paper_wd').val(arr_data['paper_wd']);
  if( (arr_data['paper_wd']=='') || (arr_data['paper_wd']=='0') ) {
    error = true; error_tmp = true;
    $('#paper_wd').addClass('inerr');
  }

  // Высота
  arr_data['paper_hg'] = replace_num_acc($('#paper_hg').val());
  $('#paper_hg').val(arr_data['paper_hg']);
  if( (arr_data['paper_hg']=='') || (arr_data['paper_hg']=='0') ) {
    error = true; error_tmp = true;
    $('#paper_hg').addClass('inerr');
  }

    // Бок
  arr_data['paper_side'] = replace_num_acc($('#paper_side').val());
  $('#paper_side').val(arr_data['paper_side']);
/*  if( (arr_data['paper_side']=='') || (arr_data['paper_side']=='0') ) {
    error = true; error_tmp = true;
    $('#paper_side').addClass('inerr');
  } */



  // Цвет пакета снаружи
  arr_data['paper_col_ext'] = replace_str($('#paper_col_ext').val());
  $('#paper_col_ext').val(arr_data['paper_col_ext']);
  if(arr_data['paper_col_ext']=='') {
    error = true; error_tmp = true;
    $('#paper_col_ext').addClass('inerr');
  }

  // Цвет пакета внутри
  arr_data['paper_col_inn'] = replace_str($('#paper_col_inn').val());
  $('#paper_col_inn').val(arr_data['paper_col_inn']);
  if(arr_data['paper_col_inn']=='') {
    error = true; error_tmp = true;
    $('#paper_col_inn').addClass('inerr');
  }

  // Бумага (плотность)
  arr_data['paper_density'] = replace_str($('#paper_density').val());
  $('#paper_density').val(arr_data['paper_density']);
  if(arr_data['paper_density']=='') {
    error = true; error_tmp = true;
    $('#paper_density').addClass('inerr');
  }

  // Название бумаги
  arr_data['paper_name'] = replace_str($('#paper_name').val());
  $('#paper_name').val(arr_data['paper_name']);
  if(arr_data['paper_name']=='') {
    error = true; error_tmp = true;
    $('#paper_name').addClass('inerr');
  }

  // Планируемая дата поставки листов на производство
  arr_data['pap_przv_dat'] = replace_str($('#pap_przv_dat').val());


  // Типография
  arr_data['paper_press'] = replace_str($('#paper_press').val());

  // Поставщик бумаги
  arr_data['paper_suppl'] = replace_str($('#paper_suppl').val());


  // Из скольких листов собирается
  arr_data['paper_num_list'] = $('#paper_num_list').val();

  // Листы на пакете разные
  arr_data['paper_list_typ_tx'] = '';
  arr_data['rad_paper_list_typ'] = ($('#rad_paper_list_typ_1').get(0).checked == true)?0:1;
  if(arr_data['rad_paper_list_typ'] == 1) {
    arr_data['paper_list_typ_tx'] = replace_str($('#paper_list_typ_tx').val());
    $('#paper_list_typ_tx').val(arr_data['paper_list_typ_tx']);
    if(arr_data['paper_list_typ_tx']=='') {
      error = true; error_tmp = true;
      $('#paper_list_typ_tx').addClass('inerr');
    }
  }

  // Ламинация снаружи,внутри
  arr_data['lami_sel'] = $('#lami_sel1').val();
  arr_data['ch_lami_tp1'] = ($('#ch_lami_tp1').get(0).checked == true)?1:0;
  arr_data['ch_lami_tp2'] = ($('#ch_lami_tp2').get(0).checked == true)?1:0;
  if(arr_data['lami_sel'] != 0) {
     if((arr_data['ch_lami_tp1']==0)&&(arr_data['ch_lami_tp2'] == 0)) {
        error = true; error_tmp = true;
        $('#ch_lami_tp1').addClass('inerr');
        $('#ch_lami_tp2').addClass('inerr');
     }
  }

  // Тиснение
  arr_data['tisn_sel1'] = $('#tisn_sel1').val(); // тиснение селект
  arr_data['ch_tisn_tp'] = ($('#ch_tisn_tp1').get(0).checked == true)?0:1; // одинаковое, разное
  arr_data['stamp_width'] = 0;
  arr_data['stamp_height'] = 0;
  arr_data['stamp_color'] = '';
  arr_data['stamp_indent_bott'] = 0;
  arr_data['stamp_indent_right'] = 0;
  arr_data['stamp_foil_name'] = '';

  if(arr_data['tisn_sel1'] > 0) {
     arr_data['stamp_width'] = replace_num_acc($('#stamp_width').val());  // ширина
     $('#stamp_width').val(arr_data['stamp_width']);
     if((arr_data['stamp_width'] == '') || (arr_data['stamp_width'] == '0')) {
        error = true; error_tmp = true;
        $('#stamp_width').addClass('inerr');
     }
     arr_data['stamp_height'] = replace_num_acc($('#stamp_height').val());  // ширина
     $('#stamp_height').val(arr_data['stamp_height']);
     if((arr_data['stamp_height'] == '') || (arr_data['stamp_height'] == '0')) {
        error = true; error_tmp = true;
        $('#stamp_height').addClass('inerr');
     }
     arr_data['stamp_color'] = replace_str($('#stamp_color').val());     // цвет
     $('#stamp_color').val(arr_data['stamp_color']);
     if(arr_data['stamp_color']=='') {
       error = true; error_tmp = true;
       $('#stamp_color').addClass('inerr');
     }
     arr_data['stamp_indent_bott'] = replace_num_acc($('#stamp_indent_bott').val());  // отступ от дна, мм
     $('#stamp_indent_bott').val(arr_data['stamp_indent_bott']);
     if((arr_data['stamp_indent_bott'] == '') || (arr_data['stamp_indent_bott'] == '0')) {
        error = true; error_tmp = true;
        $('#stamp_indent_bott').addClass('inerr');
     }
     arr_data['stamp_indent_right'] = replace_num_acc($('#stamp_indent_right').val());  // отступ справа, мм
     $('#stamp_indent_right').val(arr_data['stamp_indent_right']);
     if((arr_data['stamp_indent_right'] == '') || (arr_data['stamp_indent_right'] == '0')) {
        error = true; error_tmp = true;
        $('#stamp_indent_right').addClass('inerr');
     }
     arr_data['stamp_foil_name'] = replace_str($('#stamp_foil_name').val());     // название фольги
     $('#stamp_foil_name').val(arr_data['stamp_foil_name']);
     if(arr_data['stamp_foil_name']=='') {
       error = true; error_tmp = true;
       $('#stamp_foil_name').addClass('inerr');
     }
  }
  // если есть ошибки в этом разделе - раскрыть раздел
  if(error_tmp)
    razd_paper_show();

  // Ручки и укрепление

  error_tmp = false;

  // Материал ручек
  arr_data['hand_sel1'] = $('#hand_sel1').val();
  arr_data['hand_mat_oth'] = '';
  if(arr_data['hand_sel1'] == 0) {
    arr_data['hand_mat_oth'] = replace_str($('#hand_mat_oth').val());
    $('#hand_mat_oth').val(arr_data['hand_mat_oth']);
    if(arr_data['hand_mat_oth']=='') {
       error = true; error_tmp = true;
       $('#hand_mat_oth').addClass('inerr');
    }
  }

  // Крепление ручек
  arr_data['hand_mount_sel'] = $('#hand_mount_sel').val();  // селект
  arr_data['hand_mount_oth'] = '';  // другое
  arr_data['hand_mount_color'] = '';  // клипсы цвет
  if(arr_data['hand_mount_sel'] == 0) {
     arr_data['hand_mount_oth'] = replace_str($('#hand_mount_oth').val());
     $('#hand_mount_oth').val(arr_data['hand_mount_oth']);
     if( (arr_data['hand_mount_oth'] == '') && ($('#hand_sel1').val() != '4') ) {
       error = true; error_tmp = true;
       $('#hand_mount_oth').addClass('inerr');
     }
  }
  if(arr_data['hand_mount_sel'] == 2) {
    arr_data['hand_mount_color'] = replace_str($('#hand_mount_color').val());
    $('#hand_mount_color').val(arr_data['hand_mount_color']);

  }

  // Толщина ручек, мм
  arr_data['hand_thick'] = replace_num_acc($('#hand_thick').val());

   // Цвет ручек
   arr_data['hand_color'] = replace_str($('#hand_color').val());
   $('#hand_color').val(arr_data['hand_color']);
   if( (arr_data['hand_color'] == '') && ($('#hand_sel1').val() != '4') ) {
      error = true; error_tmp = true;
      $('#hand_color').addClass('inerr');
	  $('#hand_color').focus();
   }

    // Видимая длина ручек (без учета узелков), см
    arr_data['hand_length'] = replace_num_acc($('#hand_length').val());
    $('#hand_length').val(arr_data['hand_length']);
    if( ((arr_data['hand_length']=='') || (arr_data['hand_length']=='0')) && ($('#hand_sel1').val() != '4') ) {
      error = true; error_tmp = true;
      $('#hand_length').addClass('inerr');
	  $('#hand_length').focus();
    }

    // Материал для скрепления пакета
    arr_data['hand_mater_sk'] = ($('#hand_mater_sk').get(0).checked == true)?1:0;    // Скотч норма
    arr_data['hand_mater_sk_tx'] = replace_str($('#hand_mater_sk_tx').val());
    $('#hand_mater_sk_tx').val(arr_data['hand_mater_sk_tx']);
    arr_data['hand_mater_kl'] = ($('#hand_mater_kl').get(0).checked == true)?1:0;    // Клей горячий
    arr_data['hand_mater_kl_tx'] = replace_str($('#hand_mater_kl_tx').val());
    $('#hand_mater_kl_tx').val(arr_data['hand_mater_kl_tx']);



    // Пикало
    arr_data['ch_pikalo'] = ($('#ch_pikalo').get(0).checked == true)?1:0;
    arr_data['pikalo_no_diam'] = 0;
    arr_data['pikalo_on_color'] = '';
    arr_data['pikalo_on_diam'] = 0;

    if(arr_data['ch_pikalo'] == 0) {
      if($('#hand_sel1').val() != '2') {
        arr_data['pikalo_no_diam'] = replace_num_acc($('#pikalo_no_diam').val());   // диаметр отверстий, мм

      }
    } else {
      arr_data['pikalo_on_color'] = replace_str($('#pikalo_on_color').val());
      $('#pikalo_on_color').val(arr_data['pikalo_on_color']);
      if( (arr_data['pikalo_on_color'] == '') && ($('#hand_sel1').val() != '4') ) {
         error = true; error_tmp = true;
         $('#pikalo_on_color').addClass('inerr');
      }
      arr_data['pikalo_on_diam'] = replace_num_acc($('#pikalo_on_diam').val());   // диаметр
      $('#pikalo_on_diam').val(arr_data['pikalo_on_diam']);
      if( ((arr_data['pikalo_on_diam']=='') || (arr_data['pikalo_on_diam']=='0')) && ($('#hand_sel1').val() != '4') ) {
        error = true; error_tmp = true;
        $('#pikalo_on_diam').addClass('inerr');
      }
    }

    // Укрепление пакета
    arr_data['strengt_bot'] = ($('#strengt_bot').get(0).checked == true)?1:0;     // дно
    if(arr_data['strengt_bot']==1)
      arr_data['strengt_bot_col'] = replace_str($('#strengt_bot_col').val());       // дно цвет
    else
      arr_data['strengt_bot_col'] = '';
    arr_data['strengt_side'] = ($('#strengt_side').get(0).checked == true)?1:0;   // бок
//    arr_data['strengt_oth'] = ($('#strengt_oth').get(0).checked == true)?1:0;     // другое
    arr_data['strengt_oth_tx'] = replace_str($('#strengt_oth_tx').val());         // другое текст


    // если есть ошибки в этом разделе - раскрыть раздел
    if(error_tmp)
      razd_hand_show();



   // Маркировка, упаковка, доставка, сборщики, сроки

   error_tmp = false;

   // Упаковка
   arr_data['ch_packing'] = ($('#ch_packing').get(0).checked == true)?1:0;
   arr_data['packing_kor'] = 0;
   arr_data['packing_plen'] = 0;
   arr_data['packing_sel'] = 1;       // по умолч тип коробки
   arr_data['packing_oth'] = '';      // другая упаковка или по шт выбранной
   if(arr_data['ch_packing'] == 0) {

      arr_data['packing_sel'] = $('#packing_sel').val();

      if(arr_data['packing_sel'] == '0') {            // не выбрана упаковка
        $('#packing_sel').addClass('inerr');
        error = true; error_tmp = true;
      } else {
        if( (arr_data['packing_sel']=='1') || (arr_data['packing_sel']=='2') ) {
           arr_data['packing_oth'] = replace_num_acc($('#packing_oth').val());
           $('#packing_oth').val(arr_data['packing_oth']);
           if( arr_data['packing_oth'] == '' ) {
             error = true; error_tmp = true;
             $('#packing_oth').addClass('inerr');
           }
        } else {   // другая упаковка
           arr_data['packing_oth'] = replace_str($('#packing_oth').val());
           $('#packing_oth').val(arr_data['packing_oth']);
           if( arr_data['packing_oth'] == '' ) {
             error = true; error_tmp = true;
             $('#packing_oth').addClass('inerr');
           }
        }
      }
   }

   // Маркировка и накладные от имени
   arr_data['packing_nameof_sel'] = $('#packing_nameof_sel').val();   // селект
   arr_data['packing_nameof_oth'] = '';
   if(arr_data['packing_nameof_sel'] == 0) {
      arr_data['packing_nameof_oth'] = replace_str($('#packing_nameof_oth').val());
      $('#packing_nameof_oth').val(arr_data['packing_nameof_oth']);
      if(arr_data['packing_nameof_oth'] == '') {
         error = true; error_tmp = true;
         $('#packing_nameof_oth').addClass('inerr');
      }
   }

   //Если выбран серийник, проверяем art_num_inp чтобы был заполнен.
   //потом берем номер артикула и пишем его в массив
type = document.getElementById("type").value;

if (type == "2") {
if ($('#art_num_inp').val())
{arr_data['art_num'] = $('#art_num_inp').val()}
else
{$('#art_num_inp').focus()}
}

   // Сборка разрешается только
   arr_data['ch_assperm1'] = ($('#ch_assperm1').get(0).checked == true)?1:0;
   arr_data['ch_assperm2'] = ($('#ch_assperm2').get(0).checked == true)?1:0;
   arr_data['ch_assperm3'] = ($('#ch_assperm3').get(0).checked == true)?1:0;
   arr_data['ch_assperm4'] = ($('#ch_assperm4').get(0).checked == true)?1:0;
   if((arr_data['ch_assperm1'] == 0) && (arr_data['ch_assperm2'] == 0) && (arr_data['ch_assperm3'] == 0) && (arr_data['ch_assperm4'] == 0)) {
     error = true; error_tmp = true;
     $('#ch_assperm1,#ch_assperm2,#ch_assperm3,#ch_assperm4').addClass('inerr');
   }


   // Особые требования
   arr_data['spec_req'] = replace_str($('#spec_req').val());
   $('#spec_req').val(arr_data['spec_req']);


	//Тип скотча
	arr_data['yellow_tape'] = ($('#yellow_tape').get(0).checked == true)?1:0;
	

   // Тарифы
   arr_data['rate_in'] = replace_str($('#rate_in').val());
   $('#rate_in').val(arr_data['rate_in']);
   if(arr_data['rate_in'] == '') {
         error = true; error_tmp = true;
         $('#rate_in').addClass('inerr');
      }
   arr_data['rate_lamin'] = replace_str($('#rate_lamin').val());
   $('#rate_lamin').val(arr_data['rate_lamin']);
   arr_data['rate_tigel_pril'] = replace_str($('#rate_tigel_pril').val());
   $('#rate_tigel_pril').val(arr_data['rate_tigel_pril']);
   arr_data['rate_tigel_udar'] = replace_str($('#rate_tigel_udar').val());
   $('#rate_tigel_udar').val(arr_data['rate_tigel_udar']);
   arr_data['rate_tisn_pril'] = replace_str($('#rate_tisn_pril').val());
   $('#rate_tisn_pril').val(arr_data['rate_tisn_pril']);
   arr_data['rate_tisn_udar'] = replace_str($('#rate_tisn_udar').val());
   $('#rate_tisn_udar').val(arr_data['rate_tisn_udar']);
   arr_data['rate_vstavka_dna_bok'] = replace_str($('#rate_vstavka_dna_bok').val());
   $('#rate_vstavka_dna_bok').val(arr_data['rate_vstavka_dna_bok']);
   arr_data['rate_line_truba_pril'] = replace_str($('#rate_line_truba_pril').val());
   $('#rate_line_truba_pril').val(arr_data['rate_line_truba_pril']);
   arr_data['rate_line_truba_prokat'] = replace_str($('#rate_line_truba_prokat').val());
   $('#rate_line_truba_prokat').val(arr_data['rate_line_truba_prokat']);
   arr_data['rate_line_dno_pril'] = replace_str($('#rate_line_dno_pril').val());
   $('#rate_line_dno_pril').val(arr_data['rate_line_dno_pril']);
   arr_data['rate_line_dno_prokat'] = replace_str($('#rate_line_dno_prokat').val());
   $('#rate_line_dno_prokat').val(arr_data['rate_line_dno_prokat']);
   arr_data['rate_upak'] = replace_str($('#rate_upak').val());
   $('#rate_upak').val(arr_data['rate_upak']);
   arr_data['rate_drugoe'] = replace_str($('#rate_drugoe').val());
   $('#rate_drugoe').val(arr_data['rate_drugoe']);
   arr_data['rate_podgotovka_truby'] = replace_str($('#rate_podgotovka_truby').val());
   $('#rate_podgotovka_truby').val(arr_data['rate_podgotovka_truby']);

	 //Тип заказа, 1 - заказ, 2 - серийник
  arr_data['type'] = $('#type').val();
   
   	 //Название заказа
  arr_data['ClientName'] = $('#ClientName').val();
     
	 //ID заказа
  arr_data['zakaz_id'] = $('#zakaz_id').val();
  

  
   // Порядок отгрузки заказа
   arr_data['chipping_nm1'] = replace_num_acc($('#chipping_nm1').val());
   $('#chipping_nm1').val(arr_data['chipping_nm1']);
   arr_data['chipping_tx1'] = replace_str($('#chipping_tx1').val());
   $('#chipping_tx1').val(arr_data['chipping_tx1']);

   arr_data['chipping_nm2'] = replace_num_acc($('#chipping_nm2').val());
   $('#chipping_nm2').val(arr_data['chipping_nm2']);
   arr_data['chipping_tx2'] = replace_str($('#chipping_tx2').val());
   $('#chipping_tx2').val(arr_data['chipping_tx2']);

   arr_data['chipping_nm3'] = replace_num_acc($('#chipping_nm3').val());
   $('#chipping_nm3').val(arr_data['chipping_nm3']);
   arr_data['chipping_tx3'] = replace_str($('#chipping_tx3').val());
   $('#chipping_tx3').val(arr_data['chipping_tx3']);


   // если есть ошибки в этом разделе - раскрыть раздел
   if(error_tmp)
     razd_packing_show();


   // если нету ошибок - сохранение
   if(error == false) {

      ajrun(
    		'/acc/applications/backend/apl.php',
    		{
          id						: edit,										  // id редактируемой заявки
          data          : arr_data

    		},
    		function(a,b) {	  // обработка результатов
//alert(b)
		 document.location = 'list.php';
          //$('#debug').html(b);
    		},
    		'apl_save_all'
  	  );
   }
}

//-->
</script>

<body onload="make_able_save_but()">

<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>
<? require_once("../templates/top.php"); ?>
<input name="subm" type="hidden" value="1" />
<?$name_curr_page = 'apl_list'; require_once("../templates/main_menu.php");?>
<table align="center" width="1100" border="0" cellpadding=0 bgcolor="#F6F6F6">
      	<tr>
      		<td valign="top">
<table width="1100" border=0 cellspacing="0" cellpadding="0">
<tr>
<td width="300"><a href="edit.php" class="sublink"><img src="../../i/manufacture.png" width="24" height="24" alt=""  style="vertical-align: middle;"/></a>
<a href="edit.php" class="sublink">Добавить заявку</a></td>
<td width="300" align=center>
<a href="list.php" class="sublink"><img src="../../i/manufacture.png" width="24" height="24" alt=""  style="vertical-align: middle;"/></a>
<a href="list.php" class="sublink">Список заявок</a>
</td></tr>
<tr><td colspan=2>
<h3>
<?if($res3["short"]){echo "Заявка по заказу <a href=\"/acc/query/query_send.php?show=".$uid."\">".$res3["short"]."</a>";}
else{echo "Заявка на серийник";}
?></h3>
<? if ($r_apl['type'] == "0") {?>Эта заявка не привязана к заказу и не имеет типа<? } ?>
				 </td></tr>
      			</table></td>
      </tr>
      <tr>
      	<td align="center">
           <table id="apl_edit" width="100%" cellpadding="0" cellspacing="0" border="0">
             <tr>
               <td align="center">
                  <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td align="center">
                        Название заказа<span class="err">*</span>
                      </td>
                      <td width="80" align="center">
&nbsp;
</td>
                      <td align="center">
                        <? if($id != 0) { ?>
                        Номер закакза<span class="err">*</span>
                        <?}?>
                      </td>
                      <td width="20">&nbsp;</td>
                      <td align="center">
                      Менеджер
                      </td>
                    </tr>
                    <tr>
                      <td>
 <input class="tx" name="apl_title" id="apl_title" type="text" maxlength="255"  size="50" value="<?=@$r_apl['title']?>" />
                      </td>
                      <td></td>
                      <td>
                        <? if($id != 0) { ?>
                        <input onkeyup="this.value=replace_num_acc(this.value);" class="tx" name="apl_num_ord" id="apl_num_ord" type="text" maxlength="255"  size="15" value="<?=@$r_apl['num_ord']?>" />
                        <?}?>
                      </td>
                      <td></td>
                      <td>

					     <?
                          $res = mysql_query("SELECT uid,surname,name FROM users WHERE 1=1 ORDER BY type");
                        ?>
                        <select <?=(($tpacc)?'':'disabled="disabled"')?> class="mang" name="manager" id="manager" size="1">
                          <? while($r = mysql_fetch_array($res)) {

                          $sel = '';
                          if($id==0) {
                            if($user_id == $r['uid'])
                              $sel = 'selected="selected"';
                          } else {
                            if($r_apl['user_id']==$r['uid'])
                              $sel = 'selected="selected"';
                          }
                          ?>
                          <option <?=$sel?> value="<?=$r['uid']?>">&nbsp;&nbsp;<?=$r['name']?>&nbsp;<?=$r['surname']?></option>
                          <?}?>
                        </select>

                      </td>
                    </tr>
                    <tr>
                      <td colspan="5">
                        &nbsp;
                      </td>
                    </tr>
                    <tr>
                      <td align="center">
Общий тираж, шт.<span class="err">*</span>
                      </td>
					   <td width="20">&nbsp;</td>
                      <td align="center">
                        Пределы перекат/недокат<span class="err">*</span>
                      </td>
                      <td width="20">&nbsp;</td>
                      <td align="center">
                        Дата
                      </td>

                    </tr>
                    <tr>
                      <td align="center">
<select name="izd_type" id="izd_type">
<option value="">выбрать</option>
<option value="4" <?if ($r_apl['izd_type'] == "4" || !$r_apl['izd_type']){echo "selected";}?>>Пакет бумажный</option>
<option value="6" <?if ($r_apl['izd_type'] == "6"){echo "selected";}?>>Тарелка</option>
<option value="17" <?if ($r_apl['izd_type'] == "17"){echo "selected";}?>>Упаковочная бумага</option>
<option value="16" <?if ($r_apl['izd_type'] == "16"){echo "selected";}?>>Конверт</option>
<option value="15" <?if ($r_apl['izd_type'] == "15"){echo "selected";}?>>Сумка</option>
<option value="14" <?if ($r_apl['izd_type'] == "14"){echo "selected";}?>>Лента хлопковая</option>
<option value="13" <?if ($r_apl['izd_type'] == "13"){echo "selected";}?>>Лента репсовая</option>
<option value="12" <?if ($r_apl['izd_type'] == "12"){echo "selected";}?>>Лента атласная</option>
<option value="11" <?if ($r_apl['izd_type'] == "11"){echo "selected";}?>>Коробка китпак</option>
<option value="10" <?if ($r_apl['izd_type'] == "10"){echo "selected";}?>>Лента</option>
<option value="9" <?if ($r_apl['izd_type'] == "9"){echo "selected";}?>>Ручки бумажные</option>
<option value="8" <?if ($r_apl['izd_type'] == "8"){echo "selected";}?>>Ручки с замком</option>
<option value="7" <?if ($r_apl['izd_type'] == "7"){echo "selected";}?>>Стакан</option>
<option value="5" <?if ($r_apl['izd_type'] == "5"){echo "selected";}?>>Коробка</option>
<option value="18" <?if ($r_apl['izd_type'] == "18"){echo "selected";}?>>Фиксирующие наклейки-замочки</option>
</select>

  <input onkeyup="this.value=replace_num_acc(this.value);" onchange="document.getElementById('chipping_nm1').value = document.getElementById('apl_tiraz').value;" class="tx" name="apl_tiraz" id="apl_tiraz" type="text" maxlength="255"  size="25" value="<?=@$r_apl['tiraz']?>" />



                      </td>
                      <td></td>
<td>
                        <table cellpadding="0" cellspacing="0" border="0">
                          <tr>
                            <td>
                              <select name="limit_per_sign" id="limit_per_sign" size="1" style="width: 35px;">
                                <option value="0" <?=(($id==0)?'selected="selected"':((@$r_apl['limit_per_sign']==0)?'selected="selected"':''))?>>+</option>
                                <option value="1" <?=(($id==0)?'':((@$r_apl['limit_per_sign']==1)?'selected="selected"':''))?>>-</option>
                              </select>
                            </td>
                            <td>
                              <input class="tx" onkeyup="limit_per_check();" name="apl_limit_per" id="apl_limit_per" type="text" maxlength="2"  size="13" value="<?if ($r_apl['limit_per']==""){echo $apl_limit_per_u;}else{echo $r_apl['limit_per'];}?>" />
                            </td>
                            <td>&nbsp;%</td>
                          </tr>
                        </table>

                      </td>
                      <td>
                 </td>
                      <td align=center><?
                        if($id>0) {
                          @$dat_ord = new dat_fn(@$r_apl['dat_ord']);
                          @$dat_ord = $dat_ord -> stringtm;
                        } else {
                          $dat_ord = date("d.m.Y H:i");
                        }
                      ?>
                        <input <?=(($tpacc || $user_id==$r_apl['user_id'])?'onclick="return showCalendar(\'apl_dat\', \'%d.%m.%Y %H:%M\',\'24\');"':'')?>  class="tx" name="apl_dat" id="apl_dat" readonly="1" type="text" maxlength="20" size="15" value="<?=$dat_ord?>" /><a></a>
                      </td>

                    </tr>
                  </table>
               </td>
             </tr>
             <tr>
              <td>&nbsp;</td>
             </tr>
             <tr>
              <td class="tab_tit_td">
              Бумага и размер пакета
              </td>
             </tr>
             <tr>
              <td id="tab_razd_paper" align="center" valign="top">
                <br /><script type="text/javascript">
$(document).on('keydown keypress', '.input-group', function (evt){
    var inp = evt.target, char = String.fromCharCode(evt.which);
    if( evt.type == 'keypress' ){
        if( inp.value.length == inp.maxLength ){
            var $group = $(':text', this);
            $group.eq($group.index(inp) + 1).focus().val( char );
        }
    } else if( evt.keyCode == 8 ){
        if( inp.value == '' ){
            var $group = $(':text', this);
            $group.eq($group.index(inp) - 1).focus();
            evt.preventDefault();
        }
    }
});
</script>
<table cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    <td align="center">
                      Ширина:<span class="err">*</span>
                    </td>
                    <td width="10">&nbsp;</td>
                    <td align="center">
                      Высота:<span class="err">*</span>
                    </td>
                    <td width="10">&nbsp;</td>
                    <td align="center">
                      Бок:<span class="err">*</span>
                    </td>
                    <td width="10">&nbsp;</td>
                    <td align="center">
                      Цвет пакета снаружи:<span class="err">*</span>
                    </td>
                    <td width="10">&nbsp;</td>
                    <td align="center">
                      Цвет пакета внутри:<span class="err">*</span>
                    </td>
                  </tr>
				  <script type="text/javascript">

                function jump(jumpfrom, maxsize, jumpto){

                if($("#jumpoff").is(":not(:checked)")){
                maxsize = maxsize-1
				if ($('#'+jumpfrom).val().length > maxsize){$('#'+jumpto).focus()}
                }
				}

function kor(){
$('#paper_col_inn').val("коричневый")
}
function white(){
$('#paper_col_inn').val("белый")
}
function grey(){
$('#paper_col_inn').val("серый")
}

				  </script>
                  <tr>
                    <td valign=top>
                      <input onkeyup="this.value=replace_num_acc(this.value);jump('paper_wd','2','paper_hg')"   class="tx" name="paper_wd" id="paper_wd" type="text" size="10" value="<?=@$r_apl['paper_width']?>" maxlength="255" />
                      <br><input type="checkbox" id=jumpoff /> <label for="jumpoff" style="font-size:7px; cursor:pointer; vertical-align: middle">отключить прыжки</label>
                    </td>
                    <td></td>
                    <td valign=top>
                      <input onkeyup="this.value=replace_num_acc(this.value);jump('paper_hg','2','paper_side')" class="tx" name="paper_hg" id="paper_hg" type="text" size="10" value="<?=@$r_apl['paper_height']?>" maxlength="255" />
                    </td>
                    <td></td>
                    <td valign=top>
                      <input onkeyup="this.value=replace_num_acc(this.value);jump('paper_side','2','paper_col_ext')" class="tx" name="paper_side" id="paper_side" type="text" size="10" value="<?=@$r_apl['paper_side']?>" maxlength="255" />
                    </td>
                    <td></td>
                    <td valign=top>
                      <input class="tx"  onkeyup="document.getElementById('paper_col_inn').value = document.getElementById('paper_col_ext').value;" name="paper_col_ext" id="paper_col_ext" type="text" size="25" value="<?=@$r_apl['paper_color_ext']?>" maxlength="255" />
                    </td>
                    <td align=center valign=top>
<span style="width: 15px; height: 15px; color: white; background-color: #995B2B; position: relative; cursor: pointer" onclick="kor()">коричн</span>
<br /><span style="width: 15px; height: 15px; color: black; background-color: white; position: relative; cursor: pointer" onclick="white()">белый</span>
<br /><span style="width: 15px; height: 15px; color: white; background-color: #929292; position: relative; cursor: pointer" onclick="grey()">серый</span>
					</td>
                    <td valign=top>
                      <input class="tx" name="paper_col_inn" id="paper_col_inn" type="text" size="25" value="<?=@$r_apl['paper_color_inn']?>" maxlength="255" />

                    </td>
                  </tr>
                </table>
                <br />
                <table cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    <td align="center" valign="bottom">
                      Бумага (плотность)<span class="err">*</span>
                    </td>
                    <td width="10">&nbsp;</td>
                    <td align="center" valign="bottom">
                      Название бумаги<span class="err">*</span>
                    </td>
                    <td width="10">&nbsp;</td>
                    <td align="center">
                      Планируемая дата поставки <br />листов на производство
                    </td>
                  </tr>
                  <tr>
                    <td valign=top>
                      <input class="tx" name="paper_density" onkeyup="jump('paper_density','3','paper_name')" id="paper_density" type="text" size="18" value="<?=@$r_apl['paper_density']?>" maxlength="255" />
                    </td>
                    <td></td>
                    <td valign=top>
                      <input class="tx" name="paper_name" id="paper_name" type="text" size="50" value="<?=@$r_apl['paper_name']?>" maxlength="255" />
	<br />

<font face="tahoma" size=1 onclick="document.getElementById('paper_name').value = 'меловка';" style="cursor: pointer">меловка</font> |
<font face="tahoma" size=1 onclick="document.getElementById('paper_name').value = 'плотный крафт';" style="cursor: pointer">плотный крафт</font> |
<font face="tahoma" size=1 onclick="document.getElementById('paper_name').value = 'белый крафт';" style="cursor: pointer">белый крафт</font> |
<font face="tahoma" size=1 onclick="document.getElementById('paper_name').value = 'крафт лайнер коричневый';" style="cursor: pointer">крафт лайнер корич.</font> |
<br />
<font face="tahoma" size=1 onclick="document.getElementById('paper_name').value = 'крафт лайнер бело-коричневый';" style="cursor: pointer">крафт лайнер бел/корич.</font> |
<font face="tahoma" size=1 onclick="document.getElementById('paper_name').value = 'картон';" style="cursor: pointer">картон</font> |
<font face="tahoma" size=1 onclick="document.getElementById('paper_name').value = 'имитлин';" style="cursor: pointer">имитлин</font> |
<font face="tahoma" size=1 onclick="document.getElementById('paper_name').value = 'картон там брайт';" style="cursor: pointer">картон там брайт</font>

                    </td>
                    <td></td>
                    <td valign=top>
                      <?
                       $dat_przv = new dat_fn(@$r_apl['paper_dat_deliv'],1);
                       $dat_przv = $dat_przv -> stringdat;
                      ?>
                      <input onclick="return showCalendar('pap_przv_dat', '%d.%m.%Y');" class="tx" name="pap_przv_dat" id="pap_przv_dat"  readonly="1" type="text" maxlength="20" size="25" value="<?=$dat_przv?>" /><a></a>&nbsp;<a href="#" onclick="$('#pap_przv_dat').val('');return false;" onmouseover="Tip('Очистить')">X</a>
                    </td>
                  </tr>
                </table>
                <br />
                <table cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    <td align="center" valign="bottom">
                      Типография
                    </td>
                    <td width="10">&nbsp;</td>
                    <td align="center" valign="bottom">
                      Поставщик бумаги
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input class="tx" name="paper_press" id="paper_press" type="text" size="50" value="<?=@$r_apl['paper_press']?>" maxlength="255" />
                    </td>
                    <td></td>
                    <td>
                      <input class="tx" name="paper_suppl" id="paper_suppl" type="text" size="50" value="<?=@$r_apl['paper_suppl']?>" maxlength="255" />
                    </td>
                  </tr>
                </table>
                <br />
                <table cellpadding="2" cellspacing="0" border="0">
                  <tr>
                    <td align="center" valign="bottom">
                      Из скольких листов собирается:<span class="err">*</span>
                    </td>
                    <td width="30">&nbsp;</td>
                    <td align="left" valign="bottom">
                      Листы на пакете<span class="err">*</span>
                    </td>
                  </tr>
                  <tr>
                    <td valign="top">
                      <select class="paper_num_list" name="paper_num_list" id="paper_num_list" size="1">
                        <option value="1" <?=((@$r_apl['paper_num_list']==1)?'selected="selected"':'')?> >Из одного</option>
                        <option value="2" <?=((@$r_apl['paper_num_list']==2)?'selected="selected"':'')?> >Из двух</option>
                      </select>
                    </td>
                    <td></td>
                    <td>
                      <table width="270" cellpadding="2" cellspacing="0" border="0">
                        <tr>
                          <td width="20">
                            <input onclick="switch_paper_list_typ_txt();" style="padding: 0; margin: 0; width: 20px;" type="radio" name="rad_paper_list_typ" id="rad_paper_list_typ_1" value="1" <?=(($id==0)?'checked="checked"':(( !trim(@$r_apl['paper_list_typ']))?'checked="checked"':''))?>  />
                          </td>
                          <td align="left" width="250">
                            <a onclick="$('#rad_paper_list_typ_1').check('on');switch_paper_list_typ_txt();return false;" href="#">
                              Одинаковые
                            </a>
                          </td>
                        </tr>
                        <tr>
                          <td width="10">
                            <input onclick="switch_paper_list_typ_txt();" style="padding: 0; margin: 0; width: 20px;" type="radio" name="rad_paper_list_typ" id="rad_paper_list_typ_2" <?=(($id==0)?'':(( trim(@$r_apl['paper_list_typ']) )?'checked="checked"':''))?> value="1" />
                          </td>
                          <td align="left">
                            <a onclick="$('#rad_paper_list_typ_2').check('on');switch_paper_list_typ_txt();return false;" href="#">
                              Разные
                            </a>
                          </td>
                        </tr>
                        <tr>
                          <td id="td_paper_list_typ" colspan="2" align="left" style="display: none;">
                            <textarea name="paper_list_typ_tx" id="paper_list_typ_tx" rows="2" cols="40"><?=((trim(@$r_apl['paper_list_typ']))?$r_apl['paper_list_typ']:'')?></textarea>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
                <br />
                <table cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    <td align="center" valign="bottom">
                      Ламинация<span class="err">*</span>
                    </td>
                    <td width="30">&nbsp;</td>
                    <td align="center" valign="bottom">
                      Тиснение<span class="err">*</span>
                    </td>
                  </tr>
                  <tr>
                    <td valign="top">
                      <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                          <td valign="top">
                            <select onchange="switch_lami_tp();" class="lami_sel1" id="lami_sel1" name="lami_sel1" size="1">
                              <option value="0" <?=(($id==0)?'selected="selected"':((@$r_apl['lamination_tp'] == 0)?'selected="selected"':''))?>>Без ламинации</option>
                              <option value="1" <?=((@$r_apl['lamination_tp'] == 1)?'selected="selected"':'')?>>Матовая</option>
                              <option value="2" <?=((@$r_apl['lamination_tp'] == 2)?'selected="selected"':'')?>>Глянцевая</option>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td id="td_lami_tp" valign="top" align="left" style="display: none;">
                            <table cellpadding="0" cellspacing="0" border="0">
                              <tr>
                                <td width="10">
                                  <input type="checkbox" name="ch_lami_tp1" id="ch_lami_tp1" value="1" <?=((@$r_apl['lamination_ext']==1)?'checked="checked"':'')?> <?=((@$r_apl['lamination_ext']=="")?'checked="checked"':'')?>/>
                                </td>
                                <td valign="middle">
                                  <a onclick="$('#ch_lami_tp1').check('toggle');return false;" href="#">снаружи</a>
                                </td>
                                <td width="10">&nbsp;</td>
                                <td width="10">
                                  <input type="checkbox" name="ch_lami_tp2" id="ch_lami_tp2" value="1" <?=((@$r_apl['lamination_inn']==1)?'checked="checked"':'')?> />
                                </td>
                                <td>
                                  <a onclick="$('#ch_lami_tp2').check('toggle');return false;" href="#">внутри</a>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                    </td>
                    <td></td>
                    <td valign="top" align="left" width="405">
                      <table width="400" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                          <td valign="top" align="center">
                            <select onchange="switch_tisn_tp();" class="tisn_sel1" id="tisn_sel1" name="tisn_sel1" size="1">
                              <option value="0" <?=(($id==0)?'selected="selected"':((@$r_apl['stamp']==0)?'selected="selected"':''))?> >Без тиснения</option>
                              <option value="1" <?=((@$r_apl['stamp']==1)?'selected="selected"':'')?>>С одной стороны</option>
                              <option value="2" <?=((@$r_apl['stamp']==2)?'selected="selected"':'')?>>С двух сторон</option>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td id="td_tisn_tp" valign="top" align="center" style="display: block;">
                            <table cellpadding="0" cellspacing="0" border="0">
                              <tr>
                                <td width="10">
                                  <input type="radio" name="ch_tisn_tp" id="ch_tisn_tp1" value="1" <?=(($id==0)?'checked="checked"':((@$r_apl['stamp_typ']==0)?'checked="checked"':''))?>  />
                                </td>
                                <td valign="middle">
                                  <a onclick="$('#ch_tisn_tp1').check('on');return false;" href="#">одинаковое</a>
                                </td>
                                <td width="10">&nbsp;</td>
                                <td width="10">
                                  <input type="radio" name="ch_tisn_tp" id="ch_tisn_tp2" value="2" <?=((@$r_apl['stamp_typ']==1)?'checked="checked"':'')?> />
                                </td>
                                <td>
                                  <a onclick="$('#ch_tisn_tp2').check('on');return false;" href="#">разное</a>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td>
                        </tr>
                        <tr>
                          <td id="td_tisn_opt" rowspan="2" valign="top" style="display: block;">
                            <table cellpadding="0" cellspacing="0" border="0">
                              <tr>
                                <td align="center">
                                  ширина<span class="err">*</span>
                                </td>
                                <td align="center">
                                  высота<span class="err">*</span>
                                </td>
                                <td align="center">
                                  цвет<span class="err">*</span>
                                </td>
                              </tr>
                              <tr>
                                <td>
                                  <input onkeyup="this.value=replace_num_acc(this.value);" class="tx" name="stamp_width" id="stamp_width" type="text" size="18" value="<?=((trim(@$r_apl['stamp_width']))?@$r_apl['stamp_width']:'0')?>" maxlength="255" />
                                </td>
                                <td>
                                  <input onkeyup="this.value=replace_num_acc(this.value);" class="tx" name="stamp_height" id="stamp_height" type="text" size="18" value="<?=((trim(@$r_apl['stamp_height']))?@$r_apl['stamp_height']:'0')?>" maxlength="255" />
                                </td>
                                <td>
                                  <input class="tx" name="stamp_color" id="stamp_color" type="text" size="25" value="<?=@$r_apl['stamp_color']?>" maxlength="255" />
                                </td>
                              </tr>
                              <tr>
                                <td colspan="3">&nbsp;</td>
                              </tr>
                              <tr>
                                <td align="center">
                                  отступ от дна, мм<span class="err">*</span>
                                </td>
                                <td align="center">
                                  отступ справа, мм<span class="err">*</span>
                                </td>
                                <td align="center">
                                  название фольги<span class="err">*</span>
                                </td>
                              </tr>
                              <tr>
                                <td>
                                  <input onkeyup="this.value=replace_num_acc(this.value);" class="tx" name="stamp_indent_bott" id="stamp_indent_bott" type="text" size="18" value="<?=((trim(@$r_apl['stamp_indent_bott']))?@$r_apl['stamp_indent_bott']:'0')?>" maxlength="255" />
                                </td>
                                <td>
                                  <input onkeyup="this.value=replace_num_acc(this.value);" class="tx" name="stamp_indent_right" id="stamp_indent_right" type="text" size="18" value="<?=((trim(@$r_apl['stamp_indent_right']))?@$r_apl['stamp_indent_right']:'0')?>" maxlength="255" />
                                </td>
                                <td>
                                  <input class="tx" name="stamp_foil_name" id="stamp_foil_name" type="text" size="25" value="<?=@$r_apl['stamp_foil_name']?>" maxlength="255" />
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
             </tr>
             <tr>
              <td>&nbsp;</td>
             </tr>
             <tr>
                <td class="tab_tit_td">
      Ручки и укрепление
                </td>
             </tr>
             <tr>
                <td id="tab_hand" align="center" valign="top">
                   <br />
                   <table cellpadding="0" cellspacing="0" border="0">
                     <tr>
                       <td valign="top" align="center">
                         Материал ручек<span class="err">*</span>
                       </td>
                       <td width="10">&nbsp;</td>
                       <td valign="top" align="center">
                         Крепление ручек<span class="err">*</span>
                       </td>
                       <td width="10">&nbsp;</td>
                       <td valign="top" align="center">
                         Толщина ручек, мм
                       </td>
                     </tr>
                     <tr>
                       <td valign="top" align="center">
                         <select onchange="switch_hand_tp1();" class="hand_sel1" name="hand_sel1" id="hand_sel1" size="1">
                            <option value="0" <?=(($id==0)?'selected="selected"':((@$r_apl['hand_mater_tp']==0)?'selected="selected"':''))?>>другой...</option>
                            <option value="1" <?=((@$r_apl['hand_mater_tp']==1)?'selected="selected"':'')?><?=((@$r_apl['hand_mater_tp']==0)?'selected="selected"':'')?>>п/п шнур</option>
                            <option value="2" <?=((@$r_apl['hand_mater_tp']==2)?'selected="selected"':'')?>>бум. шпагат</option>
                            <option value="3" <?=((@$r_apl['hand_mater_tp']==3)?'selected="selected"':'')?>>лента</option>
                            <option value="4" <?=((@$r_apl['hand_mater_tp']==4)?'selected="selected"':'')?>>без ручек</option>
                         </select>
                       </td>
                       <td>&nbsp;</td>
                       <td valign="top" align="center">
                         <select onchange="switch_hand_mount();" class="hand_sel1" name="hand_mount_sel" id="hand_mount_sel" size="1">
                            <option value="0" <?=(($id==0)?'selected="selected"':((@$r_apl['hand_mount_tp']==0)?'selected="selected"':''))?> >другой...</option>
                            <option value="1" <?=((@$r_apl['hand_mount_tp']==1)?'selected="selected"':'')?>>Узелок</option>
                            <option value="2" <?=((@$r_apl['hand_mount_tp']==2)?'selected="selected"':'')?><?=((@$r_apl['hand_mount_tp']=="")?'selected="selected"':'')?>>Клипсы</option>
                            <option value="3" <?=((@$r_apl['hand_mount_tp']==3)?'selected="selected"':'')?>>Клей</option>
                            <option value="4" <?=((@$r_apl['hand_mount_tp']==4)?'selected="selected"':'')?>>Прорубные</option>
                            <option value="5" <?=((@$r_apl['hand_mount_tp']==5)?'selected="selected"':'')?>>Без ручек</option>
                            <option value="6" <?=((@$r_apl['hand_mount_tp']==6)?'selected="selected"':'')?>>Бумажные</option>
                         </select>
                       </td>
                       <td></td>
                       <td valign="top" align="right">
                         <input onkeyup="this.value=replace_num_acc(this.value);" class="tx" name="hand_thick" id="hand_thick" type="text" size="20" maxlength="255" value="<?=trim(@$r_apl['hand_thick'])?>" />
                       </td>
                     </tr>
                     <tr>
                       <td valign="top">
                          <input class="tx" name="hand_mat_oth" id="hand_mat_oth" type="text" size="35" maxlength="255" value="<?=@$r_apl['hand_mater_txt']?>" />
                       </td>
                       <td>&nbsp;</td>
                       <td valign="top" align="right">
                          <div id="div_hand_mount_color">цвет:
                            <input class="tx" name="hand_mount_color" id="hand_mount_color" type="text" size="28" maxlength="255" value="<?if (isset($r_apl['hand_mount_color'])){echo $r_apl['hand_mount_color'];}else{echo "прозрачный";}?>" />
                          </div>
                          <input class="tx" name="hand_mount_oth" id="hand_mount_oth" type="text" size="35" maxlength="255" value="<?=@$r_apl['hand_mount_txt']?>" />
                       </td>
                       <td></td>
                       <td></td>
                     </tr>
                   </table>
                   <br />
                   <table cellpadding="0" cellspacing="0" border="0">
                     <tr>
                       <td valign="bottom" align="center">
                         Цвет ручек<span class="err">*</span><br>
						 <a style="cursor: pointer;" onclick="document.getElementById('hand_color').value = 'черные';">черные</a> |
						 <a style="cursor: pointer;" onclick="document.getElementById('hand_color').value = 'белые';">белые</a> |
						 <a style="cursor: pointer;" onclick="document.getElementById('hand_color').value = document.getElementById('paper_col_ext').value;">в тон пакета</a>

                       </td>
                       <td width="10">&nbsp;</td>
                       <td valign="top" align="center">
                         Видимая длина ручек <br />(без учета узелков), см<span class="err">*</span>
                       </td>
                       <td width="20">&nbsp;</td>
                       <td valign="bottom" align="left">
                         Материал для скрепления пакета
                       </td>
                     </tr>
                     <tr>
                       <td valign="top" align="center">
                         <input class="tx" name="hand_color" id="hand_color" type="text" size="20" maxlength="255" value="<?=@$r_apl['hand_color']?>" />
                       </td>
                       <td></td>
                       <td valign="top" align="center">
                         <input onkeyup="this.value=replace_num_acc(this.value);" class="tx" name="hand_length" id="hand_length" type="text" size="20" maxlength="255" value="<?
if ($r_apl['hand_length'] == "") {echo $hand_length_u;} else {echo $r_apl['hand_length'];}?>" />
                       </td>
                       <td></td>
                       <td valign="top" align="center">
                          <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                              <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                  <tr>
                                    <td>
                                      <input type="checkbox"  onchange="calc_tape();return false;" name="hand_mater_sk" id="hand_mater_sk" value="1" <?=((@$r_apl['hand_mater_scotch']==1)?'checked="checked"':'')?> />
                                    </td>
                                    <td>
									<script type="text/javascript"><!--
									//примерный расчет расхода скотча на пакет
									function calc_tape()
									{
//получаем значения всех необходимых переменных
//ширина пакета
var bag_width = document.getElementById('paper_wd').value;

//высота пакета
var bag_height = document.getElementById('paper_hg').value;

//бок пакета
var bag_side = document.getElementById('paper_side').value;

//пакет состоит из Х листов
var bag_parts = document.getElementById('paper_num_list').value;

//подгиб пакета
var podgib = "5";

//преобразуем данные в номера
bag_width = bag_width-0;
bag_height = bag_height-0;
bag_side = bag_side-0;
bag_parts = bag_parts-0;
podgib = podgib-0;
k = 8;

//вычисляем сколько нужно скотча на проклейку бокового шва
var x = (podgib+bag_height+(bag_side*k/10))*bag_parts;
//вычисляем сколько нужно скотча на проклейку дна
var y = (bag_side*k/10*4+bag_width*k/10);
//вычисляем сколько нужно скотча подгиба
var z = (bag_width*k/10)*2;
//получаем сумму
var sum = (x+y+z);

sum = sum.toFixed();

document.getElementById('hand_mater_sk_tx').value = sum;
}
									--></script>
                                       <a href="#"  onclick="$('#hand_mater_sk').check('toggle');calc_tape();return false;">Скотч норма</a> 
                                    </td>
                                  </tr>
                                </table>
                              </td>
                              <td>
                                <input class="tx" name="hand_mater_sk_tx" onclick="calc_tape();return false;" id="hand_mater_sk_tx" type="text" size="12" maxlength="255" value="<?=@$r_apl['hand_mater_scotch_tx']?>" />
                              </td>
                              <td>
                                см/пакет<br><input type="checkbox"  name="yellow_tape" id="yellow_tape" value="1" <?=((@$r_apl['yellow_tape']==1)?'checked="checked"':'')?> /> <a href="#"  onclick="$('#yellow_tape').check('toggle');return false;">желтый скотч!</a>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                  <tr>
                                    <td>
                                      <input type="checkbox"  name="hand_mater_kl" id="hand_mater_kl" value="1" <?=((@$r_apl['hand_mater_glue']==1)?'checked="checked"':'')?> />
                                    </td>
                                    <td>
                                       <a href="#" onclick="$('#hand_mater_kl').check('toggle');return false;">
                                          Клей горячий
                                       </a>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                              <td>
                                <input class="tx" name="hand_mater_kl_tx" id="hand_mater_kl_tx" type="text" size="12" maxlength="255" value="<?=@$r_apl['hand_mater_glue_tx']?>" />
                              </td>
                              <td>
                                 / пакет
                              </td>
                            </tr>
                          </table>
                       </td>
                     </tr>
                   </table>
                   <br />
                   <table cellpadding="0" cellspacing="0" border="0">
                     <tr>
                       <td align="center" valign="top">
                          <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                              <td width="10" align="left" valign="top" style="padding-top: 12px;">
                                <input onclick="ch_pikalo_opt();" type="checkbox" name="ch_pikalo" id="ch_pikalo" value="1" <?=((@$r_apl['pikalo_on']==1)?'checked="checked"':'')?> />
                              </td>
                              <td align="left" valign="top"><br />
                                <a onclick="$('#ch_pikalo').check('toggle');ch_pikalo_opt();return false;" href="#">
                                  Пикало
                                </a>
                              </td>
                              <td valign="top" width="155">
                                <div id="div_pikalo_opt1" style="display: none;">
                                   <table width="150" cellpadding="0" cellspacing="0" border="0">
                                     <tr>
                                       <td align="center">
                                          диаметр отверстий, мм
                                       </td>
                                     </tr>
                                     <tr>
                                       <td>
                                          <input onkeyup="this.value=replace_num_acc(this.value);" class="tx" name="pikalo_no_diam" id="pikalo_no_diam" type="text" size="24" maxlength="255" value="<?if ($r_apl['pikalo_diam_hol']=="" or $r_apl['pikalo_diam_hol']=="0"){echo $pikalo_diam_hol_u;}else{echo $r_apl['pikalo_diam_hol'];}?>" />
                                       </td>
                                     </tr>
                                   </table>
                                 </div>
                                 <div id="div_pikalo_opt2" style="display: none;">
                                   <table width="150" cellpadding="0" cellspacing="0" border="0">
                                     <tr>
                                       <td align="center" valign="top">
                                          цвет<span class="err">*</span>
                                       </td>
                                       <td align="center" valign="top">
                                          диаметр<span class="err">*</span>
                                       </td>
                                     </tr>
                                     <tr>
                                       <td>
                                          <input class="tx" name="pikalo_on_color" id="pikalo_on_color" type="text" size="10" maxlength="255" value="<?=@$r_apl['pikalo_color']?>" />
                                       </td>
                                       <td>
                                          <input onkeyup="this.value=replace_num_acc(this.value);" class="tx" name="pikalo_on_diam" id="pikalo_on_diam" type="text" size="10" maxlength="255" value="<?=@$r_apl['pikalo_diam_hol']?>" />
                                       </td>
                                     </tr>
                                   </table>
                                 </div>
                              </td>
                              <td width="30">&nbsp;</td>
                              <td align="left">
                                  <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                      <td align="center" colspan="2">
                                        Укрепление пакета
                                      </td>
                                    </tr>
                                    <tr>
                                      <td align="right" width="65">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                          <tr>
                                            <td width="20">
                                              <input type="checkbox" name="strengt_bot" id="strengt_bot" value="1" <?=(($id==0)?'checked="checked"':((@$r_apl['strengt_bot']==1)?'checked="checked"':''))?> />
                                            </td>
                                            <td>
                                              <a href="#" onclick="$('#strengt_bot').check('toggle');return false;">
                                                Дно
                                              </a>&nbsp;
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                      <td width="150" align="right">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                          <tr>
                                            <td>цвет&nbsp;</td>
                                            <td>
                                              <input class="tx" name="strengt_bot_col" id="strengt_bot_col" type="text" size="20" maxlength="255" value="<?=@$r_apl['strengt_bot_col']?>" />
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td align="right">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                          <tr>
                                            <td width="20">
                                              <input type="checkbox" name="strengt_side" id="strengt_side" value="1" <?=(($id==0)?'checked="checked"':((@$r_apl['strengt_side']==1)?'checked="checked"':''))?> />
                                            </td>
                                            <td>
                                              <a href="#" onclick="$('#strengt_side').check('toggle');return false;">
                                                Бок
                                              </a>&nbsp;&nbsp;
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                      <td></td>
                                    </tr>
                                    <tr>
                                      <td align="left">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                          <tr>
                                            <td width="20">
                                              <!--<input type="checkbox" name="strengt_oth" id="strengt_oth" value="1" <?=((@$r_apl['strengt_oth']==1)?'checked="checked"':'')?> />-->
                                            </td>
                                            <td>
                                              <!--<a href="#" onclick="$('#strengt_oth').check('toggle');return false;">-->
                                                Другое
                                              <!--</a>-->
                                            </td>
                                          </tr>
                                        </table>
                                      </td>
                                      <td align="right">
                                        <input class="tx" name="strengt_oth_tx" id="strengt_oth_tx" type="text" size="25" maxlength="255" value="<?=trim(@$r_apl['strengt_oth_tx'])?>" />
                                      </td>
                                    </tr>
                                  </table>
                               </td>
                            </tr>
                          </table>
                       </td>
                     </tr>
                   </table>
                </td>
             </tr>
             <tr>
              <td>&nbsp;</td>
             </tr>
             <tr>
                <td class="tab_tit_td">
         Маркировка, упаковка, доставка, сборщики, сроки
                </td>
             </tr>
             <tr>
                <td id="tab_packing" align="center" valign="top">
                   <br />
                   <table cellpadding="0" cellspacing="0" border="0">
                     <tr>
                       <td width="210" align="left" valign="top">
                         Упаковка<span class="err">*</span>
                       </td>
                       <td width="20">&nbsp;</td>
                       <td align="center" valign="top">
                         Маркировка и накладные от имени<span class="err">*</span>
                       </td>
                       <td width="20">&nbsp;</td>
                       <td align="center" valign="top">
                         Сборка раз решается только<span class="err">*</span>
                       </td>
                     </tr>
                     <tr>
                       <td align="left" valign="top" height="10">
                          <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                              <td align="left" width="10">
                                <input onclick="ch_packing_opt();" type="checkbox" name="ch_packing" id="ch_packing" value="1" <?=((@$r_apl['packing_korob']==1)?'checked="checked"':'')?> />
                              </td>
                              <td align="left">
                                <a href="#" onclick="$('#ch_packing').check('toggle');ch_packing_opt();return false;">
                                  Не важно
                                </a>
                              </td>
                            </tr>
                          </table>
                       </td>
                       <td></td>
                       <td align="left" valign="top" height="10">
                          <select onchange="packing_nameof_oth_switch();" class="packing_nameof" name="packing_nameof_sel" id="packing_nameof_sel" size="1">
                             <option value="0" <?=(($id==0)?'selected="selected"':((@$r_apl['mark_of_company_tp']==0)?'selected="selected"':''))?>>другое...</option>
                             <option value="1" <?=((@$r_apl['mark_of_company_tp']==1)?'selected="selected"':'')?>>Принтфолио</option>
                             <option value="2" <?=((@$r_apl['mark_of_company_tp']==2)?'selected="selected"':'')?><?=((@$r_apl['mark_of_company_tp']=="")?'selected="selected"':'')?>>Пакетофф</option>
                             <option value="3" <?=((@$r_apl['mark_of_company_tp']==3)?'selected="selected"':'')?>>без</option>
                          </select>
                       </td>
                       <td></td>
                       <td rowspan="2" valign="top" align="left">
                         <table cellpadding="0" cellspacing="0" border="0">
                           <tr>
                             <td align="left" width="10">
                                <input type="checkbox" name="ch_assperm1" id="ch_assperm1" value="1" <?=((@$r_apl['assperm_1']==1)?'checked="checked"':'')?> 
<?=(($id==0)?'checked="checked"':'')?>
/>
                             </td>
                             <td align="left">
                                <a href="#" onclick="$('#ch_assperm1').check('toggle');return false;">
                                  Цех дневная смена
                                </a>
                             </td>
                           </tr>
                           <tr>
                             <td align="left" width="10">
                                <input type="checkbox" name="ch_assperm2" id="ch_assperm2" value="1" <?=((@$r_apl['assperm_2']==1)?'checked="checked"':'')?><?=(($id=="")?'checked="checked"':'')?> />
                             </td>
                             <td align="left">
                                <a href="#" onclick="$('#ch_assperm2').check('toggle');return false;">
                                  Цех вечерняя смена
								  
                                </a>
                             </td>
                           </tr>
                           <tr>
                             <td align="left" width="10">
                                <input type="checkbox" name="ch_assperm3" id="ch_assperm3" value="1" <?=((@$r_apl['assperm_3']==1)?'checked="checked"':'')?>
                             </td>
                             <td align="left">
                                <a href="#" onclick="$('#ch_assperm3').check('toggle');return false;">
                                  Надомники надежные
                                </a>
                             </td>
                           </tr>
                           <tr>
                             <td align="left" width="10">
                                <input type="checkbox" name="ch_assperm4" id="ch_assperm4" value="1" <?=((@$r_apl['assperm_4']==1)?'checked="checked"':'')?> />
                             </td>
                             <td align="left">
                                <a href="#" onclick="$('#ch_assperm4').check('toggle');return false;">
                                  Надомники все
                                </a>
                             </td>
                           </tr>
                         </table>
                       </td>
                     </tr>
                     <tr>
                        <td align="left" valign="top" height="60">
                          <table id="packing_opt" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                              <td>
                                <select onchange="packing_sel_switch(1);" class="packing_sel" name="packing_sel" id="packing_sel" size="1">
                               <option value="2" <?=((@$r_apl['packing_sel']==2)?'selected="selected"':'')?><?=((@$r_apl['packing_sel']=="")?'selected="selected"':'')?>>Пленка</option>
                                   <option value="1" <?=((@$r_apl['packing_sel']==1)?'selected="selected"':'')?>>Коробки</option>
                                  
                                   <option value="3" <?=((@$r_apl['packing_sel']==3)?'selected="selected"':'')?>>другая...</option>
                                </select>
                              </td>
                            </tr>
                            <tr>
                              <td align="right" height="20">
                                <div id="packing_other_div" style="display:none;">
                                  <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                      <td id="packing_other_nam" width="45">
                                        другое<span class="err">*</span>&nbsp;
                                      </td>
                                      <td>
                                        <input onkeyup="if( ($('#packing_sel').val() == 1) || ($('#packing_sel').val() == 2) ) {this.value=replace_num_acc(this.value);}" class="tx" name="packing_oth" id="packing_oth" type="text" size="23" value="<?if ($r_apl['packing_other']=="0" or $r_apl['packing_other']==""){echo $packing_other_u;}else{echo $r_apl['packing_other'];}?>" maxlength="255" />
                                      </td>
                                    </tr>
                                  </table>
                                </div>
                              </td>
                            </tr>
                          </table>
                        </td>
                        <td></td>
                        <td align="left" valign="top">
                          <div id="div_packing_nameof_oth" style="display:none;">
                             <input class="tx" name="packing_nameof_oth" id="packing_nameof_oth" type="text" size="32" value="<?=@$r_apl['mark_of_company']?>" maxlength="255" />
                          </div>
                        </td>
                        <td></td>
                     </tr>
                   </table>

                   <br />
                   <table cellpadding="0" cellspacing="0" border="0">
                     <tr>
                       <td align="left" valign="top">
                          <table cellpadding="0" cellspacing="0" border="0">
                             <tr>
                               <td align="center" valign="top">
                                  Порядок отгрузки заказа
                               </td>
                             </tr>
                             <tr>
                               <td>
                                 <table cellpadding="0" cellspacing="0" border="0">
                                   <tr>
                                     <td align="left">
                                        <input class="tx" name="chipping_nm1" id="chipping_nm1" type="text" size="10" maxlength="255" value="<?=trim(@$arr_otgr[0][0])?>" />
                                     </td>
                                     <td>
                                       шт. к
                                     </td>
                                     <td align="left">
                                        <input onclick="return showCalendar('chipping_tx1', '%d.%m.%Y');" class="tx" name="chipping_tx1" id="chipping_tx1" type="text" size="10" maxlength="255" value="<?=trim(@$arr_otgr[0][1])?>" readonly="1" /><a></a>&nbsp;<a href="#" onclick="$('#chipping_tx1').val('');return false;" onmouseover="Tip('Очистить')">X</a>
                                     </td>
                                   </tr>
                                   <tr>
                                     <td align="left">
                                        <input class="tx" name="chipping_nm2" id="chipping_nm2" type="text" size="10" maxlength="255" value="<?=trim(@$arr_otgr[1][0])?>" />
                                     </td>
                                     <td>
                                      шт. к
                                     </td>
                                     <td align="left">
                                        <input onclick="return showCalendar('chipping_tx2', '%d.%m.%Y');" class="tx" name="chipping_tx2" id="chipping_tx2" type="text" size="10" maxlength="255" value="<?=trim(@$arr_otgr[1][1])?>" readonly="1" /><a></a>&nbsp;<a href="#" onclick="$('#chipping_tx2').val('');return false;" onmouseover="Tip('Очистить')">X</a>
                                     </td>
                                   </tr>
                                   <tr>
                                     <td align="left">
                                        <input class="tx" name="chipping_nm3" id="chipping_nm3" type="text" size="10" maxlength="255" value="<?=trim(@$arr_otgr[2][0])?>" />
                                     </td>
                                     <td>
                                     шт. к
                                     </td>
                                     <td align="left">
                                        <input onclick="return showCalendar('chipping_tx3', '%d.%m.%Y');" class="tx" name="chipping_tx3" id="chipping_tx3" type="text" size="10" maxlength="255" value="<?=trim(@$arr_otgr[2][1])?>" readonly="1" /><a></a>&nbsp;<a href="#" onclick="$('#chipping_tx3').val('');return false;" onmouseover="Tip('Очистить')">X</a>
                                     </td>
                                   </tr>
                                 </table>
                               </td>
                             </tr>
                           </table>
                       </td>
                       <td width="30">
                          &nbsp;
                       </td>
                       <td align="left" valign="top">
                         <table cellpadding="0" cellspacing="0" border="0">
                           <tr>
                             <td align="center">
                               Особые требования
                             </td>
                           </tr>
                           <tr>
                             <td valign="top" align="left">
                               <textarea class="spec_req" name="spec_req" id="spec_req" ><?=@$r_apl['special_requir']?></textarea>
                             </td>
                           </tr>
                         </table>
                       </td>
                     </tr>
                   </table>
                   <br />
                   <table cellpadding="0" width=100% cellspacing="0" border="0" height=50>
                     <tr>
					 <td align=right width=500>

					 <span id=art_span style="<?=(($type == 2 or $r_apl['art_num'])?'display: block;':'display: none;')?>">Артикул


<input type=text name="art_num_inp" onchange="get_uid()"  id="art_num_inp" onchange="unblock_save_but()" value="<?if (@$r_apl['art_num']){echo @$r_apl['art_num'];}?>">
<span style="opacity: 0.5;"> uid <input type=text name="art_uid_inp" disabled size=5 class=tx id="art_uid_inp" value="<?if(@$r_apl['art_uid']){echo @$r_apl['art_uid'];}?>"></span></span>
<span id="art_uid_span"></span>
					 </td>
                       <td align="right">
<script>
/*function show_tarif(type){
    if(type == "linia"){
	$('#linia').toggle(250)
    }
    if(type == "dops"){
	$('#dops').toggle(250)
    }

}   */
function show_tarifs(){
	$('#tarifs').toggle(250)

}

function show_tarif(type){
	$('#'+type).toggle(250)
}

 </script>

<table>
<tr style="background-color:#9ACC1C">
<td>сборка ручная:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" onchange="change_tarif()" name="rate_in" id="rate_in" type="text" size="10" maxlength="255" value="<?if($r_apl['rate'] == "0" or $r_apl['rate']) {echo str_replace(',','.',$r_apl['rate']);}else{echo "3";}?>" /></td>
</tr>

</table>







&darr; <span onclick="show_tarifs()" style="cursor:pointer"><b>Тарифы:</b></span>

<table width="450" border="1" cellpadding="1" cellspacing="1" id="tarifs"style="display:none">
<tr>
<td>ламинирование:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_lamin" id="rate_lamin" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_lamin']){echo "0.5";}else{echo $r_apl['rate_lamin'];}?>" /></td>

</tr>
<tr>
<td>приладка вырубка:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_tigel_pril" id="rate_tigel_pril" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_tigel_pril']) {echo "300";}else{echo $r_apl['rate_tigel_pril'];}?>" /></td>

</tr>
<tr>
<td>вырубка за удар:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" onkeyup="this.value=replace_zap(this.value);" name="rate_tigel_udar" id="rate_tigel_udar" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_tigel_udar']){echo "0.35";}else{echo $r_apl['rate_tigel_udar'];}?>" /></td>

</tr>
<tr>
<td>приладка тиснение:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_tisn_pril" id="rate_tisn_pril" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_tisn_pril']){echo "300";}else{echo $r_apl['rate_tisn_pril'];}?>" /></td>

</tr>
<tr>
<td>тиснение за удар:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_tisn_udar" id="rate_tisn_udar" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_tisn_udar']){echo "0.40";}else{echo $r_apl['rate_tisn_udar'];}?>" /></td>
</tr>

<tr>
<td>допработы в целом:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_upak" id="rate_upak" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_upak']){echo "0.17";}else{echo $r_apl['rate_upak'];}?>" />
</td>
</tr>

<tr>
<td>другое:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_drugoe" id="rate_drugoe" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_drugoe']){echo "0";}else{echo $r_apl['rate_drugoe'];}?>" />
</td>
</tr>
    <script>
    function change_tarif()   {
 	$('#rate_podgotovka_truby').val($('#rate_in').val() * 0.7)
    }
    </script>

<tr><td colspan=2><span style="border-bottom: 2px dotted; font-size: 30px;cursor:pointer" onclick="show_tarif('linia')">линия</span></td></tr>
<tr>
<td valign=top>
<table style="display:none; width:400px;" id=linia>
<tr>
<td>вставка дна и боковин:</td>
<td style="width:50px;"><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_vstavka_dna_bok" id="rate_vstavka_dna_bok" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_vstavka_dna_bok']) {echo "1.50";}else{echo $r_apl['rate_vstavka_dna_bok'];}?>" /></td>
</tr><tr>
<td>ручная подготовка трубы:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_podgotovka_truby" id="rate_podgotovka_truby" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate']) {echo "1.75";}else{echo str_replace(',','.',$r_apl['rate'])*0.7;}?>" /></td>
</tr><tr>
<td>линия труба приладка:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_line_truba_pril" id="rate_line_truba_pril" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_line_truba_pril']) {echo "1000";}else{echo $r_apl['rate_line_truba_pril'];}?>" /></td>
</tr><tr>
<td>линия труба прокатка:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_line_truba_prokat" id="rate_line_truba_prokat" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_line_truba_prokat']) {echo "0.5";}else{echo $r_apl['rate_line_truba_prokat'];}?>" /></td>
</tr><tr>
<td>линия дно приладка:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_line_dno_pril" id="rate_line_dno_pril" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_line_dno_pril']) {echo "500";}else{echo $r_apl['rate_line_dno_pril'];}?>" /></td>
</tr><tr>
<td>линия дно прокатка:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_line_dno_prokat" id="rate_line_dno_prokat" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_line_dno_prokat']) {echo "0.3";}else{echo $r_apl['rate_line_dno_prokat'];}?>" /></td>
</tr></table>

</td>
</tr>



<tr><td colspan=2><span style="border-bottom: 2px dotted; font-size: 30px;cursor:pointer" onclick="show_tarif('dops')">допработы</span></td></tr>
<tr>
<td valign=top>

<table style="display:none; width:400px;" id=dops>
<tr>
<td>нарезка шнура на станке (2 шт):</td>
<td style="width:50px;"><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_narezka_shnur_stanok" id="rate_narezka_shnur_stanok" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_narezka_shnur_stanok']) {echo "0.07";}else{echo $r_apl['rate_narezka_shnur_stanok'];}?>" /></td>
</tr>
<tr>
<td>нарезка ленты/шнура вручную (2 шт):</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_narezka_shnur_hand" id="rate_narezka_shnur_hand" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_narezka_shnur_hand']) {echo "0.2";}else{echo $r_apl['rate_narezka_shnur_hand'];}?>" /></td>
</tr>
<tr>
<td>нарезка дна/боковин на 1 пакет:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_narezka_dna_bokovin" id="rate_narezka_dna_bokovin" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_narezka_dna_bokovin']) {echo "0.05";}else{echo $r_apl['rate_narezka_dna_bokovin'];}?>" /></td>
</tr>
<tr>
<td>привязка ленты с бантом 1 пакет:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_privyazka_lenty" id="rate_privyazka_lenty" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_privyazka_lenty']) {echo "0.8";}else{echo $r_apl['rate_privyazka_lenty'];}?>" /></td>
</tr>
<tr>
<td>привязка шнура узел 1 пакет:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_privyazka_shnur" id="rate_privyazka_shnur" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_privyazka_shnur']) {echo "0.3";}else{echo $r_apl['rate_privyazka_shnur'];}?>" /></td>
</tr>
<tr>
<td>вставка ручек с клипсами на 1 пакет:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_vstavka_ruchek_klipsy" id="rate_vstavka_ruchek_klipsy" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_vstavka_ruchek_klipsy']) {echo "0.15";}else{echo $r_apl['rate_vstavka_ruchek_klipsy'];}?>" /></td>
</tr>
<tr>
<td>сверление 1 пакет:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_sverlenie" id="rate_sverlenie" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_sverlenie']) {echo "0.04";}else{echo $r_apl['rate_sverlenie'];}?>" /></td>
</tr>
<tr>
<td>установка люверсов 4шт:</td>
<td><input class="tx" onkeyup="this.value=replace_zap(this.value);" name="rate_ustanovka_luversov" id="rate_ustanovka_luversov" type="text" size="10" maxlength="255" value="<?if(!$r_apl['rate_ustanovka_luversov']) {echo "0.13";}else{echo $r_apl['rate_ustanovka_luversov'];}?>" /></td>
</tr>

</table>





</td>
</tr>




</table>





<br>

                       </td>
                       <td align="left">

                       </td>
                     </tr>
                   </table>
                </td>
             </tr>
             <tr>
                <td height="30" align="center">
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
				<td valign="top">

<table width="100%" border="0" cellpadding="10" cellspacing="1"><tr>
<td valign=top>
<input type=hidden size=2 value=<?=$type?> name=type id=type>
</td>
<td valign=top>

<span id="ClientN" style="<?=(($type == 1 and $res3["short"])?'display: block;"':'display: none;')?>" valign=top>название заказа: <input type=text name=ClientName id="ClientName" size="30" value="<?=$res3["short"];?>">
<input type=hidden size=6 value="<?=$uid?>" name=zakaz_id id="zakaz_id"></span>

<span id="new_old" style="<?=(($type == 2 and !is_numeric($r_apl['art_num']))?'display: block;':'display: none;')?>">
<input type=radio onchange="create_art()" name=art_new_old id="art_new"/> <label for="art_new" style="cursor: pointer">новый</label>
&nbsp; <input type=radio name=art_new_old id="art_old" onchange="hide_new()"/> <label for="art_old" style="cursor: pointer">уже существует</label>
</span></td>
<td></td>
<td></td>
</tr>
<tr>
<td colspan=4>
<span id=save_iframe></span>
</td></tr>
</table>
</td>
				</tr>
				</table>

                </td>
             </tr>
             <tr>
                <td align="center">
                   <input <?//if ($r_apl['type'] == "0"){ }else{if($type == "2"){echo "disabled";}}?> onclick="apl_check_all_feld();"  type="button" id=save_but name="save_all" value="Сохранить" style="width: 500px; cursor:pointer; height: 70px; font-size: 42px;" />
                </td>
             </tr>
           </table>
        </td>
      </tr>
      </table>
    </td>
  </tr>
  </form>
</table>
<div id="debug"></div>

<br /><br /><br /><br />
</body>
</html>
<? ob_end_flush();?>