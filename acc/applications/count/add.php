<?header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
ob_start();
$auth = false;
$rand = microtime(true).rand();

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");?>
<html>

<head>
  <title>Учет работы</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css?cache=<?=$rand?>">
</head>
<body>

<?

if ($user_access['accounting_user'] == 1 && $user_access['jobs_access'] !== '0') {
  ?>
<script type="text/javascript" src="../../includes/js/jquery-1.11.3.min.js?cache=<?=rand(1,10000000);?>"></script>
<script type="text/javascript" src="../../includes/js/jquery-ui.js?cache=<?=rand(1,10000000);?>"></script>
<?

$jobs_allowed = explode('|', $user_access['jobs_access']);

$jobs_types = array();
$jobs_types_q = "SELECT * FROM job_types ORDER BY sort ASC";
$jobs_types_r = mysql_query("$jobs_types_q");
while ($row = mysql_fetch_array($jobs_types_r))
{
  $jobs_type = array();
  $jobs_type['id'] = $row['id'];
  $jobs_type['sort'] = $row['sort'];
  $jobs_type['name'] = $row['name'];
  array_push($jobs_types, $jobs_type);
}

?>
<div class="test_jobs" style="display: none;">
<?

$jobs_count = 0;
foreach ($jobs_types as $key => $value) {
  $type = $value['id'];
  $jobs_names_q = "SELECT * FROM job_names WHERE job_type = '$type' ORDER BY seq ASC";
  $jobs_names_r = mysql_query("$jobs_names_q");
  $text = '';
  while ($row = mysql_fetch_assoc($jobs_names_r))
  {
    if (in_array($row['id'], $jobs_allowed))
    {
      $text .= '<ul><li><span id="job_name_' . $row['id'] . '" class="job_names" onclick="set_job(' . "'" . $row['id'] . "'" . ')">' . $row['name'] . '</span></li></ul>' . PHP_EOL;
      $jobs_count++;
    }
  }
  if (!empty($text)) {
    echo '<span class="job_types">' . $value['name'] . '</span>' . PHP_EOL;
    echo $text;
    unset($text);
  }
}
?>

</div>


<form action="" id=insert_form name=insert_form>
<table border="0" style="width:880px" cellpadding="0" cellspacing="0">
<tr>
<td class="td_title">
  <span class="title">Номер сотрудника:</span><br><br>
  <input type="checkbox" class="checkbox" id="save_num_sotr" /> <label class="label_checkbox" for="save_num_sotr" style="cursor:pointer">сохр.</label>
</td>
<td class="td_input"><input type=text class=fld_num onchange="get_sotr_name('')" maxlength=5 name="num_sotr" id="num_sotr" onkeyup="this.value=replace_num(this.value);">
  <input type=hidden name=num_sotr_err id=num_sotr_err value="0">
</td>
<td class="td_info">
  <span class="result" id=sotr_name_span style="width:340px"></span>
  <span id=sotr_load_span></span>
</td>
</tr>
<?
if ($user_access['is_in_division'] == 1 && $user_access['account_access_dep'] !== '0' && $user_access['account_access_dep'] !== '') {
  ?>
  <tr>
    <td class="td_title"><span class=title>Номер отправки:</span><br><br>
      <input type="checkbox" class="checkbox" id="save_sending" /> <label class="label_checkbox" for="save_sending" style="cursor:pointer">сохр.</label>
    </td>
    <td class="td_input">
      <select class="sending_select" name="num_sending" id="num_sending" disabled onchange="get_sending_data()">
        <?
        $q = "SELECT id FROM shipments WHERE id <> 0 AND archive = 0 AND status <> 4 AND division = " . $user_access['division_id'];
        $r = mysql_query($q);
        while ($row = mysql_fetch_assoc($r)) {
          ?>
            <option value="<?=$row['id']?>"><?=$row['id']?></option>
          <?
        }
        ?>

      </select>
      <!--<input type=text class=fld_num maxlength=5 name="num_sending" id="num_sending" disabled onkeyup="this.value=replace_num(this.value);">-->
      <input type=hidden name=num_sending_err id=num_sending_err value="0">
    </td>
    <td class="td_info">
      <div class="result" id=num_sending_span style="width:340px"></div>
      <div id=num_sending_load_span></div>
    </td>
  </tr>
  <?
}
?>
<?
$account_access_dep = explode('|', $user_access['account_access_dep']);
if ($user_access['is_in_division'] == 0 && (in_array('22', $account_access_dep) || in_array('23', $account_access_dep))) {
  ?>
  <tr>
    <td class="td_title"><span class=title>Номер отправки:</span><br><span>(не обязательное поле)</span><br><br>
      <input type="checkbox" class="checkbox" id="save_sending" /> <label class="label_checkbox" for="save_sending" style="cursor:pointer">сохр.</label>
    </td>
    <td class="td_input">
      <select class="sending_select" name="num_sending" id="num_sending" disabled onchange="get_sending_data()">
        <?
        if (in_array('22', $account_access_dep) && !in_array('23', $account_access_dep)) {
          $q = "SELECT id FROM shipments WHERE id <> 0 AND archive = 0 AND status <> 4 AND division = 1";
        }
        if (in_array('23', $account_access_dep) && !in_array('22', $account_access_dep)) {
          $q = "SELECT id FROM shipments WHERE id <> 0 AND archive = 0 AND status <> 4 AND division = 2";
        }
        if (in_array('23', $account_access_dep) && in_array('22', $account_access_dep)) {
          $q = "SELECT id FROM shipments WHERE id <> 0 AND archive = 0 AND status <> 4 ";
        }

        $r = mysql_query($q);
        ?>
          <option value="0">не выбрано</option>
        <?
        while ($row = mysql_fetch_assoc($r)) {
          ?>
            <option value="<?=$row['id']?>"><?=$row['id']?></option>
          <?
        }
        ?>

      </select>
      <!--<input type=text class=fld_num maxlength=5 name="num_sending" id="num_sending" disabled onkeyup="this.value=replace_num(this.value);">-->
      <input type=hidden name=num_sending_err id=num_sending_err value="0">
    </td>
    <td class="td_info">
      <div class="result" id=num_sending_span style="width:340px"></div>
      <div id=num_sending_load_span></div>
    </td>
  </tr>
  <?
}
?>
<tr>
  <td class="td_title"><span class=title>Номер заявки:</span><br><br>
    <input type="checkbox" class="checkbox" id="save_num_ord" /> <label class="label_checkbox" for="save_num_ord" style="cursor:pointer">сохр.</label>
  </td>
  <td class="td_input"><input type=text class=fld_num onchange="get_app_data()" maxlength=5 name="num_ord" id="num_ord" disabled onkeyup="this.value=replace_num(this.value);">
    <input type=hidden name=num_ord_err id=num_ord_err value="0">
  </td>
  <td class="td_info">
    <div class="result" id=num_ord_span style="width:340px"></div>
    <div id=num_ord_load_span></div>
  </td>
</tr>
<tr>
  <td class="td_title">
    <span class=title>Вид работы:</span><br><br>
    <input type="checkbox" class="checkbox" id="save_job_name" /> <label class="label_checkbox" for="save_job_name" style="cursor:pointer">сохр.</label>
  </td>
  <td class="td_input">
  <?
  if ($user_access['is_in_division'] == 1 && $user_access['account_access_dep'] !== '0' && $user_access['account_access_dep'] !== '') {
    $action = 'brigadir';
  } else {
    $action = 'click';
  }
  ?>
  <div onclick="show_job_names_span('<?=$action?>')" id="choose_job_name">выбрать...</div>
  <span id="jobs_list_err" style="display: none;" class="err"></span>
  <span id="job_names_span" class="job_names_span">
  <?

  $jobs_allowed = explode('|', $user_access['jobs_access']);

  $jobs_types = array();
  $jobs_types_q = "SELECT * FROM job_types ORDER BY sort ASC";
  $jobs_types_r = mysql_query("$jobs_types_q");
  while ($row = mysql_fetch_array($jobs_types_r))
  {
    $jobs_type = array();
    $jobs_type['id'] = $row['id'];
    $jobs_type['sort'] = $row['sort'];
    $jobs_type['name'] = $row['name'];
    array_push($jobs_types, $jobs_type);
  }
  $jobs_count = 0;
  foreach ($jobs_types as $key => $value) {
    $type = $value['id'];
    $jobs_names_q = "SELECT * FROM job_names WHERE job_type = '$type' ORDER BY seq ASC";
    $jobs_names_r = mysql_query("$jobs_names_q");
    $text = '';
    while ($row = mysql_fetch_assoc($jobs_names_r))
    {
      if (in_array($row['id'], $jobs_allowed))
      {
        $text .= '<li id="li_job_' . $row['id'] . '" class="job_li" data-job="' . $row['id'] . '"><span id="job_name_' . $row['id'] . '" class="job_names" onclick="set_job(' . "'" . $row['id'] . "'" . ')">' . $row['name'] . '</span></li>' . PHP_EOL;
        $jobs_count++;
      }
    }
    if (!empty($text)) {
      echo '<span class="job_types">' . $value['name'] . '</span>' . PHP_EOL;
      echo '<ul>' . $text . '</ul>';
      unset($text);
    }
  }
  ?>
  </span>
  <input type="hidden" id="job" name="job" value="" />
   <input type="hidden" id="who_entered" name="who_entered" value="<?=$user_id?>" />
  </td>
  <td class="td_info" valign=top></td>
</tr>
<tr style="display:none;" id="tr_nadomn">
  <td class="td_title">
    <span class=title>Номер надомника:</span>
  </td>
  <td class="td_input">
    <input type=text class=fld_num onchange="get_sotr_name('nadomn')" maxlength=5 name="num_sotrnadomn" id="num_sotrnadomn" onkeyup="this.value=replace_num(this.value);">
    <input type=hidden name=num_sotrnadomn_err id=num_sotrnadomn_err value="0">
  </td>
  <td class="td_info">
    <span class="result" id=sotr_namenadomn_span  style="width:340px"></span>
    <span id=sotr_nadomn_load_span></span>
  </td>
</tr>
<tr>
  <td class="td_title"><span class=title>Количество:</span></td>
  <td class="td_input"><input type=text class=fld_num name="num_of_work" id="num_of_work" maxlength=5 disabled onkeyup="this.value=replace_num(this.value);">
    <input type=hidden name=num_of_work_err id=num_of_work_err value="0">
    <span id=uid_job_span class=result></span>
  </td>
  <td class="td_info" valign=top></td>
</tr>
<tr>
  <td class="td_title"></td>
  <td class="td_input"><label class="job-list" for="order">ордер?</label> <input class="checkbox" type="checkbox" id="order" name="order" onclick="show_span('order')" value="1" /><br><br>
  <span style="display:none" id="order_price_div">тариф: <input type=text class=fld_num name="order_price" id="order_price" maxlength=5 onkeyup="this.value=replace_num_dots(this.value);"></span>
    <label class="job-list" for="zero_tarif">&laquo;нулевой&raquo; тариф</label> <input class="checkbox" type="checkbox" id="zero_tarif" name="zero_tarif" onclick="show_span('zero')"/>
  </td>
  <td class="td_info" valign=top></td>
</tr>

<tr>

<td colspan=3>
<div class=err id=error></div>
<div class="ok" id=resp_ok></div><br><br>
<input type=button value="Ввести запись!" id="save_but" onclick="add_job()"></td>

</tr>
<tr><td class="history_td" colspan=3><a class="history_link" href="index.php" target="_blank">просмотр журнала</a></td></tr>
</table></form>

<script>
$("#num_sotr").focus();
</script>
<div id="fon" onclick="show_job_names_span('click')" style="width: 100%; height: 100%; margin: 0px auto; position: absolute; top: 0px; left: 0px; display:none;"></div>

<?
// Если не выбраны отделы, то добавление работы только на себя
if ($user_access['account_access_dep'] == '0' || empty($user_access['account_access_dep']) ) {
  ?>
  <script type="text/javascript">
    $('#num_sotr').val('<?=$user_access['job_id']?>');
    $('#num_sotr').prop('readonly', true);
    $('#num_sotr').prop('style', 'opacity: 0.5;');
    $('#num_ord').prop('disabled', false);
  </script>
  <?
}
?>


<?
} else {
  if ($user_access['jobs_access'] == '0' || $user_access['jobs_access'] == '') {
    $info = 'У вас нет доступа к начислению ни одной работы. Обратитесь к администратору!';
  }
  if ($user_access['accounting_user'] == '0') {
    $info = 'Нет доступа к разделу. Обратитесь к администратору!';
  }

  ?><div class="no_access_info"><?=$info?></div><?
}
?>

