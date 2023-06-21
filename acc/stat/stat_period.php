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

// ----- перейти на главную если доступ запрещен ---------
if(!$auth) {
	header("Location: /");
	exit;
}


$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;

// если 1 -админ или бухгалтер, иначе 0 - менеджер
//$tpacc = (($user_type == 'acc') || ($user_type == 'adm')) ? 1 : 0;





// ----------------------------------- сортировка ---------------------------------

$sort_f = 'dat';
$order_f = 'desc';

if(isset($_GET['sort']) && !empty($_GET['sort']))
	$sort_f = $_GET['sort'];
else
	$sort_f = 'dat';

if(isset($_GET['order']) && !empty($_GET['order'])) 	// обратный, прямой порядок
	$order_f = $_GET['order'];
else
	$order_f = 'desc';

//---------------------------------------------------------------------------------





$curr_year = (isset($_GET['year']) && trim($_GET['year'])) ? $_GET['year'] : '';
$curr_month = (isset($_GET['month']) && trim($_GET['month'])) ? $_GET['month'] : '';

$arr_vals = array();

// возвращает true если для данного месяца в столбце есть хоть одно не нулевое значение
function check_cols($month) {
	global $arr_vals;
	for($i=0; $i<count($arr_vals); $i++) {
		if(isset($arr_vals[$i]['sum'][$month]) && ($arr_vals[$i]['sum'][$month]!=0))
			return true;
		if(isset($arr_vals[$i]['prib'][$month]) && ($arr_vals[$i]['prib'][$month]!=0))
			return true;
	}
	return false;
}

// возвращает сумму за месяц суммы счета и прибыли
function summ_month($month) {
	global $arr_vals;
	$sum = 0;
	$prib = 0;
	for($i=0; $i<count($arr_vals); $i++) {
		if(isset($arr_vals[$i]['sum'][$month]) && ($arr_vals[$i]['sum'][$month]!=0))
			$sum += $arr_vals[$i]['sum'][$month];
		if(isset($arr_vals[$i]['prib'][$month]) && ($arr_vals[$i]['prib'][$month]!=0))
			$prib += $arr_vals[$i]['prib'][$month];
	}
	return array($sum,$prib);
}

