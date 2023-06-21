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

$tpus = $user_type;		// тип пользователя
$typ_ord = $_GET['typ_ord'];
$form_of_payment  = $_GET['form_of_payment'];
$deliv_id  = $_GET['deliv_id'];
$courier_task_id = $_GET["courier_task_id"];

// ----- перейти на главную если доступ запрещен ---------
if(!$auth) {
	header("Location: /");
	exit;
}
// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;

// форматирование дробного числа до 2 чисел после запятой
function form_num($v) {
	$v = preg_replace('/\,/', '.', ''.$v);
	$v = number_format($v,2, '.', '');
	$v = preg_replace('/\.00/', '', $v);
	$v = preg_replace('/-0/', '0', $v);
	return $v;
}

// чтение всех адресов майлов для отправки уведомлений о новых оплатах
$list_mail = '';

	$arr_mail = array();

	$query = "SELECT email FROM mail";
	$res = mysql_query($query);
	while( $r = mysql_fetch_array($res) ) {
		if(!in_array($r['email'], $arr_mail))
			$arr_mail[] = $r['email'];
	}
	// весь список майлов в одну строку разделяемых точкой запятой
	$list_mail = implode(',', $arr_mail);

$sort_f = 'client';
$order_f = 'asc';

if(isset($_GET['sort']) && !empty($_GET['sort']))
	$sort_f = $_GET['sort'];
else
	$sort_f = 'date';

if(isset($_GET['order']) && !empty($_GET['order']))
	$order_f = $_GET['order'];
else
	$order_f = 'asc';

$lk_sort_f = $sort_f;
$lk_order_f = $order_f;

//  ------------- разбиение по страницам -------------

if($_GET["lim"]){
$rows_onpage = $_GET["lim"];
}else{
$rows_onpage = 80; 		// запросов на странице
}

$all_pages = false; 	// истина при нажатии ссылки "показать все"
$page = 1;						// страница по умолчанию

if(isset($_GET['page'])) {
	if(is_numeric($_GET['page']))
		$page = ($_GET['page']) > 0 ? $_GET['page'] : 1;
	elseif($_GET['page'] = 'all')
		$all_pages = true;
}

// Выполнение запроса на документ
function set_query_sel($id) {
	$query = "SELECT prdm_num_acc FROM queries WHERE uid=".$id." AND type='1' AND ready='0'";	// определение номера счета
	$res = mysql_query($query);
	$r = mysql_fetch_array($res);
	if(trim(@$r['prdm_num_acc'])) {		// определение uid запроса на счет по номеру счета
		$query = "SELECT uid FROM queries WHERE uid<>".$id." AND prdm_num_acc='".$r['prdm_num_acc']."' LIMIT 1";
		$res = mysql_query($query);
		$r = mysql_fetch_array($res);
		if(trim(@$r['uid']) && is_numeric(@$r['uid'])) {	// перевод запроса на счет  в  запрос на документ
			$query = "UPDATE queries SET type='2',date_ready=NOW(),ready='1' WHERE uid=".$r['uid'];
			if(mysql_query($query)) {		// удаление невыполненного запроса на документ
				$query = "DELETE FROM queries WHERE uid=".$id;
				mysql_query($query);
			}
		}
	}
}


// Выполнение запроса на документ
if(isset($_GET['set']) && is_numeric($_GET['set'])) {
	set_query_sel($_GET['set']);
}


// удаление несвязных подрядчиков, предмет счета, поля оплаты
function del_unlink_query() {
	//список предмета счета
	$query = "SELECT obj_accounts.uid FROM obj_accounts LEFT JOIN queries ON obj_accounts.query_id=queries.uid WHERE queries.uid IS NULL";
	$res = mysql_query($query);
	while($r = mysql_fetch_array($res)) {
		$query = "DELETE FROM obj_accounts WHERE uid=".$r['uid'];
		mysql_query($query);
	}

	// поля оплаты для предмета счета
	$query = "SELECT payment_predm.uid FROM payment_predm LEFT JOIN queries ON payment_predm.query_id=queries.uid WHERE queries.uid IS NULL";
	$res = @mysql_query($query);
	while($r = @mysql_fetch_array($res)) {
		$query = "DELETE FROM payment_predm WHERE uid=".$r['uid'];
		@mysql_query($query);
	}

	// список подрядчиков
	$query = "SELECT contractors_list.uid FROM contractors_list LEFT JOIN queries ON contractors_list.query_id=queries.uid WHERE queries.uid IS NULL";
	$res = mysql_query($query);
	while($r = mysql_fetch_array($res)) {
		$query = "DELETE FROM contractors_list WHERE uid=".$r['uid'];
		mysql_query($query);
	}

	// поля оплаты  для подрядчиков
	$query = "SELECT payment_podr.uid FROM payment_podr LEFT JOIN contractors_list ON payment_podr.contr_id	=contractors_list.uid WHERE contractors_list.uid IS NULL";
	$res = mysql_query($query);
	while($r = mysql_fetch_array($res)) {
		$query = "DELETE FROM payment_podr WHERE uid=".$r['uid'];
		mysql_query($query);
	}
}

// удаление запроса по его ид
function del_query($id) {
	// удаление полей оплаты подрядчиков
	$query = "SELECT uid FROM contractors_list WHERE query_id=".$id;
	$res = mysql_query($query);
	while($r = mysql_fetch_array($res)) {
		$query = "DELETE FROM payment_podr WHERE contr_id=".$r['uid'];
		mysql_query($query);
	}
	// удаление списка подрядчиков
	$query = "DELETE FROM contractors_list WHERE query_id=".$id;
	mysql_query($query);
	// удаление списка предмета счета
	$query = "DELETE FROM obj_accounts WHERE query_id=".$id;
	mysql_query($query);
	//удаление полей оплаты для передмета счета
	$query = "DELETE FROM payment_predm WHERE query_id=".$id;
	mysql_query($query);
	//удаление запроса на счет
	$query = "DELETE FROM queries WHERE uid=".$id;
	mysql_query($query);
}

//	------------------- Удаление запроса кнопкой удалить -----------------
if(isset($_GET['del']) && is_numeric($_GET['del']))	{
	del_query($_GET['del']);
	del_unlink_query();
}

    //получаем имена манагеров в массив
$users_q = mysql_query("SELECT uid, surname FROM users WHERE (type = 'mng' OR type = 'adm' OR type = 'sup' OR type = 'meg')");
            while ( $row = mysql_fetch_array($users_q) ) {
           $users_arr[$row[uid]] = $row[surname];

            }
// ---------------------------------------------------------
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.2</title>

<link href="../style.css?cache=<?=rand(1,1000000);?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
<script src="../includes/js/jquery.ui.datepicker-ru.js"></script>

<link rel="stylesheet" href="../includes/js/jquery-ui.css">

</head>
<script src="../includes/lib/JsHttpRequest/JsHttpRequest.js"></script>

<script language="JavaScript" type="text/javascript">

<!--

ssttss = 0;

// координаты мыши
var xpos=0;
var ypos=0;

var txpos = 0;
var typos = 0;

var curr_ball_men_id = 0;

var list_mail = '<?=$list_mail?>'; // список майлов для отправки им уведомлений об оплатах

var tpacc = <?=$tpacc?>;
var user_id = <?=$user_id?>;

var arr_cost_tmp = new Array();
var num_opl;						// номер строки полей оплаты

var curr_cost_id = 0;		// текущий ид запроса, при редактировании полей оплаты

var curr_date = '<?=date("d.m.Y")?>';		// текущая дата в формате '01.05.2007'

var cost_tmp_count = 0; // число оплат во временном массиве после загрузки из базы

<!-- <<<<<<<<<<<< ******** ОПРЕДЕЛЕНИЕ КООРДИНАТ МЫШИ  *****************  //-->

function defPosition(event) {
      var x = y = 0;
      if (document.attachEvent != null) { // Internet Explorer & Opera
            x = window.event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
            y = window.event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
      }
      if (!document.attachEvent && document.addEventListener) { // Gecko
            x = event.clientX + window.scrollX;
            y = event.clientY + window.scrollY;
      }
      return {x:x, y:y};
}

document.onmousemove = function(event) {
     var event = event || window.event;
      xpos = defPosition(event).x;
	  ypos = defPosition(event).y;
}

//используется при внесении оплат
function date_picker() {
    $( "#datepicker" ).datepicker();

 }


