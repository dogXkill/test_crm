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
    //$sel_year = $_COOKIE['sel_year'];

if(!empty($_REQUEST['year']) && is_numeric($_REQUEST['year'])) {
    $sel_year = $_REQUEST['year'];
}
 $sel_year =$_GET['year'];
//setcookie('sel_year', $sel_year, 0);	// запомнить год

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
               // $arr_filtr[$fltr_s[$i]][0] 			= @$_COOKIE['fltr_'.$fltr_s[$i].'_case'];
               // $arr_filtr[$fltr_s[$i]][1] 			= @$_COOKIE['fltr_'.$fltr_s[$i].'_val'];
//				echo $arr_filtr[$fltr_s[$i]][0].'___'.$arr_filtr[$fltr_s[$i]][1].'<br>';
            }
    }


// ОЧИСТКА ВСЕХ ФИЛЬТРОВ
if((@$_GET['filtr'] == 'clear')||(@$_GET['clear'] == '1')) {
    for($i=0;$i<count($fltr_s);$i++) {
        //setcookie('fltr_'.$fltr_s[$i].'_case');
        //setcookie('fltr_'.$fltr_s[$i].'_val');
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

                //setcookie(('fltr_dat_case'), $fltr_case, 0);
                //setcookie(('fltr_dat_val'), $fltr_case, 0);
                $arr_filtr['dat'][0] = $fltr_case;		// нач месяц и конечный
                $arr_filtr['dat'][1] = $fltr_case;

                //setcookie(('fltr_manager_case'), 'manager', 0);
                //setcookie(('fltr_manager_val'), $fltr_val, 0);
                $arr_filtr['manager'][0] = 'manager';
                $arr_filtr['manager'][1] = $fltr_val;		// ид менеджера

            }
            else {
                //setcookie(('fltr_'.$fltr.'_case'), $fltr_case, 0);
                //setcookie(('fltr_'.$fltr.'_val'), $fltr_val, 0);
                $arr_filtr[$fltr][0] = $fltr_case;
                $arr_filtr[$fltr][1] = $fltr_val;
            }
//	}
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
//$query .= " ORDER BY a.date_query ASC";
//echo $query;
$year=$_GET['year'];
$month=$_GET['month'];
$used_id_g=$_GET['user_id'];
$new_query="SELECT a.* FROM queries as a, users as b WHERE a.prdm_num_acc <> '' AND a.prdm_num_acc <> '0' AND (a.prdm_sum_acc - a.prdm_opl) > -10 AND (a.prdm_sum_acc - a.prdm_opl) < 10 AND a.user_id=b.uid AND a.deleted = '0' AND a.CancelPercentage = '0' AND 10 > (a.prdm_sum_acc - a.prdm_opl) > -10 AND YEAR(a.date_query)={$year} AND MONTH(a.date_query)={$month} AND user_id={$used_id_g} ORDER BY a.date_query ASC";
echo $new_query;
$res = mysql_query($new_query);

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



					//echo $typ_ord;
                    if($date_query > '2021-07-01 00:00:00'){
                    if($typ_ord == "1"){$komis_rub = ($percent * $marz) / 100;}
                    if($typ_ord == "2"){$komis_rub = ($percent * $prdm_sum_acc) / 100;}
                    if($typ_ord == "3"){$komis_rub = ($percent * $prdm_sum_acc) / 100;}
                    }
                    else{
                    // комиссионные в руб.
                    $komis_rub = ($percent * $marz) / 100;
                    }
					//echo $percent;
					$ball_itg += $komis_rub; ?>
					
                <? } ?>
                       
                    
			<?php echo form_num($ball_itg);?>
           
			
