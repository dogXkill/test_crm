<?
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");

if ($user_access['accounting_user'] == 0 || $user_access['list_access'] == 0) {
    header('Location: /');
}

$auth = false;

$str = $_SERVER['QUERY_STRING'];
parse_str($str);
require_once("lib.php");
$rand = microtime(true).rand();
?>
<html>
<head>
    <title>Ведомость</title>
    <link href="style.css?cache=<?=$rand?>" rel="stylesheet" type="text/css" />
    <!--<script src="../../includes/js/autoblock.js"></script>-->
    <script type="text/javascript" src="../../includes/js/jquery-1.11.3.min.js"></script>
	<script src="//ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/jquery-ui.min.js"></script>
    <script src="../../includes/js/jquery.cookie.js"></script>
    <script type="text/javascript" src="../../includes/js/jscalendar/calendar.js"></script>
    <script type="text/javascript" src="../../includes/js/jscalendar/lang/calendar-ru.js"></script>
    <script type="text/javascript" src="../../includes/js/jscalendar/calendar-setup-art-stat.js"></script>
    <!--<link rel="stylesheet" href="/assets/libs/font-awesome-4.7.0/css/font-awesome.min.css" type="text/css" media="all">-->
	<link rel="stylesheet" href="../../includes/fonts/css/all.min.css" type="text/css" media="all">
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
<!--<div id=block_div style="display:<?if($_COOKIE["auth"] == "on") {echo "block";}else{echo "none";}?>">-->
<div id="block_div" style="display:block;" class='fixed-box'>
    <?
	function check_pay($mas){
		$flag_pay=0;
		foreach ($mas as $value){
			if ($value>=1){$flag_pay=1;break;}
		}
		return $flag_pay;
	}
    if ($user_access['list_access'] == '1'){
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
		//echo $act_ids;
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
            $rpt[$uid]["pay8"] = $row[pay8];
            $rpt[$uid]["pay8date"] = $row[pay8date];
			
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
			$dep_arr[$row['id']] = $row['name'];
        }
        $deps_count = count($deps);