// форматирование стоимости
function replace_price(v) {
	for(i=0;i<3;i++) {
		var reg_sp = /[^\d,\.]*/g;		// вырезание всех символов кроме цифр, запятой и точки
		v = v.replace(reg_sp, '');
		var reg_sp = /\.|,{2,}|\.{2,}|,\.|\.,/g; 	// вырезание подряд идущих запятых и точек
		v = v.replace(reg_sp, ',');
		var reg_sp = /^,|^\./g;				// если первый символ точка или запятая, заменяет на '0,'
		v = v.replace(reg_sp, '0,');
	}
	var reg_sp = /^0*$/g;						// если в строке только одни нули, стереть
	v = v.replace(reg_sp, '0');

//	for(i=0;i<20;i++) {
//		var reg_sp = /(\s*(\d{3})\b)/g;			// формативование пробелами по 3 цифры
//		v = v.replace(reg_sp, " $2");
//	}
	var reg_sp = /^\s|,$/g;						// убрать самый первый пробел
	v = v.replace(reg_sp, "");
	var reg_sp = /,(\s)/g;					// убрать пробелы после запятой
	v = v.replace(reg_sp, ",");
	var reg_sp = /^0,$/g;						// стереть все если в выражении только '0,'
	v = v.replace(reg_sp, "");

	v = fix_number(v);

	return v;
}



// форматирование числа для корректного подсчета и отображения
function fix_number(v) {
	v = replace_zap((''+v))*1;		// преобразование запятой в точку
	v = ''+(v).toFixed(2);			// округление до 2х цифр после запятой
	var reg_sp = /\.00/g; 			// вырезание подряд идущих запятых и точек
	v = v.replace(reg_sp, '');		// если число целое - убрать в конце .00
	return v;
}



// Заменяет запятые на точки, для корректного подсчета суммы счета
function replace_zap(v) {
	var reg_sp = /,/g; 				// замена запятых на точки
	v = v.replace(reg_sp, '.');
	var reg_sp = /\s/g; 			// удаление пробелов
	v = v.replace(reg_sp, '');
	return v;
}





function load_opl(query_id){

var pos = $("#td_opl_"+query_id).position();

$('#div_opl').css({
    position: 'absolute',
    left: pos.left,
    top: pos.top
});

   $('#div_opl').fadeIn(300)

  var geturl;
  geturl = $.ajax({
    type: "GET",
    url: '../backend/load_opl.php',
	data : '&query_id='+query_id+'&tpacc='+tpacc+'&user_id='+user_id,
    success: function () {
var resp = geturl.responseText
$("#div_opl_in").html(resp);
}
})

}

function check_date(obj_id){
//проверяем чтобы дата не была случайно из будущего
var dt = new Date();
year = dt.getFullYear()
month = dt.getMonth()
day = dt.getDate()
insertedate = $("#paydate_"+obj_id).val();
ins = insertedate.split('.')
ins_day = ins[0]
ins_month = ins[1]
ins_year = ins[2]
//alert(ins_year+" "+year)
if(ins_day > day && ins_month > month && (ins_year > year || ins_year == year)){alert("Платеж не может быть проведен датой, позже чем сегодня")
$("#"+obj_id).focus();
return false}
else{return true}

return true

}

function save_paydate(query_id, act, uid){

if(act == "add"){
obj_id = query_id;
if(check_date(obj_id) == false){return false}
}
if(act=="edit"){
obj_id = uid+"_"+query_id;
if(check_date(obj_id) == false){return false}
}
if(act == "del")
{obj_id = uid+"_"+query_id;}


paydate = $("#paydate_"+obj_id).val();
number_pp = $("#number_pp_"+obj_id).val();
sum = $("#sum_"+obj_id).val();
if(sum == "" || sum <= "0"){$("#sum_"+obj_id).focus(); return false;}


  var geturl;
  geturl = $.ajax({
    type: "GET",
    url: '../backend/save_paydate.php',
	data : '&query_id='+query_id+'&act='+act+'&sum='+sum+'&paydate='+paydate+'&number_pp='+number_pp+'&uid='+uid+'&list_mail='+list_mail,
beforeSend : function (){$("#save_but_"+query_id).attr("disabled", true); },
    success: function () {

var resp = geturl.responseText
  load_opl(query_id)
  resp = resp.split(';;;')
  oplaceno = resp[0]
  if(oplaceno=="0" || oplaceno==""){oplaceno = "---"}
  dolg = resp[1]
  status = resp[13]

  if($.isNumeric(oplaceno)){$("#td_opl_"+query_id).html(oplaceno)}
  if($.isNumeric(dolg)){$("#td_dolg_"+query_id).html(dolg)}

  if(status == "OK"){bgcolor = "#009900";} else {bgcolor = "#FF0000";}
$('<div id="div_opl_resp" style="display:none; position: fixed; top:20px; right:20px;font-size:18px;background-color: '+bgcolor+'; color:white; font-face:arial; font-weight:bold; width: 200px; height: 45px; z-index:10000;">'+status+'</div>').insertAfter("#div_opl");
  $("#div_opl_resp").fadeIn(200);
  $("#div_opl_resp").fadeOut(200);
  $("#val").select();
if(act == "add" || act == "edit"){setTimeout(hide_pay(),100);}

}
})
 }


function hide_pay(){
$('#div_opl').fadeOut(200);
$("#val").select();
}

function unblock_but(id){
$('#'+id).prop('disabled', false);
}


// отображение окна редактирования процента менеджеру
function ShowBallMen(id) {

		curr_ball_men_id = id;
		txpos = xpos;
		typos = ypos;
		document.getElementById('div_ball').style.top = (typos)+'px';
		document.getElementById('div_ball').style.left = (txpos-160)+'px';
		document.getElementById('div_ball').style.display = 'block';
		document.getElementById('inp_ball_men').value = document.getElementById(('hidd_ball_men_'+id)).value;

}
// сохранение процента менеджеру
function SaveBallMen() {
		tmp_val = document.getElementById('inp_ball_men').value;
    var req_ball = new JsHttpRequest();
    req_ball.onreadystatechange = function() {
        if (req_ball.readyState == 4) {
						str = req_ball.responseJS.str;				// массив возвращенных значений
						document.getElementById(('ball_men_'+curr_ball_men_id)).innerHTML = ''+tmp_val;
						document.getElementById('div_ball').style.display = 'none';
						document.getElementById(('hidd_ball_men_'+curr_ball_men_id)).value=tmp_val;
						document.location = 'query_list.php?prcmpl=' + curr_ball_men_id + '&page=<?=@$_GET['page']?>&sort=<?=$lk_sort_f?>&order=<?=$lk_order_f?>';
				}
    }
    req_ball.open(null, '../backend/back_SaveBallMeneger.php', true);
    req_ball.send( { id:curr_ball_men_id, val:tmp_val } );
}

function del_query(id,page) {
	if(confirm("Удалить?"))
		document.location = 'query_list.php?del=' + id + '&page=' + page + '&sort=<?=$lk_sort_f?>&order=<?=$lk_order_f?>';
}



/*   **************    ПЕРЕМЕЩЕНИЕ СЛОЕВ МЫШКОЙ   ******** <<<<<<<<<<    */

var flag=false;
var shift_x;
var shift_y;

function start_drag(itemToMove,e){
if(!e) e = window.event;
flag=true;
shift_x = e.clientX-parseInt(itemToMove.style.left);
shift_y = e.clientY-parseInt(itemToMove.style.top);

if(e.stopPropagation) e.stopPropagation();
else e.cancelBubble = true;
if(e.preventDefault) e.preventDefault();
else e.returnValue = false;
}

function end_drag(){ flag=false; }

function dragIt(itemToMove,e){
if(!flag) return;
if(!e) e = window.event;
itemToMove.style.left = (e.clientX-shift_x) + "px";
itemToMove.style.top = (e.clientY-shift_y) + "px";

if(e.stopPropagation) e.stopPropagation();
else e.cancelBubble = true;
if(e.preventDefault) e.preventDefault();
else e.returnValue = false;
}

/*   >>>>> *********    ПЕРЕМЕЩЕНИЕ СЛОЕВ МЫШКОЙ   ***********    */

// показать фильтр по клиенту
function ShowFiltrPrClient() {
	txpos = xpos;
	typos = ypos;
	document.getElementById('div_fltr_num').style.top = (typos+5)+'px';
	document.getElementById('div_fltr_num').style.left = (txpos)+'px';
	document.getElementById('div_fltr_num').style.display = 'block';
	document.getElementById('val').focus();
}



//-->
</script>

<body>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>

