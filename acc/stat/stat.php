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
$arr_vals = array();
$curr_year = (isset($_GET['year']) && trim($_GET['year'])) ? $_GET['year'] : '';





// изменение плана на месяц
if(isset($_POST['prib_butt']) && trim($_POST['prib_butt'])) {

	$query = sprintf("DELETE FROM plan_users WHERE user_id=%d AND year=%d AND month=%d", $_POST['us_id'], $_POST['nm_year'], $_POST['nm_month']);
	mysql_query($query);

	$query = sprintf("INSERT INTO plan_users(user_id,year,month,summ) VALUES(%d,%d,%d,'%s')", $_POST['us_id'], $_POST['nm_year'], $_POST['nm_month'], $_POST['summ_pr']);
	mysql_query($query);
}





// ----------------------------------- сортировка ---------------------------------

$sort_f = 'type';
$order_f = 'asc';

if(isset($_GET['sort']) && !empty($_GET['sort']))
	$sort_f = $_GET['sort'];
else
	$sort_f = 'type';

if(isset($_GET['order']) && !empty($_GET['order'])) 	// обратный, прямой порядок
	$order_f = $_GET['order'];
else
	$order_f = 'asc';

//---------------------------------------------------------------------------------







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
function summ_month($yr,$month) {
	global $arr_vals;
	$sum = 0;
	$prib = 0;
	$sum_proc = 0;
	$plan = 0;

	for($i=0; $i<count($arr_vals[$yr]['list']); $i++) {
		if(isset($arr_vals[$yr]['list'][$i]['sum'][$month]) && ($arr_vals[$yr]['list'][$i]['sum'][$month]!=0))
			$sum += $arr_vals[$yr]['list'][$i]['sum'][$month];
		if(isset($arr_vals[$yr]['list'][$i]['prib'][$month]) && ($arr_vals[$yr]['list'][$i]['prib'][$month]!=0))
			$prib += $arr_vals[$yr]['list'][$i]['prib'][$month];
		if(isset($arr_vals[$yr]['list'][$i]['proc'][$month]) && ($arr_vals[$yr]['list'][$i]['proc'][$month]!=0))
			$sum_proc += $arr_vals[$yr]['list'][$i]['proc'][$month];
		if(isset($arr_vals[$yr]['list'][$i]['plan'][$month]) && ($arr_vals[$yr]['list'][$i]['plan'][$month]!=0))
			$plan += $arr_vals[$yr]['list'][$i]['plan'][$month];
	}
	return array($sum,$prib,$plan);
}

//Возвращает массив занчений array(сумма счета, прибыль);
function get_vals($yrnm,$num,$month) {
	global $arr_vals;
	$sum = ((!isset($arr_vals[$yrnm]['list'][$num]['sum'][$month])) || ($arr_vals[$yrnm]['list'][$num]['sum'][$month] == 0)) ? 0 : $arr_vals[$yrnm]['list'][$num]['sum'][$month];
	$prib = ((!isset($arr_vals[$yrnm]['list'][$num]['prib'][$month])) || ($arr_vals[$yrnm]['list'][$num]['prib'][$month] == 0)) ? 0 : $arr_vals[$yrnm]['list'][$num]['prib'][$month];
//	$prib = $sum - $prib;
	$proc = ((!isset($arr_vals[$yrnm]['list'][$num]['proc'][$month])) || ($arr_vals[$yrnm]['list'][$num]['proc'][$month] == 0)) ? 0 : $arr_vals[$yrnm]['list'][$num]['proc'][$month];
//	$proc = ($prib !=0) ? number_format(($proc*100)/$prib,1) : 0;
	return array($sum,$prib,$proc);
}

