<?
$auth = false;
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
$jobs_allowed = explode('|', $user_access['jobs_access']);
$allowed_deps = explode('|', $user_access['account_access_dep']);
$allowed_sotr = array();
$str = $_SERVER['QUERY_STRING'];
parse_str($str);

//если нужна информация только по конкретному заказу (из applications) то привязка к датам не нужна, т.к. заказ выполняется в разные месяцы
if(!$num_ord and $act == 'all'){
if(!$month){$month = date('m');}
if(!$year){$year = date('Y');}
}

if(!$items_on_page){$items_on_page = "500";}
if($act == 'all'){$items_on_page = "1000000";}

?>
<html>

<head>
  <title>Просмотр записей</title>
  <style type="text/css">



body{
  font-family: tahoma;
  background-color: #F6F6F6;
}

select,input,textarea {
  font-family: Tahoma, Verdana, Arial;
  font-size: 13px;
}
a {
  color:#000033;
}


#forma .inputs{
    left:10px;
    position: relative;
    line-height: 37px;
}
#forma input[type="text"] {
  width: 300px;
  font-size: 18px;
  padding: 6px 0 4px 10px;
  border: 1px solid #cecece;
  background: white;
  border-radius: 8px;
  padding:3px;
}
#forma input:disabled {
  width: 300px;
  font-size: 18px;
  padding: 6px 0 4px 10px;
  border: 1px solid #cecece;
  background: #D1D1D1;
  border-radius: 8px;
  padding:3px;
}
#forma select {
  width: 400px;
  padding: 2px 0px 3px 0px;
  border: 1px solid #cecece;
  background: white;
  border-radius: 8px;
  font-size: 18px;
}



.apps_tbl  {
    border: #C0C0C0 solid 1px;
    border-collapse: collapse;
}

.apps_tbl tr {
    height: 35px;
}

.apps_tbl tr:hover {
    background-color: white;
}

.apps_tbl td {

 padding: 3px;
}
.apps_tbl th {
 text-align: center;
 padding: 6px;
 font-weight: bold;
}

.date{
    font-size: x-small;
}

.who{
    font-size: x-small;
    text-align:center;
}

#forma input[type="button"]{
   cursor:pointer;
}


  </style>
</head>
<script type="text/javascript" src="../../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../../includes/js/jquery-ui.js"></script>

<script>

user_type = '<?=$user_type;?>';
//console.log(user_type)


function hide_show_query(){
$("#sql").toggle('100');
}

function replace_num(v) {
  var reg_sp = /[^\d^.]*/g;		// вырезание всех символов кроме цифр и точки
  v = v.replace(reg_sp, '');
  return v;
}

function del(job_uid){
var pass;

if(job_uid){


var del;
  del = $.ajax({
    type: "GET",
    url: '../backend/del.php',
    data : '&job_uid='+job_uid+'&pass='+pass,
    success: function () {
var resp1 = del.responseText;
resp1 = resp1.split(',');



jQuery.each(resp1, function(){
job_uid = this;
if(job_uid > 0){
$('#tr_'+job_uid).css("opacity", 0.2);
}
});



}


});

}}

