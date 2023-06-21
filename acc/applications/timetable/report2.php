<?
if (!isset($_GET['group'])) {
  header('Location: /acc/applications/timetable/report.php?group=1');
}
$auth = false;
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
$str = $_SERVER['QUERY_STRING'];
parse_str($str);
require_once("lib.php");
//if(!$type){$type = "proizvodstvo";}
?>
<html>
<head>
  <title>Отчет</title>
  <link href="style.css" rel="stylesheet" type="text/css" />
  <script src="../../includes/js/autoblock.js"></script>
  <script type="text/javascript" src="../../includes/js/jquery-1.11.3.min.js"></script>
  <script src="../../includes/js/jquery.cookie.js"></script>
  <script type="text/javascript" src="../../includes/js/jscalendar/calendar.js"></script>
  <script type="text/javascript" src="../../includes/js/jscalendar/lang/calendar-ru.js"></script>
  <script type="text/javascript" src="../../includes/js/jscalendar/calendar-setup-art-stat.js"></script>
  <link rel="stylesheet" type="text/css" media="all" href="../../includes/js/jscalendar/calendar-blue.css">
    <style>
   .warning {
    display: inline-block; /* Строчно-блочный элемент */
    position: relative; /* Относительное позиционирование */
   }
   .warning:hover::after {
    content: attr(data-title); /* Выводим текст */
    position: absolute; /* Абсолютное позиционирование */
    left: 20%; top: 30%; /* Положение подсказки */
    z-index: 1; /* Отображаем подсказку поверх других элементов */
    background: rgba(255,255,230,0.9); /* Полупрозрачный цвет фона */
    font-family: Arial, sans-serif; /* Гарнитура шрифта */
    font-size: 11px; /* Размер текста подсказки */
    padding: 5px 10px; /* Поля */
    border: 1px solid #333; /* Параметры рамки */
   }
  </style>
</head>

<body onload="sum()">
  <style media="screen">
  .filter-select {
    padding: 2px 0px 3px 0px;
    border: 1px solid #cecece;
    background: white;
    border-radius: 8px;
    font-size: 18px;
  }
  </style>
<input type="hidden" id="counter"/>
<script type="text/javascript" src="../../includes/js/wz_tooltip.js"></script>
<a href="/">на главную</a>
<div id=block_div style="display:<?if($_COOKIE["auth"] == "on") {echo "block";}else{echo "none";}?>">