function swith_arr_val($nm,$month) {
	global $arr_vals;
	$tmp_val = @$arr_vals[$nm];
	$arr_vals[$nm] = @$arr_vals[$nm+1];
	$arr_vals[$nm+1] = $tmp_val;
}





	// СОЗДАНИЕ МАССИВА ПО ГОДАМ

	$arr_year = array();

    	if(!$curr_year)
		$curr_year = date("Y");				// текущий год


    //три года отнимаем
    if($_GET["act"] == "all")
     {$last_three="2003";}else{
    $last_three = $curr_year-3;}

	if($tpacc)
		$query = "SELECT DISTINCT YEAR(date_query) as year FROM queries WHERE type='0' AND ready='1' ORDER BY date_query DESC";
	else
		$query = "SELECT DISTINCT YEAR(date_query) as year FROM queries WHERE type='0' AND ready='1' AND user_id=".$user_id." ORDER BY date_query DESC";

	$res = mysql_query($query);
	while($r = mysql_fetch_array($res)) {
       if($r['year'] >= $last_three){
		$arr_year[] = $r['year'];}
	}

	if(count($arr_year)) {					// если есть данные в базе
		if(!in_array($curr_year, $arr_year))
			$curr_year = $arr_year[0];	// первый год, имеющийся в базе
			$fl_data = 1;								// если 1 значит таблица не пустая
		}
	else
		$fl_data = 0;									// иначе пустая, не показывать





?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.2</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>

<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
<script src="../includes/js/jquery.cookie.js"></script>
<script language="javascript">
<!--
function foc_prib() {
	document.getElementById('summ_pr').focus();
}
//-->
</script>