//массив с должностями
            $dolj_arr = array();
            $q = "SELECT * FROM doljnost";
            $r = mysql_query($q);
            while ($row = mysql_fetch_assoc($r))
            {
                $dolj_arr[$row['id']] = $row['name'];
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
		if (isset($_GET['name'])){
			//$prev_month_link.="&name".$_GET['name'];
		}
        ?>
		
        <table style="width:1200px;" class='table_top'>
            <tr class=' fixed-div'>
                <td style="width:100px; text-align: center; font-size: 20px; font-weight: bold;"><?=$prev_month_link;?></td>
                <td style="width:200px; text-align: center; font-size: 20px; font-weight: bold;"><?echo $months[$month]." ". $year; ?>
                    <br><a href="http://<?=$_SERVER['SERVER_NAME'];?>/acc/applications/timetable/report.php?year=<?=$current_year;?>&month=<?=$current_month;?>&type=<?=$type;?><?if(isset($_GET['group'])){echo '&group='.$_GET['group'];}if(isset($_GET['department'])){echo '&department='.$_GET['department'];}?><?if(isset($_GET['name'])){echo '&name='.$_GET['name'];}?>" style="font-size:8px;">перейти в текущий месяц</a></td>
                <td style="width:100px; text-align: center; font-size: 20px; font-weight: bold;"><?=$next_month_link;?></td>
                <td style="width:400px;">Кол-во рабочих дней: <input type="text" name="working_days" id="working_days" disabled size=2 value="<?if($working_days>"0"){echo $working_days;};?>"/>
                    <br>Социальные дни: <input type="text" name="working_days_soc" id="working_days_soc" size=2 value="<?=$working_days_social;?>" disabled/></td>
                <td style="width:500px;"><?if($working_days){ ?>Ведомость на дату:
                <br><input type="test" size=8 id="date_ved" autocomplete="off" />
            <button onclick="generate_vedomost()">сформировать!</button></br></br>
			<select id='day_sp_pay_ved'>
					<?php
						$sql1 = "SELECT * FROM tip_pay ORDER BY `tip_pay`.`nn` ASC";
						if($res1 = mysql_query($sql1)){
							{
								if (mysql_num_rows($res1) > 0){
									$k=0;
									
								  while($row1 = mysql_fetch_assoc($res1))
								  {
									  $id_option_pay=$row1['id'];
									  echo "<option value='{$id_option_pay}'>{$row1['name']}</option>";
								  }
								}
							}
						}
					?>
				</select>
        <? } ?>  </td>
              <td><?if ($user_access['table_access'] == '1'){?><a target="_blank" href="index.php" class="sublink">табель</a> <?}?></td>
              <td style="width:400px;">


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
            <?
            $allowed_deps = explode('|', $user_access['list_access_dep']);
            if (count($allowed_deps) > 0 && $user_access['list_access_dep'] != 0) {
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
                <?
            }
            ?>

        </div>

              </td>
              <td>
                  <span onclick="additional_fld('show')" class="sublink">показать то, что скрыто</span>
              </td>
            </tr></table>
        <br>
        <div>
        </div>
        <!-- Popup штрафы -->
        <div class="fines_popup" style="display: none;">
            <div class="fines_popup_info">
                <div class="fines_popup_head">Штрафы</div>
                <div class="loading"></div>
                <div class="fines_popup_content"></div>
                <div class="fines_popup_buttons">
                    <button id="add_fine_btn" type="button" name="add_fine_btn" onclick="newFine(this); return false;">Добавить</button>
                </div>
                <a href="#" class="fines_popup_close" onclick="toggleFinesPopup('close'); return false;"><i class="fa fa-times" aria-hidden="true"></i></a>
            </div>
        </div>
        <!-- End: Popup штрафы -->
        <!-- Popup прочее -->
        <div class="other_popup" style="display: none;">
            <div class="other_popup_info">
                <div class="other_popup_head">Прочие начисления</div>
                <div class="loading"></div>
                <div class="other_popup_content"></div>
                <div class="other_popup_buttons">
                    <button id="add_other_btn" type="button" name="add_other_btn" onclick="newOther(this); return false;">Добавить</button>
                </div>
                <a href="#" class="other_popup_close" onclick="toggleOtherPopup('close'); return false;"><i class="fa fa-times" aria-hidden="true"></i></a>
            </div>
        </div>
		
        <!-- End: Popup прочее -->
		<!--popup day-->
		<div class="day_popup" style="display: none;">
            <div class="day_popup_info">
                <div class="day_popup_head">Оплата #<span id='day_text' ></span></div>
                <div class="loading"></div>
                <div class="day_popup_content">
				<input type='hidden' id='day_uid'>
				<input type='hidden' id='day_id_report'>
				
				<p>
				<span style='width:100px;    display: inline-block;'>Сумма</span>
				&nbsp;
				<span style='width:125px;    display: inline-block;'>Дата</span>
				&nbsp;
				Способы оплаты
				</p>
				<p><input type='text' onkeyup="this.value=replace_num(this.value);" autocomplete="off" style='width:100px;' id='day_summa'>
					&nbsp;
					 <input type='text' id='day_date' style='width:100px;' >
				<!--<img src="/acc/i/calendar.gif" alt="" size="11" style="cursor:pointer;opacity:0.2;" id="payIzmdate_img" onmouseover="Tip('00-00-0000', PADDING, 5)">-->
				&nbsp;
				
				<select id='day_sp_pay'>
					<?php
						$sql1 = "SELECT * FROM tip_pay ORDER BY `tip_pay`.`nn` ASC";
						if($res1 = mysql_query($sql1))
							{
								if (mysql_num_rows($res1) > 0){
									$k=0;
									
								  while($row1 = mysql_fetch_assoc($res1))
								  {
									  $id_option_pay=$row1['id'];
									  echo "<option value='{$id_option_pay}'>{$row1['name']}</option>";
								  }
								}
							}
					?>
				</select>
				
				<p>
				Комментарий:</br>
				<textarea class='comment_day'></textarea>
				</p>
				</div>
                <div class="day_popup_buttons" style="    ">
                    <button id="add_other_btn" type="button" name="add_other_btn" onclick="IzmDays(this); return false;">Сохранить</button>
					<button id="btn_delet" type="button" name="delete_btn" onclick="delDays(this);return false;">Удалить</button>
                </div>
				<p style='padding-left: 15px;'>Кто изменил: <span id="user_izm_vid"></span></p>
                <a href="#" class="day_popup_close" onclick="togglePopupDay('close'); return false;"><i class="fa fa-times" aria-hidden="true"></i></a>
            </div>
        </div>
		<!--end popup day-->
		<div class='search_report'>
			<input type='text' class='input_search_report' placeholder='Поиск по имени (от 2 букв)' onkeydown="checkForEnter(event)" value="<? if (isset($_GET['name'])) {
			echo iconv('utf-8//IGNORE', 'windows-1251//IGNORE', $_GET['name']);}?>">
			<button type='button' class='btn_restart'>x</button>
			<!--<button type="button" onclick='search_report_name();'><i class="fa fa-search"></i></button>-->
			<button type="button" onclick='search_report_name();'>Найти</button>
		</div>
		<div class='layer'>
        <table cellpadding=3 cellspacing=0 id=table class="fixtable table-body">
            <!--<thead class='th_tables'>-->
			<thead class='thead'>
			<tr>
			<td name="cnt_col" class="table_title " style="widtd:50px">№</td>
            <td name="num_col" class="table_title" style="widtd:50px">#</td>
            <td class="table_title" style="widtd:150px">ФИО
                <span onclick="additional_fld('hide')" id="add_fld_hide" style="cursor:pointer;font-size:23px;color:red; font-weight:bold;"><strong>-</strong></span>
                <span onclick="additional_fld('show')" id="add_fld_show" style="cursor:pointer;display:none;font-size:23px;color:green; font-weight:bold;"><strong>+</strong></span></td>
			<td name="dept_col" class="table_title">Отдел / должность</td>
			<td class="table_title" style="widtd:80px">База</td>
            <td name="socoklad_col" class="table_title" style="widtd:80px">Соцбаза</td>
            <td name="work_time_col" class="table_title" style="widtd:70px">Норма часов</td>

            <td name="oklad_hour_col" class="table_title" style="widtd:70px">Рабочий час</td>
            <td name="socoklad_hour_col" class="table_title" style="widtd:70px">Соцчас</td>
            <td name="worked_time_col" class="table_title" style="widtd:70px">Отработано часов</td>
            <td name="socnachisl_col" class="table_title" style="widtd:70px">Начислено соцчасов</td>
            <td name="progul_col" class="table_title" style="widtd:70px">Прогулы</td>
            <td name="procee_rez_col" class="table_title" style="widtd:70px">Прочее</td>
            <td class="table_title" style="widtd:100px">Начислено</td>
            <td class="table_title" style="widtd:100px">Сделка <img src="../../i/refresh.png" widtd="16" height="16" alt="" onclick="get_full_sdelka('get_sdelka')" style="cursor:pointer;"><br><input type="checkbox" id="save_sdelka" name="save_sdelka"/> <label for="save_sdelka" style="cursor:pointer">сохр.</label></td>
            <td class="table_title" style="widtd:100px">Прочие начисления</td>
            <td class="table_title" style="widtd:100px">Штрафы</td>
            <td class="table_title" style="widtd:100px;background-color: #DDFFCC;">Итого</td>
            <td name="pay1_col" class="table_title" valign=top>#1</td>
            <td name="pay2_col" class="table_title" valign=top>#2</td>
            <td name="pay3_col" class="table_title" valign=top>#3</td>
            <td name="pay4_col" class="table_title" valign=top>#4</td>
            <td name="pay5_col" class="table_title" valign=top>#5</td>
            <td name="pay6_col" class="table_title" valign=top>#6</td>
            <td name="pay7_col" class="table_title" valign=top>#7</td>
            <td name="pay8_col" class="table_title" valign=top>#8</td>
            <td name="ostatok_col" class="table_title">Остаток</td>
			</tr>
            </thead> <tbody class="tbody">
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

            if (isset($_GET['department']) && $_GET['department'] !== 'all') {
                $department = explode('_', $_GET['department']);
                $conditions = array();
                foreach ($department as $key => $value) {
                    if (in_array($value, explode('|', $user_access['list_access_dep']))) {
                        array_push($conditions, 'user_department = '.$value);
                    }
                }
				
				 array_push($conditions, 'user_department = 0');
				 //echo 'tyt';
                $conditions = implode(' OR ', $conditions);
                if (!empty($conditions)) {
                    $n_vstavka .= " AND ( $conditions  )";
                } else {
                    $n_vstavka .= " AND ( user_department = 123456789 )";
                }
				

            } else {
				//echo 'tyt';
                $list_access_dep = explode('|', $user_access['list_access_dep']);
                $account_access = array();
                foreach ($list_access_dep as $key => $value) {
                    array_push($account_access, 'user_department = ' . $value);
                }
				array_push($account_access, 'user_department = 0');
                $account_access = " AND (" . implode(' OR ', $account_access) . ")";
            }
			 
			//фильтр по имени 
			if (isset($_GET['name'])) {
				$name_search=iconv('utf-8//IGNORE', 'windows-1251//IGNORE', $_GET['name']);
				$filter="AND (`name` LIKE '$name_search%' OR  `surname` LIKE '$name_search%')";
				//echo iconv('utf-8//IGNORE', 'windows-1251//IGNORE', $name_search);
			}

            if($type == "administration"){
                $vstavka = " administration = '1'";
            }

            if($type == "proizvodstvo"){
                $vstavka = " proizv = '1' ";
            }

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

            if($working_days){
                //if($act_ids!==""){$vstavka_IN = "AND job_id IN($act_ids)";}else{$vstavka_IN = " AND archive != '1' ";}
				if($act_ids!==""){
					//$vstavka_IN = " AND (archive != '1' OR job_id IN($act_ids))";
				}else{
					//$vstavka_IN = " AND (archive != '1' )";
				}
                
//получаем список сотрудниов с базовыми параметрами
//$query = "SELECT uid, job_id, surname, name, doljnost, oklad, socoklad, work_time FROM users WHERE ".$vstavka." ".$vstavka_IN."  AND job_id != '1000' ORDER BY surname ASC";
                $query = "SELECT uid,archive, job_id, surname, name, doljnost, oklad, socoklad, work_time, SUM(uf.amount) fine_amount,user_department";
                $query .= " FROM users LEFT JOIN user_fines uf ON uf.user_id = uid AND uf.fine_year = $year AND uf.fine_month = $month";
                $query .= " WHERE job_id != '1000' $n_vstavka $vstavka_IN $account_access $filter";
                $query .= " GROUP BY uid";
                $query .= " ORDER BY surname ASC";
                echo $query;
                $res = mysql_query($query);
                //echo mysql_error();
				$cnt = 0;
                while($us = mysql_fetch_array($res)) {
					
                    $job_id=$us['job_id'];
					//
					//$pay1=$rpt[$job_id]['pay1'];
					if ($rpt[$job_id]['pay1']!=undefined && $rpt[$job_id]['pay1']!=null){
						$pay1=$rpt[$job_id]['pay1'];
						
					}else{
						$pay1=0;
					}
					//$pay2=$rpt[$job_id]['pay2'];
					//$pay2=0;
					if ($rpt[$job_id]['pay2']!=undefined && $rpt[$job_id]['pay2']!=null){
						$pay2=$rpt[$job_id]['pay2'];
					}else{
						$pay2=0;
					}
					if ($rpt[$job_id]['pay3']!=undefined && $rpt[$job_id]['pay3']!=null){
						$pay3=$rpt[$job_id]['pay3'];
					}else{
						$pay3=0;
					}
					if ($rpt[$job_id]['pay4']!=undefined && $rpt[$job_id]['pay4']!=null){
						$pay4=$rpt[$job_id]['pay4'];
					}else{
						$pay4=0;
					}
					if ($rpt[$job_id]['pay5']!=undefined && $rpt[$job_id]['pay5']!=null){
						$pay5=$rpt[$job_id]['pay5'];
					}else{
						$pay5=0;
					}
					if ($rpt[$job_id]['pay6']!=undefined && $rpt[$job_id]['pay6']!=null){
						$pay6=$rpt[$job_id]['pay6'];
					}else{
						$pay6=0;
					}
					if ($rpt[$job_id]['pay7']!=undefined && $rpt[$job_id]['pay7']!=null){
						$pay7=$rpt[$job_id]['pay7'];
					}else{
						$pay7=0;
					}
					if ($rpt[$job_id]['pay8']!=undefined && $rpt[$job_id]['pay8']!=null){
						$pay8=$rpt[$job_id]['pay8'];
					}else{
						$pay8=0;
					}
					$mas_pay=null;
					$mas_pay=array($pay1,$pay2,$pay3,$pay4,$pay5,$pay6,$pay7,$pay8);
					//
                    $id_us=$us['uid'];
                    $jq="SELECT SUM(amount)AS other_sum FROM user_other WHERE user_id = $id_us AND fine_year = $year AND fine_month = $month ";
                    // echo $jq;
                    $sum_other=0;
                    $sum_other=mysql_query($jq);
                    $sum_other = mysql_fetch_row($sum_other);
                    if($rpt[$job_id]["oklad"]){$oklad=$rpt[$job_id]["oklad"];}else{$oklad=$us['oklad'];}
                    if($rpt[$job_id]["socoklad"]){$socoklad=$rpt[$job_id]["socoklad"];}else{$socoklad=$us['socoklad'];}
                    if($rpt[$job_id]["work_time"]){$work_time=$rpt[$job_id]["work_time"];}else{$work_time=$us['work_time'];}
                    $hours = $hrs[$job_id]["hours"];
                    if($work_time >"0"){
                        $oklad_hour = round($oklad/$work_time/$working_days);
                        $socoklad_hour = round($socoklad/$work_time/$working_days_social);}
                    $progul = $hrs[$job_id]["progul"];
                    $fio = $us['surname'].' '.$us['name'];
                    $fine_amount = $us['fine_amount'];
                    if ($fine_amount==undefined || $fine_amount==null){$fine_amount=0;}
                    $procee_rez=$rpt[$job_id]["procee"];
                    $procee= $sum_other[0];
                    if ($procee==undefined || $procee==""){$procee=0;}
                    if ($sdelka==undefined || $sdelka==""){$sdelka=0;}
                    if ($nachisleno==undefined || $nachisleno==""){$nachisleno=0;}
					$mas_pay[]=$hrs[$job_id]["hours"]*$oklad_hour+$hrs[$us['job_id']]["boln"]*$work_time*$socoklad_hour-$progul_multa*$hrs[$job_id]["progul"];
					$mas_pay[]=$rpt[$job_id]['sdelka'];
					$mas_pay[]=$procee;
					$mas_pay[]=$fine_amount;
					
					
					if ( $us['job_id']==554){
						//print_r($mas_pay);
					}
                    // echo "N=".$nachisleno." / SD=".$sdelka." / PR=".$procee."</br>";
					$flag_vivod=1;//выводим
					if ($us['archive']==1 AND check_pay($mas_pay)!=0){
						$flag_vivod=0;
					}else if ($us['archive']==1 AND check_pay($mas_pay)==1){$flag_vivod=1;}
					else if ($us['archive']==0  AND $us['user_department']==0){$flag_vivod=1;}
					else if ($us['archive']==0){$flag_vivod=0;}
					//echo $us['job_id']."[{$flag_vivod}]";
					if ($flag_vivod==0){
						$cnt = $cnt+1;
                    ?>
                    <tr id="tr_<?=$job_id;?>" class="table-row">
						<td name="cnt_col" class=name align=center style='opacity:0.3'><?=$cnt;?></td>
                        <td name="num_col" class=name align=center><?=$job_id;?></td>
                        <td class=name><a href="/acc/users/users.php?edit=<?=$us['uid'];?>&oper=edit" target="_blank" id="username_<?=$us['uid'];?>"><?=$fio;?></a>
                            <a href="index.php?year=<?=$year;?>&month=<?=$month;?>&uid=<?=$us['uid'];?>&type=<?=$type;?>" target="_blank"><img src="../../../i/timetable.png" width="16" height="16" alt=""></a>
                        </td>
                        <td style="font-size:12px;" name="dept_col"><b><?echo $dolj_arr[$us['doljnost']];?></b> <br> <?echo $dep_arr[$us['user_department']];?> </td>
                        <td align=center><input type="text" name="oklad" onkeyup="this.value=replace_num(this.value);" autocomplete="off" id="oklad_<?=$job_id;?>" value="<?=$oklad;?>" onchange="save_monthly_report('<?=$job_id;?>', 'oklad_<?=$job_id;?>')"  class="general_inp"/></td>
                        <td align=center name="socoklad_col"><input type="text" name="socoklad" onkeyup="this.value=replace_num(this.value);" autocomplete="off" id="socoklad_<?=$job_id;?>" value="<?=$socoklad;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'socoklad_<?=$job_id;?>')" class="general_inp"/></td>
                        <td align=center name="work_time_col"><input type="text" name="work_time" onkeyup="this.value=replace_num(this.value);" autocomplete="off" id="work_time_<?=$job_id;?>" value="<?if(!$work_time){$work_time="9";} echo $work_time;?>" onchange="save_monthly_report('<?=$job_id;?>', 'work_time_<?=$job_id;?>')" class="hour_inp"/></td>
                        <td align=center name="oklad_hour_col"><input type="text" name="oklad_hour" id="oklad_hour_<?=$job_id;?>" value="<?=$oklad_hour;?>" class="general_inp" disabled/></td>
                        <td align=center name="socoklad_hour_col"><input type="text" name="socoklad_hour" id="socoklad_hour_<?=$job_id;?>" value="<?=$socoklad_hour;?>" class="general_inp" disabled/></td>
                        <td align=center name="worked_time_col"><input type="text" name="worked_time" value="<?=$hrs[$job_id]["hours"];?>" id="worked_time_<?=$job_id;?>"  class="general_inp" disabled /></td>
                        <td align=center name="socnachisl_col"><input type="text" name="socnachisl" value="<?=$hrs[$job_id]["boln"]*$work_time;?>" id="socnachisl_<?=$job_id;?>"  class="general_inp" disabled/></td>
                        <td align=center name="progul_col"><input type="text" name="progul" value="<?=$hrs[$job_id]["progul"];?>" id="progul_<?=$job_id;?>"  class="general_inp" disabled/></td>
                        <td align=center name="procee_rez_col"><input type="text" name="procee_rez" value="<?=$procee_rez;?>" id="procee_rez_<?=$job_id;?>"  class="general_inp" disabled/></td>
                        <td name="nachisleno_col"><input type="text" name="nachisleno" 
value="<?$nachisleno=$hrs[$job_id]["hours"]*$oklad_hour+$hrs[$us['job_id']]["boln"]*$work_time*$socoklad_hour-$progul_multa*$hrs[$job_id]["progul"]; echo $nachisleno;?>" 
id="nachisleno_<?=$job_id;?>"  class="general_inp" disabled/>
                            <?if($nachisleno > $oklad*1.05){echo $warning_1;}?>
                            <?//=calc_nachisleno($hours, $oklad_hour, $boln, $work_time, $socoklad_hour, $progul_multa, $progul);?>
                        </td>

                        <td align=left><nobr><input type="text" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="sdelka" value="<?$sdelka=$rpt[$job_id]['sdelka']; echo $sdelka;?>" id="sdelka_<?=$job_id;?>"  class="general_inp" onchange="save_monthly_report('<?=$job_id;?>', 'sdelka_<?=$job_id;?>')"/>
                                <span style="white-space: nowrap;word-wrap: normal;"><img src="../../i/refresh.png" width="16" height="16" alt="" onclick="n_get_sdelka('<?=$us['uid'];?>','<?=$job_id;?>', 'get_sdelka')" id="load_img_<?=$job_id;?>" style="cursor:pointer;">
								<?php if ($us['user_department']==3){
									//?dop1=1&dat=6&mang=249&year=2022
									?>
									<a href="../../stat/stat_table_query.php?year=<?=$year;?>&dat=<?=$month;?>&dop1=1&mang=<?=$us['uid'];?>" target="_blank"><img src="../../../i/table.png" width="16" height="16" alt="" id="table_sdelka_link_<?=$job_id;?>" style="display: <? if($sdelka > "0"){?>display: inline;<?}else{?>none<?}?>"></a>
									<?php
								}else{?>
								<a href="../count/index.php?year=<?=$year;?>&month=<?=$month;?>&num_sotr=<?=$job_id;?>" target="_blank"><img src="../../../i/table.png" width="16" height="16" alt="" id="table_sdelka_link_<?=$job_id;?>" style="display: <? if($sdelka > "0"){?>display: inline;<?}else{?>none<?}?>"></a>
								<?php } ?>
								</span>
                            </nobr>
                        </td>
                        <td align=center>
							<!-- <input type="text" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="procee" value="" id="procee_<?=$job_id;?>"  class="general_inp" onchange="save_monthly_report('<?=$job_id;?>', 'procee_<?=$job_id;?>')"/> -->
                          <a href="#" class='a-none-dec' onclick="toggleOtherPopup('open', <?= $us['uid']; ?>, <?= $job_id; ?>); return false;" data-other-amount="<?= $us['uid']; ?>"><?= $procee ?></a>
                          <input type="hidden" name="user_other" value="<?= $procee ?>" id="procee_<?= $job_id; ?>" data-user-other="<?= $us['uid']; ?>" class="general_inp" disabled />
                          <img src="../../i/plus.gif" onclick="toggleOtherPopup('open', <?= $us['uid']; ?>, <?= $job_id; ?>); return false;" class="add_other_icon" />
                        </td>
                        <td align=center>
                            <a href="#" class='a-none-dec' onclick="toggleFinesPopup('open', <?= $us['uid']; ?>, <?= $job_id; ?>); return false;" data-fine-amount="<?= $us['uid']; ?>"><?= $fine_amount ?></a>
                            <input type="hidden" name="user_fines" value="<?= $fine_amount ?>" id="user_fines_<?= $job_id; ?>" data-user-fines="<?= $us['uid']; ?>" class="general_inp" disabled />
                            <img src="../../i/plus.gif" onclick="toggleFinesPopup('open', <?= $us['uid']; ?>, <?= $job_id; ?>); return false;" class="add_fine_icon" />
                        </td>
                        <td align=center style="width:100px;background-color: #DDFFCC;"><input type="text" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="itogo" value="<?=$nachisleno+$sdelka+$procee;?>" id="itogo_<?=$job_id;?>"  class="general_inp" disabled/>
                            <?
                            if($type == "proizvodstvo"){
                                $itog_sdelka = $sdelka+$procee;
								//$itog_sdelka = $sdelka+$procee_rez;
                            }?>
                        </td>

                        <td name="pay1_col">
                            <input type="checkbox" class="pay_sum" onchange="pay_sum_total()" value="pay1_<?=$job_id;?>"/>
                            <input type="hidden" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="pay1"  value="<?echo $pay1;?>" id="pay1_<?=$job_id;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'pay1_<?=$job_id;?>');pay_sum_total();" class="pay_inp"/>
                            <input type="hidden" size=8 id="pay1date_<?=$job_id;?>" value="<?$pay1date=$rpt[$job_id]['pay1date']; echo $pay1date;?>" onchange="save_monthly_report('<?=$job_id;?>', 'pay1date_<?=$job_id;?>')" />
                            <!--<img src="/acc/i/calendar.gif" alt="" size=11 style="cursor:pointer;opacity:<?if($pay1date == "0000-00-00" or $pay1date == ""){?>0.2<?}else{?>0.8<?}?>;" id="pay1date_<?=$job_id;?>_img"  onmouseover="Tip('<?echo $pay1date;?>', PADDING, 5)"/>
                            <img src="/i/del_sm.png" alt="" id="del_pay1date_<?=$job_id;?>" style="cursor:pointer;display:<?if($pay1date == "0000-00-00" or $pay1date == ""){echo "none";}else{echo "inline";}?>;" onclick="del_paydate('pay1date', '<?=$job_id;?>')" />
                            <i class="fa fa-comment-o" style="cursor:pointer;opacity:0.5;vertical-align: top; display:none"></i>
							-->
							<a href="#" class='a-none-dec' onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,1); return false;"  id="pay1_text_<?= $job_id; ?>">
							<? echo $pay1;?>
							</a>
                             <!--<input type="hidden" name="user_fines" value="<?= $fine_amount ?>" id="user_fines_<?= $job_id; ?>" data-user-fines="<?= $us['uid']; ?>" class="general_inp" disabled />-->
                            <img src="../../i/plus.gif" onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,1); return false;" class="add_fine_icon" />
						
						</td>
                        <td name="pay2_col">
                            <input type="checkbox" class="pay_sum" onchange="pay_sum_total()" value="pay2_<?=$job_id;?>"/>
                            <input type="hidden" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="pay2"  value="<?echo $pay2;?>" id="pay2_<?=$job_id;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'pay2_<?=$job_id;?>');pay_sum_total();" class="pay_inp"/>
                            <input type="hidden" size=8 id="pay2date_<?=$job_id;?>" value="<?$pay2date=$rpt[$job_id]['pay2date']; echo $pay2date;?>" onchange="save_monthly_report('<?=$job_id;?>', 'pay2date_<?=$job_id;?>')" />
                            <!--<img src="/acc/i/calendar.gif" alt="" size=11 style="cursor:pointer;opacity:<?if($pay2date == "0000-00-00" or $pay2date == ""){?>0.2<?}else{?>0.8<?}?>;" id="pay2date_<?=$job_id;?>_img" onmouseover="Tip('<?echo $pay2date;?>', PADDING, 5)"/>
                            <img src="/i/del_sm.png" alt="" id="del_pay2date_<?=$job_id;?>" style="cursor:pointer;display:<?if($pay2date == "0000-00-00" or $pay2date == ""){echo "none";}else{echo "inline";}?>;" onclick="del_paydate('pay2date', '<?=$job_id;?>')" />
                            <i class="fa fa-comment" style="cursor:pointer;opacity:0.5;vertical-align: top; display:none"></i>
							-->
							<a href="#" class='a-none-dec' onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,2); return false;"  id="pay2_text_<?= $job_id; ?>">
							<? echo $pay2;?>
							</a>
                             <!--<input type="hidden" name="user_fines" value="<?= $fine_amount ?>" id="user_fines_<?= $job_id; ?>" data-user-fines="<?= $us['uid']; ?>" class="general_inp" disabled />-->
							 <img src="../../i/plus.gif" onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,2); return false;" class="add_fine_icon" />
                        </td>
                        <td name="pay3_col">
                            <input type="checkbox" class="pay_sum" onchange="pay_sum_total()" value="pay3_<?=$job_id;?>"/>
                            <input type="hidden" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="pay3"  value="<?echo $pay3;?>" id="pay3_<?=$job_id;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'pay3_<?=$job_id;?>');pay_sum_total();" class="pay_inp"/>
                            <input type="hidden" size=8 id="pay3date_<?=$job_id;?>" value="<?$pay3date=$rpt[$job_id]['pay3date']; echo $pay3date;?>" onchange="save_monthly_report('<?=$job_id;?>', 'pay3date_<?=$job_id;?>')" />
                            <!--<img src="/acc/i/calendar.gif" alt="" size=11 style="cursor:pointer;opacity:<?if($pay3date == "0000-00-00" or $pay3date == ""){?>0.2<?}else{?>0.8<?}?>;" id="pay3date_<?=$job_id;?>_img" onmouseover="Tip('<?echo $pay3date;?>', PADDING, 5)"/>
                            <img src="/i/del_sm.png" alt="" id="del_pay3date_<?=$job_id;?>" style="cursor:pointer;display:<?if($pay3date == "0000-00-00" or $pay3date == ""){echo "none";}else{echo "inline";}?>;" onclick="del_paydate('pay3date', '<?=$job_id;?>')" />
							-->
							<a href="#" class='a-none-dec' onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,3); return false;" id="pay3_text_<?= $job_id; ?>">
							<? echo $pay3;?>
							</a>
                             <!--<input type="hidden" name="user_fines" value="<?= $fine_amount ?>" id="user_fines_<?= $job_id; ?>" data-user-fines="<?= $us['uid']; ?>" class="general_inp" disabled />-->
							 <img src="../../i/plus.gif" onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,3); return false;" class="add_fine_icon" />
					   </td>
                        <td name="pay4_col">
                            <input type="checkbox" class="pay_sum" onchange="pay_sum_total()" value="pay4_<?=$job_id;?>"/>
                            <input type="hidden" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="pay4"  value="<? echo $pay4;?>" id="pay4_<?=$job_id;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'pay4_<?=$job_id;?>');pay_sum_total();" class="pay_inp"/>
                            <input type="hidden" size=8 id="pay4date_<?=$job_id;?>" value="<?$pay4date=$rpt[$job_id]['pay4date']; echo $pay4date;?>" onchange="save_monthly_report('<?=$job_id;?>', 'pay4date_<?=$job_id;?>')" />
                            <!--<img src="/acc/i/calendar.gif" alt="" size=11 style="cursor:pointer;opacity:<?if($pay4date == "0000-00-00" or $pay4date == ""){?>0.2<?}else{?>0.8<?}?>;" id="pay4date_<?=$job_id;?>_img" onmouseover="Tip('<?echo $pay4date;?>', PADDING, 5)"/>
                            <img src="/i/del_sm.png" alt="" id="del_pay4date_<?=$job_id;?>" style="cursor:pointer;display:<?if($pay4date == "0000-00-00" or $pay4date == ""){echo "none";}else{echo "inline";}?>;" onclick="del_paydate('pay4date', '<?=$job_id;?>')" />
							-->
							<a href="#" class='a-none-dec' onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,4); return false;"  id="pay4_text_<?= $job_id; ?>">
							<? echo $pay4;?>
							</a>
                             <!--<input type="hidden" name="user_fines" value="<?= $fine_amount ?>" id="user_fines_<?= $job_id; ?>" data-user-fines="<?= $us['uid']; ?>" class="general_inp" disabled />-->
							 <img src="../../i/plus.gif" onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,4); return false;" class="add_fine_icon" />
						</td>
                        <td name="pay5_col">
                            <input type="checkbox" class="pay_sum" onchange="pay_sum_total()" value="pay5_<?=$job_id;?>"/>
                            <input type="hidden" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="pay5"  value="<? echo $pay5;?>" id="pay5_<?=$job_id;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'pay5_<?=$job_id;?>');pay_sum_total();" class="pay_inp"/>
                            <input type="hidden" size=8 id="pay5date_<?=$job_id;?>" value="<?$pay5date=$rpt[$job_id]['pay5date']; echo $pay5date;?>" onchange="save_monthly_report('<?=$job_id;?>', 'pay5date_<?=$job_id;?>')" />
                            <!--<img src="/acc/i/calendar.gif" alt="" size=11 style="cursor:pointer;opacity:<?if($pay5date == "0000-00-00" or $pay5date == ""){?>0.2<?}else{?>0.8<?}?>;" id="pay5date_<?=$job_id;?>_img" onmouseover="Tip('<?echo $pay5date;?>', PADDING, 5)"/>
                            <img src="/i/del_sm.png" alt="" id="del_pay5date_<?=$job_id;?>" style="cursor:pointer;display:<?if($pay5date == "0000-00-00" or $pay5date == ""){echo "none";}else{echo "inline";}?>;" onclick="del_paydate('pay5date', '<?=$job_id;?>')" />
							-->
							<a href="#" class='a-none-dec' onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,5); return false;" id="pay5_text_<?= $job_id; ?>">
							<? echo $pay5;?>
							</a>
                             <!--<input type="hidden" name="user_fines" value="<?= $fine_amount ?>" id="user_fines_<?= $job_id; ?>" data-user-fines="<?= $us['uid']; ?>" class="general_inp" disabled />-->
							 <img src="../../i/plus.gif" onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,5); return false;" class="add_fine_icon" />
						
						</td>
                        <td name="pay6_col">
                            <input type="checkbox" class="pay_sum" onchange="pay_sum_total()" value="pay6_<?=$job_id;?>"/>
                            <input type="hidden" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="pay6"  value="<? echo $pay6;?>" id="pay6_<?=$job_id;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'pay6_<?=$job_id;?>');pay_sum_total();" class="pay_inp"/>
                            <input type="hidden" size=8 id="pay6date_<?=$job_id;?>" value="<?$pay6date=$rpt[$job_id]['pay6date']; echo $pay6date;?>" onchange="save_monthly_report('<?=$job_id;?>', 'pay6date_<?=$job_id;?>')" />
                            <!--<img src="/acc/i/calendar.gif" alt="" size=11 style="cursor:pointer;opacity:<?if($pay6date == "0000-00-00" or $pay6date == ""){?>0.2<?}else{?>0.8<?}?>;" id="pay6date_<?=$job_id;?>_img" onmouseover="Tip('<?echo $pay6date;?>', PADDING, 5)"/>
                            <img src="/i/del_sm.png" alt="" id="del_pay6date_<?=$job_id;?>" style="cursor:pointer;display:<?if($pay6date == "0000-00-00" or $pay6date == ""){echo "none";}else{echo "inline";}?>;" onclick="del_paydate('pay6date', '<?=$job_id;?>')" />
							-->
							<a href="#" class='a-none-dec' onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,6); return false;"  id="pay6_text_<?= $job_id; ?>">
							<? echo $pay6;?>
							</a>
                             <!--<input type="hidden" name="user_fines" value="<?= $fine_amount ?>" id="user_fines_<?= $job_id; ?>" data-user-fines="<?= $us['uid']; ?>" class="general_inp" disabled />-->
							 <img src="../../i/plus.gif" onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,6); return false;" class="add_fine_icon" />
						</td>

                        <td name="pay7_col">
                            <input type="checkbox" class="pay_sum" onchange="pay_sum_total()" value="pay7_<?=$job_id;?>"/>
                            <input type="hidden" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="pay7"  value="<? echo $pay7;?>" id="pay7_<?=$job_id;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'pay7_<?=$job_id;?>');pay_sum_total();" class="pay_inp"/>
                            <input type="hidden" size=8 id="pay7date_<?=$job_id;?>" value="<?$pay7date=$rpt[$job_id]['pay7date']; echo $pay7date;?>" onchange="save_monthly_report('<?=$job_id;?>', 'pay7date_<?=$job_id;?>')" />
                            <!--<img src="/acc/i/calendar.gif" alt="" size=11 style="cursor:pointer;opacity:<?if($pay7date == "0000-00-00" or $pay7date == ""){?>0.2<?}else{?>0.8<?}?>;" id="pay7date_<?=$job_id;?>_img" onmouseover="Tip('<?echo $pay7date;?>', PADDING, 5)"/>
                            <img src="/i/del_sm.png" alt="" id="del_pay7date_<?=$job_id;?>" style="cursor:pointer;display:<?if($pay7date == "0000-00-00" or $pay7date == ""){echo "none";}else{echo "inline";}?>;" onclick="del_paydate('pay7date', '<?=$job_id;?>')" />
							-->
							<a href="#" class='a-none-dec' onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,7); return false;"  id="pay7_text_<?= $job_id; ?>">
							<? echo $pay7;?>
							</a>
                            <!--<input type="hidden" name="user_fines" value="<?= $fine_amount ?>" id="user_fines_<?= $job_id; ?>" data-user-fines="<?= $us['uid']; ?>" class="general_inp" disabled />-->
							<img src="../../i/plus.gif" onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,7); return false;" class="add_fine_icon" />
						</td>

                        <td name="pay8_col">
                            <input type="checkbox" class="pay_sum" onchange="pay_sum_total()" value="pay8_<?=$job_id;?>"/>
                            <input type="hidden" onkeyup="this.value=replace_num(this.value);" autocomplete="off" name="pay8"  value="<?echo $pay8;?>" id="pay8_<?=$job_id;?>"  onchange="save_monthly_report('<?=$job_id;?>', 'pay8_<?=$job_id;?>');pay_sum_total();" class="pay_inp"/>
                            <input type="hidden" size=8 id="pay8date_<?=$job_id;?>" value="<?$pay8date=$rpt[$job_id]['pay8date']; echo $pay8date;?>" onchange="save_monthly_report('<?=$job_id;?>', 'pay8date_<?=$job_id;?>')" />
                            <!--<img src="/acc/i/calendar.gif" alt="" size=11 style="cursor:pointer;opacity:<?if($pay8date == "0000-00-00" or $pay8date == ""){?>0.2<?}else{?>0.8<?}?>;" id="pay8date_<?=$job_id;?>_img" onmouseover="Tip('<?echo $pay8date;?>', PADDING, 5)"/>
                            <img src="/i/del_sm.png" alt="" id="del_pay8date_<?=$job_id;?>" style="cursor:pointer;display:<?if($pay8date == "0000-00-00" or $pay8date == ""){echo "none";}else{echo "inline";}?>;" onclick="del_paydate('pay8date', '<?=$job_id;?>')" />
							-->
							<a href="#" class='a-none-dec' onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,8); return false;"  id="pay8_text_<?= $job_id; ?>">
							<? echo $pay8;?>
							</a>
                             <!--<input type="hidden" name="user_fines" value="<?= $fine_amount ?>" id="user_fines_<?= $job_id; ?>" data-user-fines="<?= $us['uid']; ?>" class="general_inp" disabled />-->
							 <img src="../../i/plus.gif" onclick="togglePopupDay('open', <?= $us['uid']; ?>, <?= $job_id; ?>,8); return false;" class="add_fine_icon" />
						</td>

                        <?$job_ids_no_spec = $job_ids_no_spec.",".$job_id; ?>
						<?$new_sdelka_spec=$new_sdelka_spec.",".$us['uid'];?>

                        <script>
                          /*Calendar.setup({
                            inputField     :    "pay1date_<?=$job_id;?>",      // id of the input field
                            button         :    "pay1date_<?=$job_id;?>_img",   // trigger for the calendar (button ID)
                          });*/
                           /*Calendar.setup({
                            inputField     :    "pay2date_<?=$job_id;?>",      // id of the input field
                            button         :    "pay2date_<?=$job_id;?>_img",   // trigger for the calendar (button ID)
                          });*/

						  Calendar.setup({
							  ifFormat       :    "%d.%m.%Y",
							  dateFormat : "%d-%m-%Y",
                            inputField     :    "day_date",      // id of the input field
                            button         :    "day_date"   // trigger for the calendar (button ID)
							
                          });
                        </script>

                        <td align=center name="ostatok_col"><input type="text" name="ostatok" value="<?=($nachisleno+$sdelka+$procee_rez-$fine_amount)-$pay1-$pay2-$pay3-$pay4-$pay5-$pay6-$pay7-$pay8;?>" id="ostatok_<?=$job_id;?>"  class="general_inp" disabled/>
						<?php 
						//echo $nachisleno+$sdelka+$procee_rez+$fine_amount."|";
						//echo ($nachisleno+$sdelka+$procee_rez+$fine_amount)-$pay1-$pay2-$pay3-$pay4-$pay5-$pay6-$pay7-$pay8;
						?>
						</td>
                    </tr>
					<? } ?>
                <?}}?>
            <tr class="tr_itog">
				<td name="cnt_col" class="table_itog" style="width:50px"></td>
                <td name="num_col" class="table_itog" style="width:50px"></td>
				<td name="dept_col"></td>
                <td class="table_itog" style="width:100px">ИТОГО</td>
                <td class="table_itog" id=oklad_itog></td>
                <td name="socoklad_col" class="table_itog" id=socoklad_itog></td>
                <td name="work_time_col" class="table_itog"></td>
                <td name="oklad_hour_col" class="table_itog"></td>
                <td name="socoklad_hour_col" class="table_itog"></td>
                <td name="worked_time_col" class="table_itog" id="worked_time_itog"></td>
                <td name="socnachisl_col" class="table_itog" id="socnachisl_itog"></td>
                <td name="progul_col" class="table_itog" id="progul_itog"></td>
				<td name="procee_rez_col" class="table_itog" id="procee_rez_itog"></td>
                <td name="nachisleno_col" class="table_itog" id="nachisleno_itog"></td>
                <td name="sdelka_col" class="table_itog" style="width:100px" id="sdelka_itog"></td>
                <td name="procee_col" class="table_itog" style="width:100px" id="user_other_itog"></td>
                <td name="user_fines_col" class="table_itog" style="width:100px" id="user_fines_itog"></td>
                <td name="itogo_col" class="table_itog" style="width:100px;background-color: #99FF66;" id="itogo_itog"></td>
                <td name="pay1_col" class="table_itog" id="pay1_itog"></td>
                <td name="pay2_col" class="table_itog" id="pay2_itog"></td>
                <td name="pay3_col" class="table_itog" id="pay3_itog"></td>
                <td name="pay4_col" class="table_itog" id="pay4_itog"></td>
                <td name="pay5_col" class="table_itog" id="pay5_itog"></td>
                <td name="pay6_col" class="table_itog" id="pay6_itog"></td>
                <td name="pay7_col" class="table_itog" id="pay7_itog"></td>
                <td name="pay8_col" class="table_itog" id="pay8_itog"></td>
                <td name="ostatok_col" class="table_itog" id="ostatok_itog"></td>
            </tr>

            </tbody>
        </table>
		</div>
        <? /*if($working_days){ */?>
           <!-- <h2>Сформировать ведомость: </h2>
            <!--<label for="administration" style="cursor:pointer;">администрация:</label> <input type="checkbox" id="administration" value="1"/><br>
            <label for="proizvodstvo" style="cursor:pointer;">производство:</label> <input type="checkbox" id="proizvodstvo" value="1"/><br>
            <label for="nadomniki" style="cursor:pointer;">надомники:</label> <input type="checkbox" id="nadomniki" value="1"/><br>
            <label for="all" style="cursor:pointer;">все:</label> <input type="checkbox" id="all" value="1"/><br>-->
           <!-- дата: <input type="test" size=8 id="date_ved"  />
            <button onclick="generate_vedomost()">сформировать!</button>-->
        <? /*}*/ ?>

    <? } else { ?>  доступ ограничен    <? } ?>

