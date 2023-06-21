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

body{
  font-family: tahoma;
}

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

.job_names{
    padding:3px;
    cursor:pointer;
}

.job_names:hover{
    background-color: #C0C0C0;
}

.job_types{
    padding:3px;
    cursor:pointer;
    font-weight:bold;
}
.job_names_span{
    display:none;
    width:450px;
    border:solid 1px #000000;
    position: fixed;
    top: 10px;
    left:500px;
    z-index:100;
    background-color: white;
    padding: 10px;
}
#choose_job_name{
    background-color: #C0C0C0;
    padding:3px; 
    cursor:pointer;
}

  -->
  </style>
</head>
<?if($auth){?>
<script type="text/javascript" src="../../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../../includes/js/jquery-ui.js"></script>


<script>

function show_job_names_span(){
$("#job_names_span").fadeIn('300');
}

function set_job(id){
$("#job").val(id);
job_text  = $("#job_name_"+id).html();
$("#choose_job_name").html(job_text);
$('.job_names').each(
  function(){
    $(this).css('font-weight','normal');
  }
)

$("#job_name_"+id).css("font-weight", "bold");
$("#job_names_span").fadeOut('300');

}

</script>


<body>

<form action="" id=insert_form name=insert_form>
<table width="1000" border="0" cellpadding="10" cellspacing="1">
<tr>
<td style="width:350px">
<span class=title>Номер сотрудника:</span>
</td>
<td style="width:400px"><input type=text class=fld_num onchange="get_sotr_name()" maxlength=4 name="num_sotr" id="num_sotr" onkeyup="this.value=replace_num(this.value);">
<input type=hidden name=sotr_err id=sotr_err value="0"></td>
<td style="width:250px" id=sotr_name_span class=result></td>
</tr>
<tr>
<td height=200><span class=title>Номер заявки:</span></td>
<td><input type=text class=fld_num onchange="get_uid_name()" maxlength=4 name="num_ord" id="num_ord" disabled onkeyup="this.value=replace_num(this.value);">
<input type=hidden name=uid_err id=uid_err value="0"></td>
<td style="width:300px" id=uid_name_span class=result></td>
</tr>
<tr>
<td>
<span class=title>Вид работы:</span>
</td>
<td>

<span onmouseover="show_job_names_span()" id="choose_job_name">выбрать...</span>

<span id="job_names_span" class="job_names_span">
<?
$job_types = mysql_query("SELECT * FROM job_types ORDER BY seq ASC");
while($jt = mysql_fetch_array($job_types)){
$job_type_id=$jt["id"];
$job_type=$jt["name"];
?>
<span class="job_types"><?=$job_type;?></span>
<ul>
<?
$job_names = mysql_query("SELECT * FROM job_names WHERE job_type = '$job_type_id' ORDER BY seq ASC");
while($jn = mysql_fetch_array($job_names)){
$job_id = $jn["id"];
$job_name = $jn["name"];
?>
<li><span onclick="set_job('<?=$job_id;?>')" id="job_name_<?=$job_id;?>" class="job_names"><?=$job_name;?></span></li>
<?}?>
</ul>
<?}?>

</span>


<input type="hidden" id="job" value="" />
</td>
<td rowspan=3 valign=top>



</td>
</tr>
<tr>
<td><span class=title>Количество:</span></td>
<td><input type=text class=fld_num name="num_of_work" id="num_of_work" maxlength=5 disabled onkeyup="this.value=replace_num(this.value);">
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

<td colspan=3><input type=button style="width: 450px; height: 60px; font-size: 45px;" value="Ввести запись!" id="save_but" onclick="add_job()" disabled></td>

</tr>
</table></form>

<span id=ok style="display: none; width: 250px; height:100px; border-color: #CDCDCD; padding: 10px; background-color: green; color:white; ">
<span style="font-size: 17px; align:center; vertical-align: middle; font-weight:bold;">Спасибо! Данные внесены.</span>
</span>

<?}?>

</body>

</html>