<? require_once("../templates/top.php");
$name_curr_page = 'query_list';
require_once("../templates/main_menu.php");?>
<table width=1100 border=0 cellpadding=0 cellspacing=0 align=center bgcolor="#F6F6F6">
	<tr>
		<td align="center">

        <table border="0" width=1100 cellspacing="0" cellpadding="10">
				<tr>

	  	<td align="center" width=300 valign=bottom>

   <?require_once("../templates/search_form.php"); ?>

					<td width="150">
						<a href="query_send.php" class="sublink"><img src="../../i/invoice_sm.png" width="24" height="24" alt="" align=absmiddle /></a>
						<a href="query_send.php" class="sublink">запросить счет</a>


					</td>
                    <td width="170" >
					<a href="/acc/stat/stat_shop.php?type=shop_history" class="sublink"><img src="../../i/statistics.png" width="24" height="24" alt="" align="absmiddle"></a>
						<a href="/acc/stat/stat_shop.php?type=shop_history" class="sublink">статистика магазин</a>
					</td>

					<td>
					<a href="clients_list.php" class="sublink"><img src="../../i/clients.png" width="24" height="24" alt="" align="absmiddle"></a>
						<a href="clients_list.php" class="sublink">клиенты</a>
					</td>
					<td>
				 		<a href="contractors_list.php" class="sublink"><img src="../../i/vendor.png" width="24" height="24" alt="" align="absmiddle"></a>
						<a href="contractors_list.php" class="sublink">поставщики</a>
					</td>
					<? if($tpus == 'sup' || $tpus == 'acc') { ?>
					<td>
						<a href="/acc/stat/stat_table_query.php" target="_blank" class="sublink"><img src="../../i/tables.png" width="24" height="24" alt="" align="absmiddle"></a>
						<a href="/acc/stat/stat_table_query.php" target="_blank" class="sublink">работа с таблицами</a>
					</td>
					<? } ?>
				</tr>
			</table>

<?if($tpus == 'sup' or $tpus == 'acc' or $tpus == 'meg'){?>


<script>
function per_page(){
var lim = $('#lim').val()
window.location.href = "?lim="+lim
}

<?if($tpacc){?>

function save_acc_num(uid){

set_acc = $("#acc_inp_"+uid).val()
if($.isNumeric(uid)){

var geturl;
  geturl = $.ajax({
    type: "GET",
    url: 'save_acc_num.php',
	data : '&set_acc='+set_acc+'&acc_id='+uid,
 beforeSend: function () {
 //$("#status_"+type).html("<img src=\"../../i/load.gif\">");
    },
    complete: function () {

    },
    success: function () {
var resp = geturl.responseText
if(resp == 'ok'){
$('<div id="save_acc_resp_'+uid+'" style="display:none; position: absolute;font-size:18px;background-color: #009900; color:white; font-face:arial; width: 200px; height: 35px; z-index:10000; text-align:middle"></div>').insertAfter("#acc_but_"+uid);
  $("#save_acc_resp_"+uid).html(resp);
  $("#save_acc_resp_"+uid).fadeIn(200);
  $("#save_acc_resp_"+uid).fadeOut(200);
  }else{alert(resp)}

}
})
} }
<?}?>

</script>

<?}?>
</td>
</tr>

<tr>
	<td align="center">
<form action="" method=get>
<table width="1100" border="0" cellpadding="0" cellspacing="0" align=center><tr>
<td>

<input type=hidden name="filtr" value="client"/>
<?
if($_GET["month_num"]) {
$tek_year = $_GET["year_num"];
$tek_month = $_GET["month_num"];
}else{
$tek_year = date("Y");
$tek_month = date("m");
}
?>
показать все заявки за:
<select id=month_num name=month_num>
<option value="01" <?if($tek_month=="01"){echo " selected";}?>>январь</option>
<option value="02" <?if($tek_month=="02"){echo " selected";}?>>февраль</option>
<option value="03" <?if($tek_month=="03"){echo " selected";}?>>март</option>
<option value="04" <?if($tek_month=="04"){echo " selected";}?>>апрель</option>
<option value="05" <?if($tek_month=="05"){echo " selected";}?>>май</option>
<option value="06" <?if($tek_month=="06"){echo " selected";}?>>июнь</option>
<option value="07" <?if($tek_month=="07"){echo " selected";}?>>июль</option>
<option value="08" <?if($tek_month=="08"){echo " selected";}?>>август</option>
<option value="09" <?if($tek_month=="09"){echo " selected";}?>>сентябрь</option>
<option value="10" <?if($tek_month=="10"){echo " selected";}?>>октябрь</option>
<option value="11" <?if($tek_month=="11"){echo " selected";}?>>ноябрь</option>
<option value="12" <?if($tek_month=="12"){echo " selected";}?>>декабрь</option>
</select>
<select id=year_num name=year_num>
<option value="2010" <?if($tek_year=="2010"){echo " selected";}?>>2010</option>
<option value="2011" <?if($tek_year=="2011"){echo " selected";}?>>2011</option>
<option value="2012" <?if($tek_year=="2012"){echo " selected";}?>>2012</option>
<option value="2013" <?if($tek_year=="2013"){echo " selected";}?>>2013</option>
<option value="2014" <?if($tek_year=="2014"){echo " selected";}?>>2014</option>
<option value="2015" <?if($tek_year=="2015"){echo " selected";}?>>2015</option>
<option value="2016" <?if($tek_year=="2016"){echo " selected";}?>>2016</option>
<option value="2017" <?if($tek_year=="2017"){echo " selected";}?>>2017</option>
<option value="2018" <?if($tek_year=="2018"){echo " selected";}?>>2018</option>
<option value="2019" <?if($tek_year=="2019"){echo " selected";}?>>2019</option>
<option value="2020" <?if($tek_year=="2020"){echo " selected";}?>>2020</option>
<option value="2021" <?if($tek_year=="2021"){echo " selected";}?>>2021</option>
<option value="2022" <?if($tek_year=="2022"){echo " selected";}?>>2022</option>
</select> <input type=submit value=">>">
<? if($_GET["month_num"] and $tpacc) {
    if($user_id == '11' || $user_id == '12'){?>
<img src="../../i/statistics.png" width="24" height="24" alt="" style="cursor:pointer" onclick="show_monthly_stat()">
<? }} ?>
</td></form>
<td valign=bottom>
<? if($user_id == '11' || $user_id == '12') { ?>
общая статитстика с
<select id=month_num_from name=month_num_from>
<option value="01" <?if($tek_month=="01"){echo " selected";}?>>январь</option>
<option value="02" <?if($tek_month=="02"){echo " selected";}?>>февраль</option>
<option value="03" <?if($tek_month=="03"){echo " selected";}?>>март</option>
<option value="04" <?if($tek_month=="04"){echo " selected";}?>>апрель</option>
<option value="05" <?if($tek_month=="05"){echo " selected";}?>>май</option>
<option value="06" <?if($tek_month=="06"){echo " selected";}?>>июнь</option>
<option value="07" <?if($tek_month=="07"){echo " selected";}?>>июль</option>
<option value="08" <?if($tek_month=="08"){echo " selected";}?>>август</option>
<option value="09" <?if($tek_month=="09"){echo " selected";}?>>сентябрь</option>
<option value="10" <?if($tek_month=="10"){echo " selected";}?>>октябрь</option>
<option value="11" <?if($tek_month=="11"){echo " selected";}?>>ноябрь</option>
<option value="12" <?if($tek_month=="12"){echo " selected";}?>>декабрь</option>
</select>
<select id=year_num_from name=year_num_from>
<option value="2010" <?if($tek_year=="2010"){echo " selected";}?>>2010</option>
<option value="2011" <?if($tek_year=="2011"){echo " selected";}?>>2011</option>
<option value="2012" <?if($tek_year=="2012"){echo " selected";}?>>2012</option>
<option value="2013" <?if($tek_year=="2013"){echo " selected";}?>>2013</option>
<option value="2014" <?if($tek_year=="2014"){echo " selected";}?>>2014</option>
<option value="2015" <?if($tek_year=="2015"){echo " selected";}?>>2015</option>
<option value="2016" <?if($tek_year=="2016"){echo " selected";}?>>2016</option>
<option value="2017" <?if($tek_year=="2017"){echo " selected";}?>>2017</option>
<option value="2018" <?if($tek_year=="2018"){echo " selected";}?>>2018</option>
<option value="2019" <?if($tek_year=="2019"){echo " selected";}?>>2019</option>
<option value="2020" <?if($tek_year=="2020"){echo " selected";}?>>2020</option>
<option value="2021" <?if($tek_year=="2021"){echo " selected";}?>>2021</option>
<option value="2022" <?if($tek_year=="2022"){echo " selected";}?>>2022</option>
</select>
по
<select id=month_num_to name=month_num_to>
<option value="01" <?if($tek_month=="01"){echo " selected";}?>>январь</option>
<option value="02" <?if($tek_month=="02"){echo " selected";}?>>февраль</option>
<option value="03" <?if($tek_month=="03"){echo " selected";}?>>март</option>
<option value="04" <?if($tek_month=="04"){echo " selected";}?>>апрель</option>
<option value="05" <?if($tek_month=="05"){echo " selected";}?>>май</option>
<option value="06" <?if($tek_month=="06"){echo " selected";}?>>июнь</option>
<option value="07" <?if($tek_month=="07"){echo " selected";}?>>июль</option>
<option value="08" <?if($tek_month=="08"){echo " selected";}?>>август</option>
<option value="09" <?if($tek_month=="09"){echo " selected";}?>>сентябрь</option>
<option value="10" <?if($tek_month=="10"){echo " selected";}?>>октябрь</option>
<option value="11" <?if($tek_month=="11"){echo " selected";}?>>ноябрь</option>
<option value="12" <?if($tek_month=="12"){echo " selected";}?>>декабрь</option>
</select>
<select id=year_num_to name=year_num_to>
<option value="2010" <?if($tek_year=="2010"){echo " selected";}?>>2010</option>
<option value="2011" <?if($tek_year=="2011"){echo " selected";}?>>2011</option>
<option value="2012" <?if($tek_year=="2012"){echo " selected";}?>>2012</option>
<option value="2013" <?if($tek_year=="2013"){echo " selected";}?>>2013</option>
<option value="2014" <?if($tek_year=="2014"){echo " selected";}?>>2014</option>
<option value="2015" <?if($tek_year=="2015"){echo " selected";}?>>2015</option>
<option value="2016" <?if($tek_year=="2016"){echo " selected";}?>>2016</option>
<option value="2017" <?if($tek_year=="2017"){echo " selected";}?>>2017</option>
<option value="2018" <?if($tek_year=="2018"){echo " selected";}?>>2018</option>
<option value="2019" <?if($tek_year=="2019"){echo " selected";}?>>2019</option>
<option value="2020" <?if($tek_year=="2020"){echo " selected";}?>>2020</option>
<option value="2021" <?if($tek_year=="2021"){echo " selected";}?>>2021</option>
<option value="2022" <?if($tek_year=="2022"){echo " selected";}?>>2022</option>
</select> <input type=button value=">>" onclick="show_general_stat()">
<? } ?>

