<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом 
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1 
header("Pragma: no-cache"); // HTTP/1.1 
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

$auth = false;

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");

// ----- перейти на главную если доступ запрещен ---------
if(!$auth) {
	header("Location: /");
	exit;
}

//  ------------- разбиение по страницам -------------
$rows_onpage = 15; 		// запросов на странице
$all_pages = false; 	// истина при нажатии ссылки "показать все"
$page = 1;						// страница по умолчанию

if(isset($_GET['page'])) {
	if(is_numeric($_GET['page']))
		$page = ($_GET['page']) > 0 ? $_GET['page'] : 1;
	elseif($_GET['page'] = 'all') 
		$all_pages = true;
}	

if(isset($_GET['set']) && is_numeric($_GET['set'])) {
//	echo $_GET['set'];
	$query = "UPDATE queries SET date_ready=NOW(),ready='1' WHERE uid=".$_GET['set'];
	mysql_query($query);
}

if(isset($_POST['acc_butt']) && trim($_POST['acc_butt'])) {
	$tmp_id = $_POST['acc_id'];
	$tmp_acc = trim($_POST['set_acc']);
	if($tmp_acc) {
		if( is_numeric($tmp_acc) ) {
			$query = sprintf("UPDATE queries SET date_ready=NOW(),ready='1' WHERE uid=%d", $tmp_id);
			mysql_query($query);
			$query = "SELECT query_id FROM queries WHERE uid=".$tmp_id;
			$res = mysql_query($query);
			$r = mysql_fetch_array($res);
			$query = sprintf("UPDATE data_queries SET acc_number='%s' WHERE uid=%d", $tmp_acc, $r['query_id']);
			mysql_query($query);
		}
		elseif( (strtolower($tmp_acc) == 'нет') || (strtolower($tmp_acc) == 'no') || ($tmp_acc == '-') ) {
			$query = sprintf("UPDATE queries SET date_ready=NOW(),ready='1' WHERE uid=%d", $tmp_id);
			mysql_query($query);
			$query = "SELECT query_id FROM queries WHERE uid=".$tmp_id;
			$res = mysql_query($query);
			$r = mysql_fetch_array($res);
			$query = sprintf("UPDATE data_queries SET acc_number='none' WHERE uid=%d", $r['query_id']);
			mysql_query($query);
		}
	}
}

function del_unlink_query() {
	// удаление несвязных запросов и клиентов
	$query = "SELECT queries.uid FROM queries LEFT JOIN data_queries ON queries.query_id=data_queries.uid WHERE data_queries.uid IS NULL";
	$res = mysql_query($query);
	while($r = mysql_fetch_array($res)) {
		$query = "DELETE FROM queries WHERE uid=".$r['uid'];
		mysql_query($query);
	}
	
	$query = "SELECT clients.uid FROM clients LEFT JOIN data_queries ON clients.uid=data_queries.client_id WHERE data_queries.client_id IS NULL";
	$res = mysql_query($query);
	while($r = mysql_fetch_array($res)) {
		$query = "DELETE FROM clients WHERE uid=".$r['uid'];
		mysql_query($query);
	}
}

function del_query($id) {
	$query = "SELECT query_id FROM queries WHERE uid=".$id;
	$res = mysql_query($query);
	$r = mysql_fetch_array($res);
	$query = sprintf("SELECT uid FROM queries WHERE query_id=%d AND uid<>%d",$r['query_id'],$id);
	$res = mysql_query($query);
	if(!(mysql_num_rows($res))) {
		$query = "DELETE FROM data_queries WHERE uid=".$r['query_id'];
		mysql_query($query);
	}	
	$query = "DELETE FROM queries WHERE uid=".$id;
	mysql_query($query);
}