<?
if ($user_type == "sup" || $user_type == "acc" || $user_type == "adm"){
$warning_1 = "<span style=\"font-size:14px;color:red;font-weight:bold;cursor:pointer;\" class=\"warning\" data-title=\"Сделки начислено больше чем оклад\">!</span>";
$warning_2 = "<span style=\"font-size:14px;color:red;font-weight:bold;cursor:pointer;\" class=\"warning\" data-title=\"Начислено сделки и прочее либо аномально много либо аномально мало\">!</span>";
$hrs = array();
//получаем данные об отработанных часах, опозданиях, больничных
$hours = mysql_query("SELECT uid, SUM(hours), COUNT(case when hours='Б' or hours='О' then 1 else null end), COUNT(case when hours='П' then 1 else null end) FROM timetable WHERE year='$year' AND month='$month' GROUP BY uid");
while ($row = mysql_fetch_array($hours)) {
$hrs[$row[0]]["hours"] = $row[1];
$hrs[$row[0]]["boln"] = $row[2];
$hrs[$row[0]]["progul"] = $row[3];
//выбираем только тех пользователей, у которых стоит хотя бы одна отметка в табеле за текущий месяц
if($row[1] > "0" OR $row[2]  OR $row[3]){
$job_ids = "'".$row[0]."',".$job_ids;}
}

//перечень айдишек сотрудников через запятую, у которых в табеле есть отметкиб в конце убираем запятую
$job_ids = substr($job_ids,0,-1);

$ymonth = $year."-".$month;

//смотри у кого хотя бы одно начисление есть
$nachisl = mysql_query("SELECT num_sotr, SUM(num_of_work) FROM job WHERE cur_time LIKE '".$ymonth."%' GROUP BY num_sotr");
$nachisl_text = "SELECT num_sotr, SUM(num_of_work) FROM job WHERE cur_time LIKE '".$ymonth."%' GROUP BY num_sotr";
//echo $nachisl_text;
while($row = mysql_fetch_array($nachisl)){
if($row[1]>'0'){$nach_ids = "'".$row[0]."',".$nach_ids;}
 }
//print_r($nach_ids);
$nach_ids = substr($nach_ids,0,-1);
//print_r($nach_ids);
//объединяем строки с айдишками начислений и часов в текущем месяце
//$act_ids = $nach_ids.",".$job_ids;
$act_ids = $job_ids.",".$nach_ids;
//echo "<br>".$act_ids."<br>";
//разбиваем на массив в целях дальнейшего удаления повторов
$act_ids = explode(",", $act_ids);

//удаляем повторы
$act_ids = array_unique($act_ids);

//формируем строку айдишек только активных сотрудников опять
$act_ids = implode(",", $act_ids);
//print_r($act_ids);
//удаляем из строки первый и последний символ, если это запятая
$act_ids = rtrim($act_ids, ",");
$act_ids = ltrim ($act_ids, ",");
//print_r($act_ids);
//получаем данные из уже сохраненной таблицы    report
$qtext = "SELECT * FROM report2 WHERE year='$year' AND month='$month'";
//echo $qtext;
$report = mysql_query($qtext);

while ($row = mysql_fetch_array($report)) {
$uid = $row[uid];
$rpt[$uid]["work_time"] = $row[work_time];
$rpt[$uid]["oklad"] = $row[oklad];
$rpt[$uid]["socoklad"] = $row[socoklad];
$rpt[$uid]["sdelka"] = $row[sdelka];
$rpt[$uid]["procee"] = $row[procee];
$rpt[$uid]["pay1"] = $row[pay1];
$rpt[$uid]["pay1date"] = $row[pay1date];
$rpt[$uid]["pay2"] = $row[pay2];
$rpt[$uid]["pay2date"] = $row[pay2date];
$rpt[$uid]["pay3"] = $row[pay3];
$rpt[$uid]["pay3date"] = $row[pay3date];
$rpt[$uid]["pay4"] = $row[pay4];
$rpt[$uid]["pay4date"] = $row[pay4date];
$rpt[$uid]["pay5"] = $row[pay5];
$rpt[$uid]["pay5date"] = $row[pay5date];
$rpt[$uid]["pay6"] = $row[pay6];
$rpt[$uid]["pay6date"] = $row[pay6date];
$rpt[$uid]["pay7"] = $row[pay7];
$rpt[$uid]["pay7date"] = $row[pay7date];
}
$working_d = mysql_query("SELECT working_days FROM working_days WHERE year = '$year' AND month = '$month'");
$working_d = mysql_fetch_array($working_d);
$working_days= $working_d[0];

// Массив с отделами
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

// Массив с группами
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

<table style="width:1000px;">
<tr>
<td style="width:100px; text-align: center; font-size: 20px; font-weight: bold;"><?=$prev_month_link;?></td>
<td style="width:200px; text-align: center; font-size: 20px; font-weight: bold;"><?echo $months[$month]." ". $year; ?>
<br><a href="report.php?year=<?=$current_year;?>&month=<?=$current_month;?>&type=<?=$type;?>&group=1" style="font-size:8px;">перейти в текущий месяц</a></td>
<td style="width:100px; text-align: center; font-size: 20px; font-weight: bold;"><?=$next_month_link;?></td>
<td style="width:500px;">Введите кол-во рабочих дней: <input type="text" name="working_days" id="working_days" size=3 value="<?if($working_days>"0"){echo $working_days;};?>"/>
<input type="submit" value="OK"  onclick="save_monthly_report('', 'working_days')"/>
Социальные дни: <input type="text" name="working_days_soc" id="working_days_soc" size=3 value="<?=$working_days_social;?>" disabled/></td>
</tr></table>

<br>

<!--<a href="?type=administration&year=<?=$year;?>&month=<?=$month;?>" class="sublink" <? if($type == "administration") {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>>администрация</a> |
<a href="?type=proizvodstvo&year=<?=$year;?>&month=<?=$month;?>" class="sublink" <? if($type == "proizvodstvo") {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>>производство</a> |
<a href="?type=proizvandnadomn&year=<?=$year;?>&month=<?=$month;?>" class="sublink" <? if($type == "proizvandnadomn") {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>>производство и надомники</a> |
<a href="?type=shtatnie&year=<?=$year;?>&month=<?=$month;?>" class="sublink" <? if($type == "shtatnie") {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>>все штатные</a> |
<a href="?type=nadomniki&year=<?=$year;?>&month=<?=$month;?>" class="sublink" <? if($type == "nadomniki") {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>>надомники</a> |
<a href="?type=all&year=<?=$year;?>&month=<?=$month;?>" class="sublink" <? if($type == "all") {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>>все</a>-->

<a href="index.php?year=<?=$year;?>&month=<?=$month;?>&group=1" target="_blank" class="sublink">отметить посещаемость</a>

<?if ($user_type == "sup" || $user_type == "acc"){include("report_form.php");}?>

<br><br>
<div>
  <?
  if ($check = stristr($_SERVER['REQUEST_URI'], '?', true))
  {
    $gets = array();
    $get_group = '';
    $get_dep = '';
      array_push($gets, 'year=' . $year);
      array_push($gets, 'month=' . $month);

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
  }
  ?>
  <br>
<select class="filter-select" onchange="location = this.value;">
  <option value="/acc/applications/timetable/report.php<?echo $gets . '&department=all' . $get_dep;?>">Все отделы</option>
  <?
  foreach ($deps as $key => $dep) {
    $selected = (isset($_GET['department']) && $_GET['department'] == $dep['dep_id']) ? ' selected ' : '';
    ?>
    <option value="/acc/applications/timetable/report.php<?echo $gets . $get_dep . '&department=' . $dep['dep_id'];?>"<?=$selected?>><?=$dep['dep_name']?></option>
    <?
  }
  ?>
</select>

<select class="filter-select" onchange="location = this.value;">
  <option value="/acc/applications/timetable/report.php<?echo $gets . '&group=all' . $get_group;?>">Все группы</option>
  <?$selected = (isset($_GET['group']) && $_GET['group'] == 'shtat') ? ' selected ' : '';?>
  <option value="/acc/applications/timetable/report.php<?echo $gets . '&group=shtat' . $get_group;?>" <?=$selected?>>Штатные</option>
  <?$selected = (isset($_GET['group']) && $_GET['group'] == 'p_n') ? ' selected ' : '';?>
  <option value="/acc/applications/timetable/report.php<?echo $gets . '&group=p_n' . $get_group;?>" <?=$selected?>>Производство + надомники</option>
  <?

  foreach ($groups as $key => $group) {
      $selected = (isset($_GET['group']) && $_GET['group'] == $group['group_id']) ? ' selected ' : '';
    ?>
    <option value="/acc/applications/timetable/report.php<?echo $gets . $get_group . '&group=' . $group['group_id'];?>"<?=$selected?>><?=$group['group_name']?></option>
    <?
  }
  ?>
</select>
</div>
<br><br>

<table cellpadding=3 cellspacing=0 id=table>


    <thead>

<td name="num_col" class="table_title" style="widtd:50px">#</td>
<td class="table_title" style="widtd:150px">ФИО
<span onclick="additional_fld('hide')" id="add_fld_hide" style="cursor:pointer;font-size:23px;color:red; font-weight:bold;"><strong>-</strong></span>
<span onclick="additional_fld('show')" id="add_fld_show" style="cursor:pointer;display:none;font-size:23px;color:green; font-weight:bold;"><strong>+</strong></span></td>
<td class="table_title" style="widtd:80px">База</td>
<td name="socoklad_col" class="table_title" style="widtd:80px">Соцбаза</td>
<td name="work_time_col" class="table_title" style="widtd:70px">Норма часов</td>

<td name="oklad_hour_col" class="table_title" style="widtd:70px">Рабочий час</td>
<td name="socoklad_hour_col" class="table_title" style="widtd:70px">Соцчас</td>
<td name="worked_time_col" class="table_title" style="widtd:70px">Отработано часов</td>
<td name="socnachisl_col" class="table_title" style="widtd:70px">Начислено соцчасов</td>
<td name="progul_col" class="table_title" style="widtd:70px">Прогулы</td>

<td class="table_title" style="widtd:100px">Начислено</td>
<td class="table_title" style="widtd:100px">Сделка <img src="../../i/refresh.png" widtd="16" height="16" alt="" onclick="get_full_sdelka('get_sdelka')" style="cursor:pointer;"><br><input type="checkbox" id="save_sdelka" name="save_sdelka"/> <label for="save_sdelka" style="cursor:pointer">сохр.</label></td>
<td class="table_title" style="widtd:100px">Прочее</td>
<td class="table_title" style="widtd:100px;background-color: #DDFFCC;">Итого</td>
<td name="pay1_col" class="table_title" valign=top>#1</td>
<td name="pay2_col" class="table_title" valign=top>#2</td>
<td name="pay3_col" class="table_title" valign=top>#3</td>
<td name="pay4_col" class="table_title" valign=top>#4</td>
<td name="pay5_col" class="table_title" valign=top>#5</td>
<td name="pay6_col" class="table_title" valign=top>#6</td>
<td name="pay7_col" class="table_title" valign=top>#7</td>
<td name="ostatok_col" class="table_title">Остаток</td>
</thead> <tbody>
<?
// Массив с отделами
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

// Массив с группами
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

if (isset($_GET['department']) && is_numeric($_GET['department'])) {
  $dep = $_GET['department'];
  $n_vstavka .= " AND user_department = $dep ";
}

if (isset($_GET['group']) && is_numeric($_GET['group'])) {
  $group = $_GET['group'];
  $n_vstavka .= " AND user_group = $group ";
}
if (isset($_GET['group']) && $_GET['group'] == 'p_n' ) {
  $n_vstavka .= " AND (user_group = 2 OR user_group = 3) ";
}
if (isset($_GET['group']) && $_GET['group'] == 'shtat' ) {
  $n_vstavka .= " AND (user_group = 2 OR user_group = 1) ";
}

/* ---- Старая логика ---- */
//if (!$type) {$type = 'proizvodstvo';}

if($type == "administration"){
  $vstavka = " administration = '1'";
}

/*if($type == "proizvodstvo" or !$type){
  $vstavka = " proizv = '1'";
}*/
if($type == "proizvodstvo"){
  $vstavka = " proizv = '1' ";
}

/*if($type == "proizvandnadomn" or !$type){
  $vstavka = " (nadomn = '1' OR proizv = '1') ";
}*/

if($type == "proizvandnadomn"){
  $vstavka = " (nadomn = '1' OR proizv = '1') ";
}
if($type == "nadomniki"){
  $vstavka = " nadomn = '1'";
}
if($type == "shtatnie"){
  $vstavka = " (proizv = '1' OR administration = '1') ";
}
if($type == "all"){
  $vstavka = " (nadomn = '1' OR proizv = '1' OR administration = '1') ";
 }

/* ---- Конец старой логики ---- */

if($working_days){
/*if($act_ids!==""){$vstavka_IN = "AND job_id IN($act_ids)";}else{$vstavka_IN = " AND archive != '1' ";}*/
$vstavka_IN = " AND archive != '1' ";
//получаем список сотрудниов с базовыми параметрами
//$query = "SELECT uid, job_id, surname, name, doljnost, oklad, socoklad, work_time FROM users WHERE ".$vstavka." ".$vstavka_IN."  AND job_id != '1000' ORDER BY surname ASC";
$query = "SELECT uid, job_id, surname, name, doljnost, oklad, socoklad, work_time FROM users WHERE job_id != '1000' ".$n_vstavka." ". $vstavka_IN . " ORDER BY surname ASC";
?>
<script type="text/javascript">
  //<?=$query?>
</script>
<?
//$query = "SELECT uid, job_id, surname, name, doljnost, oklad, socoklad, work_time FROM users WHERE job_id != '1000' AND ".$vstavka." ".$vstavka_IN." ORDER BY surname ASC";
//echo $query;
$res = mysql_query($query);
echo mysql_error();
while($us = mysql_fetch_array($res)) {

$job_id=$us['job_id'];

if($rpt[$job_id]["oklad"]){$oklad=$rpt[$job_id]["oklad"];}else{$oklad=$us['oklad'];}
if($rpt[$job_id]["socoklad"]){$socoklad=$rpt[$job_id]["socoklad"];}else{$socoklad=$us['socoklad'];}
if($rpt[$job_id]["work_time"]){$work_time=$rpt[$job_id]["work_time"];}else{$work_time=$us['work_time'];}
$hours = $hrs[$job_id]["hours"];
if($work_time >"0"){
$oklad_hour = round($oklad/$work_time/$working_days);
$socoklad_hour = round($socoklad/$work_time/$working_days_social);}
$progul = $hrs[$job_id]["progul"];
$fio = $us['surname'].' '.$us['name']
?>
<tr id="tr_<?=$job_id;?>">
<td name="num_col" class=name align=center><?=$job_id;?></td>
<td class=name><a href="/acc/users/users.php?edit=<?=$us['uid'];?>&oper=edit" target="_blank"><?=$fio;?></a>
<a href="index.php?year=<?=$year;?>&month=<?=$month;?>&uid=<?=$us['uid'];?>&type=<?=$type;?>" target="_blank"><img src="../../../i/timetable.png" width="16" height="16" alt=""></a>

<?$d = $us['doljnost'];?>
</td>
<td align=center><input type="text" name="oklad" onkeyup="this.value=replace_num(this.value);" autocomplete="off" id="oklad_<?=$job_id;?>" value="<?=$oklad;?>" onchange="save_monthly_report('<?=$job_id;?>', 'oklad_<?=$job_id;?>')"  class="general_inp"/></td>
<td align=center name="socoklad_col"><input type="text" name="socoklad" onkeyup="this.value=replace_num(this.value);" autocomplete="off" id="socoklad_<?=$job_id;?>" value="<?=$socoklad;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'socoklad_<?=$job_id;?>')" class="general_inp"/></td>
<td align=center name="work_time_col"><input type="text" name="work_time" onkeyup="this.value=replace_num(this.value);" autocomplete="off" id="work_time_<?=$job_id;?>" value="<?if(!$work_time){$work_time="9";} echo $work_time;?>" onchange="save_monthly_report('<?=$job_id;?>', 'work_time_<?=$job_id;?>')" class="hour_inp"/></td>
<td align=center name="oklad_hour_col"><input type="text" name="oklad_hour" id="oklad_hour_<?=$job_id;?>" value="<?=$oklad_hour;?>" class="general_inp" disabled/></td>
<td align=center name="socoklad_hour_col"><input type="text" name="socoklad_hour" id="socoklad_hour_<?=$job_id;?>" value="<?=$socoklad_hour;?>" class="general_inp" disabled/></td>
<td align=center name="worked_time_col"><input type="text" name="worked_time" value="<?=$hrs[$job_id]["hours"];?>" id="worked_time_<?=$job_id;?>"  class="general_inp" disabled /></td>
<td align=center name="socnachisl_col"><input type="text" name="socnachisl" value="<?=$hrs[$job_id]["boln"]*$work_time;?>" id="socnachisl_<?=$job_id;?>"  class="general_inp" disabled/></td>
<td align=center name="progul_col"><input type="text" name="progul" value="<?=$hrs[$job_id]["progul"];?>" id="progul_<?=$job_id;?>"  class="general_inp" disabled/></td>

<td name="nachisleno_col"><input type="text" name="nachisleno" value="<?$nachisleno=$hrs[$job_id]["hours"]*$oklad_hour+$hrs[$us['job_id']]["boln"]*$work_time*$socoklad_hour-$progul_multa*$hrs[$job_id]["progul"]; echo $nachisleno;?>" id="nachisleno_<?=$job_id;?>"  class="general_inp" disabled/>
<?if($nachisleno > $oklad*1.05){echo $warning_1;}?>
<?//=calc_nachisleno($hours, $oklad_hour, $boln, $work_time, $socoklad_hour, $progul_multa, $progul);?>
</td>

<td align=left><nobr><input type="text" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="sdelka" value="<?$sdelka=$rpt[$job_id]['sdelka']; echo $sdelka;?>" id="sdelka_<?=$job_id;?>"  class="general_inp" onchange="save_monthly_report('<?=$job_id;?>', 'sdelka_<?=$job_id;?>')"/>
<span style="white-space: nowrap;word-wrap: normal;"><img src="../../i/refresh.png" width="16" height="16" alt="" onclick="get_sdelka('<?=$job_id;?>', 'get_sdelka')" id="load_img_<?=$job_id;?>" style="cursor:pointer;">
<a href="../count/index.php?year=<?=$year;?>&month=<?=$month;?>&num_sotr=<?=$job_id;?>" target="_blank"><img src="../../../i/table.png" width="16" height="16" alt="" id="table_sdelka_link_<?=$job_id;?>" style="display: <? if($sdelka > "0"){?>display: inline;<?}else{?>none<?}?>"></a>
</span>
</nobr>
</td>
<td align=center><input type="text" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="procee" value="<?$procee=$rpt[$job_id]['procee']; echo $procee;?>" id="procee_<?=$job_id;?>"  class="general_inp" onchange="save_monthly_report('<?=$job_id;?>', 'procee_<?=$job_id;?>')"/></td>
<td align=center style="width:100px;background-color: #DDFFCC;"><input type="text" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="itogo" value="<?=$nachisleno+$sdelka+$procee;?>" id="itogo_<?=$job_id;?>"  class="general_inp" disabled/>


<?

if($type == "proizvodstvo"){
$itog_sdelka = $sdelka+$procee;

//обрабатываем доход в зависимости от занимаемой должности
//у каждого типа сотрудника свой К. Так, сборщицы должны заработать хотя бы свой оклад. Допники хотя бы 30% от начисленного. Станки хотя бы 60% от начисленного.
//15 сборщицы должны зарабатывать как минимум больше того, что начислено
/*if($nachisleno > $itog_sdelka and $d == "15"){echo $warning_2;}
//18 грузчик - будет странно, если заработал много сделки
if($nachisleno*0.5 < $itog_sdelka and $d == "18"){echo $warning_2;}
//16 мастер на станках должен зарабатывать сделкой не меньше 80% от начисленного и не больше 20% от начисленного. Отклонения являются аномалиями
if(($nachisleno < $itog_sdelka*0.9 or $nachisleno*1.25 < $sdelka) and $d == "16"){echo $warning_2;}
//33 мастер допработ
if(($nachisleno < $itog_sdelka*0.3) and $d == "33"){echo $warning_2;}
//17 упаковщицы
if(($nachisleno < $itog_sdelka*2.3) and $d == "17"){echo $warning_2;}  */
//14 кладовщик
}?>


</td>

<td name="pay1_col">
<input type="checkbox" class="pay_sum" onchange="pay_sum_total()" value="pay1_<?=$job_id;?>"/>
<input type="text" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="pay1"  value="<?$pay1=$rpt[$job_id]['pay1']; echo $pay1;?>" id="pay1_<?=$job_id;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'pay1_<?=$job_id;?>');pay_sum_total();" class="pay_inp"/>
<input type="hidden" size=8 id="pay1date_<?=$job_id;?>" value="<?$pay1date=$rpt[$job_id]['pay1date']; echo $pay1date;?>" onchange="save_monthly_report('<?=$job_id;?>', 'pay1date_<?=$job_id;?>')" />
<img src="/acc/i/calendar.gif" alt="" size=11 style="cursor:pointer;opacity:<?if($pay1date == "0000-00-00" or $pay1date == ""){?>0.2<?}else{?>0.8<?}?>;" id="pay1date_<?=$job_id;?>_img"  onmouseover="Tip('<?echo $pay1date;?>', PADDING, 5)"/>
<img src="/i/del_sm.png" alt="" id="del_pay1date_<?=$job_id;?>" style="cursor:pointer;display:<?if($pay1date == "0000-00-00" or $pay1date == ""){echo "none";}else{echo "inline";}?>;" onclick="del_paydate('pay1date', '<?=$job_id;?>')" />
</td>
<td name="pay2_col"> <? //print_r($rpt[$job_id]); ?>
<input type="checkbox" class="pay_sum" onchange="pay_sum_total()" value="pay2_<?=$job_id;?>"/>
<input type="text" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="pay2"  value="<?$pay2=$rpt[$job_id]['pay2']; echo $pay2;?>" id="pay2_<?=$job_id;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'pay2_<?=$job_id;?>');pay_sum_total();" class="pay_inp"/>
<input type="hidden" size=8 id="pay2date_<?=$job_id;?>" value="<?$pay2date=$rpt[$job_id]['pay2date']; echo $pay2date;?>" onchange="save_monthly_report('<?=$job_id;?>', 'pay2date_<?=$job_id;?>')" />
<img src="/acc/i/calendar.gif" alt="" size=11 style="cursor:pointer;opacity:<?if($pay2date == "0000-00-00" or $pay2date == ""){?>0.2<?}else{?>0.8<?}?>;" id="pay2date_<?=$job_id;?>_img" onmouseover="Tip('<?echo $pay2date;?>', PADDING, 5)"/>
<img src="/i/del_sm.png" alt="" id="del_pay2date_<?=$job_id;?>" style="cursor:pointer;display:<?if($pay2date == "0000-00-00" or $pay2date == ""){echo "none";}else{echo "inline";}?>;" onclick="del_paydate('pay2date', '<?=$job_id;?>')" />
</td>
<td name="pay3_col">
<input type="checkbox" class="pay_sum" onchange="pay_sum_total()" value="pay3_<?=$job_id;?>"/>
<input type="text" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="pay3"  value="<?$pay3=$rpt[$job_id]['pay3']; echo $pay3;?>" id="pay3_<?=$job_id;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'pay3_<?=$job_id;?>');pay_sum_total();" class="pay_inp"/>
<input type="hidden" size=8 id="pay3date_<?=$job_id;?>" value="<?$pay3date=$rpt[$job_id]['pay3date']; echo $pay3date;?>" onchange="save_monthly_report('<?=$job_id;?>', 'pay3date_<?=$job_id;?>')" />
<img src="/acc/i/calendar.gif" alt="" size=11 style="cursor:pointer;opacity:<?if($pay3date == "0000-00-00" or $pay3date == ""){?>0.2<?}else{?>0.8<?}?>;" id="pay3date_<?=$job_id;?>_img" onmouseover="Tip('<?echo $pay3date;?>', PADDING, 5)"/>
<img src="/i/del_sm.png" alt="" id="del_pay3date_<?=$job_id;?>" style="cursor:pointer;display:<?if($pay3date == "0000-00-00" or $pay3date == ""){echo "none";}else{echo "inline";}?>;" onclick="del_paydate('pay3date', '<?=$job_id;?>')" />
</td>
<td name="pay4_col">
<input type="checkbox" class="pay_sum" onchange="pay_sum_total()" value="pay4_<?=$job_id;?>"/>
<input type="text" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="pay4"  value="<?$pay4=$rpt[$job_id]['pay4']; echo $pay4;?>" id="pay4_<?=$job_id;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'pay4_<?=$job_id;?>');pay_sum_total();" class="pay_inp"/>
<input type="hidden" size=8 id="pay4date_<?=$job_id;?>" value="<?$pay4date=$rpt[$job_id]['pay4date']; echo $pay4date;?>" onchange="save_monthly_report('<?=$job_id;?>', 'pay4date_<?=$job_id;?>')" />
<img src="/acc/i/calendar.gif" alt="" size=11 style="cursor:pointer;opacity:<?if($pay4date == "0000-00-00" or $pay4date == ""){?>0.2<?}else{?>0.8<?}?>;" id="pay4date_<?=$job_id;?>_img" onmouseover="Tip('<?echo $pay4date;?>', PADDING, 5)"/>
<img src="/i/del_sm.png" alt="" id="del_pay4date_<?=$job_id;?>" style="cursor:pointer;display:<?if($pay4date == "0000-00-00" or $pay4date == ""){echo "none";}else{echo "inline";}?>;" onclick="del_paydate('pay4date', '<?=$job_id;?>')" />
</td>
<td name="pay5_col">
<input type="checkbox" class="pay_sum" onchange="pay_sum_total()" value="pay5_<?=$job_id;?>"/>
<input type="text" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="pay5"  value="<?$pay5=$rpt[$job_id]['pay5']; echo $pay5;?>" id="pay5_<?=$job_id;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'pay5_<?=$job_id;?>');pay_sum_total();" class="pay_inp"/>
<input type="hidden" size=8 id="pay5date_<?=$job_id;?>" value="<?$pay5date=$rpt[$job_id]['pay5date']; echo $pay5date;?>" onchange="save_monthly_report('<?=$job_id;?>', 'pay5date_<?=$job_id;?>')" />
<img src="/acc/i/calendar.gif" alt="" size=11 style="cursor:pointer;opacity:<?if($pay5date == "0000-00-00" or $pay5date == ""){?>0.2<?}else{?>0.8<?}?>;" id="pay5date_<?=$job_id;?>_img" onmouseover="Tip('<?echo $pay5date;?>', PADDING, 5)"/>
<img src="/i/del_sm.png" alt="" id="del_pay5date_<?=$job_id;?>" style="cursor:pointer;display:<?if($pay5date == "0000-00-00" or $pay5date == ""){echo "none";}else{echo "inline";}?>;" onclick="del_paydate('pay5date', '<?=$job_id;?>')" />
</td>
<td name="pay6_col">
<input type="checkbox" class="pay_sum" onchange="pay_sum_total()" value="pay6_<?=$job_id;?>"/>
<input type="text" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="pay6"  value="<?$pay6=$rpt[$job_id]['pay6']; echo $pay6;?>" id="pay6_<?=$job_id;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'pay6_<?=$job_id;?>');pay_sum_total();" class="pay_inp"/>
<input type="hidden" size=8 id="pay6date_<?=$job_id;?>" value="<?$pay6date=$rpt[$job_id]['pay6date']; echo $pay6date;?>" onchange="save_monthly_report('<?=$job_id;?>', 'pay6date_<?=$job_id;?>')" />
<img src="/acc/i/calendar.gif" alt="" size=11 style="cursor:pointer;opacity:<?if($pay6date == "0000-00-00" or $pay6date == ""){?>0.2<?}else{?>0.8<?}?>;" id="pay6date_<?=$job_id;?>_img" onmouseover="Tip('<?echo $pay6date;?>', PADDING, 5)"/>
<img src="/i/del_sm.png" alt="" id="del_pay6date_<?=$job_id;?>" style="cursor:pointer;display:<?if($pay6date == "0000-00-00" or $pay6date == ""){echo "none";}else{echo "inline";}?>;" onclick="del_paydate('pay6date', '<?=$job_id;?>')" />
</td>

<td name="pay7_col">
<input type="checkbox" class="pay_sum" onchange="pay_sum_total()" value="pay7_<?=$job_id;?>"/>
<input type="text" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="pay7"  value="<?$pay7=$rpt[$job_id]['pay7']; echo $pay7;?>" id="pay7_<?=$job_id;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'pay7_<?=$job_id;?>');pay_sum_total();" class="pay_inp"/>
<input type="hidden" size=8 id="pay7date_<?=$job_id;?>" value="<?$pay7date=$rpt[$job_id]['pay7date']; echo $pay7date;?>" onchange="save_monthly_report('<?=$job_id;?>', 'pay7date_<?=$job_id;?>')" />
<img src="/acc/i/calendar.gif" alt="" size=11 style="cursor:pointer;opacity:<?if($pay7date == "0000-00-00" or $pay7date == ""){?>0.2<?}else{?>0.8<?}?>;" id="pay7date_<?=$job_id;?>_img" onmouseover="Tip('<?echo $pay7date;?>', PADDING, 5)"/>
<img src="/i/del_sm.png" alt="" id="del_pay7date_<?=$job_id;?>" style="cursor:pointer;display:<?if($pay7date == "0000-00-00" or $pay7date == ""){echo "none";}else{echo "inline";}?>;" onclick="del_paydate('pay7date', '<?=$job_id;?>')" />

</td>
<?$job_ids_no_spec = $job_ids_no_spec.",".$job_id; ?>


<script>
Calendar.setup({
        inputField     :    "pay1date_<?=$job_id;?>",      // id of the input field
        button         :    "pay1date_<?=$job_id;?>_img",   // trigger for the calendar (button ID)
    });
Calendar.setup({
		inputField     :    "pay2date_<?=$job_id;?>",      // id of the input field
        button         :    "pay2date_<?=$job_id;?>_img",   // trigger for the calendar (button ID)
    });

Calendar.setup({
        inputField     :    "pay3date_<?=$job_id;?>",      // id of the input field
        button         :    "pay3date_<?=$job_id;?>_img",   // trigger for the calendar (button ID)
     });
Calendar.setup({
        inputField     :    "pay4date_<?=$job_id;?>",      // id of the input field
        button         :    "pay4date_<?=$job_id;?>_img",   // trigger for the calendar (button ID)
       });
Calendar.setup({
        inputField     :    "pay5date_<?=$job_id;?>",      // id of the input field
        button         :    "pay5date_<?=$job_id;?>_img",   // trigger for the calendar (button ID)
       });
Calendar.setup({
        inputField     :    "pay6date_<?=$job_id;?>",      // id of the input field
        button         :    "pay6date_<?=$job_id;?>_img"   // trigger for the calendar (button ID)
       });
Calendar.setup({
        inputField     :    "pay7date_<?=$job_id;?>",      // id of the input field
        button         :    "pay7date_<?=$job_id;?>_img"   // trigger for the calendar (button ID)
       });
</script>

<td align=center name="ostatok_col"><input type="text" name="ostatok" value="<?=$nachisleno+$sdelka+$procee-$pay1-$pay2-$pay3-$pay4-$pay5-$pay6-$pay7;?>" id="ostatok_<?=$job_id;?>"  class="general_inp" disabled/></td>
</tr>
<?}}?>
<tr class="tr_itog">
<td name="num_col" class="table_itog" style="width:50px"></td>
<td class="table_itog" style="width:100px">ИТОГО</td>
<td class="table_itog" id=oklad_itog></td>
<td name="socoklad_col" class="table_itog" id=socoklad_itog></td>
<td name="work_time_col" class="table_itog"></td>
<td name="oklad_hour_col" class="table_itog"></td>
<td name="socoklad_hour_col" class="table_itog"></td>
<td name="worked_time_col" class="table_itog" id="worked_time_itog"></td>
<td name="socnachisl_col" class="table_itog" id="socnachisl_itog"></td>
<td name="progul_col" class="table_itog" id="progul_itog"></td>
<td name="nachisleno_col" class="table_itog" id="nachisleno_itog"></td>
<td name="sdelka_col" class="table_itog" style="width:100px" id="sdelka_itog"></td>
<td name="procee_col" class="table_itog" style="width:100px" id="procee_itog"></td>
<td name="itogo_col" class="table_itog" style="width:100px;background-color: #99FF66;" id="itogo_itog"></td>
<td name="pay1_col" class="table_itog" id="pay1_itog"></td>
<td name="pay2_col" class="table_itog" id="pay2_itog"></td>
<td name="pay3_col" class="table_itog" id="pay3_itog"></td>
<td name="pay4_col" class="table_itog" id="pay4_itog"></td>
<td name="pay5_col" class="table_itog" id="pay5_itog"></td>
<td name="pay6_col" class="table_itog" id="pay6_itog"></td>
<td name="pay7_col" class="table_itog" id="pay7_itog"></td>
<td name="ostatok_col" class="table_itog" id="ostatok_itog"></td>
</tr>

</tbody>
</table>
<? if($working_days){ ?>
<h2>Сформировать ведомость: </h2>
<label for="administration" style="cursor:pointer;">администрация:</label> <input type="checkbox" id="administration" value="1"/><br>
<label for="proizvodstvo" style="cursor:pointer;">производство:</label> <input type="checkbox" id="proizvodstvo" value="1"/><br>
<label for="nadomniki" style="cursor:pointer;">надомники:</label> <input type="checkbox" id="nadomniki" value="1"/><br>
<label for="all" style="cursor:pointer;">все:</label> <input type="checkbox" id="all" value="1"/><br>
дата: <input type="test" size=8 id="date_ved"  />
<button onclick="generate_vedomost()">сформировать!</button>
<? } ?>

<? } else { ?>  доступ ограничен    <? } ?>

</div>
<div id="pay_sum_div" class="pay_sum_div"></div>
<? include("auth_form.php"); ?>
 <pre>
 <?//print_r ($rpt);?>
 </pre>


<script>


function generate_vedomost(){

if($("#administration").is(':checked')){administration = "1"}else{administration = "0"}
if($("#proizvodstvo").is(':checked')){proizvodstvo = "1"}else{proizvodstvo = "0"}
if($("#nadomniki").is(':checked')){nadomniki = "1"}else{nadomniki = "0"}
if($("#all").is(':checked')){all = "1"}else{all = "0"}
date_ved = $("#date_ved").val()
document.open('ved.php?administration='+administration+'&proizvodstvo='+proizvodstvo+'&nadomniki='+nadomniki+'&date='+date_ved+'&all='+all,"","");
}
function del_paydate(obj_id, uid){
save_id = obj_id+"_"+uid
$("#"+obj_id+"_"+uid).val("0000-00-00")
save_monthly_report(uid, save_id)
hide_id = 'del_'+save_id
$("#"+hide_id).hide()
$('#'+save_id+'_img').fadeTo(500,0.2);
}

Calendar.setup({
        inputField     :    "date_ved",      // id of the input field
        button         :    "date_ved"   // trigger for the calendar (button ID)

    });


function save_monthly_report(uid, obj_id){
//если удалена дата оплаты, то возвращаем все в исходный вид
if($("#"+obj_id).val() !== "0000-00-00"){
$('#del_'+obj_id).show();
$('#'+obj_id+'_img').fadeTo(500,0.8);
}

if(uid){
work_time = $("#work_time_"+uid).val();
oklad = $("#oklad_"+uid).val();
nachisleno = $("#nachisleno_"+uid).val();
socoklad = $("#socoklad_"+uid).val();
sdelka = $("#sdelka_"+uid).val();
procee = $("#procee_"+uid).val();
pay1 = $("#pay1_"+uid).val();
pay1date = $("#pay1date_"+uid).val();
pay2 = $("#pay2_"+uid).val();
pay2date = $("#pay2date_"+uid).val();
pay3 = $("#pay3_"+uid).val();
pay3date = $("#pay3date_"+uid).val();
pay4 = $("#pay4_"+uid).val();
pay4date = $("#pay4date_"+uid).val();
pay5 = $("#pay5_"+uid).val();
pay5date = $("#pay5date_"+uid).val();
pay6 = $("#pay6_"+uid).val();
pay6date = $("#pay6date_"+uid).val();
pay7 = $("#pay7_"+uid).val();
pay7date = $("#pay7date_"+uid).val();

}else{
work_time=""
oklad = ""
socoklad = ""
sdelka = ""
nachisleno = ""
procee=""
pay1 = ""
pay1date = ""
pay2 = ""
pay2date = ""
pay3 = ""
pay3date = ""
pay4 = ""
pay4date = ""
pay5 = ""
pay5date = ""
pay6 = ""
pay6date = ""
pay7 = ""
pay7date = ""
}

var geturl;
  geturl = $.ajax({
    type: "GET",
    url: 'save_monthly_report.php',
	data : '&uid='+uid+'&year=<?=$year;?>&month=<?=$month;?>&work_time='+work_time+'&oklad='+oklad+'&nachisleno='+nachisleno+'&socoklad='+socoklad+'&sdelka='+sdelka+'&procee='+procee+'&pay1='+pay1+'&pay1date='+pay1date+'&pay2='+pay2+'&pay2date='+pay2date+'&pay3='+pay3+'&pay3date='+pay3date+'&pay4='+pay4+'&pay4date='+pay4date+'&pay5='+pay5+'&pay5date='+pay5date+'&pay6='+pay6+'&pay6date='+pay6date+'&pay7='+pay7+'&pay7date='+pay7date+'&working_days='+working_days,
    success: function () {

var resp1 = geturl.responseText

if (resp1 == "ok"){
$('<span id="resp_'+obj_id+'" style="display:none; position: absolute;font-size:18px;background-color: #009900; color:white; font-face:arial; width: 200px; height: 35px; z-index:10000; text-align:middle">'+resp1+'</span>').insertAfter('#'+obj_id);
  $("#resp_"+obj_id).html(resp1);
  $("#resp_"+obj_id).fadeIn(100);
  $("#resp_"+obj_id).fadeOut(200);
h_sum(uid)
}else{alert("ошибка сохранения данных! " + resp1)}

}
})
}

function h_sum(uid){
work_time = $("#work_time_"+uid).val();
oklad = $("#oklad_"+uid).val();
oklad_hour = $("#oklad_hour_"+uid).val();
socoklad = $("#socoklad_"+uid).val();
socoklad_hour = $("#socoklad_hour_"+uid).val();
worked_time = $("#worked_time_"+uid).val();
socnachisl = $("#socnachisl_"+uid).val();
progul = $("#progul_"+uid).val();
sdelka = $("#sdelka_"+uid).val();
procee = $("#procee_"+uid).val();
pay1 = $("#pay1_"+uid).val();
pay2 = $("#pay2_"+uid).val();
pay3 = $("#pay3_"+uid).val();
pay4 = $("#pay4_"+uid).val();
pay5 = $("#pay5_"+uid).val();
pay6 = $("#pay6_"+uid).val();
pay7 = $("#pay7_"+uid).val();
working_days = $("#working_days").val();
progul_multa = "<?=$progul_multa;?>"
working_days_social = "<?=$working_days_social;?>"

working_days_social=working_days_social*1
sdelka=sdelka*1
procee=procee*1
oklad=oklad*1
socoklad=socoklad*1
socoklad_hour=socoklad_hour*1
socnachisl=socnachisl*1
progul=progul*1
worked_time=worked_time*1
progul_multa=progul_multa*1
working_days=working_days*1
pay1=pay1*1
pay2=pay2*1
pay3=pay3*1
pay4=pay4*1
pay5=pay5*1
pay6=pay6*1
pay7=pay7*1
oklad_hour = oklad/work_time/working_days
oklad_hour = oklad_hour.toFixed(0)
$("#oklad_hour_"+uid).val(oklad_hour);

socoklad_hour = socoklad/work_time/working_days_social
socoklad_hour = socoklad_hour.toFixed(0)
$("#socoklad_hour_"+uid).val(socoklad_hour);

nachisleno = oklad_hour*worked_time+socoklad_hour*socnachisl-progul_multa*progul
nachisleno = nachisleno.toFixed(0)
$("#nachisleno_"+uid).val(nachisleno);
nachisleno=nachisleno*1

ostatok = nachisleno+sdelka+procee-pay1-pay2-pay3-pay4-pay5-pay6-pay7
ostatok = ostatok.toFixed(0)
$("#ostatok_"+uid).val(ostatok);

itogo = nachisleno+sdelka+procee;
itogo = itogo.toFixed(0)
$("#itogo_"+uid).val(itogo);

//перезагружаем страницу, если менялось количество рабочих дней т.е. пересчитываем все сразу

if(!uid){
setTimeout(function(){location.href="?month=<?=$month;?>&year=<?=$year;?>&type=<?=$type;?>"} , 1100);

}
sum()
}


function sum(){

var coloumns = ["oklad", "socoklad", "worked_time", "socnachisl", "progul", "nachisleno", "sdelka", "procee", "itogo", "pay1", "pay2", "pay3", "pay4", "pay5", "pay6", "pay7", "ostatok"];
jQuery.each(coloumns, function() {

coloumn_name = this

var summa = "0"
var summa_chist = "0"

summa = summa*1
summa_chist = summa_chist*1

     var arr = $('input[name='+coloumn_name+']').map(function(){
      next_val = $(this).val()
      if(next_val!==''){
      next_val = next_val*1
	  if(next_val < 0){
      summa_chist =  summa_chist
      summa = summa  + next_val
	  }else{
	  summa_chist =  summa_chist + next_val
      summa = summa + next_val
	  }
      next_val=''
      }
      //if(coloumn_name == "oklad"){alert(summa)}

    }).get();
    summa = summa.toFixed(2);
    summa_chist = summa_chist.toFixed(2);
	if(summa==summa_chist){
$("#"+coloumn_name+"_itog").html(summa);
	}else{
$("#"+coloumn_name+"_itog").html(summa+"<br>("+summa_chist+")");
 }
     });

 }


function pay_sum_total(){

				var sum = 0;
				var arr = $('input.pay_sum:checked');
				arr.each(function(index, el){
					var vl = el.value;
					chislo = $("#"+vl).val();
					if(chislo>0){sum += parseFloat(chislo);}

				})
				if(sum > 0){
               $("#pay_sum_div").fadeIn(200);
               $("#pay_sum_div").html(sum+" <span onclick=\"pay_sum_off()\" class=\"x\">x</span>")
				}else{
               $("#pay_sum_div").html("")
               $("#pay_sum_div").fadeOut(200);
				}


      }
function pay_sum_off(){
				var arr = $('input.pay_sum:checked');
				arr.each(function(index, el){
				$(this).removeAttr("checked");
               $("#pay_sum_div").fadeOut(200);
				})
}


function replace_num(v) {
	var reg_sp = /[^\d]*/g;		// вырезание всех символов кроме цифр
	v = v.replace(reg_sp, '');
	return v;
}


function additional_fld(act){

var coloumns_to_hide = ["num_col", "oklad_hour_col", "socoklad_hour_col", "worked_time_col", "socnachisl_col", "progul_col", "work_time_col"];

jQuery.each(coloumns_to_hide, function() {
coloumn_name = this

if(act == "hide"){
$('td[name^='+coloumn_name+']').fadeOut(500);
$('colgroup[name^='+coloumn_name+']').fadeOut(500);
$('#add_fld_show').show();
$('#add_fld_hide').hide();

}else{
$('td[name^='+coloumn_name+']').fadeIn(500);
$('colgroup[name^='+coloumn_name+']').fadeIn(500);
$('#add_fld_show').hide();
$('#add_fld_hide').show();

}
});
}



function get_full_sdelka(act){
job_ids = '<?=$job_ids_no_spec;?>';
jobid = job_ids.split(',');
for(var i = 0; i < jobid.length; i++)
if(jobid[i] !== ''){
get_sdelka(jobid[i], act)

}
//alert(type)
}

function save_nachisleno(){
job_ids = '<?=$job_ids_no_spec;?>';
jobid = job_ids.split(',');
for(var i = 0; i < jobid.length; i++)
if(jobid[i] !== ''){
 //сохраняем колонку с начислениями в любом случае
save_monthly_report(jobid[i], 'nachisleno')
}
}

save_nachisleno()


function get_sdelka(job_id){
$('#load_img_'+job_id).attr('src', '../../../../i/load.gif');
var geturl;

  geturl = $.ajax({
    type: "GET",
    url: '../backend/job_entries.php',
	data : '&year=<?=$year;?>&month=<?=$month;?>&act=get_sdelka&num_sotr='+job_id+'&items_on_page=10000',
    success: function () {

var resp1 = geturl.responseText

if (resp1){

if(resp1 !== "error"){$("#sdelka_"+job_id).val(resp1);

if($("#save_sdelka").prop("checked")){
save_monthly_report(job_id, 'sdelka')

}

h_sum(job_id)
}else{alert("Произошла ошибка в файле job_entries. ("+resp1+")")}


$('#load_img_'+job_id).attr('src', '../../i/refresh.png');
if(resp1>0)
$('#table_sdelka_link_'+job_id).show();



}else{alert("ошибка!" + resp1)}

}
})

}



additional_fld('hide')
//alert('<?=$month;?>')
  </script>
<pre>
 <? //print_r($rpt); ?>
</pre>

</body>

</html>