<script>

function replace_num(v) {
	var reg_sp = /[^\d^.]*/g;		// вырезание всех символов кроме цифр и точки
	v = v.replace(reg_sp, '');
	return v;
}

function replace_num_dots(v) {
	var reg_sp = /[^\d^\.]*/g;		// вырезание всех символов кроме цифр
	v = v.replace(reg_sp, '');
	return v;
}

function show_job_names_span(type){
  if (type == 'brigadir') {
    var num_ord = $("#num_ord").val();
    var num_sending = $('#num_sending').val();
    var division = '<?=$user_access['division_id']?>';
    $.ajax({
      type: "GET",
      url: "../backend/brigadir_job.php",
      data: {num_ord: num_ord, division: division},
      success: function(data){

        data = JSON.parse(data);
      if (data == 0) {
        $("#jobs_list_err").attr('style', 'display: block;');
        $("#jobs_list_err").html('Сначала укажите корректный номер заявки');

      } else {
        $("#job_names_span").toggle('100');
        $("#fon").toggle();
        $("#jobs_list_err").attr('style', 'display: none;');
          lis = Array.from(document.getElementsByClassName('job_li'));
          lis.forEach((item, i) => {
            if (data.indexOf(item.attributes['data-job'].value) < 0) {
              $("#" + item.id).attr('style', 'display: none;');
            }
          });
      }

      }
    });
  }

job = $("#job").val();
if(type == 'click'){
  $("#job_names_span").toggle('100');
  $("#fon").toggle();
}
if(type == 'over'){if(job == ""){$("#job_names_span").fadeIn('100');}}
}