<body>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>
<? require_once("../templates/top.php"); ?>
<table width="750" border=0 align="center">
<tr>
<td>
<br>
<?
$tit = 'Статистика / Пользователи';
$name_curr_page = 'stat';
require_once("../templates/main_menu.php");?>
<table width="1200" border=0 cellpadding="5" cellspacing="0" bgcolor="#F6F6F6" align="center">
	<tr>
		<td align="center" class="title_razd"><?=@$tit?></td>
	</tr>
	<tr>
		<td valign="top" align="center">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="110">
						<span class="sublink_pl_off">+</span>
						<span class="sublink_off">Пользователи</span></td>
					<td width="100">
						<span class="sublink_pl">+</span>
						<a href="stat_period.php" class="sublink">Периоды</a>
					</td>
					<? if($tpacc) {?>
					<td width="100">
						<span class="sublink_pl">+</span>
						<a href="stat_table_tender.php" class="sublink">Тендеры</a>
          </td>     <?
          			}
          			if(($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg'))  { ?>
					<td width="150">
						<span class="sublink_pl">+</span>
						<a href="stat_table_query.php" target="_blank" class="sublink">Работа с таблицами</a>					</td>
					<? } ?>

                    	<td width="200">
						<span class="sublink_pl">+</span>
						<a href="stat_shop.php" class="sublink">Товары и магазин</a>
					</td>
				</tr>
			</table></td>
</tr>

<tr>
	<td align="center">
  	<? if($fl_data) {?>
		<table border="0" cellpadding="3" cellspacing="2" bordercolor="#999999" >
			<tr>
				<td><img src="/i/pix.gif" width="60" height="1"></td>
				<? if($tpacc) {?>
				<td>
                <?if($_GET["act"] !== "all"){?>
               <a href="?act=all">все годы</a>
                 <?}else{?>
               <a href="?act=">последние 3 года</a>
                 <?}?>
                </td>
				<? }
				for($i=11;$i>=0;$i--) {
					if(1) {?>
				<td><img src="/i/pix.gif" width="1" height="1"></td>
				<?}}?>
			</tr>
<?

	$order_f = ($order_f == 'asc') ? 'ASC' : 'DESC';
	$order_d = ($order_f == 'ASC') ? 'DESC' : 'ASC';

	$query_sort = 'type '.$order_f;

	if($sort_f == 'name')
		$query_sort = 'surname '.$order_f;
	elseif($sort_f == 'type')
		$query_sort = 'type '.$order_f.',surname ASC';





if($fl_data) {

	// --------- СОЗДАНИЕ МАССИВА ЗНАЧЕНИЙ СУММЫ И ПРИБЫЛИ ДЛЯ ВСЕХ МЕНЕДЖЕРОВ -----

	$arr_users = array();		// массив пользователей: ид, полное имя

	if($tpacc)
		$query = "SELECT * FROM users WHERE type<>'oth' AND proizv!='1' ORDER BY ".$query_sort;
	else
		$query = "SELECT * FROM users WHERE type<>'oth' AND proizv!='1' uid=".$user_id." ORDER BY ".$query_sort;

	$res = mysql_query($query);

	$i=0;
	while($r = mysql_fetch_array($res)) {
		$arr_users[$i]['id'] = $r['uid'];
		$arr_users[$i]['full_name'] = $r['surname'].' '.$r['name'].' '.$r['father'];
		$i++;
	}


	for($y=0;$y<count($arr_year);$y++) {		// цикл по годам

	  $c_yr = $arr_year[$y];	// следующий год в списке

		$arr_vals[$y]['year'] = $c_yr;

		$num = 0;


		for($u=0;$u<count($arr_users);$u++) {		// цикл по пользователям

			$arr_vals[$y]['list'][$num]['name'] = $arr_users[$u]['full_name'];


			for($i=1;$i<=12;$i++) {						// цикл по месяцам

				$summ_acc = 0;		// сумма счета
				$summ_cost = 0;		// общая себистоимость
				$proc = 0;				// процент

			 if($tpacc)
					$query = sprintf("SELECT prdm_sum_acc,podr_sebist,percent FROM queries WHERE user_id=%d AND MONTH(date_query)=%d AND ready='1' AND YEAR(date_query)=%d ORDER BY prdm_sum_acc", $arr_users[$u]['id'], $i, $c_yr);
				else
					$query = sprintf("SELECT prdm_sum_acc,podr_sebist,percent FROM queries WHERE user_id=%d AND MONTH(date_query)=%d AND ready='1' AND YEAR(date_query)=%d AND user_id=%d ORDER BY prdm_sum_acc", $arr_users[$u]['id'], $i, $c_yr, $arr_users[$u]['id']);

				$res_val = mysql_query($query);


				while($r_val = mysql_fetch_array($res_val)) {

					$summ_acc+=intval($r_val['prdm_sum_acc']);
					$summ_cost+=intval($r_val['podr_sebist']);

					// расчет суммы процента : ((сумма счета - себистоимость)*процент)/100%
					$tmp = ((intval($r_val['prdm_sum_acc']) - intval($r_val['podr_sebist']))*$r_val['percent'])/100;
					$proc += $tmp;
				}
				if($summ_acc!=0)
					$arr_vals[$y]['list'][$num]['sum'][$i-1] = 		$summ_acc;								// сумма счета
				if($summ_cost)
					$arr_vals[$y]['list'][$num]['prib'][$i-1] = 	$summ_acc - $summ_cost;		// прибыль
				if($proc)
					$arr_vals[$y]['list'][$num]['proc'][$i-1] = 	$proc;										// cумма процента

				// чтение плана на месяц для данного пользователя
				$query = sprintf("SELECT summ FROM plan_users WHERE user_id=%d AND year=%d AND month=%d LIMIT 1", $arr_users[$u]['id'], $c_yr, $i);

				$res_pr = mysql_query($query);

				if(!$r_pr = mysql_fetch_array($res_pr))
					$r_pr = 0;
				else
					$r_pr = $r_pr['summ'];

				$arr_vals[$y]['list'][$num]['plan'][$i-1] = $r_pr;							// план на месяц
				$arr_vals[$y]['list'][$num]['user_id'] = $arr_users[$u]['id'];	// ид пользователя
			}
			$num++;
		}
	}






	// сортировка массива по выбранному месяцу
	$sort_month = (isset($_GET['month'])) ? $_GET['month'] : 1;

	if($sort_f == 'acc') {
		$fl = 1;
		while($fl) {
			$fl = 0;
			for($i=0;$i<(count($arr_vals)-1);$i++) {
				if(strtolower($order_f) == 'asc') {
					if((@$arr_vals[$i]['sum'][$sort_month-1] > @$arr_vals[$i+1]['sum'][$sort_month-1])) {
						swith_arr_val($i,$sort_month);
						$fl = 1;
					}
				}
				else {
					if(@$arr_vals[$i]['sum'][$sort_month-1] < @$arr_vals[$i+1]['sum'][$sort_month-1]) {
						swith_arr_val($i,$sort_month);
						$fl = 1;
					}
				}
			}
		}
	}


}



	// ----------------------------------------------------------------------------

		$img_s = '';
		$src_s = (strtolower($order_f) == 'asc') ? 'order_desc_active' : 'order_asc_active';
		$img_s = '<img src="/i/icons/'.$src_s.'.gif" />';



?>
			<tr class="tab_query_tit">
			<td align="center" class="tab_query_tit">
				Год
			</td>
			<? if($tpacc) {?>
			<td align="center" class="tab_query_tit">
				<?
				$order = 'asc';
				if( $sort_f == 'name' )  {
					$order = ((strtolower($order_f) == 'asc') ? 'desc' : 'asc');
				}
				$alt_tit = 'Пользователь запросивший счет';
				$link = '<a href="stat.php?year='.$curr_year.'&sort=name&order='.$order.'" onmouseover="Tip(\'%s\')">';
				echo sprintf($link, $alt_tit).'Пользователь'.'</a>&nbsp;';

				if( $sort_f == 'name' ) {
					$alt_sort = 'Сортировка по фамилии пользователя';
					echo sprintf($link.$img_s,$alt_sort).'</a>';
				}?>

				</td>
				<? }
				 for($i=0;$i<=11;$i++) {											// заголовок месяцев
						if(1) { ?>
				<td align="center" class="tab_query_tit">
					<?
					$order = 'asc';
					if( ($sort_f == 'acc') && (($sort_month-1) == $i) )
						$order = ((strtolower($order_f) == 'asc') ? 'desc' : 'asc');

					$alt_tit = 'Период';
					$link = '<a href="stat.php?year='.$curr_year.'&sort=acc&order='.$order.'&month='.($i+1).'" onmouseover="Tip(\'%s\')">';
					echo sprintf($link, $alt_tit).$month_sel[$i].'</a>&nbsp;';

					if( ($sort_f == 'acc') && (($sort_month-1) == $i) ) {
						$alt_sort = 'Сортировка сумме счета';
						echo sprintf($link.$img_s,$alt_sort).'</a>';
					} ?>
				</td>
				<?	}} ?>
			</tr>







<?
	// цикл по годам
	for($i_yr=0;$i_yr<count($arr_vals);$i_yr++) {

		$curr_year = $arr_vals[$i_yr]['year'];



		// цикл по пользователям
		for($i=0;$i<count($arr_vals[$i_yr]['list']);$i++) {
?>
			<form action="stat.php" method="post" name="stat_us">
			<tr>
				<? if($i==0) {?>
				<td rowspan="<?=count($arr_vals[$i_yr]['list'])?>" align="center" class="tab_td_year"><strong><?=$curr_year?></strong></td>
				<? }
				if($tpacc) {		// если админ, показать имя след пользователя в списке?>
				<td align="left" class="tab_td_norm">
        	<a href="stat_table_query.php?filtr=manager&case=manager&val=<?=$arr_vals[$i_yr]['list'][$i]['user_id']?>&clear=1" target="_blank" class="stat_user">
						<?=$arr_vals[$i_yr]['list'][$i]['name']?>
           </a>

        </td>
				<? }



				// цикл по месяцам
				for($j=1;$j<=12;$j++) {

					if($tpacc) {	// подсказка дла поля план на месяц
						$alt_stat = 'Tip(\'<table height=10 border=0 cellspacing=0 cellpadding=0><tr onmouseover=\\\'foc_prib();\\\'><td valign=top><form action=\\\'\\\' name=ab method=post>';
						$alt_stat .= '<input name=nm_year type=hidden value='.$curr_year.' />';
						$alt_stat .= '<input name=nm_month type=hidden value='.$j.' />';
						$alt_stat .= '<input name=us_id type=hidden value='.$arr_vals[$i_yr]['list'][$i]['user_id'].' />';
						$alt_stat .= '<input title=\\\'\\\' name=summ_pr id=summ_pr type=text class=inp_hint_acc  value=\\\'\\\'  maxlength=50 />';
						$alt_stat .= '<input class=butt_hint_acc name=prib_butt type=submit id=accok value=OK />';
						$alt_stat .= '</td></tr></form></table>';
						$alt_stat .= '\', CLOSEBTN, 1, STICKY, 1, DURATION, 0, OFFSETX, 0, OFFSETY, -5, FOLLOWMOUSE, 0, CENTERMOUSE, 1, DELAY, 800, TITLE, \'План месяца\')';
					}
					else
						$alt_stat = 'Tip(\'План на месяц\')';



					// получение значения суммы и прибыли для данного пользователя
					$tmp_val = get_vals($i_yr,$i,$j-1);

					?> <td align="center" class="tab_td_norm"> <?
					if($tmp_val[0] == 0)
						echo '<span class="stat_null"  onmouseover="Tip(\'Запросов не было\')">*&nbsp;&nbsp;</span>';
					else
						echo '<span class="stat_sum_acc" onmouseover="Tip(\'Сумма счета\')">'.$tmp_val[0].'</span>&nbsp;&nbsp;';

					if($arr_vals[$i_yr]['list'][$i]['plan'][$j-1] == 0)
						echo '<span class="stat_null"  onmouseover="'.$alt_stat.'">*</span>&nbsp;&nbsp;';
					else
						echo '<span class="stat_plan_num"  onmouseover="'.$alt_stat.'">'.$arr_vals[$i_yr]['list'][$i]['plan'][$j-1].'</span>&nbsp;&nbsp;';


					if($tmp_val[1] == 0)
						echo '<span class="stat_null"  onmouseover="Tip(\'Прибыли нет\')">*</span>';
					else {
						if($tpacc) {
							echo '<a target="_blank" href="stat_table_query.php?filtr=datman&case='.$j.'&val='.$arr_vals[$i_yr]['list'][$i]['user_id'].'&clear=1&year='.$curr_year.'" class="stat_tot_cost"  onmouseover="Tip(\'Прибыль,<br> (процент: '.$tmp_val[2].')\')">'.$tmp_val[1].'</a>';
						}
						else {
							echo '<span class="stat_tot_cost"  onmouseover="Tip(\'Прибыль,<br> (процент: '.$tmp_val[2].')\')">'.$tmp_val[1].'</span>';

						}
					}
					?>
					</td>
					<? }?>
			</tr>
		</form>



<? } if($tpacc) {?>
		<tr>
			<td align="center" class="tab_query_bottom" colspan="2">суммарная информация</td>

			<? for($i=0;$i<=11;$i++) {	// заголовк месяца
					if(1) {
						$arr_summ_month = summ_month($i_yr,$i);
						if(!$arr_summ_month[0] && !$arr_summ_month[1] && !$arr_summ_month[2]) {
						?>
						<td align="center" class="tab_query_bottom"><span class="stat_empty">---</span></td>
						<? } else { ?>
						<td align="center" class="tab_query_bottom"><span class="stat_sum_acc"><?=$arr_summ_month[0]?></span> <span class="stat_plan_num"><?=$arr_summ_month[2]?></span> <a href="stat_table_query.php?filtr=dat&case=<?=($i+1)?>&val=<?=($i+1)?>&clear=1" target="_blank" class="stat_tot_cost"><?=$arr_summ_month[1]?></a></td>
					<?	}
						}
          } ?>
		</tr>
		<? }
		} ?>
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