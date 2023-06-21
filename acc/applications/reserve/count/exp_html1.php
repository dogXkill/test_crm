<?
require_once("../../includes/db.inc.php");
$items_on_page = $_GET["items_on_page"];
if(!$items_on_page){$items_on_page = "50";}
$orderby = $_GET["orderby"];
if (!$orderby){$orderby = "job.uid";}
$order = $_GET["order"];
if (!$order){$order = "DESC";}

if($_GET["year"]){$year = $_GET["year"];}
if($_GET["month"]){$month = $_GET["month"];}

$act = $_GET["act"];
$num_sotr = $_GET['num_sotr'];
$art_num = $_GET['art_num'];
$num_order = $_GET['num_ord'];
$type = $_GET["type"];
$job_id = $_GET["job_id"];

//получаем имена сотрудников в массив
$sotr = "SELECT job_id, surname FROM users WHERE job_id > '0'";
$sotr = mysql_query($sotr);
$sotr_arr = array();
while($rows = mysql_fetch_row($sotr)){
$sotr_arr[$rows[0]] = $rows[1];
}

//собираем типы изделий в массив
$types = "SELECT tid, type FROM types ORDER BY seq DESC";
$types = mysql_query($types);
while ($r = mysql_fetch_array($types)){
    $types_arr[$r[0]] = $r[1];
}

//собираем базовые тарифы и их названи€ в массив
$job_names = "SELECT id, name, price FROM job_names ORDER BY seq DESC";
$job_names = mysql_query($job_names);
while ($r = mysql_fetch_array($job_names)){
    $job_names_arr[$r[0]][job_name] = $r[1];
    $job_names_arr[$r[0]][job_price] = $r[2];
}

if($act == "get_sdelka" || $act == "get_orders"){
//дл€ timetable report
$vstavka = " AND job.cur_time LIKE '".$year."-".$month."-%' AND job.num_sotr='".$num_sotr."'  AND applications.num_ord=job.num_ord ";
$items_on_page = 10000;
}else{
if ($year){$vst_date = " AND job.cur_time LIKE '".$year."-%'";}
if ($month && $year){$vst_date = " AND job.cur_time LIKE '".$year."-".$month."-%'";}
if ($month && !$year){$vst_date = " AND job.cur_time LIKE '".date("Y")."-".$month."-%'";}
if ($job_id){$vst_job = " AND job.job='".$job_id."'";}
if ($type){$vst_type = " AND applications.izd_type='".$type."'";}
if (is_numeric($art_num)){$vst_art_num = " AND applications.art_num='".$art_num."'";}
if (is_numeric($num_order)){$vst_num_ord = " AND job.num_ord='".$num_order."'";}
if (is_numeric($num_sotr)){$vst_num_sotr = " AND job.num_sotr='".$num_sotr."'";}
$vstavka = $vst_date." ".$vst_art_num." ".$vst_num_ord." ".$vst_num_sotr." ".$vst_job." ".$vst_type."  AND applications.num_ord=job.num_ord ";
}



$select_txt = "SELECT job.uid, job.num_sotr, job.num_ord, job.job, job.num_of_work, job.cur_time, job.nadomn, job.order_price, applications.izd_type, applications.uid, applications.art_id FROM job, applications WHERE 1 ".$vstavka." GROUP BY job.uid ORDER BY ".$orderby." ".$order." LIMIT 0, ".$items_on_page;
$select = mysql_query($select_txt);

$select1 = mysql_query($select_txt);

//получаем стоимость каждого вида работ в массив
while($row = mysql_fetch_array($select1)){$nords[] = $row[2];}

if($nords){
$nords = implode(",", $nords);
$nords_vst = "AND num_ord IN($nords)";}
$q = "SELECT num_ord, rate_1, rate_2, rate_3, rate_4, rate_5, rate_6, rate_7, rate_8, rate_9, rate_10, rate_11, rate_12, rate_13, rate_14, rate_15, rate_17, rate_18, rate_19, rate_20, rate_21, rate_22, rate_23, rate_24, title FROM applications WHERE 1 $nords_vst";
$sel = mysql_query($q);
//получаем в массив тарифы только по тем за€вкам, которые присутствуют на странице
$rates = mysql_fetch_array($sel);

print_r($rates);
echo mysql_error();

$total_cost = "0";
$total_orders = "0";
$total_qty = "0";