function get_sending_data() {
  var num_sending = $('#num_sending').val();
  $('#num_ord_load_span').attr('style', 'display:none;');
  $('#num_ord').val('');
  $.ajax({
    type: "GET",
    url: "get_sending_data.php",
    data: {num_sending: num_sending},
    success: function(str){
      if (str != '') {
        $('#num_ord_span').html(str);
      }
    }
  });
}

function get_sotr_name(type){

num_sotr = $("#num_sotr"+type).val();
user_id = "<?=$user_id?>";
$("#sotr_name"+type+"_span").html("");



if(num_sotr > 0 && $.isNumeric(num_sotr)){
$("#sotr"+type+"_load_span").html("<img src=\"../../../i/load.gif\">");
$.ajax({
  type: "GET",
  url: "get_sotr_name.php",
  data: {num_sotr: num_sotr, type: type, user_id: user_id, account_access_dep: '<?=$user_access['account_access_dep']?>'},
  success: function(str){
console.log(str);
var str = str.split(';');

$("#sotr_name"+type+"_span").html(str[1]);
$("#sotr_"+type+"load_span").html("");

<?
if ($user_access['is_in_division'] == 0) {
  ?> if(type == 'nadomn') {
      $("#num_of_work").prop('disabled', false).focus();
    } else {
      $("#num_ord").prop('disabled', false).focus();
    }
    $("#num_sending").prop('disabled', false); <?
} else {
  ?>
  if(type == 'nadomn'){
    $("#num_of_work").prop('disabled', false).focus();
  } else {
    $("#num_sending").prop('disabled', false).focus();
    $("#num_ord").prop('disabled', false);
    var num_sending = $('#num_sending').val();
    $.ajax({
      type: "GET",
      url: "get_sending_data.php",
      data: {num_sending: num_sending},
      success: function(str){
        $('#num_ord_span').html(str);
      }
    });
  }
  <?
}
?>

if(str[0] !== "error"){
$("#num_sotr"+type+"_err").val("0");
}else{$("#num_sotr"+type+"_err").val("1");}
$("#sotr"+type+"_load_span").html("");
}

});

}}