<a href="?deliv_id=2&form_of_payment=&typ_ord=2&courier_task_id=check"><!--<img width="20" height="20" src="../i/logistic.png">--><i class="fa-solid fa-truck icon_btn_r21 icon_btn_blue"></i>к доставке</a>
</td>
<td>
<?
if($_GET["lim"]) {
$lim = $_GET["lim"];
}else{
$lim = "80";
}
?>
<select name="lim" id="lim" onchange="per_page()">
<option value="20" <?if($lim=="20"){echo " selected";}?>>20</option>
<option value="40" <?if($lim=="40"){echo " selected";}?>>40</option>
<option value="60" <?if($lim=="60"){echo " selected";}?>>60</option>
<option value="80" <?if($lim=="80"){echo " selected";}?>>80</option>
<option value="100" <?if($lim=="100"){echo " selected";}?>>100</option>
<option value="200" <?if($lim=="200"){echo " selected";}?>>200</option>
<option value="300" <?if($lim=="300"){echo " selected";}?>>300</option>
<option value="500" <?if($lim=="500"){echo " selected";}?>>300</option>
</select>
	</td>
	</tr></table>

<form name=list_f action="" method="post">
<input name="subm" type="hidden" value="1" />
		<? if($auth) {  ?>
			<table width="1100" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
<tr class="tab_query_tit">
		  <td align="center" class="tab_query_tit"></td>
		  <td align="center" class="tab_query_tit">Название</td>
          <td align="center" class="tab_query_tit">Менеджер</td>
          <td align="center" class="tab_query_tit">
		  <select onchange="document.location.href = this.value">
		  <option value="?typ_ord=&deliv_id=<?=$deliv_id;?>&form_of_payment=<?=$form_of_payment;?>&courier_task_id=<?=$courier_task_id;?>">тип заказа</option>
          <option value="?typ_ord=1&deliv_id=<?=$deliv_id;?>&form_of_payment=<?=$form_of_payment;?>&courier_task_id=<?=$courier_task_id;?>" <?if($typ_ord == "1"){echo "selected";}?>>под заказ</option>
		  <option value="?typ_ord=2&deliv_id=<?=$deliv_id;?>&form_of_payment=<?=$form_of_payment;?>&courier_task_id=<?=$courier_task_id;?>" <?if($typ_ord == "2"){echo "selected";}?>>магазин</option>
		  <option value="?typ_ord=3&deliv_id=<?=$deliv_id;?>&form_of_payment=<?=$form_of_payment;?>&courier_task_id=<?=$courier_task_id;?>" <?if($typ_ord == "3"){echo "selected";}?>>магазин с лого</option>
		  </select></td>
<td align="center" class="tab_query_tit">
<select onchange="document.location.href = this.value">
<option value="?deliv_id=&form_of_payment=<?=$form_of_payment;?>&typ_ord=<?=$typ_ord;?>&courier_task_id=<?=$courier_task_id;?>">доставка</option>
<option value="?deliv_id=1&form_of_payment=<?=$form_of_payment;?>&typ_ord=<?=$typ_ord;?>&courier_task_id=<?=$courier_task_id;?>" <?if ($deliv_id=="1") echo "selected";?>>самовывоз</option>
<option value="?deliv_id=2&form_of_payment=<?=$form_of_payment;?>&typ_ord=<?=$typ_ord;?>&courier_task_id=<?=$courier_task_id;?>" <?if ($deliv_id=="2") echo "selected";?>>по Мск</option>
<option value="?deliv_id=8&form_of_payment=<?=$form_of_payment;?>&typ_ord=<?=$typ_ord;?>&courier_task_id=<?=$courier_task_id;?>" <?if ($deliv_id=="8") echo "selected";?>>до ТК</option>
</select></td>


<td align="center" class="tab_query_tit">
<select onchange="document.location.href = this.value" style="width:70px">
<option value="?form_of_payment=&deliv_id=<?=$deliv_id;?>&typ_ord=<?=$typ_ord;?>&courier_task_id=<?=$courier_task_id;?>">оплата</option>
<option value="?form_of_payment=1&deliv_id=<?=$deliv_id;?>&typ_ord=<?=$typ_ord;?>&courier_task_id=<?=$courier_task_id;?>" <?if($form_of_payment == "1"){echo "selected";}?>>наличные</option>
<option value="?form_of_payment=2&deliv_id=<?=$deliv_id;?>&typ_ord=<?=$typ_ord;?>&courier_task_id=<?=$courier_task_id;?>" <?if($form_of_payment == "2"){echo "selected";}?>>безнал</option>
<option value="?form_of_payment=3&deliv_id=<?=$deliv_id;?>&typ_ord=<?=$typ_ord;?>&courier_task_id=<?=$courier_task_id;?>" <?if($form_of_payment == "3"){echo "selected";}?>>квитанция</option>
<option value="?form_of_payment=4&deliv_id=<?=$deliv_id;?>&typ_ord=<?=$typ_ord;?>&courier_task_id=<?=$courier_task_id;?>" <?if($form_of_payment == "4"){echo "selected";}?>>другое</option>
</select></td>
					<td align="center" class="tab_query_tit">
					<?
					$order = 'asc';
					if( $sort_f == 'date' )
						$order = (($order_f == 'asc') ? 'desc' : 'asc');

 					$alt_tit = 'Дата создания запроса';
					$link = '<a href="query_list.php?sort=date&order='.$order.'&page='.$page.'" onmouseover="Tip(\'%s\')">';
					echo sprintf($link, $alt_tit).'Дата'.'</a>&nbsp;';

					if( $sort_f == 'date' ) {
						$alt_sort = 'Сортировка по дате';
						echo sprintf($link.$img_s,$alt_sort).'</a>';
					}
					?>
					</td>
					<td align="center" class="tab_query_tit" >
					<?
					$order = 'asc';
					if( $sort_f == 'summ' )
						$order = (($order_f == 'asc') ? 'desc' : 'asc');

 					$alt_tit = 'Сумма счета в рублях';
					$link = '<a href="query_list.php?sort=summ&order='.$order.'&page='.$page.'" onmouseover="Tip(\'%s\')">';
					echo sprintf($link, $alt_tit).'Сумма'.'</a>&nbsp;';

					if( $sort_f == 'summ' ) {
						$alt_sort = 'Сортировка по сумме счета';
						echo sprintf($link.$img_s,$alt_sort).'</a>';
					}
					?>
					</td>
					<td align="center" class="tab_query_tit">
					<?
					$order = 'asc';
					if( $sort_f == 'complcost' )
						$order = (($order_f == 'asc') ? 'desc' : 'asc');

 					$alt_tit = 'Сумма оплаты (руб.) / Статус';
					$link = '<a href="query_list.php?sort=complcost&order='.$order.'&page='.$page.'" onmouseover="Tip(\'%s\')">';
					echo sprintf($link, $alt_tit).'Оплачено'.'</a>&nbsp;';

					if( $sort_f == 'complcost' ) {
						$alt_sort = 'Сортировка по сумме оплаты';
						echo sprintf($link.$img_s,$alt_sort).'</a>';
					}
					?>
					</td>
					<td align="center" class="tab_query_tit">
					<?
					$order = 'asc';
					if( $sort_f == 'dolg' )
						$order = (($order_f == 'asc') ? 'desc' : 'asc');

 					$alt_tit = 'Сумма долга (руб.)';
					$link = '<a href="query_list.php?sort=dolg&order='.$order.'&page='.$page.'" onmouseover="Tip(\'%s\')">';
					echo sprintf($link, $alt_tit).'Долг'.'</a>&nbsp;';

					if( $sort_f == 'dolg' ) {
						$alt_sort = 'Сортировка по сумме долга';
						echo sprintf($link.$img_s,$alt_sort).'</a>';
					}
					?>
					</td>
					<td align="center" class="tab_query_tit">
<?
					$order = 'asc';
					if( $sort_f == 'accnt' )
						$order = (($order_f == 'asc') ? 'desc' : 'asc');

 					$alt_tit = 'Номер счета / статус';
					$link = '<a href="query_list.php?sort=accnt&order='.$order.'&page='.$page.'" onmouseover="Tip(\'%s\')">';
					echo sprintf($link, $alt_tit).'Номер счета'.'</a>&nbsp;';

					if( $sort_f == 'accnt' ) {
						$alt_sort = 'Сортировка по номеру счета';
						echo sprintf($link.$img_s,$alt_sort).'</a>';
					}
					?>
					</td>
    <td align="center" class="tab_query_tit"><a href="#" onmouseover="Tip('Процент менеджеру')">Баллы</a></td>
          <td align="center" class="tab_query_tit">Операция</td>
				</tr>
		<?
			// чтение количества запросов из базы для разбиения их по страницам
			if($tpacc) {
				$query = "SELECT a.uid FROM queries as a, clients as b WHERE a.client_id=b.uid AND a.ready='0'";
				$res = mysql_query($query);
				$num_noready_rows = mysql_num_rows($res);
				$query = "SELECT a.uid FROM queries as a, clients as b WHERE a.client_id=b.uid";
			}
			else {
				$query = sprintf("SELECT a.uid FROM queries as a, clients as b WHERE a.user_id=%d AND a.client_id=b.uid AND a.ready='0'", $user_id);
				$res = mysql_query($query);
				$num_noready_rows = mysql_num_rows($res);
				$query = sprintf("SELECT a.uid FROM queries as a, clients as b WHERE a.user_id=%d AND a.client_id=b.uid", $user_id);
			}
			$res = mysql_query($query);
			$num_all_rows = mysql_num_rows($res);

			if($num_noready_rows > $rows_onpage)
				$num_pages = ceil(($num_all_rows - $num_noready_rows)/$rows_onpage + 1);
			else
				$num_pages = ceil($num_all_rows/$rows_onpage);

			if($page > $num_pages)
				$page = $num_pages;

			if($num_noready_rows > $rows_onpage) {
				if($page == 1) {
					$limit_start = 0;
					$limit_num = ($all_pages) ? 10000 : $num_noready_rows;
				} else {
					$limit_start = ($all_pages) ? 0 : (($page-2)*$rows_onpage) + $num_noready_rows;
					$limit_num = ($all_pages) ? 10000 : $rows_onpage;
				}
			}
			else {
				$limit_start = ($all_pages) ? 0 : ($page-1)*$rows_onpage;
				$limit_num = ($all_pages) ? 10000 : $rows_onpage;
			}
			$order_f = ($order_f == 'asc') ? 'ASC' : 'DESC';
			$order_d = ($order_f == 'ASC') ? 'DESC' : 'ASC';

			$query_sort = 'a.ready,a.type,';

			if($sort_f == 'type')
				$query_sort = 'a.ready,a.type '.$order_f.',a.date_query';
			elseif($sort_f == 'date')
				$query_sort .= 'a.date_query '.$order_d;
			elseif($sort_f == 'summ')
				$query_sort .= 'CAST(a.prdm_sum_acc AS UNSIGNED) '.$order_f;
			elseif($sort_f == 'accnt')
				$query_sort .= 'a.prdm_num_acc '.$order_f;
			elseif($sort_f == 'complcost')
				$query_sort .= 'CAST(a.prdm_opl AS UNSIGNED) '.$order_f;
			elseif($sort_f == 'dolg')
				$query_sort .= 'CAST(a.prdm_dolg AS UNSIGNED) '.$order_f;
			else
				$query_sort .= 'b.short '.$order_f;

			// фильтр по клиенту
			$fl_cl_res = '';
			$filtr = @$_GET['filtr'];
            $type_search = $_GET["type_search"];


          	if($type_search == "by_name") {
				$fl_client = @$_GET['val'];
				$fl_cl_res = " AND b.short LIKE '%".$fl_client."%' OR b.name LIKE '%".$fl_client."%' OR a.uid LIKE '%".$fl_client."%' OR a.corsina_order_num LIKE '%".$fl_client."%' OR b.cont_pers LIKE '%".$fl_client."%' OR b.email LIKE '%".$fl_client."%' OR b.gen_dir LIKE '%".$fl_client."%'  AND b.legal_address LIKE '%".$fl_client."%' OR b.postal_address LIKE '%".$fl_client."%' OR b.deliv_address LIKE '%".$fl_client."%' OR b.inn LIKE '%".$fl_client."%' OR b.cont_tel LIKE '%".$fl_client."%' OR b.firm_tel LIKE '%".$fl_client."%'";
				$limit_start = 0;
				$limit_num = 100;
		  	}

			if($typ_ord or $form_of_payment or $deliv_id){
               if($typ_ord){$typ_ord_vst = " AND typ_ord = '$typ_ord' ";}
               if($form_of_payment){$form_of_payment_vst = " AND form_of_payment = '$form_of_payment' ";}
               if($deliv_id){$deliv_id_vst = " AND deliv_id = '$deliv_id' ";}
               if($courier_task_id == "check"){$courier_task_id_vst = " AND courier_task_id = '0' "; $deliv_id_vst = " AND (deliv_id = '8' OR deliv_id = '2') ";}
 				$fl_cl_res = $typ_ord_vst." ".$form_of_payment_vst." ".$deliv_id_vst." ".$courier_task_id_vst;
			}

           //echo "TEST".$form_of_payment;


			if($_GET["month_num"]) {
				$show_month = date($_GET["year_num"]."-".$_GET["month_num"]);
				$fl_cl_res = " AND date_query LIKE '".$show_month."%'";
				$limit_start = 0;
				$limit_num = 1000;
			}


			if($tpacc) {
				$query = sprintf("SELECT a.*, b.name as client, b.short, b.cont_pers, t.done FROM queries as a inner join clients as b on a.client_id=b.uid LEFT JOIN courier_tasks AS t ON a.courier_task_id = t.id WHERE 1 %s ORDER BY %s LIMIT %d,%d",$fl_cl_res, $query_sort, $limit_start,$limit_num);
   			}
        else {
				$query = sprintf("SELECT a.*, b.name as client, b.short, b.cont_pers, t.done FROM queries as a inner join clients as b ON a.client_id=b.uid LEFT JOIN courier_tasks AS t ON a.courier_task_id = t.id WHERE a.user_id=%d %s ORDER BY %s LIMIT %d,%d", $user_id, $fl_cl_res, $query_sort, $limit_start,$limit_num);
			}

		$res = mysql_query($query);

			$nm = 1;
			while(@$r = mysql_fetch_array($res)) {



$r['short'] = str_replace("Общество с ограниченной ответственностью", "", $r['short']);
$r['short'] = str_replace("ООО", "", $r['short']);
				//Предмет счета первые 50 символов

//	чтение подрядчиков для запроса на счет -------------------------------------------
	$query = "SELECT * FROM obj_accounts WHERE query_id=".$r['uid']." ORDER BY nn";
	$res_podr = mysql_query($query);

	$predm = '';



	while($r_podr = mysql_fetch_array($res_podr))
		$predm.='<strong>'.$r_podr['name'].'</strong> / '.$r_podr['num'].' шт. / '.$r_podr['price'].' руб.<br>';

	$predm = htmlspecialchars($predm);

	$predm = (trim($predm)) ? '<div class=stat_podr_alt>'.$predm.'</div>' : '';
// -------------------------------------------------------------------------------------
				// Статус оплаты, сумма оплаты

			  if( ($r['type'] == 0)||($r['type'] == 2) ) {
				   $alt_compl_cost = '';
				   	if($compl_cost && ($compl_cost != '0')) {

						//	$alt_compl_cost = form_num(trim($r['prdm_opl']));
				  	}
				  	else


				  	//$alt_compl_cost .= '';

				 	$fl_compl_cost = true;
			   	}
			   	else {
						$fl_compl_cost = false;
					 $alt_compl_cost = '';
              	}

				// дата, время запроса
				$tmp = explode(' ', $r['date_query']);
				$tmp2 = explode('-',$tmp[0]);
				$date_str = $tmp2[2].' '.$month[intval($tmp2[1])-1];
				$date_str_y = $tmp2[0];
				$tmp2 = explode(':', $tmp[1]);
				$time_str = $tmp2[0].':'.$tmp2[1];

				$alert_r = '';

				// если проект закрыт и сумма менеджеру не оплачена, выделить фиолетовым
				if( ($r['prj_ready'] == '1')) {
					$rest = ( ($r['prdm_sum_acc'] - $r['podr_sebist']) * $r['percent'] ) / 100;
					if(($r['percent']==0)||(($r['komis_opl'] - $rest) < 0))
						$alert_r = ' class="alert_row2" ';
				}

				// Статус, время выпонения
				if($r['ready']) {
					$tmp = explode(' ', $r['date_ready']);
					$tmp2 = explode('-',$tmp[0]);
					$date_str2 = $tmp2[2].' '.$month[intval($tmp2[1])-1];
					$date_str2_y = $tmp2[0].'г.';
					$tmp2 = explode(':', $tmp[1]);
					$time_str2 = $tmp2[0].':'.$tmp2[1];
					if( $r['prdm_num_acc'] != 'none' ) {
						//$alt_stat = 'Tip(\''.htmlspecialchars($date_str2.' '.$date_str2_y.' '.$time_str2, ENT_QUOTES ).'\', TITLE, \'Выполнено\')';
						$alt_stat = ' Tip(\'<table height=10 border=0 cellspacing=0 cellpadding=0><tr><td valign=top>';
	 							$alt_stat .= '<input title=\\\'Введите &#8220;нет&#8221; если счет не нужен\\\' id=\\\'acc_inp_'.$r['uid'].'\\\' type=text class=inp_hint_acc  value=\\\'\\\' maxlength=50 />';
								$alt_stat .= '<input class=butt_hint_acc id=\\\'acc_but_'.$r['uid'].'\\\'  type=submit onclick=save_acc_num('.$r['uid'].') value=OK />';
								$alt_stat .= '</td></tr></table>';
								$alt_stat .= '\', CLOSEBTN, 1, STICKY, 1, DURATION, 0, OFFSETX, 0, OFFSETY, -5, FOLLOWMOUSE, 0, CENTERMOUSE, 1, DELAY, 800, TITLE, \'Номер счета\')';
								$alert_r = ' class="alert_row" ';
					}
					else {
						$alt_stat = 'Tip(\'<span class=stat_null><strong>Счет не нужен</strong></span>\')';
					}
				}
				else {
					if( $r['prdm_num_acc'] != 'none' ) {
					if ($tpacc) {
							if( ($r['type'] == 0)||($r['type'] == 2) ) {
								$alt_stat = ' Tip(\'<table height=10 border=0 cellspacing=0 cellpadding=0><tr><td valign=top>';
	 							$alt_stat .= '<input title=\\\'Введите &#8220;нет&#8221; если счет не нужен\\\' id=\\\'acc_inp_'.$r['uid'].'\\\' type=text class=inp_hint_acc  value=\\\'\\\' maxlength=50 />';
								$alt_stat .= '<input class=butt_hint_acc id=\\\'acc_but_'.$r['uid'].'\\\'  type=submit onclick=save_acc_num('.$r['uid'].') value=OK />';
								$alt_stat .= '</td></tr></table>';
								$alt_stat .= '\', CLOSEBTN, 1, STICKY, 1, DURATION, 0, OFFSETX, 0, OFFSETY, -5, FOLLOWMOUSE, 0, CENTERMOUSE, 1, DELAY, 800, TITLE, \'Номер счета\')';
								$alert_r = ' class="alert_row" ';
							}
							else {
								$alt_stat = 'Tip(\'<span class=stat_no><strong>Не выполнено</strong></span>\')';
								$alert_r = ' class="alert_row" ';
							}
						}
						else {
							if( ($r['type'] == 0)||($r['type'] == 2) ) {
								$alt_stat = '';
								$alert_r = ' class="alert_row" ';
							}
							else {
								$alt_stat = 'Tip(\'<span class=stat_no><strong>Не выполнено</strong></span>\')';
								$alert_r = ' class="alert_row" ';
							}
						}
					}
					else {
						$alt_stat = 'Tip(\'<span class=stat_null><strong>Счет не нужен</strong></span>\')';
						$alert_r = ' class="alert_row" ';
					}
				}





				$acc_num = ($r['prdm_num_acc']) ? $r['prdm_num_acc'] : '---';

				$summ = '<span class="list_sum">---</span>';
				if($r['prdm_sum_acc'])
				   $summ = '<span onmouseover="Tip(\'Сумма счета\')" class="list_sum" id="acc_sum_'.$r['uid'].'">'.form_num($r['prdm_sum_acc']).'</span>';


				$arr_im = array();
				$arr_link = array();
				$arr_hint = array();
				$arr_scr = array();
				$arr_num_apps = array();


//заявки
	$zakazquery = "SELECT uid FROM applications WHERE zakaz_id = ".$r['uid']." ORDER BY uid DESC";
	$reszakazquery = mysql_query($zakazquery);
	$zquery = mysql_fetch_array($reszakazquery);
	$num_of_apps = mysql_num_rows($reszakazquery);




				$arr_im[] = '';
				$arr_hint[] = 	'';
				$arr_scr[]='';
				$arr_num_apps[] = '';
			  if (($r['type'] == 0)||($r['type'] == 2))
					$arr_link[] = 'query_send.php?show='.$r['uid'];
			   	else
				 $arr_link[] = 'query_doc.php?show='.$r['uid'];



   	if(!is_null($zquery['uid']))
				{
if ($num_of_apps == "1"){
				$arr_scr[]='';
				$arr_hint[] = 'Просмотреть заявку на производство';
				$arr_link[] = '/acc/applications/edit.php?zakaz_id='.$r['uid'].'&uid=' . $zquery['uid'];
			    $arr_im[] = '../../i/manufacture.png';
				$arr_num_apps[] = '<span style=\"color: black; position:relative;\">1 <a href=/acc/applications/edit.php?app_type='.$r['typ_ord'].'&zakaz_id='.$r['uid'].' onmouseover="Tip(\'добавить еще одну\')" target=_blank><b>+</b></a></span>';
}
if ($num_of_apps > "1"){
				$arr_scr[]='';
				$arr_hint[] = 'Просмотреть заявки на производство';
				$arr_link[] = '/acc/applications/?zakaz_id='. $r['uid'];
			    $arr_im[] = '../../i/manufacture.png';
				$arr_num_apps[] = $num_of_apps.' <a href=/acc/applications/edit.php?app_type='.$r['typ_ord'].'&zakaz_id='.$r['uid'].' onmouseover="Tip(\'добавить еще одну\')" target=_blank><b>+</b></a>';
}}
				else
				{
				  $arr_scr[]='';
				  $arr_im[] = '../../i/manufacture_pr.png';
				  $arr_hint[] = 'Создать заявку на производство';
				  $arr_link[] = '/acc/applications/edit.php?app_type='.$r['typ_ord'].'&zakaz_id='.$r['uid'];
				  $arr_num_apps[] = '';
			   }

				// логистика

				if($r["courier_task_id"] !== "0")
				{
				  $arr_scr[]='';
				  $arr_hint[] = 'Просмотреть заявку на курьера';
				  $arr_link[] = '/acc/logistic/courier_tasks.php?id=' . $r["courier_task_id"];
				  $arr_im[] = 'logistic.png';
				  $arr_num_apps[] = '';
				}
				else
				{
				  $arr_scr[]='';
				  $arr_im[] = 'logistic_tp.png';
				  $arr_hint[] = 'Создать заявку на доставку';
				  $arr_link[] = '/acc/logistic/courier_tasks.php?query_id=' . $r['uid'];
				  $arr_num_apps[] = '';
			   }
				// кнопка сформировать приложение
				if(($r['type'] == 0) ||($r['type'] == 2)) {
					$arr_im[] = 'doc_24.gif';
					$arr_hint[] = 'Сформировать приложение';
					$arr_scr[] = '';
					$arr_link[] = 'form_app.php?dog='.$r['uid'];
					$arr_num_apps[] = '';
				}
				if( ($r['type'] == 0) ||($r['type'] == 2) ) {

					if( $r['type'] == 2 ) {		// Выполненный запрос на документ
						$type_im = '<img src="../i/type_doc.gif" width="28" height="28" onmouseover="Tip(\''.$predm.'\', TITLE, \'Предмет счета\')">';
					}
					else {		// Запрос на счет
						//$type_im = '<img src="../i/rm_icon.gif" width="28" height="28" onmouseover="Tip(\''.$predm.'\', TITLE, \'Предмет счета\')">';
						$type_im='<i class="fa-light fa-file-circle-question icon_btn_r17 icon_btn_black" onmouseover="Tip(\''.$predm.'\', TITLE, \'Предмет счета\')"></i>'
					}
					if(($r['ready']) && ($r['type'] == 0) ) {
							$arr_im[] = 	'za.gif';
							$arr_link[] = 	'query_doc.php?doc='.$r['uid'];
							$arr_hint[] = 	'Запросить документ';
							$arr_scr[]='';
							$arr_num_apps[] = '';
						}
						if ($tpacc) {
							$arr_im[] = 'del.gif';
							$arr_link[] = '#';
							$arr_hint[] = 	'Удалить';
							$arr_scr[]='onclick="del_query('.$r['uid'].','.intval(@$_GET['page']).')"';
							$arr_num_apps[] = '';
						}
				}
				else {
					$type_im = '<img src="../i/type_doc.gif" width="28" height="28" onmouseover="Tip(\''.htmlspecialchars($predm,ENT_QUOTES).'\', TITLE, \'Предмет счета\')">';
				// кнопка закрытия проекта
				if(($r['prj_ready'] == 0)&&(($r['type'] == 0) ||($r['type'] == 2))) {
					$arr_im[] = 'pr_ok.gif';
					$arr_hint[] = 'Закрыть проект';
					$arr_scr[] = ' onclick="show_prj_ready('.$r['uid'].');return false;" ';
					$arr_link[] = '#';
					$arr_num_apps[] = '';
				}
				else {
					$arr_im[] = 'pr_ok_off.gif';
					$arr_hint[] = 'Проект закрыт';
					$arr_num_apps[] = '';
					if($tpacc)
						$arr_scr[] = ' onclick="cancl_prj_coml('.$r['uid'].');" ';
					else
						$arr_scr[] = ' onclick="return false;" ';
					$arr_link[] = '#';
				}

					if($tpacc) {
						if(!$r['ready']) {
							$arr_im[] = 'ok.gif';
							$arr_link[] = 'query_list.php?set='.$r['uid'].'&page='.@$_GET['page'];
							$arr_hint[] = 	'Выполнить';
							$arr_scr[]='';
							$arr_num_apps[] = '';
						}
						else {
							$arr_im[] = 'del.gif';
							$arr_link[] = '#';
							$arr_hint[] = 	'Удалить';
							$arr_scr[]='onclick="del_query('.$r['uid'].','.intval(@$_GET['page']).')"';
							$arr_num_apps[] = '';					}
					}
					if(!$r['ready']) {
						$arr_im[] = 'del.gif';
						$arr_link[] = '#';
						$arr_hint[] = 	'Удалить';
						$arr_scr[]='onclick="del_query('.$r['uid'].','.intval(@$_GET['page']).')"';
						$arr_num_apps[] = '';
					}
				}

				$butt = '';
				for($i=0;$i<count($arr_im);$i++) {
					if($arr_im[$i]) {
						$butt .= '<a href="'.@$arr_link[$i].'" '.@$arr_scr[$i].' target=_blank>';
						$butt .= '<img width="20" height="20" src="../i/'.@$arr_im[$i].'" onmouseover="Tip(\''.@$arr_hint[$i].'\')" />';
						$butt .= '</a>'.$arr_num_apps[$i];
					}
					else {
						$butt .= '';
					}
				}
			?>
				<tr <?if($r['prdm_num_acc'] == "" or $r['prdm_num_acc'] == "0"){echo "class=alert_row";}?>  onmouseover="this.style.background='#BDCDFF';" onmouseout="this.style.background='';">

					<td align="center" class="tab_td_norm"><?=$type_im?></td>
<td align="center" class="tab_td_norm"><strong>
				 <a href="<?=@$arr_link[0]?>" class="client"><?=htmlspecialchars($r['short'], ENT_QUOTES)?></a></strong></td>
                    <td align="center" class="tab_td_norm" style="font-size:8px;"><?=$users_arr[$r['user_id']];?></td>
          <td class="tab_td_norm">

<b><?=$r['uid'];?> </b>

<? if($r['typ_ord']==2) {?>магазин<?if($r['corsina_order_uid'] !== "0"){?> <a href="http://www.paketoff.ru/admin/shop/orders/view/?id=<?=$r['corsina_order_uid'];?>" target="_blank" style="font-size:9px;font-weight:bold; text-decoration: none;">--> <?=$r['corsina_order_num'];?></a>
<?}
}elseif($r['typ_ord']==1){ ?>заказ<? }
elseif($r['typ_ord']==3){ ?>готовые с лого

 <?if($r['corsina_order_uid'] !== "0"){?> <a href="http://www.paketoff.ru/admin/shop/orders/view/?id=<?=$r['corsina_order_uid'];?>" target="_blank" style="font-size:8px;font-weight:bold;"><?=$r['corsina_order_num'];?></a>

<?}} else {?>
               <img onmouseover="Tip('Тип заказа: Пакетофф');" src="/acc/i/button_green_16x16.png" width="16" height="16" alt="" />
            <?}?>
			</td>
<td width=35  class="tab_td_norm" align=center >
<?if ($r['deliv_id'] == "0" or ""){?>---<?}?>
<?if ($r['deliv_id'] == "1"){?>самовывоз<?}?>
<?if ($r['deliv_id'] == "2"){?>по Мск<?}?>
<?if ($r['deliv_id'] == "8"){?>до ТК<?}?>
</td>
<td width=35  class="tab_td_norm" align=center>
<?if ($r['form_of_payment'] == "0" or ""){?>&nbsp;<?}?>
<?if ($r['form_of_payment'] == "1"){?> <img src="../../i/cash.png" width="16" height="16" alt="" onmouseover="Tip('Наличные');" align="absmiddle" /><?}?>
<?if ($r['form_of_payment'] == "2"){?> <img src="../../i/invoice16.png" width="16" height="16" alt="" onmouseover="Tip('Безнал по счету');" align="absmiddle" /><?}?>
<?if ($r['form_of_payment'] == "3"){?> <img src="../../i/kvit16.png" width="16" height="16" alt=""  onmouseover="Tip('По квитанции');" align="absmiddle"/><?}?>
<?if ($r['form_of_payment'] == "4"){?>другое<?}?>
<input type="hidden" id="opl_method_<?=$r['uid'];?>" value="<?=$r['form_of_payment'];?>" />
</td>
<?$tek_year = date("Y");?>
<td align="center" class="tab_td_norm" onmouseover="Tip('<?=$date_str.' '.$date_str_y.' '.$time_str?>');"><span class="date_row"><?echo $date_str; if($tek_year !== $date_str_y){echo $date_str_y;}?> <br /></span></td>
					<td align="center" class="tab_td_norm_nobr" ><?=$summ?></td>
					<td align="center" class="tab_td_norm_nobr" id="td_opl_<?=$r['uid']?>" <? if($fl_compl_cost) {?>onmouseover="Tip('Редактировать список платежей');" onclick="load_opl(<?=$r['uid']?>); return false;" style="cursor:pointer;font-weight:bold;color:#090"><?}?><?=form_num(trim($r['prdm_opl']));?></td>
					<td align="center" class="tab_td_norm_nobr" onmouseover="Tip('Долг');" style="font_weight:bold" id="td_dolg_<?=$r['uid']?>"><?=$r['prdm_dolg']?></td>
 					<td class="tab_td_norm_nobr">

<input id="acc_inp_<?=$r['uid'];?>" type=text class=inp_hint_acc style="width:45px;"  value="<?if($r['prdm_num_acc'] !== "0"){echo $r['prdm_num_acc'];}?>" maxlength=50 />
<? if($tpacc){  ?>
				  <input id="acc_but_<?=$r['uid'];?>"  type=button onclick=save_acc_num('<?=$r['uid'];?>') value="OK" /> <? } ?>
</td>
					<td align="center" class="tab_td_norm">
						<? if( ($r['type'] == 0) || ($r['type'] == 2)) { ?>
						<? if($tpacc) {?><a onclick="ShowBallMen(<?=$r['uid']?>);return false;" onmouseover="Tip('Редактировать')"  href="#" ><div id="ball_men_<?=$r['uid']?>"><? }?><?=$r['percent']?><? if($tpacc) {?></div></a><? } ?>
					<input type="hidden" value="<?=$r['percent']?>" id="hidd_ball_men_<?=$r['uid']?>">
						<? } else { ?>
						---
						<? } ?>
          </td>
					<td align="left" class="tab_td_norm"><?=$butt?></td>
				</tr>
			<? $nm++; } ?>
				<tr>
				  <td colspan="9" align="left">


					</td>
			  </tr>
				</table>


		<? } ?>	</td>