//	------------------- Удаление запроса кнопкой удалить -----------------
if(isset($_GET['del']) && is_numeric($_GET['del']))	{
	del_query($_GET['del']);
	del_unlink_query();
/*	$query = "SELECT query_id FROM queries WHERE uid=".$_GET['del'];
	$res = mysql_query($query);
	$r = mysql_fetch_array($res);
	$query = sprintf("SELECT uid FROM queries WHERE query_id=%d AND uid<>%d",$r['query_id'],$_GET['del']);
	$res = mysql_query($query);
	if(!(mysql_num_rows($res))) {
		$query = "DELETE FROM data_queries WHERE uid=".$r['query_id'];
		mysql_query($query);
	}	
	$query = "DELETE FROM queries WHERE uid=".$_GET['del'];
	mysql_query($query);
	
	// удаление несвязных запросов и клиентов
	$query = "SELECT queries.uid FROM queries LEFT JOIN data_queries ON queries.query_id=data_queries.uid WHERE data_queries.uid IS NULL";
	$res = mysql_query($query);
	while($r = mysql_fetch_array($res)) {
		$query = "DELETE FROM queries WHERE uid=".$r['uid'];
		mysql_query($query);
	}
	
	$query = "SELECT clients.uid FROM clients LEFT JOIN data_queries ON clients.uid=data_queries.client_id WHERE data_queries.client_id IS NULL";
	$res = mysql_query($query);
	while($r = mysql_fetch_array($res)) {
		$query = "DELETE FROM clients WHERE uid=".$r['uid'];
		mysql_query($query);
	} */
}

// ------------------- Удаление нескольких запросов ---------------------
if(isset($_POST['subm']) && trim($_POST['subm'])) {
	$ch_arr = $_POST['ch_arr'];
	
	for($i=0;$i<count($ch_arr);$i++) {
		$query = "DELETE FROM ";
			del_query($ch_arr[$i]);
	}
	del_unlink_query();
}
// ---------------------------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.1</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>
<script language="JavaScript" type="text/javascript">
<!--
function del_query(id,page) {
	if(confirm("Удалить?")) 
		document.location = 'query_list.php?del=' + id + '&page=' + page;
}

function sel_all() {
	obj = document.list_f;
	for(i=0;i<obj.elements.length;i++)  {
		if(obj.elements[i].type == 'checkbox')
			obj.elements[i].checked = 1;
	}
}

function unsel_all() {
	obj = document.list_f;
	for(i=0;i<obj.elements.length;i++)  {
		if(obj.elements[i].type == 'checkbox')
			obj.elements[i].checked = 0;
	}
}

function confr_submit() {
	obj = document.list_f;
	num = 0;
	for(i=0;i<obj.elements.length;i++)  {
		if(obj.elements[i].type == 'checkbox') {
			if(obj.elements[i].checked)
				num++;
		}	
	}
	if(num > 0) {
		if(confirm("Удалить " + num + " выбранных запросов?")) 
			document.list_f.submit();
	}	
	return;
} 
//-->
</script>

<body>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>
<table align=center width=750 border=0>
<form name=list_f action="" method="post">
<input name="subm" type="hidden" value="1" />
<tr>
<td align=center width="270" height="62">
<a href="http://printfolio.ru"><img src="/i/pf.gif" alt="" width="270" height="62" border="0"></a>
</td>
<td align=center width="190"><a href="http://comcad.ru"><img src="/i/cm.gif" alt="" width="180" height="51" border="0"></a></td>
<td align=center width="200"><? if($auth) require_once("../includes/auth_form.php"); ?></td>
</tr>

<tr>
<td colspan=3>
<br>
<table align=center border=0 cellpadding="0" cellspacing="0">
<tr>
<td background="/i/bgr.jpg" align=center width="122">
	<a href="/" class="menu_act">Общие</a>
</td>
<td background="/i/bg.jpg" align=center class="menu_no_act" width="122">Документы</td>
<? if(@$auth && ($user_type == 'adm')) {?>
<td background="/i/bgr.jpg" align=center width="122">
	<a href="users.php" class="menu_act">Пользователи</a>
</td>
<? } ?>
<td align=center width="50">
	<a class="menu_act" title="Переключиться на новую версию" href="/acc/query/query_list.php"><img src="/i/strel2.gif" alt="Переключиться на новую версию" border="0" /></a></td>