function jump(jumpfrom, maxsize, jumpto){
maxsize = maxsize-1
if ($('#'+jumpfrom).val().length > maxsize){$('#'+jumpto).select();
$('#'+jumpto).focus();
}}




      function change_date_form(uid){

        old_date = $("#entry_date_span_"+uid).html();

        old_date = old_date.split(" ");

        old_date = old_date[0].split(".");

        old_date_formatted = old_date[2]+"-"+old_date[1]+"-"+old_date[0];

         console.log(old_date+" ==> "+old_date_formatted)

        // old_date_formatted = dateFormat(old_date, "mm/dd/yy");


         $("#entry_date_span_"+uid).hide().after("<input type=\"date\" style=\"width:120px;height:20px;font-size:10px;\" onchange=\"change_entry(\'"+uid+"\', \'change_date\')\" value="+old_date_formatted+" id=\"entry_date_form_"+uid+"\"/>");

       }

       function change_qty_form(uid){

        old_qty = $("#entry_qty_span_"+uid).html();


         console.log(old_qty)

        // old_date_formatted = dateFormat(old_date, "mm/dd/yy");


         $("#entry_qty_span_"+uid).hide().after("<input type=\"text\" style=\"width:70px;height:30px;font-size:12px;\" onchange=\"change_entry(\'"+uid+"\', \'change_qty\')\" value="+old_qty+" id=\"entry_qty_form_"+uid+"\"/>");


       }


      function change_entry(uid, act){
        var changed_date;
        var changed_qty;

        if(act == "change_date"){
        changed_date = $("#entry_date_form_"+uid).val();
        }
        if(act == "change_qty"){
        changed_qty = $("#entry_qty_form_"+uid).val();
        }


            //console.log(changed_date)


                if(uid){


                        var change_entry;
                        change_entry = $.ajax({
                        type: "GET",
                        url: '../backend/change_entry.php',
                        data : '&job_uid='+uid+'&act='+act+'&changed_date='+changed_date+'&changed_qty='+changed_qty,
                        success: function () {
                        var resp = change_entry.responseText;
                        resp = resp.split(',');

                            if(resp == "no_pass"){alert("Введен неправильный пароль!")}
                            if(resp == "OK"){
                                    if(act == "change_date"){
                                    new_date_formatted = changed_date.split("-");
                                    new_date_formatted = new_date_formatted[2]+"."+new_date_formatted[1]+"."+new_date_formatted[0]+" 01:01:01";

                                   $("#entry_date_span_"+uid).html(new_date_formatted).show();
                                   $("#entry_date_form_"+uid).detach();
                                   }
                                    if(act == "change_qty"){
                                   $("#entry_qty_span_"+uid).html(changed_qty).show();
                                   $("#entry_qty_form_"+uid).detach();
                                    }

                            }
                                else{
                                alert(resp)
                                }
                             }

                        });

            }

}




</script>


<body>
<form id=forma>

<table width="1400" border="0" cellpadding="3" cellspacing="1" class=tbl>
<tr>
<td style="vertical-align: bottom;position:relative">
<form onsubmit="get_entries()">

<span onclick="toggle_span('app_type_select_span')" style="text-decoration:underline;cursor:pointer">тип заявки</span>

<span id="app_type_select_span" style="display:none;position:absolute;top:55px;left:0px;">
<select id=app_type name=app_type style="width:250px;" size="4" multiple>
<option value="">все</option>
<option value="1">заказная продукция</option>
<option value="2">серийная продукция</option>
<option value="4">готовые с лого</option>

</select>
</span>


</td>
<td style="vertical-align: bottom;">





с
<input type="date" style="width:88px;height:30px;font-size:12px;" value="" id="from" name="from">

по
<input type="date" style="width:88px;height:30px;font-size:12px;" value="" id="to" name="to">

<input type="hidden" name=year id=year value="<?=$year;?>" />
<input type="hidden" name=month id=month value="<?=$month;?>" />


</td>
<td style="width:70px" style="vertical-align: bottom;">арт:<br>
<input type="text" size=4 maxlength=5 name=art_id id=art_id style="width:70px" onkeydown="replace_num(this.value)" value="<?=$art_id;?>">
</td>
<td>
 кол-во:<br>
 <input type="text" size=4 maxlength=5 name=num_of_work id=num_of_work style="width:70px" onkeydown="replace_num(this.value)" value="<?=$num_of_work;?>">
</td>
<td style="width:90px" style="vertical-align: bottom;">заявка:<br>
<input type="text" size=4 maxlength=5 name=num_ord id=num_ord style="width:90px" onkeydown="replace_num(this.value)" value="<?=$num_ord;?>">
</td>
<td>ш:<br><input type="text" size=3 maxlength=3 name=izd_w id=izd_w style="width:35px" onkeydown="replace_num(this.value)" onkeyup="jump('izd_w','2','izd_v');this.value=replace_num(this.value);" value=""></td>
<td>в:<br><input type="text" size=3 maxlength=3 name=izd_v id=izd_v style="width:35px" onkeydown="replace_num(this.value)" onkeyup="jump('izd_v','2','izd_b');this.value=replace_num(this.value);" value=""></td>
<td>б:<br><input type="text" size=3 maxlength=3 name=izd_b id=izd_b style="width:35px" onkeydown="replace_num(this.value)" value=""></td>
<td style="vertical-align: bottom;position:relative">
    <span onclick="toggle_span('num_sotr_select_span')" style="text-decoration:underline;cursor:pointer">сотрудники</span>

