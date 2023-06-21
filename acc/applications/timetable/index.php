<?
$auth = false;
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("lib.php");

if ($user_access['accounting_user'] == 0 || $user_access['table_access'] == 0) {
  header('Location: /');
}


if ($_SERVER['REQUEST_URI'] == "/acc/applications/timetable/") {header('Location: /acc/applications/timetable/index.php');}
$countY = date('Y');
$countM = date('m');

$rand = microtime(true).rand();
?>
<html>

<head>
  <title>Табель</title>
  <link href="style.css?cache=<?=$rand?>" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="../../includes/js/jquery-1.11.3.min.js"></script>
  <script src="../../includes/js/jquery.cookie.js"></script>
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
<!--<div id=block_div style="display:<?if($_COOKIE["auth"] == "on"){echo "block";}else{echo "none";}?>">-->
<div id="block_div" style="display:block;">
<h2>

<table style="width:400px;">
<tr>
<td style="width:100px; text-align: center; font-size: 20px; font-weight: bold;"><?=$prev_month_link;?></td>
<td style="width:200px; text-align: center; font-size: 20px; font-weight: bold;"><?echo $months[$month]." ". $year; ?><br>
<a href="index.php?year=<?=$current_year;?>&month=<?=$current_month;?>&type=<?=$type;?><?if(isset($_GET['department'])){echo '&department='. $_GET['department'];} if(isset($_GET['group'])){echo '&group='.$_GET['group'];}?>" style="font-size:8px;">перейти в текущий месяц</a></td>
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
<?if ($user_access['list_access'] == 1){include("report_form.php");?>

| <a href="report.php?type=administration&year=<?=$year;?>&month=<?=$month;?>&type=<?=$type;?>" target="_blank">ведомость за текущий месяц</a>
<br>
<!--<a href="?type=administration&year=<?=$year;?>&month=<?=$month;?>" class="sublink" <? if($_GET['type'] == "administration") {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>>администрация</a> |
<a href="?type=proizvodstvo&year=<?=$year;?>&month=<?=$month;?>" class="sublink" <? if($_GET['type'] == "proizvodstvo") {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>>производство</a>-->

<?}
if($uid){?>
| <a href="?year=<?=$year;?>&month=<?=$month;?>&type=<?=$type;?>" class="sublink">показать всех сотрудников</a>
<? } ?>
<?
$deps = array();
$q = "SELECT * FROM user_departments ORDER BY sort ASC";
$r = mysql_query("$q");
while ($row = mysql_fetch_assoc($r))
{
  $dep = array();
  $dep['dep_id'] = $row['id'];
  $dep['dep_name'] = $row['name'];
  array_push($deps, $dep);
}
$deps_count = count($deps);

$groups = array();
$q = "SELECT * FROM user_groups ORDER BY sort ASC";
$r = mysql_query("$q");
while ($row = mysql_fetch_assoc($r))
{
  $group = array();
  $group['group_id'] = $row['id'];
  $group['group_name'] = $row['name'];
  array_push($groups, $group);
}
?>

<div style="/*display: none;*/">
  <?
  $gets = array();
  if ($check = stristr($_SERVER['REQUEST_URI'], '?', true))
  {
    $get_group = '';
    $get_dep = '';
    if (isset($_GET['year']))
    {
      array_push($gets, 'year=' . $_GET['year']);
    } else {
      array_push($gets, 'year=' . $countY);
    }
    if (isset($_GET['month']))
    {
      array_push($gets, 'month=' . $_GET['month']);
    } else {
      array_push($get, 'month=' . $countM);
    }

    if (isset($_GET['group']) && !isset($_GET['department']) )
    {
      $get_dep = '&group=' . $_GET['group'];
    }
    if (isset($_GET['department']) && !isset($_GET['group']) )
    {
      $get_group = '&department=' . $_GET['department'];
    }

    if (isset($_GET['department']) && isset($_GET['group']) )
    {
      $get_group = '&department=' . $_GET['department'];
      $get_dep = '&group=' . $_GET['group'];
    }
    $gets = '?' . implode('&', $gets);

  } else {
    array_push($gets, 'year=' . date("Y"));
    array_push($gets, 'month=' . date("m"));
    $gets = '?' . implode('&', $gets);
  }



  ?>
  <br>

  <?

  $allowed_deps = explode('|', $user_access['table_access_dep']);
  if (count($allowed_deps) > 0 && $user_access['table_access_dep'] != 0) {
    if (isset($_GET['department']) && $_GET['department'] !== 'all' && count(explode('_', $_GET['department'])) > 0 && count(explode('_', $_GET['department'])) != $deps_count) {
      $text = 'Выбрано ' . count(explode('_', $_GET['department'])) . ' отделов';
    } elseif (!isset($_GET['department']) || count(explode('_', $_GET['department'])) == $deps_count) {
      $text = 'Все отделы';
    } else {
      $text = 'Выберите отдел...';
    }
    ?>
    <button type="button" class="popup_open_btn" id="popup_open" data-open="0" name="button" return false; onclick="depPopup()"><?=$text?></button>
    <div class="dep_select_popup" style="display: none;" id="dep_select_popup" data-open="0" onclick="hidePopup()">
      <div class="dep_popup_info">
        <div class="dep_popup_head">Выберите отделы</div>
        <?
        if (count(explode('_', $_GET['department'])) == $deps_count) {
          $text = 'снять все';
          $btnid = 'unset_all_deps';
        } elseif (!isset($_GET['department'])) {
          $text = 'снять все';
          $btnid = 'unset_all_deps';
        } else {
          $text = 'отметить все';
          $btnid = 'select_all_deps';
        }

        ?>
        <div class="select_all_deps" id="<?=$btnid?>" type="button" onclick="depPopup()"><?=$text?></div>
        <div class="dep_popup_list">
          <?
          $selected_deps = explode('_', $_GET['department']);
          foreach ($deps as $key => $dep) {
            if (isset($_GET['department']) && in_array($dep['dep_id'], $selected_deps) ) {
              $checked = ' checked ';
            } elseif (!isset($_GET['department'])) {
              $checked = ' checked ';
            } else {
              $checked = '';
            }
            ?>
            <div class="dep_popup_tr" data-dep="<?=$dep['dep_id']?>">
              <input id="dep-<?=$dep['dep_id']?>" data-dep="<?=$dep['dep_id']?>" class="dep_input" type="checkbox" name="" value="" <?=$checked?>>
              <span><label style="cursor: pointer;" for="dep-<?=$dep['dep_id']?>"><?=$dep['dep_name']?></label></span>
            </div>
            <?
          }
          ?>
        </div>
        <div class="dep_popup_btn_cont">
          <button id="choose_dep_btn" type="button" name="choose_dep_btn" onclick="depPopup();">Выбрать</button>
        </div>
      </div>
    </div>
  <!--  <select id="dep_select" class="filter-select" onchange="location = this.value;">
      <option value="/acc/applications/timetable/index.php<?echo $gets . '&department=all' . $get_dep;?>">Все отделы</option>
      <?
      foreach ($deps as $key => $dep) {
        $selected = (isset($_GET['department']) && $_GET['department'] == $dep['dep_id']) ? ' selected ' : '';
        if (in_array($dep['dep_id'], $allowed_deps)) {
          ?>
          <option value="/acc/applications/timetable/index.php<?echo $gets . $get_dep . '&department=' . $dep['dep_id'];?>"<?=$selected?>><?=$dep['dep_name']?></option>
          <?
        }
      }
      ?>
    </select>-->
      <?
  }

  ?>

</div>

<script type="text/javascript">

</script>
<br>
<strong>П</strong> - прогул | <strong>Б</strong> - больничный | <strong>О</strong> - оплачиваемый отпуск  | <strong>Н</strong> - неоплачиваемый отпуск
<table cellpadding=3 cellspacing=0>
<colgroup style="width:20px"></colgroup>
<colgroup style="width:100px"></colgroup>

<? for($i=1;$i<=$days_in_month;$i++) { ?>
<colgroup class="<? if(dow($i)=="Сб" or dow($i)=="Вс"){?>slim_vyh<?}else{?>slim<?$working_days_arr .= $i.",";}?>"></colgroup>

<? } ?>


<tr id="title_line">
<td class="table_title">#</td>
<td class="table_title" style="width:100px">ФИО</td>
<? for($i=1;$i<=$days_in_month;$i++) { ?>
<td class="table_title"><?=$i;?><br><? echo dow($i); ?></td>
<? } ?>
</tr>
<tbody>
<?
$hrs = array();
$hours = mysql_query("SELECT uid, day, hours FROM timetable WHERE year=$year AND month=$month");
while ($row = mysql_fetch_array($hours)) {
$hrs[$row[0]][$row[1]] = $row[2];
}


if(isset($_GET['department']) && $_GET['department'] !== 'all' ) {
  $department = explode('_', $_GET['department']);
  $conditions = array();
  foreach ($department as $key => $value) {
    if (in_array($value, explode('|', $user_access['table_access_dep']))) {
      array_push($conditions, 'user_department = '.$value);
    }
  }
  $conditions = implode(' OR ', $conditions);
  $vstavka = " AND ( $conditions  )";
} else {
  $table_access_dep = explode('|', $user_access['table_access_dep']);
  $account_access = array();
  foreach ($table_access_dep as $key => $value) {
    array_push($account_access, 'user_department = ' . $value);
  }
  $account_access = " AND (" . implode(' OR ', $account_access) . ")";
}

?>

<?
$query = "SELECT uid, job_id, surname, name, father, doljnost, work_time FROM users WHERE archive != '1'" . $account_access . " AND job_id != '1000' " .$vstavka. " ORDER BY surname ASC";
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

<script type="text/javascript">
$(function() {
  let titles = document.getElementsByClassName('table_title');
//let titles = $('.table_title');
let header = $('#title_line');

$(window).scroll(function() {
 if($(this).scrollTop() > 1) {
//  header.addClass('header_fixed');
//  console.log(titles);
  for (var title in titles) {
    if (titles.hasOwnProperty(title)) {
    //  titles[title].className= "table_title header_fixed";
    }
  }
 } else {
//  header.removeClass('header_fixed');
  for (var title in titles) {
    if (titles.hasOwnProperty(title)) {
    //  titles[title].className = "table_title";
    }
  }
 }
});
});

var allowedDeps = JSON.parse('<?echo json_encode($allowed_deps);?>');
var deps = $('.dep_popup_tr');
for (var i = 0; i < deps.length; i++) {
  if (deps[i] !== undefined) {
    if (allowedDeps.indexOf(deps[i].attributes['data-dep'].value) < 0 ) {
      deps[i].remove();
    }
  }
}


function depPopup() {
  var btn = event.target;
  switch (btn.id) {
    case 'popup_open':
      var open = $('#popup_open').attr('data-open') == 0 ? 1 : 0;
      $('#popup_open').attr('data-open', open);
      if (open == 1) {
        selected_inputs = 0;
        inputs = $('.dep_input');
        for (i = 0; i <= inputs.length; i++) {
          if (inputs[i] !== undefined) {
            if (inputs[i].checked == true) {selected_inputs += 1};
          }
        }
        $('#popup_open').html('Выберите отдел... ');
        $('#dep_select_popup').attr('style', '');
        $('#dep_select_popup').attr('data-open', 1);
      } else {
        $('#dep_select_popup').attr('style', 'display: none;');
        $('#dep_select_popup').attr('data-open', 0);
        if (selected_inputs != 0 && selected_inputs != <?=$deps_count?>) {
          $('#popup_open').html('Выбрано ' + selected_inputs + ' отделов');
        } else if (selected_inputs == <?=$deps_count?>) {
          $('#popup_open').html('Все отделы ');
        } else {
          $('#popup_open').html('Выберите отдел... ');
        }
      }

      break;
    case 'choose_dep_btn':
      inputs = $('.dep_input');
      selected_inputs = [];
      for (i = 0; i <= inputs.length; i++) {
        if (inputs[i] !== undefined) {
          if (inputs[i].checked == true) {selected_inputs.push(inputs[i].attributes['data-dep'].value)};
        }
      }
      if (selected_inputs.length != 0) {
        selected_inputs.join('_');
        var err = false;
        var dep_path = selected_inputs.join('_');
      } else {
        alert('Выберите хотя бы один отдел');
        var err = true;
      }
      if (err == false) {
        <?
        if (stristr($_SERVER['QUERY_STRING'], 'department') ) {
          $gets = explode('&', $_SERVER['QUERY_STRING']);
          foreach ($gets as $key => $value) {
            if (stristr($value, 'department')) {unset($gets[$key]);}
          }
          $path = 'index.php?' . implode('&', $gets) . '&department=';
        } else {
          $path = 'index.php?' . $_SERVER['QUERY_STRING'] . '&department=' ;
        }
        ?>
        path = '<?=$path?>' + dep_path;
        window.location = path;
      }
      break;

    case 'select_all_deps':
      inputs = $('.dep_input');
      for (i = 0; i <= inputs.length; i++) {
        if (inputs[i] !== undefined) {
          inputs[i].checked = true;
        }
      }
      $('#select_all_deps').html('снять все');
      $('#select_all_deps').attr('id', 'unset_all_deps');
      break;
    case 'unset_all_deps':
      inputs = $('.dep_input');
      for (i = 0; i <= inputs.length; i++) {
        if (inputs[i] !== undefined) {
          inputs[i].checked = false;
        }
      }
      $('#unset_all_deps').html('отметить все');
      $('#unset_all_deps').attr('id', 'select_all_deps');
      break;
    default:

  }
}

function hidePopup() {
  if (event.target.id == 'dep_select_popup') {
    switch (event.target.attributes['data-open'].value) {
      case "1":

        inputs = $('.dep_input');
        selected_inputs = [];
        for (i = 0; i <= inputs.length; i++) {
          if (inputs[i] !== undefined) {
            if (inputs[i].checked == true) {selected_inputs.push(inputs[i].attributes['data-dep'].value)};
          }
        }
        if (selected_inputs.length != 0) {
          selected_inputs.join('_');
          var err = false;
          var dep_path = selected_inputs.join('_');
        } else {
          alert('Выберите хотя бы один отдел');
          var err = true;
        }
        if (err == false) {
          <?
          if (stristr($_SERVER['QUERY_STRING'], 'department') ) {
            $gets = explode('&', $_SERVER['QUERY_STRING']);
            foreach ($gets as $key => $value) {
              if (stristr($value, 'department')) {unset($gets[$key]);}
            }
            $path = 'index.php?' . implode('&', $gets) . '&department=';
          } else {
            $path = 'index.php?' . $_SERVER['QUERY_STRING'] . '&department=' ;
          }
          ?>
          path = '<?=$path?>' + dep_path;
          window.location = path;
          $('#dep_select_popup').attr('data-open', "0");
          $('#dep_select_popup').attr('style', 'display: none;');
          $('#popup_open').attr('data-open', "0");
        }
        break;
      case "0":
        $('#dep_select_popup').attr('data-open', 1);
        $('#dep_select_popup').attr('display', '');
        $('#popup_open').attr('data-open', 1);
        break;
    }
  }
}
</script>
<?// include("auth_form.php"); ?>

</body>

</html>
