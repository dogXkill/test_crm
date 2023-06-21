<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

ob_start();

$auth = false;

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");

if ($user_access['tabl_access'] == '0' || empty($user_access['tabl_access'])) {
  header('Location: /');
}

// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;

// ----- перейти на главную если доступ запрещен ---------
/*if(!$auth or ($user_type !== 'sup' and $user_type !== 'acc' and $user_type !== 'meg')) {
    header("Location: /");
    exit;
}*/

if ($_COOKIE['tabl_access'] !== '1') {
  header('Location: /');
}


// текущий год
if(!isset($_COOKIE['sel_year'])) {
    $sel_year = date("Y");
} else
    $sel_year = $_COOKIE['sel_year'];

if(!empty($_REQUEST['year']) && is_numeric($_REQUEST['year'])) {
    $sel_year = $_REQUEST['year'];
}

setcookie('sel_year', $sel_year, 0);	// запомнить год

// чтение годов из базы по убыванию
$query = "SELECT DISTINCT YEAR(a.date_query) as yr FROM queries as a, users as b WHERE a.user_id=b.uid  AND a.deleted = '0' ORDER BY a.date_query DESC";

$yr_res = mysql_query($query);

$arr_all_year = array();		// массив годов по которым есть заявки в базе

while($yr_r = mysql_fetch_array($yr_res))
    $arr_all_year[] =  $yr_r['yr'];


// проверка на правильность выбранного года
if(count($arr_all_year)) {
    if(!in_array($sel_year,$arr_all_year))
        $sel_year = $arr_all_year[0];
        setcookie('sel_year', $sel_year, 0);	// запомнить год
}
//--------------------------



// форматирование дробного числа до 2 чисел после запятой
function form_num($v) {
    $v = preg_replace('/\,/', '.', ''.$v);
    $v = number_format($v,2, '.', '');
    $v = preg_replace('/\.00/', '', $v);
    $v = preg_replace('/-0/', '0', $v);
    $v = round($v);
    return $v;
}


// массив переменных передаваемых через GET
$fltr_s = array(
    'typ_ord',
    'nm_acc',
    'dat',
    'client',
    'summ_acc',
    'pr_opl',
    'dolg',
    'sebist',
    'marz',
    'kom_proc',
    'komiss',
    'opl_maneg',
    'dolg_maneg',
    'fin_marz',
    'manager',
    'datmen',
    'close_proj'
);
// массив соответствующих полей в базе
$fltr_t = array(
    'typ_ord',
    'prdm_num_acc',
    'date_query',
    'client_id',
    'prdm_sum_acc',
    'prdm_opl',
    'prdm_dolg',
    'podr_sebist',
    'marz',			//!
    'percent',
    'komis_opl',
    'komis_opl',	//!
    'dolg_maneg',	//!
    'fin_marz',		//!
    'user_id'
);




// ЧТЕНИЕ ФИЛЬТРОВ ИЗ COKIES В ОДИН МАССИВ
$arr_filtr = array();

    for($i=0;$i<count($fltr_s);$i++) {
            if($fltr_s!='datmen') {		// кроме фильтра менеджер + дата
                $arr_filtr[$fltr_s[$i]][0] 			= @$_COOKIE['fltr_'.$fltr_s[$i].'_case'];
                $arr_filtr[$fltr_s[$i]][1] 			= @$_COOKIE['fltr_'.$fltr_s[$i].'_val'];
//				echo $arr_filtr[$fltr_s[$i]][0].'___'.$arr_filtr[$fltr_s[$i]][1].'<br>';
            }
    }


// ОЧИСТКА ВСЕХ ФИЛЬТРОВ
if((@$_GET['filtr'] == 'clear')||(@$_GET['clear'] == '1')) {
    for($i=0;$i<count($fltr_s);$i++) {
        setcookie('fltr_'.$fltr_s[$i].'_case');
        setcookie('fltr_'.$fltr_s[$i].'_val');
        $arr_filtr[$fltr_s[$i]][0] = '';
        $arr_filtr[$fltr_s[$i]][1] ='';
    }
}


