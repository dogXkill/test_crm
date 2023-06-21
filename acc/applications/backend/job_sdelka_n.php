<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

ob_start();

$auth = false;

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");

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

$year=$_GET['year'];
$month=$_GET['month'];
$used_id_g=$_GET['user_id'];
$new_query="SELECT a.* FROM queries as a, users as b WHERE a.prdm_num_acc <> '' AND a.prdm_num_acc <> '0' AND (a.prdm_sum_acc - a.prdm_opl) > -10 AND (a.prdm_sum_acc - a.prdm_opl) < 10 AND a.user_id=b.uid AND a.deleted = '0' AND a.CancelPercentage = '0' AND 10 > (a.prdm_sum_acc - a.prdm_opl) > -10 AND YEAR(a.date_query)={$year} AND MONTH(a.date_query)={$month} AND user_id={$used_id_g} ORDER BY a.date_query ASC";
//echo $new_query;
$res = mysql_query($new_query);



 //функция должна подтягивать в массив все типы, которые хранятся в бд
function get_all_users(){

$get = mysql_query("SELECT uid, name, surname FROM users WHERE (doljnost = '8' OR user_department  = '3')");

while($g = mysql_fetch_assoc($get)){
$uid = $g[uid];
$users[$uid][name] = $g[name];
$users[$uid][surname] = $g[surname];
}



return $users;
}
function get_tek_rate($job){
  if($job == '14'){$tek_rate = '0.5';}
  if($job == '15'){$tek_rate = '0.17';}
  if($job == '16'){$tek_rate = '0.17';}
  if($job == '29'){$tek_rate = '0.12';}
  if($job == '31'){$tek_rate = '0.2';}
return $tek_rate;
}