</tr>
<? if($tpacc) { ?>
<tr>
  <td align="center">
  <div id=monthly_stat class="monthly_stat">
  <?   if($_GET["month_num"]) {
        //общая сумма
        $sum = "SELECT SUM(prdm_sum_acc), COUNT(*), SUM(podr_sebist) FROM queries WHERE date_query LIKE '".$show_month."%'";
   		$res1 = mysql_query($sum);
        $res1 = mysql_fetch_array($res1);
        if ($res1[0] > 0 and $res1[1] > 0){
        $marga = round($res1[0])-round($res1[2]);
        $sr_zakaz = round($res1[0])/round($res1[1]);
        echo "<strong>всего</strong> выставлено <strong>". round($res1[1])."</strong> счетов на сумму: <strong>". round($res1[0])."</strong> р. C/c: <strong>". round($res1[2])."</strong> р. маржа: <strong>".$marga. "</strong> р. средний заказ: <strong>".round($sr_zakaz)."</strong> р.<br>";
        }

        $sum = "SELECT SUM(prdm_sum_acc), COUNT(*), SUM(podr_sebist) FROM queries WHERE typ_ord ='1' AND date_query LIKE '".$show_month."%'";
   		$res1 = mysql_query($sum);
        $res1 = mysql_fetch_array($res1);
        if ($res1[0] > 0 and $res1[1] > 0){
        $marga = round($res1[0])-round($res1[2]);
        $sr_zakaz = round($res1[0])/round($res1[1]);
        echo "<strong>заказы:</strong> выставлено <strong>". round($res1[1])."</strong> счетов на сумму: <strong>". round($res1[0])."</strong> р. C/c: <strong>". round($res1[2])."</strong> р. маржа: <strong>".$marga. "</strong> р. средний заказ: <strong>".round($sr_zakaz)."</strong> р.<br>";
        }

        $sum = "SELECT SUM(prdm_sum_acc), COUNT(*), SUM(podr_sebist) FROM queries WHERE typ_ord ='2' AND date_query LIKE '".$show_month."%'";
   		$res1 = mysql_query($sum);
        $res1 = mysql_fetch_array($res1);
        if ($res1[0] > 0 and $res1[1] > 0){
        $marga = round($res1[0])-round($res1[2]);
        $sr_zakaz = round($res1[0])/round($res1[1]);
        echo "<strong>магазин:</strong> выставлено <strong>". round($res1[1])."</strong> счетов на сумму: <strong>". round($res1[0])."</strong> р. C/c: <strong>". round($res1[2])."</strong> р. маржа: <strong>".$marga. "</strong> р. средний заказ: <strong>".round($sr_zakaz)."</strong> р.";
        }
  }
  ?>
        </div>
        <div id="monthly_stat_fade" style="position: fixed; top: 0px; left: 0px; height: 100%; width: 100%; opacity: 0.4; z-index: 99; background-color: black;display:none;" onclick="show_monthly_stat()"></div>
	</td>
</tr>
<? } ?>
</table>



