<?
$host = "http://$_SERVER[HTTP_HOST]";
$tek_date = date("Y-m-d");
$vchera = date("Y-m-d",strtotime("-1 day"));
$tek_month = date("Y-m");
$proshl_month = date("Y-m",strtotime("-1 month"));
$statistics_access = $user_access['statistics_access'];
if(!$statistics_access){$statistics_access = $_COOKIE['statistics_access'];}

?>
<script>
var user_name_full =  '<?=$_COOKIE['name'] . " " . $_COOKIE['surname'];?>';
var user = '<?=$user?>';
</script>
<? if(!$auth) { ?>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="stylesheet" href="/assets/libs/bootstrap.min.css">
<div class="d-flex flex-row justify-content-center align-items-center" style="height: 100vh;">
<form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
  <div class="form-group">
    <label for="in_user">Ћогин:</label>
    <input class="form-control" autofocus type="text" name="in_user" value="">
  </div>
  <div class="form-group">
    <label for="in_pass">ѕароль:</label>
    <input class="form-control" autofocus type="text" name="in_pass" type="password" readonly onfocus="this.removeAttribute('readonly')">
  </div>
  <div class="form-group">
    <button name="auth_in" class="btn btn-primary" value="ok" type="submit">¬ойти!</button>
  </div>
</form>
</div>
<? } ?>
<table cellpadding="0" cellspacing="0" border="0" width="1100" align="center" id=top_tbl>
<tr>
<td width="250">

<!-- “јЅЋ»÷ј ј¬“ќ–»«ј÷»» [[[ //-->

<? if(!$auth) { ?>
  <style>
    input {
      height:
    }
  </style>


  <? }
  else {
    switch($user_type) {
      case 'sup':
        $user_header = 'суперадмин';
        break;
      case 'meg':
        $user_header = 'мегаадмин';
        break;
      case 'adm':
        $user_header = 'администратор';
        break;
      case 'mng':
        $user_header = 'менеджер';
        break;
      case 'acc':
        $user_header = 'бухгалтер';
        break;
      default:
        $user_header = 'гость';
        break;
    }
  ?>
            <span class="auth_user"><?=$user?>
            </span>&nbsp;&nbsp;&nbsp;
            <span class="auth_tp_user">(<?=$user_header?>)</span>&nbsp;&nbsp;
            <a href="/?auth=exit&user_id=<?=$user_id;?>"><img src="/acc/i/login/door.gif" name="ex_butt" width="23" height="23" id="ex_butt" style="vertical-align: middle"/></a> <br>


<script>


function show_monthly_stat(){
$("#monthly_stat").toggle()
$("#monthly_stat_fade").toggle()
}


function show_general_stat(){
month_num_from = $("#month_num_from").val()
year_num_from = $("#year_num_from").val()
month_num_to = $("#month_num_to").val()
year_num_to = $("#year_num_to").val()
window.open('../backend/general_stat.php?month_num_from='+month_num_from+'&year_num_from='+year_num_from+'&month_num_to='+month_num_to+'&year_num_to='+year_num_to,"MyWin", "");
}




function svodka(){
 if($('#svodka_span').css("display")=="none"){

$('#svodka_pass').fadeOut('slow');
$('#svodka_fon').fadeIn('fast');
$('#svodka_span').fadeIn('fast');

$.ajax({
     url: '/acc/backend/svodka.php?act=not_send_report',
     type: 'POST',
     dataType: 'text',
     success: function(data) {$("#svodka_span").html(data);}
   });

}

}

function svodka_hide(){
$('#svodka_span').fadeOut('fast');
$('#svodka_fon').fadeOut('fast');
$('#svodka_pass').fadeOut('slow');
}

function show_extra(id){
if (!$('#extra_block_'+id).is(":visible")){
$('#extra_block_'+id).show();
$('#arr'+id).attr("src","/i/arrow_up.png");
}else{$('#extra_block_'+id).hide();
$('#arr'+id).attr("src","/i/arrow_down.png");
}
}


function get_managers_stat(){
year_num_managers =  $("#year_num_managers").val();
month_num_managers =  $("#month_num_managers").val();

$.ajax({
     url: '/acc/backend/managers_stat.php',
     data: 'year_num_managers='+year_num_managers+'&month_num_managers='+month_num_managers,
     dataType: 'text',
     type: 'GET',
     success: function(data) {
        already_there = $('#managers_stat_span').html();
        $('#managers_stat_span').html(data+'<br>'+already_there);
     }
   });
 }



function check_logistic(){
date_from_logistic = $("#date_from_logistic").val();
date_to_logistic = $("#date_to_logistic").val();
$.ajax({
     url: '/acc/backend/logistic_check.php',
     data: 'date_from_logistic='+date_from_logistic+'&date_to_logistic='+date_to_logistic,
     dataType: 'text',
     type: 'GET',
     success: function(data) {
     $('#check_logistic_span').html(data);
     }
   });
}

function update_courier_check(id){
if($("#"+id+"-checked").is(":not(:checked)")){
checked = '0'}else{checked = '1'}
comment = $("#"+id+"-comment").val();
$.ajax({
     url: '/acc/backend/update_courier_check.php',
     data: 'id='+id+'&checked='+checked+'&comment='+comment,
     dataType: 'text',
     type: 'GET',
     success: function(data) {
     if(data=="ok"){$("#update_courier_status_span").fadeIn(500).fadeOut(500);}
     else{console.log("ќшибка: -"+data+"-")}
     }
   });


}

function show_cap(){
$('#svodka_fon').fadeIn('fast');
$('#svodka_span').fadeIn('fast');

get_cap_stat()
}



function get_cap_stat(){

$.ajax({
     url: '/acc/backend/get_cap_stat.php',
     data: 'user_type=<?=$user_type;?>',
     dataType: 'text',
     type: 'GET',
     success: function(data) {
     $('#svodka_span').html(data);
     }
   });
}


function get_sotr_effectivity(){
year_num_eff =  $("#year_num_eff").val();
month_num_eff =  $("#month_num_eff").val();
period =  $("#period").val();
doljnost =  $("#doljnost").val();
$.ajax({
     url: '/acc/backend/get_sotr_effectivity.php',
     data: 'year_num_eff='+year_num_eff+'&month_num_eff='+month_num_eff+'&period='+period+'&doljnost='+doljnost,
     dataType: 'text',
     type: 'GET',
     success: function(data) {
        $('#sotr_effectivity_span').html(data);
     }
   });
 }


function form_pro_report(){
var str = $("#form_pro").serialize();
$("#proizv_analyt_span").html("<img src=\"../../i/load2.gif\" style=\"align:middle;padding-bottom:30px;\">");
$.post("/acc/backend/pro.php?"+str, function(data){}).done(function(data) {$("#proizv_analyt_span").html(data);});
}


function change_plan(usid, month_num, year_num, act){

var obj_id = "pl_"+month_num+""+year_num+""+usid;
var sdata = $("#frm_"+usid+""+month_num+""+year_num).serialize();

 $.ajax({
     url: '/acc/backend/change_plan.php',
     data: 'usid='+usid+'&month_num='+month_num+'&year_num='+year_num+'&act='+act+'&'+sdata,
     dataType: 'text',
     type: 'GET',
     success: function(data) {
$("#"+obj_id).html(data);

}});
}

function change_table(tbl_id){

$("#oplaceno_tbl_"+tbl_id).toggle();
$("#sheta_tbl_"+tbl_id).toggle();


}


</script>

<span id="status_span"></span>
 <?}?>
<!-- ]]] “јЅЋ»÷ј ј¬“ќ–»«ј÷»»  //-->
</td>