while($r = mysql_fetch_array($select)){
$qty = $r[4];
$num_ord = $r[2];
$job = $r[3];
$order_price =$r[7];
$price = $rates[$job];
if($price == ""){$price = $job_names_arr[$job][job_price];}
/*
if ($job == "1"){$job_name = "ламинаци€"; if($order_price>0){$price = $order_price;}else{$price = $rates[1];}}
if ($job == "2"){$job_name = "вырубка";  if($order_price>0){$price = $order_price;}else{$price = $rates[2];}}
if ($job == "3"){$job_name = "тиснение"; if($order_price>0){$price = $order_price;}else{$price = $rates[3];}}
if ($job == "4"){$job_name = "сборка"; if($order_price>0){$price = $order_price;}else{$price = $rates[4];}}
if ($job == "5"){$job_name = "труба на линии"; if($order_price>0){$price = $order_price;}else{$price = $rates[5];}}
if ($job == "6"){$job_name = "дно на линии"; if($order_price>0){$price = $order_price;}else{$price = $rates[6];}}
if ($job == "7"){$job_name = "приладка вырубки"; if($order_price>0){$price = $order_price;}else{$price = $rates[7];}}
if ($job == "8"){$job_name = "приладка тиснени€"; if($order_price>0){$price = $order_price;}else{$price = $rates[8];}}
if ($job == "9"){$job_name = "приладка на линии (труба)"; if($order_price>0){$price = $order_price;}else{$price = $rates[9];}}
if ($job == "10"){$job_name = "приладка на линии (дно)"; if($order_price>0){$price = $order_price;}else{$price = $rates[10];}}
if ($job == "11"){$job_name = "упаковка"; if($order_price>0){$price = $order_price;}else{$price = $rates[11];}}
if ($job == "12"){$job_name = "вставка дна и боковин"; if($order_price>0){$price = $order_price;}else{$price = $rates[12];}}
if ($job == "13"){$job_name = "ручна€ подготовка трубы"; if($order_price>0){$price = $order_price;}else{$price = $rates[13];}}
if ($job == "14"){$job_name = "выдача надомнику"; if($rates[14] == ""){$price = "0.50";}else{$price = $rates[14];}}
if ($job == "15"){$job_name = "ручки с клипсами (комплект)"; $price = $rates[15];}
if ($job == "17"){$job_name = "нарезка шнура на станке"; $price = $rates[17];}
if ($job == "18"){$job_name = "нарезка ленты (шнура) вручную"; $price = $rates[18];}
if ($job == "19"){$job_name = "нарезка дна и боковин"; $price = $rates[19];}
if ($job == "20"){$job_name = "прив€зка ленты банком"; $price = $rates[20];}
if ($job == "21"){$job_name = "прив€зка шнура на узелок"; $price = $rates[21];}
if ($job == "22"){$job_name = "вставка ручек с клипсами"; $price = $rates[22];}
if ($job == "23"){$job_name = "сверление"; $price = $rates[23];}
if ($job == "24"){$job_name = "установка люверсов"; $price = $rates[24];}
*/
if($order_price>0){$order_mark = "<span style=\"font-weight:bold;font-size:16px;color:red;\">!</span>";}else{$order_mark = ""; }

if($job !== '14'){
$order_cost = $order_price*$qty;
$total_orders = $total_orders+$order_cost;
}
$order_cost="0";



$price = round(str_replace(',','.',$price), 2);
$cost = $qty*$price;
$total_cost =  $total_cost+$cost;
$total_qty = $total_qty+$qty;

if ($r[6] == "1"){$nadomn = "<img src=\"../../../i/house.png\">";}else{$nadomn = "";}

$title = substr($rates[24],0,55)."...";
$oper_date = new DateTime($r[5]);
$oper_date = $oper_date->Format('<b>d-m</b>-Y G:i');
$text = "<tr id=\"td_".$r[0]."\" onmouseover=\"this.style.background='#BDCDFF';\" onmouseout=\"this.style.background='';\"><td class=tab_td_norm align=center id=num_sotr_".$r[0].">".$r[1]."</td><td class=tab_td_norm width=100>".$sotr_arr[$r[1]]."</td><td class=tab_td_norm width=50>".$types_arr[$r[8]]."</td><td class=tab_td_norm><a href=\"/acc/applications/edit.php?uid=".$r[9]."\" target=_blank >".$title."</a></td><td align=center class=tab_td_norm id=num_ord_".$r[0].">".$r[2]."</td><td class=tab_td_norm id=job_".$r[0]." align=center>".$r[3]."</td><td class=tab_td_norm>".$job_names_arr[$r[3]][job_name]."</td><td class=tab_td_norm id=num_of_work_".$r[0]." align=center>".$r[4]."</td><td class=tab_td_norm align=center>".$order_mark." ".$price."</td><td class=tab_td_norm align=center>".$cost."</td><td class=tab_td_norm align=center>".$nadomn."</td><td class=tab_td_norm>".$oper_date."</td><td class=tab_query_tit><img src=\"../../i/del.gif\" width=\"20\" align=right height=\"20\"  style=\"cursor:pointer\"  onclick=\"del('".$r[0]."')\"></td></tr>";
$ptext = $ptext.$text;

$job_name = "";
$order_price = "";
$price = "";
}