<br><br>
<? if( ($rows_onpage < $num_all_rows) && ($filtr != 'client')) { ?>
<table border="0" width=900 align="center" cellpadding="5" cellspacing="0" >
	<tr>
		<? if(!$all_pages) { ?>
		<td>Страница</td>	<td align="center">
		<? for($i=1;$i<=20;$i++) { ?>

			<font size=3><?
			$lnk ='<strong>'.$i.'</strong>';
			if($page != $i)
				$lnk = '<a href="?page='.$i.'&sort='.$lk_sort_f.'&order='.$lk_order_f.'&lim='.$lim.'">'.$lnk.'</a>';

			echo $lnk;
			?></font>

		<? } } else {?>
	   </td>	<td>
		<? if($all_pages) {?>
		<a href="?page=1&sort=<?=@$_GET['sort']?>">Постранично</a>
		<? } else {?>
		Постранично
		<? }?>
		</td>
		<? } ?>
		<td width="80" align="right">

		</td>
	</tr>
</table>
<? } ?>
<br><br>

</td>

</tr>
</form>
</table>




<!-- ******************** СЛОЙ РЕДАКТИРОВАНИЯ ОПЛАТ <<<< *****************  //-->
<div  id="div_opl"  onMouseUp="end_drag()" onMouseMove="dragIt(this,event)" style="background-color:#FFFFFF;padding:5px; border:1px #0099CC solid; display:none;"><span onmouseover="Tip('Закрыть')" onclick="hide_pay();return false;" style="font-weight:bold; color:#FF0000;position:absolute;right:15px;top:15px; cursor:pointer; font-size: 15px;" href="#">X</span>
<h2 style="cursor:move;" onMouseDown="start_drag(document.getElementById('div_opl'),event)">Список оплат<span style="padding-right:10px;"></span></h2>
<div id="div_opl_in"></div>
</div>
<!--  >>>>>>******************** СЛОЙ РЕДАКТИРОВАНИЯ ОПЛАТ  *****************  //-->






