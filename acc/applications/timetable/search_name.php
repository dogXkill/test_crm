<?php
require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
$str = $_SERVER['QUERY_STRING'];
parse_str($str);
require_once("lib.php");
//$year=$_GET['year'];
//$month=$_GET['month'];
/*if ($month<=9){
	if(stristr($month, '0') === FALSE) {
	$month='0'.$month;
	}
}*/
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
		echo "SELECT working_days FROM working_days WHERE year = '$year' AND month = '$month'";
        $working_d = mysql_query("SELECT working_days FROM working_days WHERE year = '$year' AND month = '$month'");
        $working_d = mysql_fetch_array($working_d);
		//print_r($working_d);
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
                $conditions = implode(' OR ', $conditions);
                if (!empty($conditions)) {
                    $n_vstavka .= " AND ( $conditions  )";
                } else {
                    $n_vstavka .= " AND ( user_department = 123456789 )";
                }


            } else {
                $list_access_dep = explode('|', $user_access['list_access_dep']);
                $account_access = array();
                foreach ($list_access_dep as $key => $value) {
                    array_push($account_access, 'user_department = ' . $value);
                }
                $account_access = " AND (" . implode(' OR ', $account_access) . ")";
            }
			//фильтр по имени 
			if (isset($_GET['name']) && $_GET['flag']==0) {
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
					$vstavka_IN = " AND (archive != '1' OR job_id IN($act_ids))";
				}else{
					$vstavka_IN = " AND (archive != '1' )";
				}
                 
//получаем список сотрудниов с базовыми параметрами
//$query = "SELECT uid, job_id, surname, name, doljnost, oklad, socoklad, work_time FROM users WHERE ".$vstavka." ".$vstavka_IN."  AND job_id != '1000' ORDER BY surname ASC";
                $query = "SELECT uid, job_id, surname, name, doljnost, oklad, socoklad, work_time, SUM(uf.amount) fine_amount,user_department";
                $query .= " FROM users LEFT JOIN user_fines uf ON uf.user_id = uid AND uf.fine_year = $year AND uf.fine_month = $month";
                $query .= " WHERE job_id != '1000' $n_vstavka $vstavka_IN $account_access $filter";
                $query .= " GROUP BY uid";
                $query .= " ORDER BY surname ASC";
                echo $query;
                $res = mysql_query($query);
                //echo mysql_error();
				$cnt = 0;
                while($us = mysql_fetch_array($res)) {
					$cnt = $cnt+1;
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
					//echo "<p>Test:".$socoklad."|".$work_time."|".$working_days_social."</p>";
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
                    ?>
                    <tr id="tr_<?=$job_id;?>">
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


                       

                        <td align=center name="ostatok_col"><input type="text" name="ostatok" value="<?=$nachisleno+$sdelka+$procee-$fine_amount-$pay1-$pay2-$pay3-$pay4-$pay5-$pay6-$pay7-$pay8;?>" id="ostatok_<?=$job_id;?>"  class="general_inp" disabled/></td>
                    </tr>
				<?php }?>
				 <tr class="tr_itog">
				<td name="cnt_col" class="table_itog" style="width:50px"></td>
                <td name="num_col" class="table_itog" style="width:50px"></td>
				<td name="dept_col"></td>
                <td class="table_itog" style="width:100px">ИТОГИ</td>
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
				<?php
			}else{
				echo "error";
			}?>