function sort_link($text, $order, $order_by){
global $year, $month, $items_on_page, $num_order, $num_sotr, $orderby;
if ($order == "DESC"){$arr = "&darr;"; $neworder = "ASC";}
if ($order == "ASC"){$arr = "&uarr;"; $neworder = "DESC";}
if ($orderby !== $order_by){$arr = "";}
return "<a href=\"exp_html.php?orderby=".$order_by."&order=".$neworder."&items_on_page=".$items_on_page."&year=".$year."&month=".$month."&num_ord=".$num_order."&job_id=".$job_id."&num_sotr=".$num_sotr."\">".$text." ".$arr."</a>";
}



if($act == "get_sdelka"){
echo round($total_cost);
//echo $select_txt;
}
elseif($act == "get_orders"){
echo round($total_orders);
}
else{
?>

<html>
<script type="text/javascript" src="../../includes/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="../../includes/js/jquery-ui.min.js"></script>

<link href="../../style.css" rel="stylesheet" type="text/css" />
<script>
function del(id){
if(id){
pass=prompt('¬ведите пароль на удаление','');

if (pass == "496"){
var del;
  del = $.ajax({
    type: "GET",
    url: 'del.php',
	data : '&code=fdsfds8fu883832ije99089fs&uid='+id,
    success: function () {
var resp1 = del.responseText
if (resp1 == "ok"){
$('#td_'+id).css("opacity", 0.3);
}else{alert("¬озникла ошибка!")}
}})
}else{alert("ѕароль введен не верно")}

}}

function replace_num(v) {
	var reg_sp = /[^\d]*/g;		// вырезание всех символов кроме цифр
	v = v.replace(reg_sp, '');
	return v;
}

function hide_show_query(){
    $('#mysql_q').toggle();
}
</script>



<body>

<table width="1300" border="0" cellpadding="5" cellspacing="1">
<tr><form action="exp_html.php">
<td>
<select name=year id=year>
<option value="" <?if($year== ""){echo "selected";}?>>год</option>
<option value="2014" <?if($year == "2014"){echo "selected";}?>>2014</option>
<option value="2015" <?if($year == "2015"){echo "selected";}?>>2015</option>
<option value="2016" <?if($year == "2016"){echo "selected";}?>>2016</option>
<option value="2017" <?if($year == "2017"){echo "selected";}?>>2017</option>
<option value="2018" <?if($year == "2018"){echo "selected";}?>>2018</option>
<option value="2019" <?if($year == "2019"){echo "selected";}?>>2019</option>
<option value="2020" <?if($year == "2020"){echo "selected";}?>>2020</option>
</select>

<select name=month id=month>
<option value="" <?if($month == ""){echo "selected";}?>>мес€ц</option>
<option value="01" <?if($month == "01"){echo "selected";}?>>€нварь</option>
<option value="02" <?if($month == "02"){echo "selected";}?>>февраль</option>
<option value="03" <?if($month == "03"){echo "selected";}?>>март</option>
<option value="04" <?if($month == "04"){echo "selected";}?>>апрель</option>
<option value="05" <?if($month == "05"){echo "selected";}?>>май</option>
<option value="06" <?if($month == "06"){echo "selected";}?>>июнь</option>
<option value="07" <?if($month == "07"){echo "selected";}?>>июль</option>
<option value="08" <?if($month == "08"){echo "selected";}?>>август</option>
<option value="09" <?if($month == "09"){echo "selected";}?>>сент€брь</option>
<option value="10" <?if($month == "10"){echo "selected";}?>>окт€брь</option>
<option value="11" <?if($month == "11"){echo "selected";}?>>но€брь</option>
<option value="12" <?if($month == "12"){echo "selected";}?>>декабрь</option>
</select>
</td>
<td>артикул:
<input type="text" size=4 maxlength=5 name=art_num onkeydown="replace_num(this.value)" value="<?=$_GET["art_num"];?>">
</td>
<td>за€вка:
<input type="text" size=4 maxlength=5 name=num_ord onkeydown="replace_num(this.value)" value="<?=$_GET["num_ord"];?>">
</td>
<td>
<select name="num_sotr" id="num_sotr" style="width:130px;">
<option value="">сотрудник</option>
<?$users = "SELECT `uid`, `job_id`, `surname`, `name`, archive FROM `users` WHERE (proizv = '1' OR nadomn = '1') AND archive != '1' ORDER BY surname ASC";
$users = mysql_query($users);
while ($r = mysql_fetch_array($users)){
?>
<option value="<?=$r["1"];?>" <?if($num_sotr == $r["1"]){echo "selected";}?>><?=$r["2"]." ".$r["3"];?></option>
<?}?>
</select>
</td>
<td>
<select name="type" id="type" style="width:130px;">
<option value="">тип издели€</option>
<?$types = "SELECT tid, type FROM types ORDER BY seq DESC";
$types = mysql_query($types);
while ($r = mysql_fetch_array($types)){
?>
<option value="<?=$r["tid"];?>" <?if($type == $r["tid"]){echo "selected";}?>><?=$r["type"];?></option>
<?}?>
</select>
</td>
<td>
<select name="job_id" id="job_id" style="width:130px;">
<option value="">этап</option>
<?
$job_names = "SELECT id, name FROM job_names ORDER BY id ASC";
$job_names = mysql_query($job_names);
while ($r = mysql_fetch_array($job_names)){
?>
<option value="<?=$r["id"];?>" <?if($job_id == $r["id"]){echo "selected";}?>><?=$r["name"];?></option>
<?}?>
</select>
</td>
<td>по: <select name="items_on_page" id="items_on_page">
<option value="20" <?if($items_on_page == "20") {echo "selected";}?>>20</option>
<option value="50" <?if($items_on_page == "50") {echo "selected";}?>>50</option>
<option value="100" <?if($items_on_page == "100") {echo "selected";}?>>100</option>
<option value="300" <?if($items_on_page == "300") {echo "selected";}?>>300</option>
<option value="500" <?if($items_on_page == "500") {echo "selected";}?>>500</option>
<option value="1000" <?if($items_on_page == "1000") {echo "selected";}?>>1000</option>
</select>
<input type=submit value="ok">
</form><?echo mysql_error(); ?>
</td>
<td>
<?if ($_GET["num_sotr"] or $_GET["art_num"] or $_GET["year"] or $_GET["year"] or $_GET["month"] or $_GET["num_ord"] or $_GET["order"] or $_GET["type"]) { ?><a href="exp_html.php">сбросить</a><?}?> <img src="../../../i/planner.png" width="16" height="16" alt="" style="vertical-align:middle; cursor:pointer;" onclick="hide_show_query()"></td>
</tr></table>
<div style="display:none; font_size:8px; font-style:italic; width:1100px" id=mysql_q><br><?echo $q."<br><br>". $select_txt."<br><br>";?></div>

<?
$header = "<table border=1 width=1300><tr><td class=tab_query_tit align=center># сотр</td><td class=tab_query_tit width=150>им€</td><td class=tab_query_tit width=50>тип издели€</td><td class=tab_query_tit align=center>заказ</td><td class=tab_query_tit>номер за€вки</td><td class=tab_query_tit>id этапа</td><td class=tab_query_tit>название этапа</td><td class=tab_query_tit>количество</td><td class=tab_query_tit>цена</td><td class=tab_query_tit align=center>стоимость</td><td class=tab_query_tit align=center>надомн</td><td class=tab_query_tit align=center>".sort_link("врем€", $order, "cur_time")."</td><td class=tab_query_tit> </td></tr>";
$footer = "<tr><td class=tab_query_tit align=center></td><td class=tab_query_tit width=150></td><td class=tab_query_tit align=center></td><td class=tab_query_tit align=center width=300></td><td class=tab_query_tit></td><td class=tab_query_tit></td><td class=tab_query_tit></td><td class=tab_query_tit align=center>".$total_qty."</td><td class=tab_query_tit></td><td class=tab_query_tit align=center>".$total_cost."</td><td class=tab_query_tit></td><td class=tab_query_tit> </td><td class=tab_query_tit> </td></tr>";
$ptext = $header.$ptext.$footer."</table>";
echo $ptext;?>

</body></html> <? } ?>