<td>

<?php if ($statistics_access > 0) {
    echo "<table class=\"svodka_top\" cellpadding=\"4\">";

    function get_seg_stat($for_date, $period, $usid, $show_table, $kv) {
        if ($usid !== 'all') {
            $vst = "AND user_id = '$usid'"; $k = "0.9";
        } else {
            $k = "1";
        }

        //дл€ использовани€ в запросе в plan_users
        $form_date = strtotime($for_date);
        $mes = date("m", $form_date);
        $god = date("Y", $form_date);

        //массив с мес€цами в квартале
        $kvartal_arr = array(
                    "01" => array("01","02","03"),
                    "02" => array("01","02","03"),
                    "03" => array("01","02","03"),
                    "04" => array("04","05","06"),
                    "05" => array("04","05","06"),
                    "06" => array("04","05","06"),
                    "07" => array("07","08","09"),
                    "08" => array("07","08","09"),
                    "09" => array("07","08","09"),
                    "10" => array("10","11","12"),
                    "11" => array("10","11","12"),
                    "12" => array("10","11","12")
                    );
        $tek_m = date("m");
        $tek_y = date("Y");

        //дополнение запроса мес€цами текущего квартала, к сожалению не смог сделать вывод квартальной таблицы как условие заполнени€ всех мес€цев плана цифрами
        if ($kv == "1") {
          $m1 = $kvartal_arr[$tek_m][0]; $m2 = $kvartal_arr[$tek_m][1];  $m3 = $kvartal_arr[$tek_m][2];
          $month_vst = " (month = '$m1' OR month = '$m2' OR month = '$m3') ";
          $month_vst_q = " (date_query LIKE '$tek_y-$m1%' OR date_query LIKE '$tek_y-$m2%' OR date_query LIKE '$tek_y-$m3%') ";
          //провер€ем чтобы план был задан в каждом мес€це текущего квартала
          $check_plans_kv = mysql_num_rows(mysql_query("SELECT count(*) FROM plan_users WHERE 1 $vst AND year = '$god' AND $month_vst AND summ > 0 GROUP BY month"));
        } else {
          $month_vst = " month = '$mes' ";
          $month_vst_q = " date_query LIKE '".$for_date."%' ";
        }

        //получаем план манагеров из таблицы. ≈сли админ - то планы всех манагеров
        $plans = mysql_fetch_array(mysql_query("SELECT SUM(summ) AS summ FROM plan_users WHERE 1 $vst AND year = '$god' AND $month_vst"));
        //echo "SELECT SUM(summ) AS summ FROM plan_users WHERE 1 $vst AND year = '$god' AND $month_vst";
        $plan = $plans['summ'];

        //индивидуальна€ статистика менеджера
        $q = "SELECT SUM(prdm_sum_acc) AS prdm_sum_acc, SUM(podr_sebist) AS podr_sebist, typ_ord AS typ_ord, COUNT(*) AS shetov, SUM(prdm_opl) AS prdm_opl FROM queries WHERE $month_vst_q $vst AND deleted = 0 AND client_id <> 0 GROUP BY typ_ord";

        if ($period == 'в прошлом мес€це' and $_GET['code'] == '1') {
              //echo $q;
        }

        // сумма отмеченных оплат за сегодн€
        $today_amount_q = mysql_query("SELECT SUM(sum_accounts) as amount FROM payment_predm WHERE date_ready = '$for_date' AND deleted = 0");
        $today_amount = mysql_fetch_assoc($today_amount_q);

        $st = mysql_query($q);

        while ($stat =  mysql_fetch_array($st)) {
            $prdm_sum_acc = round($stat['prdm_sum_acc'],0);
            //echo $prdm_sum_acc.",";
            $podr_sebist = round($stat['podr_sebist'],0);
            $typ_ord = round($stat['typ_ord'],0);
            $shetov = round($stat['shetov'],0);
            $prdm_opl = round($stat['prdm_opl'],0);
            //считаем суммы
            $prdm_sum_acc_total = $prdm_sum_acc_total + $prdm_sum_acc;
            $podr_sebist_total = $podr_sebist_total + $podr_sebist;
            $shetov_total = $shetov_total + $shetov;
            $prdm_opl_total = $prdm_opl_total + $prdm_opl;

            //считаем маржу
            $marja = $prdm_sum_acc - $podr_sebist;
            $marja_total = $marja_total + $marja;

            //определ€ем вознаграждение % манагеров
            if ($typ_ord == "1") {
                //под заказ
                $procent_manager = round($marja * 0.1);
            }
            if ($typ_ord == "2") {
                //магазин
                $procent_manager = round($prdm_sum_acc * 0.01);
            }
            if ($typ_ord == "3") {
                //магазин с лого
                $procent_manager = round($prdm_sum_acc * 0.025);
            }
            //перезаказ (на будущее)
            //if($typ_ord == "4"){$procent = "0.1";}

            $procent_manager_total = round(($procent_manager_total + $procent_manager));
        }
        //уменьшаем прогноз % дл€ менеджеров искуственно, чтобы они всегда получали несколько больше чем ожидают
        $procent_manager_total = $procent_manager_total*$k;
        //считаем % оплат
        if ($prdm_sum_acc_total > 0) {
            $proc_oplaceno = round($prdm_opl_total * 100 / $prdm_sum_acc_total,0);
        }

        //костыль, но ставим рамку вокруг текущего мес€ца
        if ($for_date == date("Y-m") and $kv !== "1") {
            $ramka = "ramka";
            //вычисл€ем количество дней в мес€це, текущий день дл€ понимани€ того, в ногу с планом мы движемс€ или нет
            $tek_d = date("d");
            $days_in_month = date("t");
            $plan_per_day =  $plan/$days_in_month;
            $planned_viruch = $tek_d*$plan_per_day;
        } else {
            $ramka = "";
            //если смотрим прошедший мес€ц, то там же свои данные должны подставл€тьс€, иначе будет таблица неправильно отображатьс€

            if($kv == '1')
                {$days_in_month = 91;
                $curMonth = date("m", time());
                //определ€ем текущий квартал
                $curQuarter = ceil($curMonth/3)-1;
                //определ€ем пор€дковый день текущего квартала
                $tek_d = date('z')-$curQuarter*91;
                }
            else{
                $days_in_month = date("t", $form_date);
                $tek_d = $days_in_month;}

            $plan_per_day =  $plan/$days_in_month;
            $planned_viruch = $tek_d*$plan_per_day;
        }

        if (($check_plans_kv == "3" and $kv == "1") or ($kv == "0")) {
            //показываем процент манагера, только если набралось выше 500р
            if ($procent_manager_total > 0) {
                // верхн€€ строка "сегодн€"
                $top_row = "<tr class=\"$ramka\">";
                $top_row .= "<td>";
                $top_row .= "$period <b>% - $procent_manager_total р</b>. ";
                $top_row .= "—четов <b>$shetov_total шт</b> на сумму $prdm_sum_acc_total р., ";
                $top_row .= "из которых оплачено $prdm_opl_total ($proc_oplaceno%)";
                $today_oplaceno = round($today_amount['amount']);
                if ($period === 'сегодн€' && $today_oplaceno) {
                    $top_row .= ", отмечено оплат на сумму {$today_oplaceno} р.";
                }
                $top_row .= "</td>";

                echo $top_row;

                if ($show_table == '1' && $plan > 0 && $procent_manager_total > 300) {
                    //оплаты
                    $workout_oplaceno = round($prdm_opl_total*100/$plan*2);
                    $workout_proc_oplaceno = round($workout_oplaceno/2,1);
                    if ($workout_oplaceno > 220) {
                        $workout_oplaceno = 100*2;
                    }

                    //выставлено счетов
                    $workout_prdm_sum_acc = round($prdm_sum_acc_total*100/$plan*2);
                    $workout_proc_prdm_sum_acc = round($workout_prdm_sum_acc/2,1);
                    if ($workout_prdm_sum_acc > 220) {
                        $workout_prdm_sum_acc = 100*2;
                    }

                    if ($prdm_opl_total >= $planned_viruch) {
                        $workout_class = "workout_green_topdiv";
                    } else {
                        $workout_class = "workout_red_topdiv";
                    }

                    if ($prdm_sum_acc_total >= $planned_viruch) {
                        $workout_class1 = "workout_green_topdiv";
                    } else {
                        $workout_class1 = "workout_red_topdiv";
                    }

                    $table = "<td onclick=\"change_table('$for_date$kv')\">";
                    $table .= "<div style=\"width:200px; min-height: 35px;\" id=\"oplaceno_tbl_$for_date$kv\">";
                    $table .= "<div style=\"width:".$workout_oplaceno."px;\" class=\"$workout_class\"></div>";
                    $table .= "<div class=\"workout_outer_topdiv\">оплачено<br>$prdm_opl_total из $plan <span class=\"workout_perc_text\">(".$workout_proc_oplaceno."%) </span></div>";
                    $table .= "</div>";
                    $table .= "<div style=\"width:200px; min-height: 35px; display:none;\" id=\"sheta_tbl_$for_date$kv\">";
                    $table .= "<div style=\"width:{$workout_prdm_sum_acc}px;\" class=\"$workout_class1\">";
                    $table .= "<div class=\"workout_outer_topdiv\">выставлено<br>{$prdm_sum_acc_total} из {$plan} <span class=\"workout_perc_text\">({$workout_proc_prdm_sum_acc}%)</span></div>";
                    $table .= "</div>";
                    $table .= "</td>";
                    $table .= "</tr>";

                    echo $table;
                }
            }
        }
    }
    $usid = $user_id;
    if ($statistics_access == '2') {
        $usid = "all";
    }
    get_seg_stat($tek_date, "сегодн€", $usid, '0', '0');
    get_seg_stat($vchera, "вчера", $usid, '0', '0');
    get_seg_stat($tek_month, "в этом мес€це", $usid, '1', '0');
    //смотрим прошлый мес€ц
    get_seg_stat($proshl_month, "в прошлом мес€це", $usid, '1', '0');

    get_seg_stat($tek_month, "текущий квартал", $usid, '1', '1');
    echo "</table>";
    ?>


 <?php if ($usid == 'all') { ?>
    <img src="/i/info.png" alt="" style="cursor:pointer" onclick="svodka()" />
    <form onsubmit="svodka();return false;">
    <span id="svodka_pass" class=svodka_pass><input type="password" id=svodka_pass_inp /> <input type="submit" value="OK">
    </form>
    <span style="font-size:16px;color:red;cursor:pointer;font-weight:bold;" onclick="svodka_hide()">X</span>
    </span></span>
<?php }
} ?>
 <span id="svodka_span" class="svodka"></span>
 <div id="svodka_fon" onclick="svodka_hide()" style="width:100%;height:100%; background: rgba(66, 66, 66, 0.75);display:none; margin:0 auto;position:absolute; top: 0px; left:0px;z-index:10000"></div>
</td>
</tr>
</table>