// если был добавлен фильтр по цифре
if(isset($_GET['filtr']) && !empty($_GET['filtr'])) {

    $fltr = $_GET['filtr'];
    $fltr_case = @$_GET['case'];
    $fltr_val = @$_GET['val'];



            if($fltr == 'datman') {			// если фильтр сдвоенный - месяц/менеджер

                setcookie(('fltr_dat_case'), $fltr_case, 0);
                setcookie(('fltr_dat_val'), $fltr_case, 0);
                $arr_filtr['dat'][0] = $fltr_case;		// нач месяц и конечный
                $arr_filtr['dat'][1] = $fltr_case;

                setcookie(('fltr_manager_case'), 'manager', 0);
                setcookie(('fltr_manager_val'), $fltr_val, 0);
                $arr_filtr['manager'][0] = 'manager';
                $arr_filtr['manager'][1] = $fltr_val;		// ид менеджера

            }
            else {
                setcookie(('fltr_'.$fltr.'_case'), $fltr_case, 0);
                setcookie(('fltr_'.$fltr.'_val'), $fltr_val, 0);
                $arr_filtr[$fltr][0] = $fltr_case;
                $arr_filtr[$fltr][1] = $fltr_val;
            }
//	}
}
if(isset($_GET['dop1']) && !empty($_GET['dop1'])) {
	setcookie(('fltr_dat_case'), $_GET['dat'], 0);
    setcookie(('fltr_dat_val'), $_GET['dat'], 0);
	$arr_filtr['dat'][0] = $_GET['dat'];		// нач месяц и конечный
                $arr_filtr['dat'][1] = $_GET['dat'];
	setcookie(('fltr_manager_case'), 'manager', 0);
                setcookie(('fltr_manager_val'), $_GET['mang'], 0);
                $arr_filtr['manager'][0] = 'manager';
                $arr_filtr['manager'][1] = $_GET['mang'];
				$sel_year=$_GET['year'];
}


$query = "SELECT a.* FROM queries as a, users as b WHERE a.prdm_num_acc <> '' AND a.prdm_num_acc <> '0' AND (a.prdm_sum_acc - a.prdm_opl) > -10 AND (a.prdm_sum_acc - a.prdm_opl) < 10 AND a.user_id=b.uid AND a.deleted = '0' AND a.CancelPercentage = '0'  AND 10 > (a.prdm_sum_acc - a.prdm_opl) > -10 AND YEAR(a.date_query)=".$sel_year."";



for($i=0;$i<count($fltr_s);$i++) {

    if( $fltr_s[$i] == 'dat' ) {
        if( (intval($arr_filtr['dat'][0]) == 0) && (intval($arr_filtr['dat'][1]) == 0) )
            continue;
        if( intval($arr_filtr['dat'][0]) == 0 )	{
            $query .= " AND MONTH(date_query)<=".$arr_filtr['dat'][1];
            continue;
        }
        if( intval($arr_filtr['dat'][1]) == 0 )	{
            $query .= " AND MONTH(date_query)>=".$arr_filtr['dat'][0];
            continue;
        }
        $query .= " AND MONTH(date_query)>=".$arr_filtr['dat'][0]." AND MONTH(date_query)<=".$arr_filtr['dat'][1];
        continue;
    }


    if( ($arr_filtr[$fltr_s[$i]][0]) ) {


        if($fltr_s[$i] == 'date')
            continue;


        if( $fltr_s[$i] == 'manager' ) {
            if(intval($arr_filtr[$fltr_s[$i]][1]) == 0)
                continue;
            $query .= " AND user_id=".$arr_filtr['manager'][1];
            continue;
        }


        $query .= " AND ".$fltr_t[$i].$arr_filtr[$fltr_s[$i]][0].$arr_filtr[$fltr_s[$i]][1];
    }

}

    if($_GET['typ_ord'] !== NULL) {
   $query .= " AND typ_ord = ".$_GET['typ_ord'];
}
$query .= " ORDER BY a.date_query ASC";
echo $query;
$res = mysql_query($query);

echo "<span id=ms_query style='display: none'><br><br>$query<br><br></span>";

 //функция должна подтягивать в массив все типы, которые хранятся в бд
function get_all_users(){

$get = mysql_query("SELECT uid, name, surname FROM users WHERE (doljnost = '8' OR user_department  = '3' OR user_department  = '2')");

while($g = mysql_fetch_assoc($get)){
$uid = $g[uid];
$users[$uid][name] = $g[name];
$users[$uid][surname] = $g[surname];
}



return $users;
}

$users = get_all_users();

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.2.0</title>