</tr>
</table>
<table width=100% border=0 cellpadding="5" cellspacing="0" bgcolor="#F6F6F6">
	<tr>
		<td valign="top" align="center">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="150">
						<span class="sublink_pl">+</span> 
						<a href="query_send.php" class="sublink">запросить счет</a>					
					</td>
					<td width="150">
						<span class="sublink_pl">+</span> 
						<a href="query_doc.php" class="sublink">запросить документы</a>
					</td>
				</tr>
			</table></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td align="center">
		<? if($auth) {  ?>
			<table width="200" border="0" cellpadding="0" cellspacing="2" bordercolor="#999999">
				<tr>
					<? if( ($user_type == 'acc') || ($user_type == 'adm') ) { ?>
					<td><img src="/i/pix.gif" width="1" height="1"></td>
					<?}?>
					<td><img src="/i/pix.gif" width="1" height="1"></td>
					<td><img src="/i/pix.gif" width="150" height="1"></td>
					<td><img src="/i/pix.gif" width="80" height="1"></td>
					<td><img src="/i/pix.gif" width="130" height="1"></td>
					<td><img src="/i/pix.gif" width="130" height="1"></td>
					<td><img src="/i/pix.gif" width="1" height="1"></td>
				</tr>	
				<tr class="tab_query_tit">
					<? if( ($user_type == 'acc') || ($user_type == 'adm') ) { ?>
					<td align="center" class="tab_query_tit">&nbsp;</td>
					<?}?>
					<td align="center" class="tab_query_tit" onmouseover="Tip('Тип запроса (счет, документ)')">
						Тип
					</td>
					<td align="center" class="tab_query_tit" onmouseover="Tip('Короткое название клиента')">
						Название
					</td>
					<td align="center" class="tab_query_tit"  onmouseover="Tip('Дата создания запроса')">
						Дата
					</td>
					<td align="center" class="tab_query_tit" onmouseover="Tip('Сумма счета в рублях')">
						Сумма
					</td>
					<td align="center" class="tab_query_tit" onmouseover="Tip('Номер счета / статус')">
						Номер счета
					</td>
					<td align="center" class="tab_query_tit">
						Операция					</td>
				</tr>
		<?
			if(($user_type == 'acc') || ($user_type == 'adm')) {
				if(isset($_GET['sort']) && ($_GET['sort'] == 'acc')) {
					$query = "SELECT a.uid FROM queries as a, data_queries as b, clients as c WHERE a.type='0' AND a.ready='1' AND a.query_id=b.uid AND b.client_id=c.uid";
				}
				else
					$query = "SELECT a.uid FROM queries as a, data_queries as b, clients as c WHERE a.query_id=b.uid AND b.client_id=c.uid";
			}
			else 
				$query = sprintf("SELECT a.uid FROM queries as a, data_queries as b, clients as c WHERE a.user_id=%d AND a.query_id=b.uid AND b.client_id=c.uid", $user_id);
			$res = mysql_query($query);
			$num_all_rows = mysql_num_rows($res);
			$num_pages = ceil($num_all_rows/$rows_onpage);
			if($page > $num_pages)
				$page = $num_pages;
				
			$limit_start = ($all_pages) ? 0 : ($page-1)*$rows_onpage;
			$limit_num = ($all_pages) ? 10000 : $rows_onpage;
			
			$query_sort = 'a.ready,a.type,a.date_query DESC';
			
			if(($user_type == 'acc') || ($user_type == 'adm')) {
				if(isset($_GET['sort']) && ($_GET['sort'] == 'acc')) {
					$query_sort = 'b.acc_number ASC';
					$query = sprintf("SELECT a.uid,a.type,a.date_query,a.date_ready,a.ready, b.sub_acc,b.req,b.amount_acc,b.contractors,b.total_cost,b.note,b.acc_number, c.name as client, c.short FROM queries as a, data_queries as b, clients as c WHERE a.type='0' AND a.ready='1' AND a.query_id=b.uid AND b.client_id=c.uid ORDER BY %s LIMIT %d,%d", $query_sort, $limit_start,$limit_num);
				}
				else
					$query = sprintf("SELECT a.uid,a.type,a.date_query,a.date_ready,a.ready, b.sub_acc,b.req,b.amount_acc,b.contractors,b.total_cost,b.note,b.acc_number, c.name as client, c.short FROM queries as a, data_queries as b, clients as c WHERE a.query_id=b.uid AND b.client_id=c.uid ORDER BY %s LIMIT %d,%d", $query_sort, $limit_start,$limit_num);
			}	
			else
				$query = sprintf("SELECT a.uid,a.type,a.date_query,a.date_ready,a.ready, b.sub_acc,b.req,b.amount_acc,b.contractors,b.total_cost,b.note,b.acc_number, c.name as client, c.short FROM queries as a, data_queries as b, clients as c WHERE a.user_id=%d AND a.query_id=b.uid AND b.client_id=c.uid ORDER BY %s LIMIT %d,%d", $user_id, $query_sort, $limit_start,$limit_num);
			$res = mysql_query($query);
			$nm = 1;
			while(@$r = mysql_fetch_array($res)) {
				
				//Предмет счета первые 50 символов
				if(strlen($r['sub_acc']) > 50)
					$predm = str_replace("\r\n", '<br>', substr($r['sub_acc'], 0, 50).'...');
				else
					$predm = str_replace("\r\n", '<br>', $r['sub_acc']);

				// дата, время запроса
				$tmp = explode(' ', $r['date_query']);
				$tmp2 = explode('-',$tmp[0]);
				$date_str = $tmp2[2].' '.$month[intval($tmp2[1])-1];
				$date_str_y = $tmp2[0].'г.';
				$tmp2 = explode(':', $tmp[1]);
				$time_str = $tmp2[0].':'.$tmp2[1];
				
				
				// Статус, время выпонения
				if($r['ready']) {
					$tmp = explode(' ', $r['date_ready']);
					$tmp2 = explode('-',$tmp[0]);
					$date_str2 = $tmp2[2].' '.$month[intval($tmp2[1])-1];
					$date_str2_y = $tmp2[0].'г.';
					$tmp2 = explode(':', $tmp[1]);
					$time_str2 = $tmp2[0].':'.$tmp2[1];
					if( $r['acc_number'] != 'none' ) {
						$alt_stat = 'Tip(\'<span class=stat_yes><strong>Выполнено</strong></span><br>'.htmlspecialchars($date_str2.' '.$date_str2_y.' '.$time_str2, ENT_QUOTES ).'\')';
					}	
					else {
						$alt_stat = 'Tip(\'<span class=stat_null><strong>Счет не нужен</strong></span>\')';
					}
					$alert_r = '';
				}
				else {
					if( $r['acc_number'] != 'none' ) {
					if (($user_type == 'adm') || ($user_type == 'acc')) {
							if($r['type'] == 0) {
								$alt_stat = 'Tip(\'<table height=10 border=0 cellspacing=0 cellpadding=0><tr><td valign=top><form action=\\\'\\\' name=ab method=post>';
								$alt_stat .= '<input name=acc_id type=hidden value='.$r['uid'].' />';
								$alt_stat .= '<input title=\\\'Введите &#8220;нет&#8221; если счет не нужен\\\' name=set_acc type=text class=inp_hint_acc  value=\\\'\\\' onkeyup=\\\'if(this.value.length ==0) { document.ab.acc_butt.disabled=true; } else { document.ab.acc_butt.disabled=0; }\\\' maxlength=50 />';
								$alt_stat .= '<input class=butt_hint_acc disabled=disabled name=acc_butt type=submit id=accok value=OK />';
								$alt_stat .= '</td></tr></form></table>';
								$alt_stat .= '\', CLOSEBTN, 1, STICKY, 1, DURATION, 0, OFFSETX, 0, OFFSETY, -5, FOLLOWMOUSE, 0, CENTERMOUSE, 1, DELAY, 800, TITLE, \'Номер счета\')';
								$alert_r = ' class="alert_row" ';
							}
							else {
								$alt_stat = 'Tip(\'<span class=stat_no><strong>Не выполнено</strong></span>\')';
								$alert_r = ' class="alert_row" ';
							}	
						}
						else {
							if($r['type'] == 0) {
								$alt_stat = '';
								$alert_r = ' class="alert_row" ';
							}
							else { 
								$alt_stat = 'Tip(\'<span class=stat_no><strong>Не выполнено</strong></span>\')';
								$alert_r = ' class="alert_row" ';
							}
						}	
					}
					else {
						$alt_stat = 'Tip(\'<span class=stat_null><strong>Счет не нужен</strong></span>\')';
						$alert_r = ' class="alert_row" ';
					}
				}
				$acc_num = ($r['acc_number']) ? $r['acc_number'] : '---';
				
				$summ = '<span class="list_sum">---</span>';
				if($r['amount_acc'])
					$summ = '<span onmouseover="Tip(\'Сумма счета\')" class="list_sum">'.$r['amount_acc'].'</span>';
//				$summ = ($r['amount_acc']) ? $r['amount_acc'] : '---';
				
				$arr_im = array();
				$arr_link = array();
				$arr_hint = array();
				$arr_scr = array();
				
				$arr_im[] = 'lupa.gif';
				$arr_hint[] = 	'Просмотреть / редактировать';
				$arr_scr[]='';
				if($r['type'] == 0)
					$arr_link[] = 'query_send.php?show='.$r['uid'];
				else 
					$arr_link[] = 'query_doc.php?show='.$r['uid'];

						
				if($r['type'] == 0) {
					$type_im = '<img src="/i/icons/rm_icon.gif" onmouseover="Tip(\'<span class=\\\'stat_yes\\\'><strong>Предмет</strong></span><br>'.str_replace('\#\*', '<br>', htmlspecialchars(htmlspecialchars(str_replace('<br>', '\#\*', $predm),ENT_QUOTES),ENT_QUOTES)).'\')">';
					if($r['ready']) {
							$arr_im[] = 		'edit2.gif';
							$arr_link[] = 	'query_doc.php?doc='.$r['uid'];
							$arr_hint[] = 	'Запросить документ';
							$arr_scr[]='';
						}	
						if (($user_type == 'adm') || ($user_type == 'acc')) {
							$arr_im[] = 'del2.gif';
							$arr_link[] = '#';
							$arr_hint[] = 	'Удалить';
							$arr_scr[]='onclick="del_query('.$r['uid'].','.intval(@$_GET['page']).')"';
						}	
				}
				else {
					$type_im = '<img width="28" height="28" src="/i/icons/type_doc.gif" onmouseover="Tip(\'<span class=\\\'stat_yes\\\'><strong>Предмет</strong></span><br>'.htmlspecialchars($predm,ENT_QUOTES).'\')">';
					
					if(($user_type == 'acc') || ($user_type == 'adm')) {
						if(!$r['ready']) {
							$arr_im[] = 'ok.gif';
							$arr_link[] = 'query_list.php?set='.$r['uid'].'&page='.@$_GET['page'];
							$arr_hint[] = 	'Выполнить';
							$arr_scr[]='';
						}
						else {
							$arr_im[] = 'del2.gif';
							$arr_link[] = '#';
							$arr_hint[] = 	'Удалить';
							$arr_scr[]='onclick="del_query('.$r['uid'].','.intval(@$_GET['page']).')"';						}
					}
					if(!$r['ready']) {
						$arr_im[] = 'del2.gif';
						$arr_link[] = '#';
						$arr_hint[] = 	'Удалить';
						$arr_scr[]='onclick="del_query('.$r['uid'].','.intval(@$_GET['page']).')"';
					}
				}	
					
				$butt = '';	
				for($i=0;$i<count($arr_im);$i++) {
					if($arr_im[$i]) {
						$butt .= '<a href="'.@$arr_link[$i].'" '.@$arr_scr[$i].' >';
						$butt .= '<img widt="16" height="16" src="/i/icons/'.@$arr_im[$i].'" onmouseover="Tip(\''.@$arr_hint[$i].'\')" />';
						$butt .= '</a>&nbsp;';
					}
					else {
						$butt .= '';
					}
				}
			?>
				<tr <?=$alert_r?>>
					<? if( ($user_type == 'acc') || ($user_type == 'adm') ) { ?>
					<td class="tab_td_norm" valign="middle">
						<? if(($user_type == 'mng') && ($r['type'] == 1)) { ?>
						<? } else {?>
						<? } 
						if(!trim($r['short']))
							$r['short'] = '---';
						?>					
						<input name="id_arr[]" type="hidden" value="<?=$r['uid']?>" />
						<input name="ch_arr[]" type="checkbox" value="<?=$r['uid']?>" />
					</td>
					<?}?>
					<td align="center" class="tab_td_norm"><?=$type_im?></td>
					<td align="center" class="tab_td_norm" onmouseover="Tip('<span class=stat_yes><strong><?=htmlspecialchars(htmlspecialchars($r['client'], ENT_QUOTES), ENT_QUOTES)?></strong></span>', PADDING, 5)"><strong>
						<a href="<?=@$arr_link[0]?>" class="client"><?=htmlspecialchars($r['short'], ENT_QUOTES)?></a></strong>
					</td>
					<td align="center" class="tab_td_norm" onmouseover="Tip('<?=$date_str.' '.$date_str_y.' '.$time_str?>');">
						<span class="date_row"><?=$date_str?><br />
					</td>
					<td align="center" class="tab_td_norm_nobr" ><?=$summ?></td>
					<td <?=(is_numeric($acc_num)) ? 'align="left" style="padding-left:30px;"' : 'align="center"'?> class="tab_td_norm" onmouseover="ssttss=1;<?=$alt_stat?>" onmouseout="ssttss=0">
				<? 
					if($acc_num == '---') {
//						if(($user_type == 'acc') || ($user_type == 'adm')) { ?>
								<span class="stat_no"><strong>Не выполнено</strong></span>					
						<? //}	
					} elseif($acc_num == 'none') {
					?>
						<span class="stat_null"><strong>---</strong></span>	
					<?	
					}	else {?>
						<span class="date_row">счет: </span><span class="stat_yes"><strong><?=$acc_num?></strong></span>
					<? } ?>					</td>
					<td align="left" class="tab_td_norm"><?=$butt?></td>
				</tr>

				
			<? $nm++; } ?>
				<tr>
				  <td class="tab_td_bott" colspan="7" align="left">
						<? if( ($user_type == 'acc') || ($user_type == 'adm') ) { ?>
						<table border="0" cellspacing="0" cellpadding="0" width="100%" height="16">
							<tr>
								<td width="35" valign="top"><img src="/i/strel.gif" width="25" height="12" />&nbsp;&nbsp;&nbsp;</td>
								<td width="200" valign="bottom"><a href="#" onclick="sel_all(); return false">Выбрать все</a>&nbsp;/&nbsp;<a href="#" onclick="unsel_all(); return false">Сбросить все</a></td>
								<td align="right" valign="bottom"><a href="#" onclick="confr_submit(); return false;">Удалить выбранное</a></td>
							</tr>
						</table>
						<?}?>
					</td>
			  </tr>
				</table>
	
		 
		<? } ?>	</td>
