<html>
<script type="text/javascript" src="../../includes/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="../../includes/js/jquery-ui.min.js"></script>
<?
$items_on_page = $_GET["items_on_page"];
if(!$items_on_page){$items_on_page = "20";}
 ?>
<link href="../../style.css" rel="stylesheet" type="text/css" />
<script>
function del(id){
if(id){
pass=prompt('¬ведите пароль на удаление','');

if (pass == "4307"){
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

function edit(id){

num_sotr = $('#num_sotr_'+id).html();
num_ord = $('#num_ord_'+id).html();
num_of_work = $('#num_of_work_'+id).html();
job = $('#job_'+id).html();
$('#num_sotr_'+id).html("<input type=text size=4 id=fld_num_sotr_"+id+" value="+num_sotr+">");
$('#num_ord_'+id).html("<input type=text size=4 id=fld_num_ord_"+id+" value="+num_ord+">");
$('#num_of_work_'+id).html("<input type=text size=4 id=fld_num_of_work_"+id+" value="+num_of_work+">");
$('#job_'+id).html("<input type=text size=4 id=fld_job_"+id+" value="+job+">");
$('#edit_but_'+id).html("<img src=\"../../../i/save.png\" width=\"24\" align=right height=\"24\"  style=\"cursor:pointer\"  onclick=\"save('"+id+"')\">");

}

function save(id){

pass=prompt('¬ведите пароль на изменение','');
if (pass == "4307"){

num_sotr = $('#fld_num_sotr_'+id).val();
num_ord = $('#fld_num_ord_'+id).val();
num_of_work = $('#fld_num_of_work_'+id).val();
job = $('#fld_job_'+id).val();


var save;
  save = $.ajax({
    type: "GET",
    url: 'save.php',
	data : '&code=fdsfds8fu883832ije99089fs&uid='+id+'&num_sotr='+num_sotr+'&num_ord='+num_ord+'&num_of_work='+num_of_work+'&job='+job,
    success: function () {
var resp1 = save.responseText
if (resp1 == "ok"){
$('#td_'+id).css("opacity", 0.8);
}else{alert("¬озникла ошибка!"+resp1)}
}})



num_sotr = $('#fld_num_sotr_'+id).val();
num_ord = $('#fld_num_ord_'+id).val();
num_of_work = $('#fld_num_of_work_'+id).val();
job = $('#fld_job_'+id).val();

$('#num_sotr_'+id).html(num_sotr);
$('#num_ord_'+id).html(num_ord);
$('#num_of_work_'+id).html(num_of_work);
$('#job_'+id).html(job);
$('#edit_but_'+id).html("<img src=\"../../../i/edit_bg.png\" width=\"22\" height=\"22\"  style=\"opacity: 0.5\">");

}
else
{alert("ѕароль введен не верно")}
}

</script>
<body>
<? require_once("../../includes/db.inc.php"); ?>
<table width="1000" border="0" cellpadding="5" cellspacing="1">
<tr><form action="exp_html.php">
<td width=300>
<input type=hidden name=items_on_page value="<?=$items_on_page;?>">
√од: <select name=year id=year>
<option value="2014" <?if($_GET["year"] == "2014"){echo "selected";}?>>2014</option>
<option value="2015" <?if($_GET["year"] == "2015"){echo "selected";}?>>2015</option>
<option value="2016" <?if($_GET["year"] == "2016"){echo "selected";}?>>2016</option>
<option value="2017" <?if($_GET["year"] == "2017"){echo "selected";}?>>2017</option>
<option value="2018" <?if($_GET["year"] == "2018"){echo "selected";}?>>2018</option>
<option value="2019" <?if($_GET["year"] == "2019"){echo "selected";}?>>2019</option>
</select>
ћес€ц:
<select name=month id=month>
<option value="01" <?if($_GET["month"] == "01"){echo "selected";}?>>€нварь</option>
<option value="02" <?if($_GET["month"] == "02"){echo "selected";}?>>февраль</option>
<option value="03" <?if($_GET["month"] == "03"){echo "selected";}?>>март</option>
<option value="04" <?if($_GET["month"] == "04"){echo "selected";}?>>апрель</option>
<option value="05" <?if($_GET["month"] == "05"){echo "selected";}?>>май</option>
<option value="06" <?if($_GET["month"] == "06"){echo "selected";}?>>июнь</option>
<option value="07" <?if($_GET["month"] == "07"){echo "selected";}?>>июль</option>
<option value="08" <?if($_GET["month"] == "08"){echo "selected";}?>>август</option>
<option value="09" <?if($_GET["month"] == "09"){echo "selected";}?>>сент€брь</option>
<option value="10" <?if($_GET["month"] == "10"){echo "selected";}?>>окт€брь</option>
<option value="11" <?if($_GET["month"] == "11"){echo "selected";}?>>но€брь</option>
<option value="12" <?if($_GET["month"] == "12"){echo "selected";}?>>декабрь</option>
</select>
<input type=submit value="ok">
</td></form>
<form action="exp_html.php"><input type=hidden name=items_on_page value="<?=$items_on_page;?>">
<td>номер за€вки:
<input type="text" size=4 maxlength=5 name=num_ord onkeydown="replace_num(this.value)" value="<?=$_GET["num_ord"];?>"> <input type=submit value="ok">
<script>
function replace_num(v) {
	var reg_sp = /[^\d]*/g;		// вырезание всех символов кроме цифр
	v = v.replace(reg_sp, '');
	return v;
}
</script>
</td></form>
<td>
<select name="num_sotr" id="num_sotr" onchange="document.location=this.value;return false;">
<option value=exp_html.php>все сотрудники</option>
<?



$users = "SELECT `uid`, `job_id`, `surname`, `name` FROM `users` WHERE job_id > '0' ORDER BY surname ASC";
$users = mysql_query($users);
while ($r = mysql_fetch_array($users)){
?>
<option value="exp_html.php?num_sotr=<?=$r["1"];?>&items_on_page=<?=$items_on_page;?>" <?if($_GET["num_sotr"] == $r["1"]){echo "selected";}?>><?=$r["2"]." ".$r["3"];?></option>

<?}?>
 </select>
<?echo mysql_error(); ?>
</td>


<td>выводить по: <select name="items_on_page" id="items_on_page" onchange="document.location=this.value;return false;">
<option value="exp_html.php?items_on_page=20" <? if($items_on_page == "20") {echo "selected";}?>>20</option>
<option value="exp_html.php?items_on_page=50" <? if($items_on_page == "50") {echo "selected";}?>>50</option>
<option value="exp_html.php?items_on_page=100" <? if($items_on_page == "100") {echo "selected";}?>>100</option>
<option value="exp_html.php?items_on_page=300" <? if($items_on_page == "300") {echo "selected";}?>>300</option>
</select></td>
<td>
<? if ($_GET["num_sotr"] or $_GET["year"] or $_GET["month"] or $_GET["num_ord"] or $_GET["order"]) { ?><a href="exp_html.php">сбросить</a><? } ?></td>
</tr></table>

<?

//получаем имена сотрудников в массив
$sotr = "SELECT job_id, surname FROM users WHERE job_id > '0'";
$sotr = mysql_query($sotr);
$sotr_arr = array();
while($rows = mysql_fetch_row($sotr)){
    $sotr_arr[$rows[0]] = $rows[1];
}

$year = $_GET["year"];
$month = $_GET["month"];
if ($year){
$vstavka = "WHERE cur_time LIKE '".$year."-".$month."-%'";
}

$num_ord = $_GET['num_ord'];
if (is_numeric($num_ord)){
$vstavka = "WHERE num_ord='".$num_ord."'";
}

$num_sotr = $_GET['num_sotr'];
//echo $num_sotr;
if (is_numeric($num_sotr)){
$vstavka = "WHERE num_sotr='".$num_sotr."'";
}


//получаем стоимость каждого вида работ в массив
$select_ord = "SELECT num_ord FROM job ".$vstavka." GROUP BY num_ord";
$select_ord = mysql_query($select_ord);
$rate_arr = array();
//получаем все номера заказов в данной выборке в массив
while($rows = mysql_fetch_row($select_ord)){

$nmord =$rows[0];
$select_job = "SELECT
num_ord,
rate,
rate_lamin,
rate_tigel_pril,
rate_tigel_udar,
rate_tisn_pril,
rate_tisn_udar,
rate_vstavka_dna_bok,
rate_line_truba_pril,
rate_line_truba_prokat,
rate_line_dno_pril,
rate_line_dno_prokat,
rate_upak,
rate_podgotovka_truby,
rate_drugoe,
title
FROM applications WHERE num_ord = '$nmord'";
$select_job = mysql_query($select_job);
$select_job = mysql_fetch_array($select_job);
//создаем массив, в котором по номеру заказа храним данные о стоимости работы
$rate_arr[$select_job[0]] = array($select_job[1],$select_job[2],$select_job[3],$select_job[4],$select_job[5],$select_job[6],$select_job[7],$select_job[8],$select_job[9],$select_job[10],$select_job[11],$select_job[12],$select_job[13],$select_job[14],$select_job[15]);
}
//print_r($rate_arr);
//echo mysql_error();



$tek_page = $_GET["tek_page"];

if(!$tek_page){
$tek_page = "0";
}
$start = $tek_page*$items_on_page;
$items_on_page;

$order = $_GET["order"];

if (!$order){$order = "DESC";}

$select = "SELECT uid, num_sotr, num_ord, job, num_of_work, cur_time, nadomn FROM job ".$vstavka." ORDER BY uid ".$order." LIMIT ".$start.", ".$items_on_page."";
$select = mysql_query($select);

while($r = mysql_fetch_array($select)){

$num_ord = $r[2];
$job = $r[3];
if ($job == "1"){$job_name = "ламинаци€"; $price = $rate_arr[$r[2]][1];}
if ($job == "2"){$job_name = "вырубка";  $price = $rate_arr[$r[2]][3];}
if ($job == "3"){$job_name = "тиснение"; $price = $rate_arr[$r[2]][5];}
if ($job == "4"){$job_name = "сборка"; $price = $rate_arr[$r[2]][0];}
if ($job == "5"){$job_name = "труба на линии"; $price = $rate_arr[$r[2]][8];}
if ($job == "6"){$job_name = "дно на линии"; $price = $rate_arr[$r[2]][10];}
if ($job == "7"){$job_name = "приладка вырубки"; $price = $rate_arr[$r[2]][2];}
if ($job == "8"){$job_name = "приладка тиснени€"; $price = $rate_arr[$r[2]][4];}
if ($job == "9"){$job_name = "приладка на линии (труба)"; $price = $rate_arr[$r[2]][7];}
if ($job == "10"){$job_name = "приладка на линии (дно)"; $price = $rate_arr[$r[2]][9];}
if ($job == "11"){$job_name = "упаковка"; $price = $rate_arr[$r[2]][11];}
if ($job == "12"){$job_name = "вставка дна и боковин"; $price = $rate_arr[$r[2]][6];}
if ($job == "13"){$job_name = "ручна€ подготовка трубы"; $price = $rate_arr[$r[2]][12];}
if ($job == "14"){$job_name = "выдача надомнику"; $price = "0.50";}
if ($job == "15"){$job_name = "ручки с клипсами (комплект)"; $price = "0.17";}
if ($job == "16"){$job_name = "другое"; $price = $rate_arr[$r[2]][13];}

$price = str_replace(',','.',$price);
$cost = $r[4]*$price;
$price = str_replace('.',',',$price);
if ($r[6] == "1"){$nadomn = "+";}else{$nadomn = "";}

$title = substr($rate_arr[$r[2]][14],0,35)."...";

$text = "<tr id=\"td_".$r[0]."\"  onmouseover=\"this.style.background='#BDCDFF';\" onmouseout=\"this.style.background='';\"><td class=tab_td_norm align=center>".$r[0]."</td><td class=tab_td_norm align=center id=num_sotr_".$r[0].">".$r[1]."</td><td class=tab_td_norm >".$sotr_arr[$r[1]]."</td><td class=tab_td_norm><a href=\"http://192.168.1.100/acc/applications/list.php?act=by_uid&str=".$r[2]."\" target=_blank >".$title."</a></td><td class=tab_td_norm id=num_ord_".$r[0].">".$r[2]."</td><td class=tab_td_norm id=job_".$r[0].">".$r[3]."</td><td class=tab_td_norm>".$job_name."</td><td class=tab_td_norm id=num_of_work_".$r[0].">".$r[4]."</td><td class=tab_td_norm>".$price."</td><td class=tab_td_norm align=center>".$cost."</td><td class=tab_td_norm align=center>".$nadomn."</td><td class=tab_td_norm>".$r[5]."</td><td class=tab_td_norm id=edit_but_".$r[0]." class=tab_query_tit><img src=\"../../../i/edit_bg.png\" width=\"22\" height=\"22\"  onclick=\"edit('".$r[0]."')\" style=\"cursor:pointer\"></td><td class=tab_query_tit><img src=\"../../i/del.gif\" width=\"20\" align=right height=\"20\"  style=\"cursor:pointer\"  onclick=\"del('".$r[0]."')\"></td></tr>";
$ptext = $ptext.$text;

$job_name = "";
}

if ($order == "DESC"){
$arrow = "<a href=\"exp_html.php?order=ASC&items_on_page=".$items_on_page."\">id &uarr;</a>";
}else{
$arrow = "<a href=\"exp_html.php?order=DESC&items_on_page=".$items_on_page."\">id &darr;</a>";
}

$header = "<table border=1 width=1000><tr><td class=tab_query_tit align=center>".$arrow."</td><td class=tab_query_tit align=center>id сотр</td><td class=tab_query_tit width=150>им€</td><td class=tab_query_tit align=center width=300>заказ</td><td class=tab_query_tit>номер за€вки</td><td class=tab_query_tit>id этапа</td><td class=tab_query_tit>название этапа</td><td class=tab_query_tit>количество</td><td class=tab_query_tit>цена</td><td class=tab_query_tit align=center>стоимость</td><td class=tab_query_tit align=center>надомн</td><td class=tab_query_tit align=center>врем€</td><td class=tab_query_tit> </td><td class=tab_query_tit> </td></tr>";
$ptext = $header.$ptext."</table>";


echo $ptext;
?>
<table width="1000" border="0" cellpadding="10" cellspacing="1"><tr><td width=400 align=right>
<?
if($tek_page > '0'){
?>

<a href="exp_html.php?tek_page=<?=$tek_page-1?>&num_sotr=<?=$_GET["num_sotr"];?>&order=<?=$_GET["order"];?>&num_ord=<?=$_GET["num_ord"];?>&year=<?=$_GET["year"];?>&month=<?=$_GET["month"];?>&items_on_page=<?=$items_on_page;?>">&larr; предыдущий</a>
<? }

?></td>
<td width=400><a href="exp_html.php?tek_page=<?=$tek_page+1?>&num_sotr=<?=$_GET["num_sotr"];?>&order=<?=$_GET["order"];?>&num_ord=<?=$_GET["num_ord"];?>&year=<?=$_GET["year"];?>&month=<?=$_GET["month"];?>&items_on_page=<?=$items_on_page;?>">следующа€ &rarr;</a></td>
</tr></table>
</body></html>