<span id="num_sotr_select_span" style="display:none;position:absolute;top:55px;left:0px;">
<select name=num_sotr id="num_sotr" style="width:250px;" size="10" multiple>
<option value="">нет</option>
<?
//$users = "SELECT job_id, surname, name, user_department FROM `users` WHERE (proizv = '1' OR nadomn = '1' OR user_group = '2') AND archive <> '1' ORDER BY surname ASC";
$users = "SELECT job_id, surname, name, user_department FROM users WHERE archive <> '1' AND user_department IN(2,22,23,20,27,26,24,21,25) ORDER BY surname ASC";
$users = mysql_query($users);
while ($r = mysql_fetch_assoc($users)){
  if ($user_access['account_access_dep'] !== '0') {
    if (in_array($r['user_department'], $allowed_deps)) {
      array_push($allowed_sotr, $r['job_id']);
      ?> <option value="<?=$r["job_id"];?>" <?if($num_sotr == $r["job_id"]){echo "selected";}?>><?=$r["surname"]." ".$r["name"];?></option> <?
    }
  } else {
    if ($r['user_department'] == $user_access['user_department']) {
      ?> <option value="<?=$r["job_id"];?>" <?if($num_sotr == $r["job_id"]){echo "selected";}?>><?=$r["surname"]." ".$r["name"];?></option> <?
    }
  }

}
?>
<option value="">##### АРХИВ #####</option>
<?
//$users = "SELECT job_id, surname, name, user_department FROM `users` WHERE (proizv = '1' OR nadomn = '1' OR user_group = '2') AND archive = '1' ORDER BY surname ASC";
$users = "SELECT job_id, surname, name, user_department FROM users WHERE archive = '1' AND user_department IN(2,22,23,20,27,26,24,21,25) ORDER BY surname ASC";
$users = mysql_query($users);
while ($r = mysql_fetch_assoc($users)){
  if (in_array($r['user_department'], $allowed_deps)) {
    array_push($allowed_sotr, $r['job_id']);
    ?>
    <option value="<?=$r["job_id"];?>" <?if($num_sotr == $r["job_id"]){echo "selected";}?>><?=$r["surname"]." ".$r["name"];?></option>
    <?
  }
}
?>
</select></span>
</td>
<td style="vertical-align: bottom;position:relative">

<span onclick="toggle_span('izd_type_select_span')" style="text-decoration:underline;cursor:pointer">тип изделия</span>

<span id="izd_type_select_span" style="display:none;position:absolute;top:55px;">
<select name=izd_type id="izd_type" style="width:250px;" size="10" multiple>
<option value="">нет</option>
<?$types = "SELECT tid, type FROM types ORDER BY seq DESC";
$types = mysql_query($types);
while ($r = mysql_fetch_array($types)){
?>
<option value="<?=$r["tid"];?>" <?if($type == $r["tid"]){echo "selected";}?>><?=$r["type"];?></option>
<?}?>
</select>
</span>
</td>

<td style="vertical-align: bottom;position:relative">
<span onclick="toggle_span('jpb_select_span')" style="text-decoration:underline;cursor:pointer">типы работ</span>
<span id="jpb_select_span" style="display:none;position:absolute;top:55px;">
<select name=job id="job" style="width:250px;" size="10" multiple>
<option value="">нет</option>
<?
$job_names = "SELECT id, name FROM job_names ORDER BY id ASC";
$job_names = mysql_query($job_names);
while ($r = mysql_fetch_array($job_names)){
  if (in_array($r['id'], $jobs_allowed)) {
    ?>
    <option value="<?=$r["id"];?>" <?if($job_id == $r["id"]){echo "selected";}?>><?=$r["name"];?></option>
    <?
  }
}?>
</select>
</span>