<link href="style.css" rel="stylesheet" type="text/css" />

<style type="text/css">
<!--
 .container {
    margin: auto;
    overflow: auto;
}
    .container table {
    border-collapse: inherit;
    width: 100%;
    padding: 0px;
    border-spacing: 0px;
}
    .container thead  th {
    position: sticky;
    top: 0;
    background: white;
}
    .container th, td  {
    border-collapse: inherit;
    border: 1px solid black;
    padding: 2px;
    border-spacing: 0px;

}
     tr.tbl:hover {
    background-color: white;
}

.sokr_prdm{
    font-size: 9px;
}

-->
</style>
</head>

<?//require_once("../includes/auth_stat_table.php");?>

<script src="../includes/lib/JsHttpRequest/JsHttpRequest.js"></script>
<script src="stat_table.js?cache=<?=rand(1,1000000)?>"></script>
<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>

<script language="JavaScript" type="text/javascript">
<!--

var tpacc = <?=$tpacc?>;
var curr_date = '<?=date("d.m.Y")?>';		// текущая дата в формате '01.05.2007'
var curr_filtr = '';

//-->
</script>


<body  bgcolor="#F6F6F6">
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>


<!-- ******************** СЛОЙ ФИЛЬТРА ПО ДАТЕ  <<<< *****************  //-->


<div id="div_fltr_date" style="background-color:#FFFFFF; position:absolute; top:400px; left:300px; width:300px; padding:5px; border:1px #0099CC solid; display:none;">
<table border="0" cellspacing="0" cellpadding="0" align="center" width="1">
    <tr>
        <td  colspan="4" align="center" valign="top"><strong>Фильтр по месяцу</strong></td>
    </tr>
    <tr>
        <td colspan="4" align="center" valign="top">&nbsp;</td>
    </tr>
    <form name="ff_fltr_date" action="" method="get">
    <tr>
        <td align="center">с&nbsp;</td>
        <td align="center">
            <select id="sel_filtr_dat1" name="" class="stat_fltr_data_sel" onchange="dubl()" style="width:100px;height:30px;font-size:20px;">
                <option value="0">------</option>
            <?	for($i=0;$i<count($month_sel);$i++) { ?>
                <option value="<?=$i+1?>"><?=$month_sel[$i]?></option>
            <? } ?>
            </select>
            <script type="text/javascript">
            /*<![CDATA[*/
            function dubl(){
                month = $('#sel_filtr_dat1 option:selected').val()
                $("#sel_filtr_dat2").val(month);
            }
            /*]]>*/
            </script>
        </td>
        <td align="center">&nbsp;&nbsp;по&nbsp;</td>
        <td align="center">
            <select id="sel_filtr_dat2" name="" class="stat_fltr_data_sel" style="width:100px;height:30px;font-size:20px;">
                <option value="0">------</option>
            <?	for($i=0;$i<count($month_sel);$i++) { ?>
                <option value="<?=$i+1?>"><?=$month_sel[$i]?></option>
            <? } ?>
            </select>
        </td>
    </tr>
        <tr>
        <td colspan="4" align="center">&nbsp;</td>
    </tr>
    <tr>
        <td align="center" colspan="4">
            <input class="frm_podr_opl_butt" name="" style="width:70px;height:40px;font-size:30px;" type="button" value="ОК" onclick="return SetFiltrDate();" />
            <input name="" class="frm_podr_opl_butt" type="button" style="width:120px;height:40px;font-size:30px;" value="Отмена" onclick="document.getElementById('div_fltr_date').style.display = 'none';return false;" />

        </td>
    </tr>
    </form>
</table>
</div>


<!--  >>>>>>******************** СЛОЙ ФИЛЬТРА ПО ДАТЕ  *****************  //-->



<!-- ******************** СЛОЙ ФИЛЬТРА ПО МЕНЕДЖЕРУ  <<<< *****************  //-->


