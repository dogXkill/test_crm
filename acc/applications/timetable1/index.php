<?
$auth = false;
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("lib.php");
?>
<html>

<head>
  <title>Расписание</title>
  <link href="style.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="../../includes/js/jquery-1.11.3.min.js"></script>
  <script src="../../includes/js/jquery.cookie.js"></script>
  <script src="../../includes/js/autoblock.js"></script>

<script>
function save_hours(day, uid, obj_id){
hours = $("#"+day+"_"+uid).val();
//alert(hours)
var geturl;
  geturl = $.ajax({
    type: "GET",
    url: 'save_hours.php',
	data : '&code=dsfsdfdffg54trg54yjiuk&year=<?=$year;?>&month=<?=$month;?>&day='+day+'&hours='+hours+'&uid='+uid,
    success: function () {

var resp1 = geturl.responseText

if (resp1 == "ok"){
$('<div id="resp_'+obj_id+'" style="display:none; position: absolute;font-size:18px;background-color: #009900; color:white; font-face:arial; width: 200px; height: 35px; z-index:10000; text-align:middle">'+resp1+'</div>').insertAfter('#'+obj_id);
  $("#resp_"+obj_id).html(resp1);
  $("#resp_"+obj_id).fadeIn(200);
  $("#resp_"+obj_id).fadeOut(500);

}else{mes = "ошибка добавления данных!"}

}
})
}

function check_fld(obj_id, day, uid){

inp_val = $("#"+obj_id).val();
inp_val = inp_val.toUpperCase();
if (inp_val == "П" || inp_val == "Б" || inp_val == "О" || inp_val == "Н")
{

}else{

var reg_sp = /[^\d]*/g;		// вырезание всех символов кроме цифр
inp_val = inp_val.replace(reg_sp, '');
}

$("#"+obj_id).val(inp_val);
save_hours(day, uid, obj_id)
}


</script>
</head>

<body>
<a href="/">на главную</a>
<div id=block_div style="display:<?if($_COOKIE["auth"] == "on"){echo "block";}else{echo "none";}?>">
<h2>

<table style="width:400px;">
<tr>
<td style="width:100px; text-align: center; font-size: 20px; font-weight: bold;"><?=$prev_month_link;?></td>
<td style="width:200px; text-align: center; font-size: 20px; font-weight: bold;"><?echo $months[$month]." ". $year; ?><br>
<a href="index.php?year=<?=$current_year;?>&month=<?=$current_month;?>&type=<?=$type;?>" style="font-size:8px;">перейти в текущий месяц</a></td>
<td style="width:100px; text-align: center; font-size: 20px; font-weight: bold;"><?=$next_month_link;?></td>
</tr>
</table>
<?

//формируем массив с должностями
$doljnosti = mysql_query("SELECT * FROM doljnost");
while($r = mysql_fetch_array($doljnosti)){
$id = $r["id"];
$name = $r["name"];
//$dolj_ar[id]=$id;
$dolj_ar[$id]=$name;
}

//print_r($dolj_ar);