</td>
<td style="vertical-align: bottom;position:relative">
<span onclick="toggle_span('dep_select_span')" style="text-decoration:underline;cursor:pointer">отдел</span>
<span id="dep_select_span" style="display:none;position:absolute;top:55px;">
<select name=dep id="dep" style="width:250px;" size="10" multiple>
<option value="">нет</option>
<?
$deps = "SELECT id, name FROM user_departments WHERE id IN(2,22,23,20,27,26,24,21,25) ORDER BY id ASC";
$deps = mysql_query($deps);
while ($r = mysql_fetch_array($deps)){

    ?>
    <option value="<?=$r["id"];?>" <?if($dep_id == $r["id"]){echo "selected";}?>><?=$r["name"];?></option>
    <?

}?>
</select>
</span>

</td>
<td style="vertical-align: bottom;">отправка:<br>
   <input type="text" size=4 maxlength=5 name=otpravka_num id=otpravka_num style="width:90px" onkeydown="replace_num(this.value)" value="<?=$otpravka_num;?>">
</td>
<td>по:<br><select name="items_on_page" id="items_on_page" style="width:70px">
<option value="20" <?if($items_on_page == "20") {echo "selected";}?>>20</option>
<option value="50" <?if($items_on_page == "50") {echo "selected";}?>>50</option>
<option value="100" <?if($items_on_page == "100") {echo "selected";}?>>100</option>
<option value="300" <?if($items_on_page == "300") {echo "selected";}?>>300</option>
<option value="500" <?if($items_on_page == "500" ) {echo "selected";}?>>500</option>
<option value="500" <?if($items_on_page == "1000") {echo "selected";}?>>1000</option>
<option value="1000000" <?if($items_on_page == "1000000") {echo "selected";}?>>все</option>
</select>
 </form>

</td>
<td style="vertical-align: bottom;">


<input type="button" onclick="window.open('add.php','_blank')" value="+">
<input type="submit" onclick="get_entries();return false;" value="показать!">
<input type="button" onclick="get_entries('sbros');return false;" value="сброс">

<span  style="cursor:pointer"> . </span>

</form>
</td>
</tr></table>


<div id="entries" style="width:1300px; display: table-cell;text-align: center; vertical-align: middle;"></div>
<script>

$(document).mouseup(function (e) {
    var container = $("#num_sotr_select_span");

     spans_str = "jpb_select_span,izd_type_select_span,num_sotr_select_span,app_type_select_span"

   var spans = spans_str.split(',');

   spans.forEach(function(span) {

   container = $("#"+span)

    if (container.has(e.target).length === 0){
        container.hide();
    }

  });


});

function toggle_span(name){
   $("#"+name).toggle();
   spans_str = "jpb_select_span,izd_type_select_span,num_sotr_select_span,app_type_select_span,dep_select_span"

   var spans = spans_str.split(',');

   spans.forEach(function(span) {

   if(span !== name){$("#"+span).hide();}

});


}

//serialize работает только после загрузки самой формы
  function get_entries(act){
  if(act == 'sbros'){
    str=''
    //$("#forma")[0].reset();
    $("#from").val("");
    $("#to").val("");
    $("#num_of_work").val("");
    $("#art_id").val("");
    $("#num_ord").val("");
    $("#izd_w").val("");
    $("#izd_v").val("");
    $("#izd_b").val("");
    $("#num_sotr option:selected").removeAttr("selected");
    $("#izd_type option:selected").removeAttr("selected");
    $("#job option:selected").removeAttr("selected");
    $("#app_type option:selected").removeAttr("selected");


    $("#num_sotr :first").prop('selected', true);
    $("#izd_type :first").prop('selected', true);
    $("#job :first").prop('selected', true);
    $("#app_type :first").prop('selected', true);



    get_entries()


    
  }else{
var str = $("#forma").serialize();
 console.log(str)
}
q_url = 'job_entries.php';
$("#entries").html("<img src=\"../../../i/load2.gif\" style=\"align:middle;padding-top:15px;\">");
$.post("../backend/job_entries.php?"+str, function( job_entries ) {}).done(function(job_entries) {$("#entries").html(job_entries);  });
}

get_entries()

console.log('<?=$user_access['account_access_dep']?>');
  </script>

</body>

</html>