function get_depar($id){
	$get = mysql_query("SELECT user_department FROM users WHERE uid={$id}");
	if (mysql_num_rows($get)>=1){
		while($g = mysql_fetch_assoc($get)){
		//$uid = $g[uid];
		//$users[$uid][name] = $g[name];
		//$users[$uid][surname] = $g[surname];
		$dep=$g['user_department'];
		}
	}else{
		$dep=null;
	}
	return $dep;
}
$dep=get_depar($used_id_g);
if ($dep!=3){
	//
	/*
	$str = $_SERVER['QUERY_STRING'];
	parse_str($str);

	//разбиваем на массив для дальнейшего использования
	$str_arr = explode("&", $str);
	$arr_app_type = array();
	foreach ($str_arr as $v) {
	   $str_ar = explode("=", $v);
		if($str_ar[0] == "app_type" and $str_ar[1] !== ""){array_push($arr_app_type, ' a.app_type = ' . $str_ar[1]);}
	}

	  $arr_app_type = implode(' OR ', $arr_app_type);
	  $sql_vst .= " AND (" . $arr_app_type . " ) ";


	
	if(is_numeric($year) and $month == ""){
		$sql_vst .= " AND j.cur_time LIKE '$year-%' ";
	}elseif(is_numeric($year) and $month !== "")
	{
		$sql_vst .= " AND j.cur_time LIKE '$year-$month-%' ";
	}

	if(is_numeric($art_id)){$sql_vst .= " AND a.art_id = '$art_id' ";}

	if(is_numeric($num_ord)){$sql_vst .= " AND j.num_ord = '$num_ord' ";}
	
	if($num_sotr){


	//т.к. в строке может передаваться сразу несколько значений с одинаковым идентификатором, то parse_str тут не срабоает, приходиться обрабатывать массив вручную
	//$str_arr = explode("&", $str);
	
	$arr_num_sotr = array();
	foreach ($str_arr as $v) {
	   $str_ar = explode("=", $v);
		if($str_ar[0] == "num_sotr" and $str_ar[1] !== ""){array_push($arr_num_sotr, ' j.num_sotr = ' . $str_ar[1]);}
	}

	  $arr_num_sotr = implode(' OR ', $arr_num_sotr);
	  $sql_vst .= " AND (" . $arr_num_sotr . ") ";
	  
	//$sql_vst .= " AND (j.num_sotr=673) ";



	} else {
	  $arr = array();
	  $sotr_list = array();
	  if ($user_access['account_access_dep'] !== '0' && $user_access['account_access_dep'] !== '') {
		$allowed_deps = explode('|', $user_access['account_access_dep']);
		$q = "SELECT job_id FROM users WHERE ";
		foreach ($allowed_deps as $key => $value) {
		  array_push($arr, ' user_department = ' . $value);
		}
		$arr = implode(' OR ', $arr);
		$q .= $arr;
		$r = mysql_query($q);
		while($row = mysql_fetch_row($r))
		{
		  array_push($sotr_list, 'j.num_sotr = ' . $row[0]);
		}
	  } else {
		$q = "SELECT job_id FROM users WHERE user_department = " . $user_access['user_department'];
		$r = mysql_query($q);
		while($row = mysql_fetch_row($r))
		{
		  array_push($sotr_list, 'j.num_sotr = ' . $row[0]);
		}

	  }
	  $sotr_list = implode(' OR ', $sotr_list);
	  $sql_vst .= " AND (" . $sotr_list .")";

	}
	//$sql_vst .= " AND (j.num_sotr=673) ";
	if($izd_type){

	$izd_type_sotr = array();
	foreach ($str_arr as $v) {
	   $str_ar = explode("=", $v);
		if($str_ar[0] == "izd_type" and $str_ar[1] !== ""){array_push($izd_type_sotr, ' a.izd_type = ' . $str_ar[1]);}
	}

	  $izd_type_sotr = implode(' OR ', $izd_type_sotr);
	  $sql_vst .= " AND (" . $izd_type_sotr . ") ";

	 //   $sql_vst .= " AND a.izd_type = '$izd_type' ";

		}
	
	if($job){

	//т.к. в строке может передаваться сразу несколько значений с одинаковым идентификатором, то parse_str тут не срабоает, приходиться обрабатывать массив вручную

	$arr_jobs = array();
	foreach ($str_arr as $v) {
	   $str_ar = explode("=", $v);
		if($str_ar[0] == "job" and $str_ar[1] !== ""){array_push($arr_jobs, ' j.job = ' . $str_ar[1]);}
	}

	  $arr_jobs = implode(' OR ', $arr_jobs);
	  $sql_vst .= " AND (" . $arr_jobs . ") ";



	} else {
	  $jobs_allowed = explode('|', $user_access['jobs_access']);
	  $arr = array();
	  foreach ($jobs_allowed as $key => $value) {
		array_push($arr, ' j.job = ' . $value);
	  }
	  $arr = implode(' OR ', $arr);
	  $sql_vst .= " AND (" . $arr . ") ";
	}
	
	$sql_vst .= " AND (j.job={$dep}) ";
	if(is_numeric($izd_w)){$sql_vst .= " AND a.izd_w = '$izd_w' ";}

	if(is_numeric($izd_v)){$sql_vst .= " AND a.izd_v = '$izd_v' ";}

	if(is_numeric($izd_b)){$sql_vst .= " AND a.izd_b = '$izd_b' ";}



	if($from){$sql_vst .= " AND j.cur_time >= '$from' ";}


	if($to){


	$to = date('Y-m-d',strtotime($to . "+1 days"));

	$sql_vst .= " AND j.cur_time <= '$to' ";}


	$en_q = "SELECT j.*, j.uid AS job_uid, a.* FROM job AS j, applications AS a WHERE 1 AND j.num_ord = a.num_ord $sql_vst ORDER BY j.cur_time DESC";
	$en = mysql_query($en_q);
	while ($r = mysql_fetch_array($en)) {
        $qty = $r['num_of_work'];
        $job = $r['job'];
        $order_price = $r['order_price'];
        if (is_numeric($order_price) and $job !== '14') {
            $tek_rate = $order_price;
        } else {
            //если это не ордер, то берем базовый тариф, заложенный в заявке, а на выдачу надомнику и ручки с клипсами проставляем тариф вручную
            $tek_rate = round($r["rate_" . $job], 2);
            if ($tek_rate == "") {
                $tek_rate = get_tek_rate($job);
            }
        }
        $sum = round($qty * $tek_rate, 2);
        $total_sum = $total_sum + $sum;
        $tek_rate = "";
        $order_price = "";
    }
	$mas['val']=round($total_sum);
	*/
	$mas['dep']=$dep;
	//$mas['sql']=$en_q;
	//
}else{
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
					
                <? } 
				$mas['val']=form_num($ball_itg);$mas['dep']=$dep;
}			
				?>
                       

			<?php echo json_encode($mas);
			?>
           
			