</div>
<div id="pay_sum_div" class="pay_sum_div"></div>
<? //include("auth_form.php"); ?>
<pre>
 <?//print_r ($rpt);?>
 </pre>


<script>
$nav = $('.fixed-div');

	$nav.css('width', $nav.outerWidth());
	$window = $(window);
	$h = $nav.offset().top;
	$window.scroll(function(){
		if ($window.scrollTop() > $h){
			//$nav.addClass('fixed');
			//$(".search_report").addClass('fixed');
			//$(".th_tables").addClass('fixed');
		} else {
			//$nav.removeClass('fixed');
			//$(".search_report").removeClass('fixed');
			//$(".th_tables").removeClass('fixed');
		}
	});
	$('.other_popup_info').draggable({
		start: function() {
            //$('.other_popup').css("width","0%");
			//$('.other_popup').css("height","0%");
        }
	});
	$('.fines_popup_info').draggable({
		start: function() {
            //$('.fines_popup').css("width","0%");
			//$('.fines_popup').css("height","0%");
        }
	});
	$('.day_popup_info').draggable({
		start: function() {
            //$('.day_popup').css("width","0%");
			//$('.day_popup').css("height","0%");
        }
	});
	
  function generate_vedomost() {

    deps = [];
    $('.dep_input').each(function() {
      if ($(this).is(":checked") == true) {
        deps.push($(this).attr('data-dep'));
      }
    });
    deps = deps.join('_');

    /*if($("#administration").is(':checked')){administration = "1"}else{administration = "0"}
    if($("#proizvodstvo").is(':checked')){proizvodstvo = "1"}else{proizvodstvo = "0"}
    if($("#nadomniki").is(':checked')){nadomniki = "1"}else{nadomniki = "0"}
    if($("#all").is(':checked')){all = "1"}else{all = "0"}*/

    date_ved_tmp = $("#date_ved").val().split(".");
	date_ved=date_ved_tmp[2]+"-"+date_ved_tmp[1]+"-"+date_ved_tmp[0];
	filt_pl=$("#day_sp_pay_ved").val();
    document.open('ved.php?department='+deps+'&date='+date_ved+'&tip_p='+filt_pl,"","");
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
	  ifFormat       :    "%d.%m.%Y",
							  dateFormat : "%d-%m-%Y",
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
      procee = $("#procee_rez_"+uid).val();
      user_fines = $("#user_fines_"+uid).val();
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
      pay8 = $("#pay8_"+uid).val();
      pay8date = $("#pay8date_"+uid).val();
    }else{
      work_time=""
      oklad = ""
      socoklad = ""
      sdelka = ""
      nachisleno = ""
      procee=""
      user_fines = "";
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
      pay8 = ""
      pay8date = ""
    }
	obj_izm=obj_id.split("_");
	console.log("obj:"+obj_izm);
    var geturl;
	{
    geturl = $.ajax({
      type: "GET",
      url: 'save_monthly_report.php',
      data : '&row_izm='+obj_id+'&uid='+uid+'&year=<?=$year;?>&month=<?=$month;?>&work_time='+work_time+'&oklad='+oklad+'&nachisleno='+nachisleno+'&socoklad='+socoklad+'&sdelka='+sdelka+'&procee='+procee+'&user_fines='+user_fines+'&pay1='+pay1+'&pay1date='+pay1date+'&pay2='+pay2+'&pay2date='+pay2date+'&pay3='+pay3+'&pay3date='+pay3date+'&pay4='+pay4+'&pay4date='+pay4date+'&pay5='+pay5+'&pay5date='+pay5date+'&pay6='+pay6+'&pay6date='+pay6date+'&pay7='+pay7+'&pay7date='+pay7date+'&pay8='+pay8+'&pay8date='+pay8date+'&working_days='+working_days,
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
	procee_rez = $("#procee_rez_"+uid).val();
    user_fines = $("#user_fines_"+uid).val();
    pay1 = $("#pay1_"+uid).val();
    pay2 = $("#pay2_"+uid).val();
    pay3 = $("#pay3_"+uid).val();
    pay4 = $("#pay4_"+uid).val();
    pay5 = $("#pay5_"+uid).val();
    pay6 = $("#pay6_"+uid).val();
    pay7 = $("#pay7_"+uid).val();
    pay8 = $("#pay8_"+uid).val();
    working_days = $("#working_days").val();
    progul_multa = "<?=$progul_multa;?>"
    working_days_social = "<?=$working_days_social;?>"

    working_days_social=working_days_social*1
    sdelka=sdelka*1
    procee=procee*1
	procee_rez=procee_rez*1
    user_fines=user_fines*1
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
    pay8=pay8*1
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

    ostatok = nachisleno+sdelka+procee_rez+procee-user_fines-pay1-pay2-pay3-pay4-pay5-pay6-pay7-pay8
    ostatok = ostatok.toFixed(0)
    $("#ostatok_"+uid).val(ostatok);

    //itogo = nachisleno+sdelka+procee;
	itogo=nachisleno+sdelka+procee_rez+procee;
    itogo = itogo.toFixed(0);
    console.log(nachisleno+"/"+sdelka+"/"+procee);
    $("#itogo_"+uid).val(itogo);

//перезагружаем страницу, если менялось количество рабочих дней т.е. пересчитываем все сразу

    if(!uid){
      setTimeout(function(){location.href="?month=<?=$month;?>&year=<?=$year;?>&type=<?=$type;?>"} , 1100);

    }
    sum()
  }


  function sum(){

    var coloumns = ["oklad", "socoklad", "worked_time", "socnachisl", "progul","procee_rez", "nachisleno", "sdelka", "user_other", "user_fines", "itogo", "pay1", "pay2", "pay3", "pay4", "pay5", "pay6", "pay7", "pay8", "ostatok"];
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

    var coloumns_to_hide = ["num_col","dept_col", "socoklad_col", "oklad_hour_col", "socoklad_hour_col", "worked_time_col", "socnachisl_col", "progul_col", "work_time_col","procee_rez_col"];

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
    job_ids = '<?=$new_sdelka_spec;?>';
    jobid = job_ids.split(',');
	job_ids1 = '<?=$job_ids_no_spec;?>';
    jobid1 = job_ids1.split(',');
    for(var i = 0; i < jobid.length; i++)
      if(jobid[i] !== ''){
        n_get_sdelka(jobid[i],jobid1[i], act);
		

      }
//alert(type)
sum()
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

	function n_get_sdelka(id_user,job_id){
		 $.ajax({
      url: '../backend/job_sdelka_n.php',
      data: {
        user_id: id_user,
        month:'<?=$month;?>',
        year:'<?=$year;?>',
		num_sotr:job_id
      },
	   dataType: 'json',
      type: 'GET',
      error: function (xhr, ajaxOptions, thrownError) {
        console.log(xhr.status, thrownError);
      },
      success: function(data) {
		  //console.log('ok'+data.val);
		  $("#sdelka_"+job_id).val(data.val);
		  if (data.dep!='3'){
				  var r=get_sdelka(job_id);
				  console.log(r);
			  }
		  if($("#save_sdelka").prop("checked")){
			  //
			  
              save_monthly_report(job_id, 'sdelka')
			
            }
		h_sum(job_id);
	  }
		 });
	}
	/*
  function get_sdelka(job_id){
    $('#load_img_'+job_id).attr('src', '../../../../i/load.gif');
    var geturl;

    geturl = $.ajax({
      type: "GET",
      //url: '../backend/job_sdelka_n.php',
	  url: '../backend/job_entries.php',
      //data : '&year=<?=$year;?>&month=<?=$month;?>&act=get_sdelka&num_sotr='+job_id+'&items_on_page=10000&user_id='+job_id,
      data : '&year=<?=$year;?>&month=<?=$month;?>&act=get_sdelka&num_sotr='+job_id+'&items_on_page=10000',
	  success: function (data) {

        var resp1 = geturl.responseText

        if (data){

          if(data.val !== "error"){$("#sdelka_"+job_id).val(data.val);

            if($("#save_sdelka").prop("checked")){
              save_monthly_report(job_id, 'sdelka')

            }

            h_sum(job_id)
          }else{alert("Произошла ошибка в файле job_entries. ("+data.val+")")}


          $('#load_img_'+job_id).attr('src', '../../i/refresh.png');
          if(resp1>0)
            $('#table_sdelka_link_'+job_id).show();



        }else{alert("ошибка!" + resp1)}

      }
    })

  }
*/
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
                $path = 'report.php?' . implode('&', $gets) . '&department=';
            } else {
                $path = 'report.php?' . $_SERVER['QUERY_STRING'] . '&department=' ;
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
                  $path = 'report.php?' . implode('&', $gets) . '&department=';
              } else {
                  $path = 'report.php?' . $_SERVER['QUERY_STRING'] . '&department=' ;
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

  /*
  * Popup со штрафами
  */

  // Обновление суммы штрафа в спец полях
  function update_fines_amount(uid) {
    $.ajax({
      url: '/acc/applications/timetable/user_fines.php',
      data: {
        action: 'take_amount',
        uid: uid,
        month:param('month'),
        year:param('year')
      },
      dataType: 'json',
      type: 'GET',
      error: function (xhr, ajaxOptions, thrownError) {
        console.log(xhr.status, thrownError);
      },
      success: function(data) {
        console.log(data);
        if (data.status === 'success') {
          // видимое поле для отображения суммы штрафов
          $('[data-fine-amount="' + uid + '"]').html(data.amount);
          // скрытое поле для подсчета сумм по горизонтали/вертикали
          $('[data-user-fines="' + uid + '"]').val(data.amount);
        } else {
          // error
          if (data.status === 'error') {
            console.log(data.error);
          }
        }
      }
    });
  }

  // Заполнение таблицы
  function fillFinesTable(user_id, job_id, data) {
    var table = $('<table/>');
    var tr = '';

    // Имя сотрудника
    var username = $('#username_' + user_id).text();
    tr = $('<tr/>');
    tr.append($('<th colspan="3" />').text(username));
    table.append(tr);

    // Причина штрафа + сумма
    tr = $('<tr/>');
    tr.append($('<th/>').text('Причина'))
      .append($('<th/>').text('Сумма'))
      .append($('<th/>').text('Действие'));
    table.append(tr);

    // Список штрафов
    $.each(data, function(index, element) {
      var fineTr = $('<tr/>');
      fineTr
        .append($('<td/>').attr('name', 'new_fine_reason').text(element.reason))
        .append($('<td/>').attr('name', 'new_fine_amount').text(element.amount))
        .append($('<td/>').html(
          $('<img />').attr({
            src: '/acc/i/del.gif',
            'class': 'fines_popup_delete',
            'data-id': element.id,
            'data-uid': user_id,
            'data-job-id': job_id,
            'title': 'Удалить'
          })
        ));
      table.append(fineTr);
    });

    // Добавление нового штрафа
    tr = $('<tr/>');
    tr.append($('<td/>').html('<input type="text" name="reason" autocomplete="off" placeholder="Причина штрафа">'))
      .append($('<td/>').html('<input type="text" name="amount" autocomplete="off" placeholder="Сумма штрафа" onkeyup="this.value=replace_num(this.value);">'))
      .append($('<td/>').html(''))
      .append($('<input/>').attr({
        type: 'hidden',
        name: 'fine_data',
        'data-uid': user_id,
        'data-job-id': job_id
      }));

    table.append(tr);

    return table;
  }
  //
  function param(name){
		var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
		if (results == null) {
			return 0;
		}
		return results[1] || 0;
	}
  //
  // Вывод таблицы
  function printFinesTable(user_id, job_id) {
    var container = $('.fines_popup');
    var loader = $('.loading', container);
    $.ajax({
      url: '/acc/applications/timetable/user_fines.php',
      data: { uid: user_id ,month:param('month'),year:param('year')},
      type: 'GET',
      dataType: 'json',
      beforeSend: function(xhr) {
        loader.show();
      },
      error: function (xhr, ajaxOptions, thrownError) {
        loader.hide();
        console.log(xhr.status, thrownError);
      },
      success: function(data){
        loader.hide();
        container.show();
        var content = $('.fines_popup_content');
        // Сброс
        content.html('');

        // Заполнение таблицы
        var table = fillFinesTable(user_id, job_id, data);

        content.append(table);
      }
    });
  }

  function toggleFinesPopup(action, user_id, job_id) {
    user_id = user_id | null;
    job_id = job_id | null;

    switch (action) {
      case 'close':
		$('.fines_popup').css("width","100%");
			$('.fines_popup').css("height","100%");
        $('.fines_popup').attr('style', 'display: none;');
        break;
      case 'open':
        printFinesTable(user_id, job_id);
        break;
    }
  }
  //
  
  // Добавление нового штрафа
  function newFine(buttonLink) {
    var data = $('[name="fine_data"]', '.fines_popup_content');
    var reason = $('[name="reason"]', '.fines_popup_content');
    var amount = $('[name="amount"]', '.fines_popup_content');
    var button = $(buttonLink);

        // Значения полей
        var uid = data.data('uid');
        var job_id = data.data('job-id');
        var reasonVal = $.trim(reason.val());
        var amountVal = $.trim(amount.val());

        if ($.trim(uid) === '') {
          alert('Error! User id is empty');
          return false;
        }

        if (reasonVal === '') {
          reason.css('border-color', 'red');
          console.log('reason is empty');
          return false;
        } else {
          reason.css('border-color', '');
        }

        if (amountVal === '') {
          amount.css('border-color', 'red');
          console.log('amount is empty');
          return false;
        } else {
          amount.css('border-color', '');
        }

    $.ajax({
      url: '/acc/applications/timetable/save_user_fine.php',
      data: {
        uid: uid,
        reason: reasonVal,
        amount: amountVal,
        month: <?= $month ?>,
        year: <?= $year ?>
      },
      dataType: 'json',
      type: 'POST',
      beforeSend: function(xhr) {
        button.prop('disabled', true);
      },
      error: function (xhr, ajaxOptions, thrownError) {
        button.prop('disabled', false);
        console.log(xhr.status, thrownError);
      },
      success: function(data) {
        button.prop('disabled', false);

        // Возникла ошибка
        if (data.status === 'error') {
          console.log('Error: ', data.error);
          return false;
        }

        // Обновление суммы штрафа
        update_fines_amount(uid);
        // Закрываем popup
        //toggleFinesPopup('close');
        // Обновление данных (сумма по горизонтали и вертикали)
		printFinesTable(uid, job_id);
        save_monthly_report(job_id, 'user_fines_'+job_id);
      }
    });
  }

  /* Удаление штрафа */
  $('.fines_popup_content').on('click', '.fines_popup_delete', function () {
    var target = $(this);
    var id = target.data('id');
    var user_id = target.data('uid');
    var job_id = target.data('job-id');

    $.get('/acc/applications/timetable/delete_user_fine.php', {id: id})
      .done(function(result) {
        if (result === 'success') {
          // Обновление суммы штрафа
          update_fines_amount(user_id);
          // Вывод обновленной таблицы
          printFinesTable(user_id, job_id);
          // Обновление данных (сумма по горизонтали и вертикали)
          save_monthly_report(job_id, 'user_fines_'+job_id);
        } else {
          // error
          console.log(result);
        }
      })
      .fail(function() {
        console.log('system error');
      });
  });

  additional_fld('hide');
  //alert('<?=$month;?>')

  /* прочее */
  // Обновление суммы штрафа в спец полях
  function update_other_amount(uid) {
    $.ajax({
      url: '/acc/applications/timetable/user_other.php',
      data: {
        action: 'take_amount',
        uid: uid,
        month:param('month'),
        year:param('year')
      },
      dataType: 'json',
      type: 'GET',
      error: function (xhr, ajaxOptions, thrownError) {
        console.log(xhr.status, thrownError);
      },
      success: function(data) {
        console.log(data);
        if (data.status === 'success') {
          // видимое поле для отображения суммы штрафов
          $('[data-other-amount="' + uid + '"]').html(data.amount);
          // скрытое поле для подсчета сумм по горизонтали/вертикали
          $('[data-user-other="' + uid + '"]').val(data.amount);
        } else {
          // error
          if (data.status === 'error') {
            console.log(data.error);
          }
        }
      }
    });
  }
  //заполнение "прочего"
  function fillOtherTable(user_id, job_id, data) {
    var table = $('<table/>');
    var tr = '';

    // Имя сотрудника
    var username = $('#username_' + user_id).text();
    tr = $('<tr/>');
    tr.append($('<th colspan="5" />').text(username));
    table.append(tr);

    // Причина штрафа + сумма
    tr = $('<tr/>');
    tr.append($('<th/>').text('Причина'))
      .append($('<th/>').text('Сумма'))
	  .append($('<th/>').text('Кто'))
	  .append($('<th/>').text('Дата'))
      .append($('<th/>').text('Действие'));
    table.append(tr);

    // Список штрафов
	
    $.each(data, function(index, element) {
		console.log(element);
      var fineTr = $('<tr/>');
      fineTr
        .append($('<td/>').attr('name', 'new_other_reason').text(element.reason))
        .append($('<td/>').attr('name', 'new_other_amount').text(element.amount))
		.append($('<td/>').attr('name', 'new_other_user').text(element.name))
		.append($('<td/>').attr('name', 'new_other_dat_add').text(element.date_add))
        .append($('<td/>').html(
          $('<img />').attr({
            src: '/acc/i/del.gif',
            'class': 'other_popup_delete',
            'data-id': element.id,
            'data-uid': user_id,
            'data-job-id': job_id,
            'title': 'Удалить'
          })
        ));
      table.append(fineTr);
    });

    // Добавление нового штрафа
    tr = $('<tr/>');
    tr.append($('<td/>').html('<input type="text" name="reason" autocomplete="off" placeholder="Комментарий">'))
      .append($('<td/>').html('<input type="text" name="amount" autocomplete="off" placeholder="Сумма начисления" onkeyup="this.value=replace_num(this.value);">'))
      .append($('<td/>').html(''))
	  .append($('<td/>').html(''))
	  .append($('<td/>').html(''))
      .append($('<input/>').attr({
        type: 'hidden',
        name: 'other_data',
        'data-uid': user_id,
        'data-job-id': job_id
      }));

    table.append(tr);

    return table;
  }
  //вывод "прочего"
  function printOtherTable(user_id, job_id) {
    var container = $('.other_popup');
    var loader = $('.loading', container);

    $.ajax({
      url: '/acc/applications/timetable/user_other.php',
      data: { uid: user_id,month:param('month'),year:param('year') },
      type: 'GET',
      dataType: 'json',
      beforeSend: function(xhr) {
        loader.show();
      },
      error: function (xhr, ajaxOptions, thrownError) {
        loader.hide();
        console.log(xhr.status, thrownError);
      },
      success: function(data){
        loader.hide();
        container.show();
        var content = $('.other_popup_content');
        // Сброс
        content.html('');

        // Заполнение таблицы
        var table = fillOtherTable(user_id, job_id, data);

        content.append(table);
      }
    });
  }
  //открытие/скрытие прочего
  function toggleOtherPopup(action, user_id, job_id) {
    user_id = user_id | null;
    job_id = job_id | null;

    switch (action) {
      case 'close':
	  $('.other_popup').css("width","100%");
			$('.other_popup').css("height","100%");
        $('.other_popup').attr('style', 'display: none;');
		 
        break;
      case 'open':
        printOtherTable(user_id, job_id);
        break;
    }
  }

  //Добавление "прочего"
  function newOther(buttonLink) {
    var data = $('[name="other_data"]', '.other_popup_content');
    var reason = $('[name="reason"]', '.other_popup_content');
    var amount = $('[name="amount"]', '.other_popup_content');
	//var other_add_user = $('[name="other_add_user"]', '.other_popup_content');
    var button = $(buttonLink);

    // Значения полей
    var uid = data.data('uid');
    var job_id = data.data('job-id');
    var reasonVal = $.trim(reason.val());
    var amountVal = $.trim(amount.val());

    if ($.trim(uid) === '') {
      alert('Error! User id is empty');
      return false;
    }

    if (reasonVal === '') {
      reason.css('border-color', 'red');
      console.log('reason is empty');
      return false;
    } else {
      reason.css('border-color', '');
    }

    if (amountVal === '') {
      amount.css('border-color', 'red');
      console.log('amount is empty');
      return false;
    } else {
      amount.css('border-color', '');
    }

    $.ajax({
      url: '/acc/applications/timetable/save_user_other.php',
      data: {
        uid: uid,
        reason: reasonVal,
        amount: amountVal,
        month: <?= $month ?>,
        year: <?= $year ?>
      },
      dataType: 'json',
      type: 'POST',
      beforeSend: function(xhr) {
        button.prop('disabled', true);
      },
      error: function (xhr, ajaxOptions, thrownError) {
        button.prop('disabled', false);
        console.log(xhr.status, thrownError);
      },
      success: function(data) {
        button.prop('disabled', false);

        // Возникла ошибка
        if (data.status === 'error') {
          console.log('Error: ', data.error);
          return false;
        }

        // Обновление суммы штрафа
        update_other_amount(uid);
        // Закрываем popup
        //toggleOtherPopup('close');
		printOtherTable(uid,job_id);
        // Обновление данных (сумма по горизонтали и вертикали)
        save_monthly_report(job_id, 'user_other_'+job_id);
      }
    });
  }
  //Удаление "прочего"
  $('.other_popup_content').on('click', '.other_popup_delete', function () {
    var target = $(this);
    var id = target.data('id');
    var user_id = target.data('uid');
    var job_id = target.data('job-id');

    $.get('/acc/applications/timetable/delete_user_other.php', {id: id})
      .done(function(result) {
        if (result === 'success') {
          // Обновление суммы штрафа
          update_other_amount(user_id);
          // Вывод обновленной таблицы
          printOtherTable(user_id, job_id);
          // Обновление данных (сумма по горизонтали и вертикали)
          save_monthly_report(job_id, 'user_other_'+job_id);
        } else {
          // error
          console.log(result);
        }
      })
      .fail(function() {
        console.log('system error');
      });
  });
	//day 
	function togglePopupDay(action,user_id,job_id,day){
	  user_id = user_id | null;
    job_id = job_id | null;
	day = day | null;
    switch (action) {
      case 'close':
		 $('.day_popup').css("width","100%");
		  $('.day_popup').css("height","100%");
        $('.day_popup').attr('style', 'display: none;');
        break;
      case 'open':
        printDayTable(user_id, job_id,day);
        break;
	}
  }
	function fillDayTable(){
		//заполнение
		
	}
	//функция вывода popup day 
	function printDayTable(user_id, job_id,day){
		  var container_day = $('.day_popup');
    var loader = $('.loading', container_day);

    $.ajax({
      url: '/acc/applications/timetable/user_day.php',
      data: { uid: job_id,month:param('month'),year:param('year'),day:day },
      type: 'GET',
      dataType: 'json',
      beforeSend: function(xhr) {
        loader.show();
      },
      error: function (xhr, ajaxOptions, thrownError) {
        loader.hide();
        console.log(xhr.status, thrownError);
      },
      success: function(data){
        loader.hide();
        container_day.show();
        var content = $('.day_popup_content');
		
        // Сброс данных popup
		$("#day_summa").val(0);
		let Datas = new Date();
		mont=Datas.getMonth()+1;
		$("#day_date").val(Datas.getDate()+"."+mont+"."+Datas.getFullYear());
		$('#day_sp_pay option[value=2]').prop('selected', true);
        //content.html('');
		//устанавливаем значения 
		$("#day_text").text(day);
		if (data.amount!="0"){
			$("#day_summa").val(data.amount).focus();
		}else{
			$("#day_summa").val("").focus();
		}
		if (data.date!="00.00.0000"){
			$("#day_date").val(data.date);
		}else{
			mont=Datas.getMonth()+1;
			$("#day_date").val(Datas.getDate()+"."+mont+"."+Datas.getFullYear());
		}
		$("#day_uid").val(job_id);
		$(".comment_day").val(data.comment);
		if (data.tip_pay!=null){
			$('#day_sp_pay option[value='+data.tip_pay+']').prop('selected', true);
			$("#btn_delet").show();
		}else{
			let tip_pay_null=null;
			if (data.tip_load==0){
				tip_pay_null=6;
			}else{
				tip_pay_null=2;
			}
			
			$('#day_sp_pay option[value='+tip_pay_null+']').prop('selected', true);
			$("#btn_delet").hide();
		}
		let month=param('month');
		if (month<=9){
			if (param('month').indexOf('0')==-1){
				month='0'+month;
			}
		}
		$("#day_id_report").val(param('year')+"-"+month+"-"+job_id);
		//доп запрос для уточнения ФИО user (который изменил оплату)
		 $.ajax({
			  url: '/acc/applications/timetable/load_user_izm.php',
			  data: { id_acc: data.id_acc },
			  type: 'POST',
			  success: function(htmlresult){
				  $("#user_izm_vid").html(htmlresult);
			  }
		 });
      }
    });
	}
	//функция отправки данных с popup day 
	function IzmDays(){
		//сбор всех данных в массив (для удобства)
		let arr_day = [];
		arr_day[0]=$("#day_uid").val();
		arr_day[1]=$("#day_id_report").val();
		arr_day[2]=$("#day_text").text();
		arr_day[3]=$("#day_summa").val();
		arr_day[4]=$("#day_date").val();
		arr_day[5]=$("#day_sp_pay").val();
		arr_day[6]=$(".comment_day").val();
		//console.log(arr_day);
		 $.ajax({
		  url: '/acc/applications/timetable/user_day_save.php',
		  data: { datas:arr_day},
		  type: 'POST',
		  dataType: 'json',
		  error: function (xhr, ajaxOptions, thrownError) {
			console.log(xhr.status, thrownError);
		  },
		  success: function(data){
				let dat_format=arr_day[4].split(".");
				
				//если успех ,то изменяем дату и сумму (для обновления подсчётов через save_monthly_report)
				$("#pay"+arr_day[2]+"_text_"+arr_day[0]).text(arr_day[3]);
				//console.log("#pay"+arr_day[2]+"_text_"+arr_day[0]);
				$("#pay"+arr_day[2]+"date_"+arr_day[0]).val(dat_format[2]+"-"+dat_format[1]+"-"+dat_format[0]);
				$("#pay"+arr_day[2]+"_"+arr_day[0]).val(arr_day[3]);
				save_monthly_report(arr_day[0], 'pay'+arr_day[2]+'date_'+arr_day[0]); 
		  }
		 });
		togglePopupDay('close');
	}
	//функция удаления оплаты с дня
	function delDays(){
		let day_uid=$("#day_uid").val();
		let day_text=$("#day_text").text();
		let day_id_report=$("#day_id_report").val();
		 $.ajax({
		  url: '/acc/applications/timetable/user_day_delete.php',
		  data: { uid:day_uid,id_report:day_id_report,day:day_text},
		  type: 'POST',
		  dataType: 'json',
		  error: function (xhr, ajaxOptions, thrownError) {
			console.log(xhr.status, thrownError);
		  },
		  success: function(data){
			  if (data.status=="ok"){
					
					let day_summa=0;
					//если успех ,то изменяем дату и сумму (для обновления подсчётов через save_monthly_report)
					$("#pay"+day_text+"_text_"+day_uid).text(day_summa);
					//console.log("#pay"+arr_day[2]+"_text_"+arr_day[0]);
					$("#pay"+day_text+"date_"+day_uid).val("0000-00-00");
					$("#pay"+day_text+"_"+day_uid).val(day_summa);
					save_monthly_report(day_uid, 'pay'+day_text+'date_'+day_uid);
					togglePopupDay('close');
			  }else{
				  alert("Ошибка удаления");
			  }
		  }
		 });
	}
	//функция для поиска 
	let flag_poisk=0;//флаг для возвращения фул страницы
  function search_report_name1(){
	let lengt=$(".input_search_report").val().length;
	if (lengt>=2){
	//var url=location.href;//ловим текущий адрес
	//let urls = new URL(url);//закидываем в объект для удобства 
		//urls.searchParams.set('name', $(".input_search_report").val());//устанавливаем в параметры url "name" значение с поля 
		//window.location.href = urls;//перезагрузка страницы с новым параметром 
		//
		$.ajax({
		  url: '/acc/applications/timetable/search_name.php',
		  data: { name:$(".input_search_report").val(),flag:0,year:<?=$year;?>,month:<?=$month;?>},
		  type: 'GET',
		  dataType: 'html',
		  error: function (xhr, ajaxOptions, thrownError) {
			console.log(xhr.status, thrownError);
		  },
		  success: function(data){
			  $("#table tbody").html(data);
			  
			  sum();
			  additional_fld('hide');
			  flag_poisk=1;
			  //
			  //table_top
			  var url=location.href;
			  let urls = new URL(url);
			  let tek_m=urls.searchParams.get('month');
			  console.log(urls.searchParams.get('month'));
			  if (urls.searchParams.get('month')=='1'){
				  let year=urls.searchParams.get('year');
				  let month=urls.searchParams.get('month');
				  urls.searchParams.set('year',Number(year)-1);
				  urls.searchParams.set('month',12);
			  }else{
				  let month=urls.searchParams.get('month');
				  urls.searchParams.set('month',Number(month)-1);
			  }
			  urls.searchParams.set('name', $(".input_search_report").val());
			  $(".table_top tr td:eq(0)").find('a').attr('href',urls);
			   var url=location.href;
			  let urls1 = new URL(url);
			  urls1.searchParams.set('name', $(".input_search_report").val());
			  if (urls1.searchParams.get('month')=='12'){
				  let year=urls1.searchParams.get('year');
				  let month=urls1.searchParams.get('month');
				  urls1.searchParams.set('year',Number(year)+1);
				  urls1.searchParams.set('month',1);
				  
			  }else{
				  
				  urls1.searchParams.set('month',Number(urls1.searchParams.get('month'))+1);
			  }
			  
				$(".table_top tr td:eq(2)").find('a').attr('href',urls1);
				//тек.мес.
				var url=$(".table_top tr td:eq(1)").find('a').attr('href');
				let urls2 = new URL(url);
				urls2.searchParams.set('name', $(".input_search_report").val());
				$(".table_top tr td:eq(1)").find('a').attr('href',urls2);
				console.log(urls.href);
				urls.searchParams.set('month',tek_m);
				history.pushState(null, null, urls.href);
				
			  //
		   }
		});
		//
	}else{
		if (flag_poisk==1){
			//фул вывод
			$.ajax({
		  url: '/acc/applications/timetable/search_name.php',
		  data: { name:$(".input_search_report").val(),flag:1,year:<?=$year;?>,month:<?=$month;?>},
		  type: 'GET',
		  dataType: 'html',
		  error: function (xhr, ajaxOptions, thrownError) {
			console.log(xhr.status, thrownError);
		  },
		  success: function(data){
			  $("#table tbody").html(data);
			  
			  sum();
			  additional_fld('hide');
			  flag_poisk=0;
			  var url=location.href;
			  let urls = new URL(url);
			   urls.searchParams.delete('name');
			   $(".table_top tr td:eq(0)").find('a').attr('href',urls);
			   $(".table_top tr td:eq(2)").find('a').attr('href',urls);
		   }
		});
		}else{
			$(".input_search_report").css("border-color","red");
			setTimeout(function(){
			$(".input_search_report").css("border-color","grey");
			 }, 5000);
		}
	}
  }
  function search_report_name(){
	  //window.location.href = 'homepage-url';
	  let lengt=$(".input_search_report").val().length;
	if (lengt>=2){
	//var url=location.href;//ловим текущий адрес
	//let urls = new URL(url);//закидываем в объект для удобства 
		//urls.searchParams.set('name', $(".input_search_report").val());//устанавливаем в параметры url "name" значение с поля 
		//window.location.href = urls;//перезагрузка страницы с новым параметром 
		//
		/*
		$.ajax({
		  url: '/acc/applications/timetable/search_name.php',
		  data: { name:$(".input_search_report").val(),flag:0,year:<?=$year;?>,month:<?=$month;?>},
		  type: 'GET',
		  dataType: 'html',
		  error: function (xhr, ajaxOptions, thrownError) {
			console.log(xhr.status, thrownError);
		  },
		  success: function(data){
			  $("#table tbody").html(data);
			  
			  sum();
			  additional_fld('hide');
			  flag_poisk=1;
			  //
			  //table_top
			  var url=location.href;
			  let urls = new URL(url);
			  let tek_m=urls.searchParams.get('month');
			  console.log(urls.searchParams.get('month'));
			  if (urls.searchParams.get('month')=='1'){
				  let year=urls.searchParams.get('year');
				  let month=urls.searchParams.get('month');
				  urls.searchParams.set('year',Number(year)-1);
				  urls.searchParams.set('month',12);
			  }else{
				  let month=urls.searchParams.get('month');
				  urls.searchParams.set('month',Number(month)-1);
			  }
			  urls.searchParams.set('name', $(".input_search_report").val());
			  $(".table_top tr td:eq(0)").find('a').attr('href',urls);
			   var url=location.href;
			  let urls1 = new URL(url);
			  urls1.searchParams.set('name', $(".input_search_report").val());
			  if (urls1.searchParams.get('month')=='12'){
				  let year=urls1.searchParams.get('year');
				  let month=urls1.searchParams.get('month');
				  urls1.searchParams.set('year',Number(year)+1);
				  urls1.searchParams.set('month',1);
				  
			  }else{
				  
				  urls1.searchParams.set('month',Number(urls1.searchParams.get('month'))+1);
			  }
			  
				$(".table_top tr td:eq(2)").find('a').attr('href',urls1);
				//тек.мес.
				var url=$(".table_top tr td:eq(1)").find('a').attr('href');
				let urls2 = new URL(url);
				urls2.searchParams.set('name', $(".input_search_report").val());
				$(".table_top tr td:eq(1)").find('a').attr('href',urls2);
				console.log(urls.href);
				urls.searchParams.set('month',tek_m);
				history.pushState(null, null, urls.href);
				
			  //
		   }
		})*/
		//
		var url=location.href;
			  let urls = new URL(url);
		urls.searchParams.set('name', $(".input_search_report").val());
		window.location.href = urls.href;
	}else{
		if (flag_poisk==1){
			//
			var url=location.href;
			  let urls = new URL(url);
			   urls.searchParams.delete('name');
			   window.location.href = urls.href;
			//
			//фул вывод
			
		}else{
			$(".input_search_report").css("border-color","red");
			setTimeout(function(){
			$(".input_search_report").css("border-color","grey");
			 }, 5000);
		}
	}
  }
  $(".btn_restart").click(function(e){
	  //
	  /*
		$.ajax({
		  url: '/acc/applications/timetable/search_name.php',
		  data: { name:$(".input_search_report").val(),flag:1,year:<?=$year;?>,month:<?=$month;?>},
		  type: 'GET',
		  dataType: 'html',
		  error: function (xhr, ajaxOptions, thrownError) {
			console.log(xhr.status, thrownError);
		  },
		  success: function(data){
			  $("#table tbody").html(data);
			  
			  sum();
			  additional_fld('hide');
			  flag_poisk=0;
			  var url=location.href;
			  let urls = new URL(url);
			   urls.searchParams.delete('name');
			   $(".table_top tr td:eq(0)").find('a').attr('href',urls);
			   $(".table_top tr td:eq(2)").find('a').attr('href',urls);
			   $(".input_search_report").val("");
		   }
		});
		*/
		var url=location.href;
			  let urls = new URL(url);
			   urls.searchParams.delete('name');
			   window.location.href = urls.href;
	  //
  });
  function checkForEnter(e) {
  if (e.keyCode == 13) { 
    search_report_name();
  }
}
  //ловим клик вне 3 всплывающих окон 
  $(document).click(function (e) {
	 if ($(e.target).closest(".day_popup_info").length) {
        // клик внутри элемента
        return;
    }
	if ($(e.target).closest(".fines_popup_info").length) {
		// клик внутри элемента calendar
		return;
	}
	if ($(e.target).closest(".other_popup_info").length) {
		// клик внутри элемента calendar
		return;
	}
	if ($(e.target).closest(".calendar").length) {
		// клик внутри элемента calendar
		return;
	}
	  // клик снаружи элемента
	  togglePopupDay('close');
	  toggleFinesPopup('close');	
	  toggleOtherPopup('close'); 
  });
  //отладка
  //document.addEventListener('click', function(e){
  //console.log('target', e.target);
  //console.log('currentTarget', e.currentTarget);
  //console.log('evt', e);
  //});
  
  //
	$('.tbody td').hover(function() {
		var t = parseInt($(this).index()) + 1;
		$('th:nth-child(' + t + '),.tbody td:nth-child(' + t + ')').addClass('hover');
	}, function() {
		var t = parseInt($(this).index()) + 1;
		$('th:nth-child(' + t + '),.tbody td:nth-child(' + t + ')').removeClass('hover');
	});
  //
  
  //фикс липкой строки для firefox
  var browser=null;
  if((navigator.userAgent.indexOf("Opera") || navigator.userAgent.indexOf('OPR')) != -1 ){
            browser='Opera';
        }
        else if(navigator.userAgent.indexOf("Chrome") != -1 ){
            browser='Chrome';
        }
        else if(navigator.userAgent.indexOf("Safari") != -1){
            browser='Safari';
        }
        else if(navigator.userAgent.indexOf("Firefox") != -1 ){
             browser='Firefox';
        }
        else if((navigator.userAgent.indexOf("MSIE") != -1 ) || (!!document.documentMode == true )){
          browser='IE'; 
        }  
        else{
           browser='unknown';
        }
		if (browser=='Firefox'){
		  var $win = $(window),
		  $table = $('#table'),
		  $thead = $table.children('thead'),
		  $tfoot = $table.children('tfoot'),
		  $caption = $table.children('caption'),
		  $cells = $thead.children().children().add($caption);
			var h_wind=screen.height;
			$(".layer").css('max-height',h_wind-$(".table_top").height()-$(".search_report").height()-240);
		$win.on('scroll', function() {
		  var bottom = $table.position().top +
				$table.height() -
				$thead.height() -
				($tfoot.height() || 0),
			delta = $win.scrollTop() -
				$thead.offset().top +
				$caption.outerHeight(),
			// include border thickness (minus 2)
			vertPos = (delta < 0 || delta > bottom ? 0 : delta - 2);
		  $cells.css("transform", "translate(0px," + vertPos + "px)");
		});
		}
  //end fix
</script>

<pre>
 <? //print_r($rpt); ?>
</pre>

</body>

</html>