function get_app_data(){

num_ord = $("#num_ord").val();
$("#num_ord_span").html("");
$("#num_ord_load_span").html("<img src=\"../../../i/load.gif\">");
var sending = $('#num_sending').val();
if(num_ord > 0 && $.isNumeric(num_ord)){

$.ajax({
  type: "GET",
  url: "get_app_data.php",
  data: {num_ord: num_ord, <?echo ($user_access['is_in_division'] == 1) ? 'sending: sending' : '' ;?>},
  success: function(str){

var str = str.split(';');

$("#num_ord_span").html(str[1]);
$("#num_ord_load_span").html("");

if(str[0] == "error"){$("#num_ord_err").val("1");}else{$("#num_ord_err").val("0");}

}
});

}}



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
//$("#job_names_span").fadeOut('100');
show_job_names_span('click')

//обработка надомника
if(id == '14')
{
  $("#tr_nadomn").fadeIn('100');
  $("#num_sotrnadomn").focus();
  }
  else
  {
  $("#tr_nadomn").fadeOut('100');
  $("#num_sotrnadomn").val("");
  $("#sotr_namenadomn_span").html("");
  $("#num_of_work").prop('disabled', false).focus();

  }

}



function add_job(){

num_sotr = $("#num_sotr").val();
num_sotr_err = $("#num_sotr_err").val();
num_ord = $("#num_ord").val();
num_ord_err = $("#num_ord_err").val();
job = $("#job").val();
num_of_work = $("#num_of_work").val();
who_entered = $("#who_entered").val();


if(!$.isNumeric(num_sotr)){$("#num_sotr").focus(); return false}
        if(num_sotr_err !== '0'){
            alert("Вы ввели неправильный номер сотрудника!")
            $("#num_sotr").focus();
             return false
            }
if(!$.isNumeric(num_ord)){$("#num_ord").focus(); return false}
        if(num_ord_err !== '0'){
            alert("Вы ввели неправильный номер заказа!")
            $("#num_ord").focus();
             return false
            }
if(!$.isNumeric(job)){alert("Выберите Вид работы, которую добавляете!"); return false}
if(!$.isNumeric(num_of_work) && num_of_work == 0){$("#num_of_work").focus(); return false}





$("#resp_ok").html("<img src=\"../../../i/load.gif\">");
var data = $("#insert_form").serialize();
$.ajax({
  type: "GET",
  url: "../backend/add_job.php",
  data: data,
  success: function(str){
        $("#save_but").prop('disabled', true);
        var str = str.split(';');
        if(str[0] == "error")
        {
           $("#error").show().html(str[1]);
            $("#resp_ok").hide().html("");
        }
        else if($.isNumeric(str[1]))
        {
            $("#error").hide().html("");
            $("#resp_ok").show().html("Спасибо! Данные внесены. Номер записи: " + str[1]);
            clear_form();
        }

 $("#save_but").prop('disabled', false);
}
});




}