<div id="div_fltr_man" style="background-color:#FFFFFF; position:absolute; top:400px; left:300px; width:300px; padding:5px; border:1px #0099CC solid; display:none;">
<table border="0" cellspacing="0" cellpadding="0" align="center" width="1">
    <tr>
        <td width="100%">&nbsp;</td>
    </tr>
    <form name="ff_fltr_man" action="" method="get">
    <tr>
        <td align="center">
                <?
               $query = "SELECT uid, name, surname FROM users WHERE (doljnost = '8' OR user_department  = '3' OR user_department  = '2') ORDER BY archive ASC, surname ASC";

                $res_man = mysql_query($query);
                ?>
                <select id="sel_filtr_man" name="" size="15" class="stat_fltr_client_sel">
                        <option value="0">&nbsp;&nbsp;------</option>
                    <? while($r = mysql_fetch_array($res_man)) { ?>
                        <option value="<?=$r['uid']?>">&nbsp;&nbsp;<?=$r['surname']?> <?=$r['name']?></option>
                    <? } ?>
                </select>
        </td>
    </tr>
    <tr>
        <td align="center">
            <input class="frm_podr_opl_butt" name="" type="button" value="ОК" onclick="return SetFiltrManager();"  style="width:70px;height:40px;font-size:30px;" />
            <input name="" class="frm_podr_opl_butt" type="button" value="Отмена" onclick="document.getElementById('div_fltr_man').style.display = 'none';return false;"  style="width:120px;height:40px;font-size:30px;"/>
        </td>
    </tr>
    </form>
</table>
</div>