if($uid){$vstavka = "AND uid ='$uid'";}
function dow($day){
global $year, $month;
$date = $year."-".$month."-".$day;
$dow = date("w",strtotime($date));
$days = array("Вс","Пн","Вт","Ср","Чт","Пт","Сб");
return $days[$dow];
}
$days_in_month = date("t", strtotime($year."-".$month));
?></h2>
<?if ($user_type == "sup" || $user_type == "acc"){include("report_form.php");?>
| <a href="report.php?type=administration&year=<?=$year;?>&month=<?=$month;?>&type=<?=$type;?>" target="_blank">ведомость за текущий месяц</a>
<br>
<a href="?type=administration&year=<?=$year;?>&month=<?=$month;?>" class="sublink" <? if($_GET['type'] == "administration") {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>>администрация</a> |
<a href="?type=proizvodstvo&year=<?=$year;?>&month=<?=$month;?>" class="sublink" <? if($_GET['type'] == "proizvodstvo") {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>>производство</a>

<?}
if($uid){?>
| <a href="?year=<?=$year;?>&month=<?=$month;?>&type=<?=$type;?>" class="sublink">показать всех сотрудников</a>
<? } ?>
<br>
<strong>П</strong> - прогул | <strong>Б</strong> - больничный | <strong>О</strong> - оплачиваемый отпуск  | <strong>Н</strong> - неоплачиваемый отпуск
<table cellpadding=3 cellspacing=0>
<colgroup style="width:20px"></colgroup>
<colgroup style="width:100px"></colgroup>

<? for($i=1;$i<=$days_in_month;$i++) { ?>
<colgroup class="<? if(dow($i)=="Сб" or dow($i)=="Вс"){?>slim_vyh<?}else{?>slim<?$working_days_arr .= $i.",";}?>"></colgroup>

<? } ?>

<tbody>
<tr>
<td class="table_title">#</td>
<td class="table_title" style="width:100px">ФИО</td>
<? for($i=1;$i<=$days_in_month;$i++) { ?>
<td class="table_title"><?=$i;?><br><? echo dow($i); ?></td>
<? } ?>
</tr>
<?
$hrs = array();
$hours = mysql_query("SELECT uid, day, hours FROM timetable WHERE year=$year AND month=$month");
while ($row = mysql_fetch_array($hours)) {
$hrs[$row[0]][$row[1]] = $row[2];
}

if($_GET["type"] == "administration"){$vstavka .= " AND administration = '1'";}
else{$vstavka .= " AND proizv = '1'";}

$query = "SELECT uid, job_id, surname, name, father, doljnost, work_time FROM users WHERE archive != '1' AND job_id != '1000' ".$vstavka." ORDER BY doljnost";
$res = mysql_query($query);
while($us = mysql_fetch_array($res)) {?>
<tr class>
<td class=name><?echo $us['job_id'];?></td>
<td><span class=name><?$fio = $us['surname'].' '.$us['name'];
echo $fio;?></span><br><span class=doljnost><?=$dolj_ar[$us['doljnost']];?></span>
<span style="font-size:8px;cursor:pointer;font-weight:bold;" onclick="check_all_working_days('<?=$us['work_time'];?>','<?=$us['job_id'];?>')">>>></span></td>
<?for($i=1;$i<=$days_in_month;$i++) {?>
<td align=center><input type="text" onchange="check_fld('<?=$i;?>_<?=$us['job_id'];?>','<?=$i;?>','<?=$us['job_id'];?>');count_hours_and_days();" class=hour_inp maxlength=2 id="<?=$i;?>_<?=$us['job_id'];?>" value="<?=$hrs[$us['job_id']][$i];?>"/></td>
<?}?>
</tr>
<?}?>
</tbody>
</table>

<span id="total_hours_text"></span>

</div>
<script>
function check_all_working_days(work_time,job_id){
if (confirm("Автоматическое проставление часов не учитывает праздники и просто ставит базовые часы в рабочие дни, за исключением тех дней, где уже проставлены часы. Проставить?")) {

var working_days = '<?=substr_replace($working_days_arr, '', strrpos($working_days_arr, ','));?>';
var working_days_arr = working_days.split(',');

jQuery.each(working_days_arr, function() {
cur_val = $("#"+this+"_"+job_id).val();
if (this !== "" && cur_val == ""){
$("#"+this+"_"+job_id).val(work_time);
check_fld(this+"_"+job_id,this,job_id)
 }
 cur_val = "";
});

}}

 function count_hours_and_days(){
 var working_hours_total = 0;
 var proguls_total = 0;
 var boln_total = 0;
 var otp_total = 0;
 var notp_total = 0;
  var arr = $('input[class^=hour_inp]').map(function(){
        val = $(this).val();
      //  alert(val)
       if($.isNumeric(val)){working_hours_total = working_hours_total*1 + val*1; }
       else if(val == 'П'){proguls_total = proguls_total+1;}
       else if(val == 'Б'){boln_total = boln_total+1;}
       else if(val == 'О'){otp_total = otp_total+1;}
       else if(val == 'Н'){notp_total = notp_total+1;}
    }).get();
//alert(val)
total_hours_text ="Всего отработано часов: " + working_hours_total + "<br>Прогулов: " + proguls_total;
$("#total_hours_text").html(total_hours_text);
 }

 count_hours_and_days()
</script>
<? include("auth_form.php"); ?>

</body>

</html>