function clear_form(){
id = $("#job").val();

if($("#save_job_name").prop('checked') == false){
  $("#job_name_"+id).css("font-weight", "normal");
  $("#job").val("");
  $("#choose_job_name").html("выбрать");
  $("#job_names_span").fadeOut('100');
}

if($("#save_num_sotr").prop('checked') == false){
  $("#num_sotr").val("");
  $("#num_sotr_err").val("0");
  $("#sotr_name_span").html("");
}

if($("#save_sending").prop('checked') == false){
  $("#num_sending").val("");
  $("#num_sending_err").val("0");
  $("#num_sending_span").html("");
}

if($("#save_num_ord").prop('checked') == false){
  $("#num_ord").val("");
  $("#num_ord_err").val("0");
  $("#num_ord_span").html("");
}

$("#num_of_work").val("");
$("#num_of_work_err").val("0");
$("#num_sotrnadomn").val("");
$("#num_sotrnadomn_err").val("0");
$("#sotr_namenadomn_span").html("");
$("#tr_nadomn").fadeOut('100');
$("#order").removeAttr("checked");
$("#order_price").val("");
$("#zero_tarif").removeAttr("checked");
$('#order_price').prop('readonly', false);
$('#zero_tarif').prop('readonly', false);
$('#order').prop('readonly', false);
$('#order_price_div').fadeOut('100');
$("#num_sotr").focus();

}


 function show_span(type){

        if(type == 'order'){

           if($("#order").prop('checked')){
                $('#order_price_div').show();
                $('#order_price').focus();
                $('#order_price').val('');
           }
           else{
           $('#order_price_div').hide();
           $("#zero_tarif").prop('checked', false);
           $('#order_price').prop('readonly', false);
           }

         }

        if(type == 'zero'){

        if($('#zero_tarif').prop('checked')){
                $('#order_price_div').show();
                $('#order_price').prop('readonly', true);
                $('#order').prop('readonly', true);
                $('#order_price').val('0');
                $("#order").prop('checked', true);
        }
        else{
                $('#order_price_div').hide();
                $("#order").prop('checked', false);
                $('#order_price').val('');
                $('#order_price').prop('readonly', false);
                $('#order').prop('readonly', false);
        }

       }
 }
</script>


</body>

</html>