//Возвращает массив занчений array(сумма счета, прибыль);
function get_vals($num,$month) {
	global $arr_vals;
	$sum = ((!isset($arr_vals[$num]['sum'][$month])) || ($arr_vals[$num]['sum'][$month] == 0)) ? 0 : $arr_vals[$num]['sum'][$month];
	$prib = ((!isset($arr_vals[$num]['prib'][$month])) || ($arr_vals[$num]['prib'][$month] == 0)) ? 0 : $arr_vals[$num]['prib'][$month];
	return array($sum,$prib);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.1</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>
<? require_once("../templates/top.php"); ?>
<table width="750" border=0 align="center">
<tr>
<td>
<br>
<?
$tit = 'Статистика / Периоды';
$name_curr_page = 'stat';
require_once("../templates/main_menu.php");?>
<table width="1100" border=0 cellpadding="5" cellspacing="0" bgcolor="#F6F6F6" align="center">
	<tr>
		<td align="center" class="title_razd"><?=@$tit?></td>
	</tr>
	<tr>
		<td valign="top" align="center">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="110">
						<span class="sublink_pl">+</span>
						<a href="stat.php" class="sublink">Пользователи</a>
					</td>
					<td width="100">
						<span class="sublink_pl_off">+</span>
						<span class="sublink_off">Периоды</span>
					</td>
					<? if($tpacc) {?>
					<td width="100">
						<span class="sublink_pl">+</span>
						<a href="stat_table_tender.php" class="sublink">Тендеры</a>
          </td>
          			<?
          			}
          			 if(($user_type == 'sup') || ($user_type == 'acc'))  {
          			?>
					<td width="150">
						<span class="sublink_pl">+</span>
						<a href="stat_table_query.php" target="_blank" class="sublink">Работа с таблицами</a>					</td>
					<? } ?>
				</tr>
			</table></td>
</tr>
<tr>
	<?
	// СОЗДАНИЕ МАССИВА ПО ГОДАМ
	$arr_year = array();
	if($tpacc)
		$query = "SELECT DISTINCT YEAR(date_query) as year FROM queries WHERE type='0' AND ready='1' ORDER BY date_query ASC";
	else
		$query = sprintf("SELECT DISTINCT YEAR(date_query) as year FROM queries WHERE type='0' AND ready='1' AND user_id=%d ORDER BY date_query ASC",$user_id);

	$res = mysql_query($query);
	while($r = mysql_fetch_array($res)) {
		$arr_year[] = $r['year'];
	}
	if(!$curr_year)
		$curr_year = date("Y");

	if(count($arr_year)) {
	if(!in_array($curr_year, $arr_year))
		$curr_year = $arr_year[0];
		$fl_data = 1;
	}
	else
		$fl_data = 0;

	if($fl_data)	 {
	$arr_month = array();

	if($tpacc)
		$query = sprintf("SELECT DISTINCT MONTH(date_query) as month FROM queries WHERE type='0' AND ready='1' AND (prdm_sum_acc<>'0' OR podr_sebist<>'0') AND YEAR(date_query)=%d ORDER BY date_query ASC", $curr_year);
	else
		$query = sprintf("SELECT DISTINCT MONTH(date_query) as month FROM queries WHERE type='0' AND ready='1' AND (prdm_sum_acc<>'0' OR podr_sebist<>'0') AND YEAR(date_query)=%d  AND user_id=%d ORDER BY date_query ASC", $curr_year, $user_id);

	$res = mysql_query($query);
	while($r = mysql_fetch_array($res)) {
		$arr_month[] = $r['month'];
	}
	if(!$curr_month)
		$cur_month = date("n");

	if(!in_array($curr_month, $arr_month))
		$curr_month = $arr_month[0];

	}
	?>
  <td align="center">
  <? if($fl_data) {?>
  Выбрать год:&nbsp;&nbsp;
	<select name="" onchange="document.location.href='stat_period.php?year='+this.options[this.selectedIndex].value+'&month=<?=$curr_month?>';">
	<? for($i=0;$i<count($arr_year);$i++) {?>
	  <option value="<?=$arr_year[$i]?>" <?=($curr_year==$arr_year[$i])?'selected="selected" ':''?>><?=$arr_year[$i]?></option>
	<? } ?>
	</select>
	&nbsp;&nbsp;месяц:&nbsp;
	<select name="" onchange="document.location.href='stat_period.php?year=<?=$curr_year?>&month='+this.options[this.selectedIndex].value;">
	<? for($i=0;$i<count($arr_month);$i++) {?>
	  <option value="<?=$arr_month[$i]?>" <?=($curr_month==$arr_month[$i])?'selected="selected" ':''?>><?=$month_sel[($arr_month[$i]-1)]?></option>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<td align="center">
		<table border="0" cellpadding="3" cellspacing="2" bordercolor="#999999" >
			<tr>
				<td><img src="/i/pix.gif" width="150" height="1"></td>
				<? if($tpacc) {?>
				<td><img src="/i/pix.gif" width="150" height="1"></td>
				<? } ?>
				<td><img src="/i/pix.gif" width="70" height="1"></td>
				<td><img src="/i/pix.gif" width="80" height="1"></td>
				<td><img src="/i/pix.gif" width="70" height="1"></td>
			</tr>
<?

	$order_f = ($order_f == 'asc') ? 'ASC' : 'DESC';
	$order_d = ($order_f == 'ASC') ? 'DESC' : 'ASC';

	if($tpacc)
		$query_sort = 'a.date_query '.$order_f.',b.surname ASC';
	else
		$query_sort	= 'a.date_query '.$order_f;

	if($sort_f == 'name')
		$query_sort = 'b.surname '.$order_f;
	elseif($sort_f == 'sum_acc')
		$query_sort = 'CAST(a.prdm_sum_acc AS unsigned) '.$order_f;
	elseif($sort_f == 'cost')
		$query_sort = '(a.prdm_sum_acc - a.podr_sebist) '.$order_f;

	// --------- СОЗДАНИЕ МАССИВА ЗНАЧЕНИЙ СУММЫ И ПРИБЫЛИ ДЛЯ ВСЕХ МЕНЕДЖЕРОВ -----
	if($tpacc)
		$query = sprintf("SELECT a.uid,a.prdm_sum_acc,a.podr_sebist,a.prdm_num_acc,a.date_query,b.surname,b.name,b.father,b.type FROM queries as a, users as b WHERE  a.ready='1' AND YEAR(a.date_query)=%d AND MONTH(a.date_query)=%d AND a.type='0' AND a.user_id=b.uid ORDER BY %s",$curr_year, $curr_month, $query_sort);
	else
		$query = sprintf("SELECT a.uid,a.prdm_sum_acc,a.podr_sebist,a.prdm_num_acc,a.date_query FROM queries as a WHERE  a.ready='1' AND YEAR(a.date_query)=%d AND MONTH(a.date_query)=%d AND a.type='0' AND a.user_id=%d ORDER BY %s",$curr_year, $curr_month, $user_id, $query_sort);

//	$query = "SELECT * FROM users WHERE type<>'oth' ORDER BY type,surname";
	$res = mysql_query($query);
	$num = 0;

	$sum_acc_res = 0;
	$sum_cost_res = 0;



	while($r = mysql_fetch_array($res)) {

		if($tpacc) {
			$full_name = $r['surname'].' '.$r['name'].' '.$r['father'];
			$arr_vals[$num]['name'] = $full_name;
		}
		$arr_vals[$num]['num_acc'] = $r['prdm_num_acc'];

		$query = "SELECT * FROM obj_accounts WHERE query_id=".$r['uid']." ORDER BY nn";
		$res_podr = mysql_query($query);

		$arr_vals[$num]['podr'][0]['name'] = '';
		$i_podr = 0;

		while($r_podr = mysql_fetch_array($res_podr)) {
			$arr_vals[$num]['podr'][$i_podr]['name'] = $r_podr['name'];
			$arr_vals[$num]['podr'][$i_podr]['num'] = $r_podr['num'];
			$arr_vals[$num]['podr'][$i_podr]['price'] = $r_podr['price'];
			$i_podr++;
		}

//		echo '<pre>';
//		print_r($arr_vals[$num]);
//		echo '</pre>';

		$tmp = explode(' ', $r['date_query']);
		$tmp2 = explode('-',$tmp[0]);
		$date_str2 = $tmp2[2].' '.$month[intval($tmp2[1])-1].' '.$tmp2[0].' г.';

		$arr_vals[$num]['date'] = $date_str2;

		$summ_acc = (isset($r['prdm_sum_acc'])) ? $r['prdm_sum_acc'] : 0;
		$summ_cost = (isset($r['podr_sebist'])) ? $r['podr_sebist'] : 0;
//		if(($summ_acc == 0) && ($summ_cost == 0))
//			continue;

		$arr_vals[$num]['sum'] = 		round($summ_acc);		// сумма счета
		$arr_vals[$num]['prib'] = 	round($summ_acc - $summ_cost);		// прибыль

		$sum_acc_res += $arr_vals[$num]['sum'];
		$sum_cost_res += $arr_vals[$num]['prib'];

		$num++;
	}
/*		echo '<pre>';
		print_r($arr_vals);
		echo '</pre>';
		exit;
*/

		$img_s = '';
		$src_s = (strtolower($order_f) == 'asc') ? 'order_desc_active' : 'order_asc_active';
		$img_s = '<img src="/i/icons/'.$src_s.'.gif" width="10" height="6" />';

	// ----------------------------------------------------------------------------
?>
			<tr class="tab_query_tit">
				<td align="center" class="tab_query_tit">
					<? $order = 'asc';
					if( $sort_f == 'dat' )  {
						$order = ((strtolower($order_f) == 'asc') ? 'desc' : 'asc');
					}
					$alt_tit = 'Дата выполнения запроса';
					$link = '<a href="stat_period.php?year='.$curr_year.'&month='.$curr_month.'&sort=dat&order='.$order.'" onmouseover="Tip(\'%s\')">';
					echo sprintf($link, $alt_tit).'Дата запроса'.'</a>&nbsp;';

					if( $sort_f == 'dat' ) {
						$alt_sort = 'Сортировка по дате запроса';
						echo sprintf($link.$img_s,$alt_sort).'</a>';
					} else { ?>
					<img src="/i/pix.gif" width="8" height="6">
					<?}?>
				</td>
				<? if($tpacc) {?>
				<td align="center" class="tab_query_tit">
					<? $order = 'asc';
					if( $sort_f == 'name' )  {
						$order = ((strtolower($order_f) == 'asc') ? 'desc' : 'asc');
					}
					$alt_tit = 'ФИО пользователя';
					$link = '<a href="stat_period.php?year='.$curr_year.'&month='.$curr_month.'&sort=name&order='.$order.'" onmouseover="Tip(\'%s\')">';
					echo sprintf($link, $alt_tit).'Пользователь'.'</a>&nbsp;';

					if( $sort_f == 'name' ) {
						$alt_sort = 'Сортировка по фамилии пользователя';
						echo sprintf($link.$img_s,$alt_sort).'</a>';
					} else { ?>
					<img src="/i/pix.gif" width="10" height="6">
					<?}?>
				</td>
				<? } ?>
				<td align="center" class="tab_query_tit">
					Номер счета
				</td>
				<td align="center" class="tab_query_tit">
					<? $order = 'asc';
					if( $sort_f == 'sum_acc' )  {
						$order = ((strtolower($order_f) == 'asc') ? 'desc' : 'asc');
					}
					$alt_tit = 'Сумма счета';
					$link = '<a href="stat_period.php?year='.$curr_year.'&month='.$curr_month.'&sort=sum_acc&order='.$order.'" onmouseover="Tip(\'%s\')">';
					echo sprintf($link, $alt_tit).'Сумма счета'.'</a>&nbsp;';

					if( $sort_f == 'sum_acc' ) {
						$alt_sort = 'Сортировка по сумме счета';
						echo sprintf($link.$img_s,$alt_sort).'</a>';
					} else { ?>
					<img src="/i/pix.gif" width="10" height="6">
					<?}?>
				</td>
				<td align="center" class="tab_query_tit">
					<? $order = 'asc';
					if( $sort_f == 'cost' )  {
						$order = ((strtolower($order_f) == 'asc') ? 'desc' : 'asc');
					}
					$alt_tit = 'Прибыль';
					$link = '<a href="stat_period.php?year='.$curr_year.'&month='.$curr_month.'&sort=cost&order='.$order.'" onmouseover="Tip(\'%s\')">';
					echo sprintf($link, $alt_tit).'Прибыль'.'</a>&nbsp;';

					if( $sort_f == 'cost' ) {
						$alt_sort = 'Сортировка по прибыли';
						echo sprintf($link.$img_s,$alt_sort).'</a>';
					} else { ?>
					<img src="/i/pix.gif" width="10" height="6">
					<?}?>
				</td>
			</tr>
<?

	for($i=0;$i<count($arr_vals);$i++) {	// цикл по количеству менеджеров
		$alt_podr = '';
		for($p=0;$p<count($arr_vals[$i]['podr']);$p++) {
			if(isset($arr_vals[$i]['podr'][$p]['name']) && trim($arr_vals[$i]['podr'][$p]['name']))
				$alt_podr.= '<strong>'.$arr_vals[$i]['podr'][$p]['name'].'</strong> / '.$arr_vals[$i]['podr'][$p]['num'].' шт. / '.$arr_vals[$i]['podr'][$p]['price'].' руб.<br>';
		}
		$alt_podr = htmlspecialchars($alt_podr);

		$alt_podr = (trim($alt_podr)) ? 'onmouseover="Tip(\'<div class=stat_podr_alt>'.$alt_podr.'</div>\',TITLE,\'<div class=stat_podr_alttit>Предмет счета</div>\')"' : '';
?>
			<tr>
				<td align="center" class="tab_td_norm"><span class="stat_date"><?=$arr_vals[$i]['date']?></span></td>
				<? if($tpacc) {?>
				<td align="left" class="tab_td_norm"><span class="stat_user"><?=$arr_vals[$i]['name']?></span></td>
				<? } ?>
				<td align="center" class="tab_td_norm" <?=$alt_podr?>><span class="stat_sum_acc"><?=$arr_vals[$i]['num_acc']?></span></td>
				<td align="center" class="tab_td_norm"><span class="stat_sum_acc"><?=$arr_vals[$i]['sum']?></span></td>
				<td align="center" class="tab_td_norm"><span class="stat_sum_acc"><?=$arr_vals[$i]['prib']?></span></td>
			</tr>
<? } ?>
		<tr>
			<td align="center" class="tab_query_bottom">суммарная информация</td>
			<? if($tpacc) {?>
			<td class="tab_query_bottom">&nbsp;</td>
			<? } ?>
			<td class="tab_query_bottom">&nbsp;</td>
			<td align="center" class="tab_query_bottom"><?=$sum_acc_res?></td>
			<td align="center" class="tab_query_bottom"><?=$sum_cost_res?></td>
		</tr>
		</table>
   <? } else {?>
   данные отсутствуют
    <? } ?>
	</td>
</tr>
<tr>
  <td align="center" height="50">&nbsp;</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
<? ob_end_flush(); ?>