<!-- ******************** СЛОЙ РЕДАКТИРОВАНИЯ ПРОЦЕНТА МЕНЕДЖЕРУ <<<< *****************  //-->


<div onMouseUp="end_drag()" onMouseMove="dragIt(this,event)" id="div_ball" style="background-color:#FFFFFF; position:absolute; top:400px; left:300px; padding:10px; border:1px #0099CC solid; display:none;">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td style="cursor:move;" onMouseDown="start_drag(document.getElementById('div_ball'),event)" align="center" valign="top"><strong>% менеджеру</strong></td>
		<td align="right" valign="top"><a onmouseover="Tip('Закрыть')" onclick="document.getElementById('div_ball').style.display = 'none';return false;" style="font-weight:bold; color:#FF0000;" href="#">X</a></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<form name="ff_ball" action="" method="get">
	<tr>
		<td align="right">
			<input type="text" id="inp_ball_men" style="text-align:center" value="0" size="15" maxlength="3" />
		</td>
		<td align="left">
			<input class="frm_podr_opl_butt" name="" type="button" value="ОК" onclick="return SaveBallMen();" />
		</td>
	</tr>
	<?  if($tpacc) {?>
	<tr>
	</tr>
	<? } ?>
	</form>
</table>
</div>




<div id="debug"></div>

</body>
</html>
<? ob_end_flush(); ?>