</tr>
<? if(($user_type == 'acc') || ($user_type == 'adm')) { ?>
<tr>
  <td align="center">
		<? if(@$_GET['sort'] == 'acc') { ?>
		<a href="?page=<?=@$_GET['page']?>">Сортировка по умолчанию</a>
		<? } else { ?>
		<a href="?sort=acc&page=<?=@$_GET['page']?>">Сортировка по номеру счета</a>
		<? } ?>
	</td>
</tr>
<? } ?>
</table>
<br><br>
<? if($rows_onpage < $num_all_rows) { ?>
<table border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<? if(!$all_pages) { ?>
		<td>Страница</td>
		<? for($i=1;$i<=$num_pages;$i++) { ?>
		<td width="20" align="right">
			<? 
			$lnk ='<strong>'.$i.'</strong>';
			if($page != $i) 
				$lnk = '<a href="?page='.$i.'&sort='.@$_GET['sort'].'">'.$lnk.'</a>';
			echo $lnk;
			?>
		</td>
		<? } } else {?>
		<td>
		<? if($all_pages) {?>
		<a href="?page=1&sort=<?=@$_GET['sort']?>">Постранично</a>
		<? } else {?>
		Постранично
		<? }?>
		</td>
		<? } ?>
		<td width="80" align="right">
			<? if(!$all_pages) {?>
			<a href="?page=all&sort=<?=@$_GET['sort']?>" >Показать все</a>
			<? } else {?>
			Показать все
			<? }?>
		</td>
	</tr>
</table>
<? } ?>
<br><br>

</td>

</tr>
</form>
</table>

</body>
</html>