<!--  >>>>>>******************** СЛОЙ ФИЛЬТРА ПО МЕНЕДЖЕРУ  *****************  //-->






           <div id="tech_analis" style="border-style:dashed; border-width: 1px; padding: 4px;">

           <span style="text-decoration: underline"><b>теханализ</b></span> |

             ср. мар. магазин <input type="text" value="24" size=3 id="sred_marja_shop"/>%
             ср. маржа магазин с лого <input type="text" value="55" size=3 id="sred_marja_shop_logo"/>%
             ср. мар. заказ <input type="text" value="50" size=3 id="sred_marja_order"/>%
             <input type=button onclick="tech_analis()" value="тех. анализ">
             <input type=button onclick="highlt_zero()" value="подсветить 0%">
                         <span style="cursor:pointer" onclick="ms_query_toggle()">...</span> <br />
            </div>


           <div id="new_system"  style="border-style:dashed; border-width: 1px; padding: 4px;">

           <span style="text-decoration: underline"><b>автозаполнение</b></span> |

             заказ (М) <input type="text" value="10" size=3 id="new_system_1"/>%
             магазин (В) <input type="text" value="1" size=3 id="new_system_2"/>%
             магазин c лого (В) <input type="text" value="2.5" size=3 id="new_system_3"/>%
             <input type=button onclick="insert_percent('new_system')" value="проставить!">
            </div>


            <div class="container" id="container" style="width:100%;height:900px">
            <table align="center" cellspacing=0 class=tbl id=tbl style="width:100%;">

               <thead>

                    <th width=80>№</th>
                    <th width=120>
                        <select onchange="if (this.value) window.location.href = this.value" name="" id="">
            <?foreach($arr_all_year as $t) {
                    if($sel_year == $t)
                        echo "<option value=\"?year=$t\" selected>$t</strong>";
                    else
                        echo "<option value=\"?year=$t\">$t</strong>";
             } ?>
            </select>
           <br>
            <span onclick="ShowFiltrNum('dat'); return false;" style="cursor:pointer;">месяц &dArr;</span></th>
                    <th width=255>Клиент</th>
                    <th width=250>Предмет заказа</th>
                    <th width=80>
                          <?$url = 'http://crm.upak.me/acc/stat/stat_table_query.php';?>
                        <select name="typ_ord" id="typ_ord" style="width:100px" onchange="if (this.value) window.location.href = this.value">
                        <option value="<?=$url?>" <?if($_GET["typ_ord"] == ''){echo "selected";}?>>все заказы</option>
                        <option value="<?=$url?>?typ_ord=1" <?if($_GET["typ_ord"] == '1'){echo "selected";}?>>заказные</option>
                        <option value="<?=$url?>?typ_ord=2" <?if($_GET["typ_ord"] == '2'){echo "selected";}?>>магазин</option>
                        <option value="<?=$url?>?typ_ord=3" <?if($_GET["typ_ord"] == '3'){echo "selected";}?>>магазин с лого</option>
                        </select></th>
                    <th width=60>Сумма</th>
                    <th width=60>Оплата</th>
                    <th width=80>Долг</th>
                    <th width=70>Себестоимость</th>
                    <th width=70>Маржа</th>
                    <th width=70>Баллы, %</th>
                    <th width=70>Баллы</th>
                    <th width=55><input type="checkbox" onclick="checkElements('ignoreerror')" id="ignore_all"/> <label for="ignore_all" style="cursor:pointer">игнор</label></th>
                    <th width=140 onclick="ShowFiltrNum('manager'); return false;" style="cursor:pointer">Менеджер &dArr;</th>

                </thead>
                <?
                $summ_predm_itg 		= 0;			// итоговая сумма счета
                $opl_predm_itg 			= 0;			// итоговая оплата предмета
                $dolg_predm_itg 		= 0;			// итоговый долг предмета
                $sebest_predm_itg 	= 0;			// итоговая себестоимость
                $marz_itg 					= 0;			// итоговая маржа
                $ball_itg 					= 0;			// итоговые баллы в руб
                $ball_opl_itg 			= 0;			// итоговая оплата баллов менеджерам
                $ball_dolg_itg 			= 0;			// итоговая долг менеджерам
                $marz_fin_itg 			= 0;			// итоговая  финальная маржа

                while(@$r_qr = mysql_fetch_array($res)) {

                    $typ_ord = $r_qr['typ_ord'];
                    $date_query = $r_qr['date_query'];
                    $percent = $r_qr['percent'];
                    $prdm_sum_acc = $r_qr['prdm_sum_acc'];
                    $podr_sebist = $r_qr['podr_sebist'];
                    // маржа
                    $marz = @$prdm_sum_acc - @$podr_sebist;
                    $user_id = $r_qr['user_id'];




                    if($date_query > '2021-07-01 00:00:00'){
                    if($typ_ord == "1"){$komis_rub = ($percent * $marz) / 100;}
                    if($typ_ord == "2"){$komis_rub = ($percent * $prdm_sum_acc) / 100;}
                    if($typ_ord == "3"){$komis_rub = ($percent * $prdm_sum_acc) / 100;}
                    }
                    else{
                    // комиссионные в руб.
                    $komis_rub = ($percent * $marz) / 100;
                    }
 ?>
                <tr align="center">
                  <td>
                        <?=$r_qr['prdm_num_acc']?>
                    </td>
                    <?	// форматировние даты выполнения запроса на счет
                    $tmp_dat = explode(' ',$r_qr['date_query']);
                    $tmp_dat = explode('-',$tmp_dat[0]);
                    $dat = @$tmp_dat[2].'.'.@$tmp_dat[1];
                    ?>
                  <td><?=$dat?></td>


                    <?  $query = "SELECT name, short FROM clients WHERE uid=".$r_qr['client_id'];
                        $res_cl = mysql_query($query);
                        $r_cl = mysql_fetch_array($res_cl);

                      ?>
                  <td><?=$r_cl['short']?></td>


                    <?
                    // ПРЕДМЕТ СЧЕТА
                    $query = "SELECT * FROM obj_accounts WHERE query_id=".$r_qr['uid']." ORDER BY nn";
                    $res_podr = mysql_query($query);

                    // ячейка таблицы и подсказка предмета
                    $predm = '';
                    $i=0;
                    while($r_podr = mysql_fetch_array($res_podr)) {
                        $predm.=''.$r_podr['name'].' / '.$r_podr['num'].' шт. / '.$r_podr['price'].' руб.<br>';
                        if($i==0) {
                            $tt = (strlen($predm) > 45) ? '...' : '';	// сокращение
                            $sokr_prdm = substr($predm,0,44).$tt;
                        }
                    }

                    $predm = htmlspecialchars($predm);

                    $predm = (trim($predm)) ? '<div class=stat_podr_alt>'.$predm.'</div>' : '';
                    $predm = ($predm) ? ' onmouseover="Tip(\''.$predm.'\', TITLE, \'Предмет заказа\')" ' : '';
                    ?>
                  <td <?=$predm?>><a target="_blank" href="../query/query_send.php?show=<?=$r_qr['uid']?>" class="sokr_prdm"><?=@$sokr_prdm?></a></td>
                  <td>
            <? if($typ_ord==2){?>магазин<?}
            elseif($typ_ord==1){?>заказ<?}
            elseif($typ_ord==3){?>магазин с лого<?}?>

                    <input type="hidden" id="typ_ord_<?=$r_qr['uid'];?>" value="<?=$typ_ord;?>" /></td>

                  <?$summ_predm_itg += $r_qr['prdm_sum_acc'];?>
                  <td>
                  <input type="hidden" id="prdm_sum_acc_<?=$r_qr['uid'];?>" value="<?=form_num($r_qr['prdm_sum_acc'])?>" /><?=form_num($r_qr['prdm_sum_acc'])?></td>
                    <?$compl_cost = form_num(trim($r_qr['prdm_opl']));
                    if($compl_cost && ($compl_cost != '0'))
                        $alt_compl_cost = '<span style="color:green;font-weight:bold;">'.$compl_cost.'</span>';
                    else
                        $alt_compl_cost = '<span style="color:green;font-weight:bold;">---</span>';
                    $opl_predm_itg += $compl_cost;
                    ?>
                  <td><?=$alt_compl_cost?></td>
                 	<?$dolg_t = $r_qr['prdm_sum_acc'] - $compl_cost;
                    $dolg_predm_itg += $dolg_t;?>
                  <td id="td_dolg_<?=$r_qr['uid'];?>">
                  <input type="hidden" id="dolg_<?=$r_qr['uid'];?>" value="<?=form_num($dolg_t)?>" />
                  <?=form_num($dolg_t)?></td>
                    <?$sebest_predm_itg += $r_qr['podr_sebist']; ?>
                  <td id="td_podr_sebist_<?=$r_qr['uid'];?>">
                  <input type="hidden" id="podr_sebist_<?=$r_qr['uid'];?>" value="<?=form_num($r_qr['podr_sebist'])?>" />
                  <?=form_num($r_qr['podr_sebist'])?></td>
                    <?$marz_itg += $marz; ?>
                  <td id="td_marja_<?=$r_qr['uid'];?>">
                 <input type="hidden" id="marja_<?=$r_qr['uid'];?>" value="<?=form_num($marz)?>" />
                  <?=form_num($marz)?>
                   <span id="span_marja_<?=$r_qr['uid'];?>" style="color:white; font-weight:bold;"></span>
                  </td>
                  <td  id="proc_td_<?=$r_qr['uid']?>">
                  <input type="hidden" class="ids" value="<?=$r_qr['uid']?>"/>
                  <input onkeyup="enableSaveButt()" onchange="setValTab(<?=$r_qr['uid']?>,'percent', this.value);" class="stat_inp_oplm" id=proc_<?=$r_qr['uid']?> name="" type="text" value="<?=$r_qr['percent']?>" /></td>
                  <?$ball_itg += $komis_rub; ?>

                  <td><?=(round(form_num($komis_rub)*10)/10);?></td>
                  <td>
                  <input type="checkbox" class="ignoreerror" id="ignoreerror_<?=$r_qr['uid'];?>" <?if($r_qr['ignoreerror'] == "1"){echo "checked";}?> onchange="set_ignoreerror(<?=$r_qr['uid'];?>)"/>
                  <label for="ignoreerror_<?=$r_qr['uid'];?>">игнор</label></td>
                  <td><b><?=$users[$user_id][surname];?></b> <?=$users[$user_id][name];?></td>
                  </tr>
                <? } ?>
                    <tr>
                        <td colspan="5" align="center"><strong>ИТОГО</strong></td>
                        <td align="center"><b><?=form_num($summ_predm_itg)?></b></td>
                        <td align="center"><b><?=form_num($opl_predm_itg)?></b></td>
                        <td align="center"><b><?=form_num($dolg_predm_itg)?></b></td>
                        <td align="center"><b><?=form_num($sebest_predm_itg);?></b> (<?=round($sebest_predm_itg*100/$summ_predm_itg);?>%)</td>
                        <td align="center"><b><?=form_num($marz_itg)?></b> (<?=round($marz_itg*100/$summ_predm_itg);?>%)</td>
                        <td align="center">&nbsp;</td>
                        <td align="center" style="background-color: #99FF00"><b><?=form_num($ball_itg)?></b></td>
                        <td align="center"><b><?=form_num($ball_opl_itg)?></b></td>
                        <td align="center">&nbsp;</td>
                    </tr>
            </table>
            </div>

<input onclick="SaveTabAllData()" id="SaveButt" type="button" style="width: 500px; height: 50px; font-size:35px;" value="Сохранить изменения" />






</body>
</html>
<? ob_end_flush(); ?>
