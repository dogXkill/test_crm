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

$type = $_GET["type"];

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.2</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
/*<![CDATA[*/
.hightlight{
font-size:  14px;
border: 1px solid #808080;
}
.hightlight tr:hover{
background-color:#E6E6E6;
}
.hightlight td{
border: 1px solid #808080;
}
.task_link_bold{
	font-weight: bold;
}

.hs_span{
font-weight: bold;
cursor:pointer;
}
.hs_span_plus{
font-weight: bold;
color:green;
cursor:pointer;
}



/*]]>*/

</style>

</head>

<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
<script type="text/javascript" src="../includes/js/jquery.cookie.js"></script>
<script type="text/javascript" src="../includes/js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="../includes/js/jscalendar/lang/calendar-ru.js"></script>
<script type="text/javascript" src="../includes/js/jscalendar/calendar-setup-art-stat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="../includes/js/jscalendar/calendar-blue.css" />

<body>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>
<?$name_curr_page = 'query_list';

require_once("../templates/top.php");
$tit = 'Статистика / Периоды';
require_once("../templates/main_menu.php");?>
<table width=1100 border=0 cellpadding=0 cellspacing=0 align=center bgcolor="#F6F6F6">
	<tr>
		<td align="center" class="title_razd">Статистика магазин</td>
	</tr>
	<tr>
		<td valign="top">
 <?require_once("../templates/stat_menu.php"); ?>

     </td>
</tr>
<tr>
  <td align="center">

<?

 $hvatit = $_GET["hvatit"];
 $date_from = $_GET["date_from"];
 $date_to = $_GET["date_to"];
 $start_period_umolch = date("Y-m-d",strtotime("-2 months"));
 $bolshie_sdelki = $_GET["bolshie_sdelki"];
 $sezon_udal = $_GET["sezon_udal"];


if($sezon_udal == "1"){$sezon_udal_q = "AND q.date_query NOT LIKE '%-12-%'";}

 if($date_from){
     $date_from_q = "AND q.date_query > '".$date_from." 00:00:00'";
     $date_from_txt = $date_from;
     }
     else{

     $date_from_q = "AND q.date_query > '".$start_period_umolch." 00:00:00'";
     $date_from_txt = $start_period_umolch;}

 if($date_to){
     $date_to_q = "AND q.date_query < '".$date_to." 23:59:59'";
     $date_to_txt = $date_to;
     }
 else{
     $date_to_q = "AND q.date_query < '".date('Y-m-d')." 23:59:59'";
     $date_to_txt = date('Y-m-d');
     }




if($type == "popular_art_cost"){

?>


<form action="" method=get>
от: <input type="text" id="date_from"  name="date_from" style="width: 130px; height: 32px; font-size: 20px;" value="<?=$date_from_txt;?>"/>
до: <input type="text" id="date_to"  name="date_to" style="width: 130px; height: 32px; font-size: 20px;" value="<?=$date_to_txt;?>"/>

<?

if($type=="popular_art_cost"){

 if(!$_GET["limit"]){$limit = "10";}else{$limit = $_GET["limit"];}
}
$days = round(((strtotime ($_GET["date_to"])-strtotime ($_GET["date_from"])) / (60 * 60 * 24)));
if($days > '0'){print "выбран период: ".$days." дней";}

//используем фильтр позволяющий показать на сколько осталось пакетов
 if($hvatit){$hvatit_q = " HAVING hvatit < ".$hvatit;}

 //используем фильтр удалить единоразовые закупки на сумму свыше 60 тыс руб из статистической выборки
 if($bolshie_sdelki){$bolshie_sdelki_q = " HAVING sdelka_sum < 5000";}
?>

<br>
<select id="izd_type" name="izd_type"  style="width: 180px; height: 32px; font-size: 20px;">
<option value="">тип</option>
<?

$izd_type = $_GET["izd_type"];
$izd_material = $_GET["izd_material"];
$manufacturer = $_GET["manufacturer"];
if($izd_type == "" || !$izd_type){$izd_type = ""; $izd_type_q = "";}else{$izd_type_q = " AND p.izd_type = ".$izd_type;}
if($izd_material == "" || !$izd_material){$izd_material_q = "";}else{$izd_material_q = " AND p.izd_material = ".$izd_material;}
if($manufacturer == "" || !$manufacturer){$manufacturer = ""; $manufacturer_q = "";}else{$manufacturer_q = " AND p.manufacturer = ".$manufacturer;}

$get_types = mysql_query("SELECT * FROM types");
while($gg =  mysql_fetch_array($get_types)){?>
<option value="<?=$gg["tid"];?>" <?if($izd_type == $gg["tid"]){echo "selected";}?>><?=$gg["type"];?></option>
<?}?>
</select>

<select id="izd_material" name="izd_material"  style="width: 180px; height: 32px; font-size: 20px;">
<option value="">материал</option>
<?$get_material = mysql_query("SELECT * FROM materials");
while($gg =  mysql_fetch_array($get_material)){?>
<option value="<?=$gg["tid"];?>" <?if($izd_material == $gg["tid"]){echo "selected";}?>><?=$gg["type"];?></option>
<?}?>
</select>

<select id="manufacturer" name="manufacturer" style="width: 180px; height: 32px; font-size: 20px;">
<option value="">производитель</option>
<?
$manufac = mysql_query("SELECT * FROM manufacturer ORDER BY type ASC");
while($r =  mysql_fetch_array($manufac)){?>
<option value="<?=$r["tid"];?>" <?if($manufacturer == $r["tid"]){echo "selected";}?>><?=$r["type"];?></option>
<?}?>
</select>

<select id=order_type name="order_type"  style="width: 180px; height: 32px; font-size: 20px;">
<?$order_type = $_GET["order_type"];
if($order_type == "" || !$order_type){$order_type = "viruchka";} ?>
<option value="kolichestvo" <?if($order_type == "kolichestvo"){echo "selected";}?>>по количеству</option>
<option value="rashod" <?if($order_type == "rashod"){echo "selected";}?>>среднемесячный расход</option>
<option value="viruchka" <?if($order_type == "viruchka"){echo "selected";}?>>по выручке</option>
<option value="nacenka" <?if($order_type == "nacenka"){echo "selected";}?>>по наценке</option>
<option value="sklad" <?if($order_type == "sklad"){echo "selected";}?>>склад</option>
<option value="in_work" <?if($order_type == "in_work"){echo "selected";}?>>в работе</option>
<option value="potracheno" <?if($order_type == "potracheno"){echo "selected";}?>>по затратам</option>
<option value="zarabotano" <?if($order_type == "zarabotano"){echo "selected";}?>>по марже</option>
</select>

<select id=limit name="limit"  style="width: 80px; height: 32px; font-size: 20px;">
<option value="10" <?if($limit == "10"){echo "selected";}?>>10</option>
<option value="30" <?if($limit == "30"){echo "selected";}?>>30</option>
<option value="50" <?if($limit == "50"){echo "selected";}?>>50</option>
<option value="100" <?if($limit == "100"){echo "selected";}?>>100</option>
<option value="500" <?if($limit == "500"){echo "selected";}?>>500</option>
<option value="1000000" <?if($limit == "1000000"){echo "selected";}?>>все</option>
</select>

<select id=hvatit name="hvatit"  style="width: 120px; height: 32px; font-size: 20px;">
<option value="">хватит</option>
<option value="10" <?if($hvatit == "10"){echo "selected";}?>>меньше 10 дней</option>
<option value="30" <?if($hvatit == "30"){echo "selected";}?>>на 30 дней</option>
<option value="60" <?if($hvatit == "60"){echo "selected";}?>>на 60 дней</option>
<option value="90" <?if($hvatit == "90"){echo "selected";}?>>на 90 дней</option>
<option value="180" <?if($hvatit == "180"){echo "selected";}?>>на 180 дней</option>
</select> <br>
<label for="bolshie_sdelki" style="cursor:pointer;">удалить большие сделки</label> <input type="checkbox" value="1" name="bolshie_sdelki" id="bolshie_sdelki" <? if($bolshie_sdelki == "1"){echo "checked";} ?>>
<label for="sezon_udal" style="cursor:pointer;">удалить сезон</label> <input type="checkbox" value="1" name="sezon_udal" id="sezon_udal" <? if($sezon_udal == "1"){echo "checked";} ?>>
<label for="new_window" style="cursor:pointer;">в новом окне</label> <input type="checkbox" value="1" name="new_window" id="new_window" checked="">
<input type="hidden" name=type value="popular_art_cost"/>
<input type=submit style="width: 130px; height: 30px; font-size: 20px;" value="показать!">


<table width=1300 class="hightlight" cellpadding=5 cellspacing=0>
<tr>
<td class="tab_query_tit" name="art_num">Артикул <span onclick="hide_show('art_num','hide')" class="hs_span">-</span> <span onclick="hide_show('art_num,title,col_in_pack,kolichestvo,sklad,in_work,hvatit_sklad,hvatit,rashod,price,viruchka,r_price_our,potracheno,nacenka,marja','show')" class="hs_span_plus">+++</span></td>
<td class="tab_query_tit" name="title">Название <span onclick="hide_show('title')" class="hs_span">-</span></td>
<td class="tab_query_tit" name="col_in_pack">Упаковано <span onclick="hide_show('col_in_pack')" class="hs_span">-</span></td>
<td class="tab_query_tit" onmouseover="Tip('суммирует все заказы, включая:<br> не оплаченные, с нанесением, с заменой ленты и с зашитыми опциями')" name="kolichestvo">Шт. продано за период <span onclick="hide_show('kolichestvo')" class="hs_span">-</span></td>
<td class="tab_query_tit" onmouseover="Tip('склад берется из таблицы plan_arts которая ежечасно синхронизируется с сайтом')" name="sklad">На складе</td>
<td class="tab_query_tit" onmouseover="Tip('то что в работе берется из таблицы plan_arts которые необходимо синхронизировать с заявками в разделе План')" name="in_work">В работе <span onclick="hide_show('in_work')" class="hs_span">-</span></td>
<td class="tab_query_tit" onmouseover="Tip('хватит на количество календарных дней того количества, которое есть на складе')" name="hvatit_sklad">Хватит склада</td>
<td class="tab_query_tit" name="hvatit">Хватит на (с уч. в работе)</td>
<td class="tab_query_tit" onmouseover="Tip('считается на основе выбранного периода по формуле <br>(общее кол-во продаж/количество выбранных дней*30')" name="rashod">Расход в мес</td>
<td class="tab_query_tit" onmouseover="Tip('Берется одна из цен выборки, <br>по какому алгоритму мускуль это делает не понятно')" name="price">Цена</td>
<td class="tab_query_tit" onmouseover="Tip('Поэтому, выручка не всегда получается при умножении количества на цену')" name="viruchka">Выручено <span onclick="hide_show('viruchka')" class="hs_span">-</span></td>
<td class="tab_query_tit" onmouseover="Tip('с/с берется с сайта, которая может плавать в разное время, <br> а следовательно данные не очень точные')" name="r_price_our">C/c <span onclick="hide_show('r_price_our')" class="hs_span">-</span></td>
<td class="tab_query_tit" onmouseover="Tip('соответственно затраты вычисляются точно = с/с * количество')" name="potracheno">Потрачено <span onclick="hide_show('potracheno')" class="hs_span">-</span></td>
<td class="tab_query_tit" name="nacenka">Наценка <span onclick="hide_show('nacenka')" class="hs_span">-</span></td>
<td class="tab_query_tit" name="marja">Маржа <span onclick="hide_show('marja')" class="hs_span">-</span></td>
</tr>
<?
$q = "SELECT
o.art_num AS art_num,
p.title AS name,
p.col_in_pack AS col_in_pack,
SUM(o.num) AS kolichestvo,
SUM(o.price*o.num) AS viruchka,
o.price*o.num AS sdelka_sum,
o.price AS price,
p.uid AS uid,
p.r_price_our AS r_price_our,
p.sklad AS sklad,
p.in_work AS in_work,
SUM(o.num)/".$days." AS v_den,
CEIL((p.sklad)/(SUM(o.num)/".$days.")) AS hvatit_sklad,
CEIL((p.sklad+p.in_work)/(SUM(o.num)/".$days.")) AS hvatit,
CEIL((SUM(o.num)/".$days.")*30) AS rashod,
SUM(p.r_price_our*o.num) AS potracheno,
(SUM(o.price*o.num) / SUM(p.r_price_our*o.num)) AS nacenka,
(SUM(o.price*o.num)- SUM(p.r_price_our*o.num)) AS zarabotano
FROM obj_accounts AS o, queries AS q, plan_arts AS p
WHERE query_id > '1174' AND art_num!='' ".$date_from_q."
 ".$date_to_q."
".$sezon_udal_q."
 AND o.query_id = q.uid
 AND o.art_num = p.art_id
 AND p.onn = '1'
 ".$izd_type_q."
 ".$manufacturer_q."
 ".$izd_material_q."

 GROUP BY art_num
  ".$hvatit_q."
  ".$bolshie_sdelki_q."
 ORDER BY ".$order_type." DESC LIMIT 0, ".$limit;
//echo $q;
$art_list = mysql_query($q);

 echo "<br><span style=\"display:none;\">$q</span>";
 echo mysql_error();
 while(@$r = mysql_fetch_array($art_list)) {

  ?>
<tr class=highlight>
<td style="white-space: nowrap;" name="art_num">
<?=$r["art_num"];?>
<a href="https://www.paketoff.ru/shop/view/?id=<?=$r["uid"];?>" target="_blank" onmouseover="Tip('Открыть в интернет магазине');"><img src="/i/pkf.gif" align="absmiddle"></a>
<a href="https://www.paketoff.ru/admin/shop/goods_list/edit/?id=<?=$r["uid"];?>" target="_blank" onmouseover="Tip('Редактировать в интернет магазине');"><img src="/i/editbut.png" align="absmiddle"></a>
<a href="/acc/stat/stat_shop.php?tip=by_art_num&art_num=<?=$r["art_num"];?>&date_from=&date_to=&type=shop_history" target="_blank"><img src="/i/stat_sm.png" align="absmiddle" onmouseover="Tip('Посмотреть историю продаж');"></a>
<a href="/acc/applications/list.php?val=<?=$r["art_num"];?>&act=by_art_num" target="_blank"><img src="/i/manufacture_sm.png" align="absmiddle" onmouseover="Tip('Просмотреть заявки на производство')"></a>
</td>
<td name="title"><?=$r["name"];?></td>
<td name="col_in_pack"><?=$r["col_in_pack"];?></td>
<td name="kolichestvo"><?=$r["kolichestvo"]; $sold_total = $sold_total+round($r["kolichestvo"])?></td>
<td style="<?if($r["v_den"]*20 > $r["sklad"]){echo "color: red; font-size: 14px; font-weight:bold;";} if($r["v_den"]*10 > $r["sklad"]){echo "color: red; font-size: 18px; font-weight:bold;";}?>">
<?=$r["sklad"];?></td>
<td name="in_work"><?=$r["in_work"];?></td>
<td><?if($r["hvatit_sklad"] > '0'){echo $r["hvatit_sklad"];?> дн.<?}else{echo "---";}?></td>
<td><?if($r["hvatit"] > '0'){echo $r["hvatit"];?> дн.<?}else{echo "---";}?></td>
<td><?=$r["rashod"];?>шт</td>
<td><?=$r["price"];?></td>
<td name="viruchka"><?=round($r["viruchka"]); $vir_total = $vir_total+round($r["viruchka"])?></td>
<td name="r_price_our"><?=$r["r_price_our"];?></td>
<td name="potracheno"><?=round($r["potracheno"]); $potr_total = $potr_total+round($r["potracheno"])?></td>
<td name="nacenka"><?=round($r["nacenka"], 2);?></td>
<td name="marja"><?=round($r["zarabotano"]); $zar_total = $zar_total+round($r["zarabotano"])?></td>
</tr>
   <? } ?>
   <tr>
   <td name="art_num"></td>
   <td name="title"></td>
   <td name="col_in_pack"></td>
   <td style="font-size: 14px; font-weight:bold;" name="kolichestvo"><?=$sold_total;?></td>
   <td></td>
   <td name="in_work"></td>
   <td></td>
   <td></td>
   <td></td>
   <td></td>
   <td style="font-size: 14px; font-weight:bold;" name="viruchka">выручка: <br /><?=$vir_total;?></td>
   <td name="r_price_our"></td>
   <td style="font-size: 14px; font-weight:bold;" name="potracheno">затраты: <br /><?=$potr_total;?></td>
   <td style="font-size: 14px; font-weight:bold;" name="nacenka"><?if($potr_total > "0"){?>средн. нац: <?=round(($vir_total/$potr_total),2);}?></td>
   <td style="font-size: 14px; font-weight:bold;" name="marja">заработано: <br /><?=$zar_total;?></td>
   </tr>
</table>

<?}



if($type=="shop_history"){
$tip = $_GET["tip"];
$art_num = $_GET["art_num"];?>
<form action="" method=get>
<input type="radio" name="tip" id="by_art_num_inp" value="by_art_num" <?if($tip == "by_art_num" || !$tip){echo "checked";}?>/> <label for="by_art_num_inp" style="cursor:pointer">по артикулу</label>
<input type="radio" name="tip" id="by_by_text" value="by_text"  <?if($tip == "by_text"){echo "checked";}?>/> <label for="by_by_text" style="cursor:pointer">по заголовку</label>
<input type="text" name="art_num" id="art_num" style="width: 130px; height: 25px; font-size: 20px;" value="<?=$art_num;?>"/>
дата от: <input type="date" id="date_from"  name="date_from" style="width: 150px; height: 25px; font-size: 20px;" value="<?=$date_from_txt;?>"/>
до: <input type="date" id="date_to"  name="date_to" style="width: 150px; height: 25px; font-size: 20px;" value="<?=$date_to_txt;?>"/>
<input type="hidden" name=type value="shop_history"/>
<input type=submit style="width: 130px; height: 30px; font-size: 20px;" value="показать!"></form>

<?

if($art_num){
if($tip == "by_art_num"){

$sql_vst = "o.art_num = '$art_num'";

}
else{
$sql_vst = "o.name LIKE('%".$art_num."%')";
}

 $q = "
 SELECT * FROM
 (SELECT q.uid, o.art_num, q.date_query, q.courier_task_id, q.prdm_num_acc, q.deliv_id, o.name AS title, o.query_id, o.num, o.price,
 o.num*o.price AS total_cost, q.prdm_opl AS prdm_opl, c.short, u.name AS name, u.surname, q.booking_till, q.shipped, q.time_shipped, q.date_ready = '' AS plan_shipping
 FROM obj_accounts AS o, queries AS q, clients AS c, users AS u
 WHERE ".$sql_vst."
 AND q.courier_task_id = 0
 AND o.query_id = q.uid
 AND c.uid = q.client_id
 AND u.uid = q.user_id
 AND o.deleted <> '1'
 AND q.deleted = '0'
  ".$date_from_q."
 ".$date_to_q."

 UNION ALL

 SELECT q.uid, o.art_num, q.date_query, q.courier_task_id, q.prdm_num_acc, q.deliv_id, o.name AS title, o.query_id, o.num, o.price,
 o.num*o.price AS total_cost, q.prdm_opl AS prdm_opl, c.short, u.name AS name, u.surname, q.booking_till, q.shipped, q.time_shipped, DATE_FORMAT(ct.date, '%d.%m.%Y') AS plan_shipping
 FROM obj_accounts AS o, queries AS q, clients AS c, users AS u, courier_tasks AS ct
 WHERE ".$sql_vst."
 AND o.query_id = q.uid
 AND c.uid = q.client_id
 AND u.uid = q.user_id
 AND o.deleted <> '1'
 AND q.deleted = '0'
 AND q.courier_task_id = ct.id
 ".$date_from_q."
 ".$date_to_q.") a

 ORDER BY date_query DESC";
$shop_history = mysql_query($q);

//echo mysql_error().$q;

?>

<div id="stat_info_full_paste" style="font-size:15px;"></div>

<table width=1300 class="hightlight" cellpadding=5 cellspacing=0>
<tr>
<td class="tab_query_tit">Артикул</td>
<td class="tab_query_tit">Дата заказа</td>
<td class="tab_query_tit">Название клиента</td>
<td class="tab_query_tit">Название</td>
<td class="tab_query_tit" align="center">Количество</td>
<td class="tab_query_tit">Цена</td>
<td class="tab_query_tit">Общая стоимость</td>
<td class="tab_query_tit">Оплата от клиента</td>
<td class="tab_query_tit">Логистика</td>
<td class="tab_query_tit">Данные о брони или отгрузке</td>
<td class="tab_query_tit">Имя менеджера</td>
</tr>
 <?
 $zakazano_itog = 0;
 $booked_expire_total = 0;
 $booked_total = 0;

 while($r = mysql_fetch_assoc($shop_history)) {
$uid = $r["uid"];
$date_query = $r["date_query"];
$courier_task_id = $r["courier_task_id"];
$prdm_num_acc = $r["prdm_num_acc"];
$deliv_id = $r["deliv_id"];
$title = $r["title"];
$query_id = $r["query_id"];
$qty = $r["num"];
$price = $r["price"];
$total_cost = $r["total_cost"];
$prdm_opl = $r["prdm_opl"];
if($prdm_opl == 0 or $prdm_opl == ''){$prdm_opl_txt = "<span style='color:red;'><b>оплаты нет</b><span>";}
else if($prdm_opl > 0){$prdm_opl_txt = "<span style='color:green;'><b>оплата поступила</b> <br> <span style='font-size:10px;'>$prdm_opl р.</span></span>";}
$short = $r["short"];
$name = $r["name"];
$surname = $r["surname"];
$booking_till = $r["booking_till"];
$plan_shipping  = $r["plan_shipping"];
if($booking_till == "0000-00-00" or $booking_till == ""){$booking_till = date('Y-m-d', strtotime($date_query. "+5 days"));}

$shipped = $r["shipped"];
$time_shipped = $r["time_shipped"];
$date_query = date('d.m.Y', strtotime($r['date_query']));
$tek_time = date("Y-m-d");
$date_query_form = date('Y-m-d', strtotime($date_query));
$month_ago = date('Y-m-d', strtotime($tek_time. "-1 month"));
$booking_till_nice = date('d.m.Y', strtotime($booking_till));
$time_shipped_nice = date('d.m.Y H:i', strtotime($time_shipped));

if($prdm_num_acc !== "" and $prdm_num_acc !== "0"){$data_otgr_txt = "<b>отгружено!</b> <br>(накладная $prdm_num_acc)";
$tr_class = "shipping_done";
$td_style_spipping = "color: #B7B7B7";

}

    else{

        if($shipped == '1'){$data_otgr_txt = "<span style=\"font-weight:bold;\">накладная готова / бронь снята<br>$time_shipped_nice</span>";
        $tr_class = "shipping_maintained";
        $td_style_spipping = "color: #545454";
        }
        else{

                if(strtotime($tek_time) < strtotime($booking_till))
                    {$data_otgr_txt = "<span style='font-weight:bold;'>действующая <b>бронь</b>!<br>(до $booking_till_nice)</span>";
                    $tr_class = "booked_existing";
                    $td_style_spipping = "color: green";
                    $booked_total = $booked_total + $qty;}
                elseif((strtotime($date_query_form) > strtotime($month_ago) and $prdm_opl > 0)){
                    $paid_booking_till = date('d.m.Y', strtotime($date_query. "+30 days"));
                    $data_otgr_txt = "<span style='font-weight:bold;'><u>оплаченная</u> <b>бронь</b>!<br>(до $paid_booking_till)</span>";
                    $tr_class = "booked_existing";
                    $td_style_spipping = "color: green";
                    $booked_total = $booked_total + $qty;
                }
                else
                    {$data_otgr_txt = "<span font-weight:bold;'>бронь <b>просрочена</b>!<br>(до $booking_till_nice)</span>";
                    $tr_class = "booked_expired";
                    $td_style_spipping = "color: red";
                     $booked_expire_total = $booked_expire_total + $qty;
                    }
        }
}

$zakazano_itog = $qty + $zakazano_itog;
$total_cost=round($total_cost, 2);
$total_cost_itog = $total_cost_itog + $total_cost;

  if(!$r){$errmes = "Ничего не найдено!";}
 ?>
<tr onmouseover="this.style.background='#BDCDFF';" onmouseout="this.style.background='';" class="<?=$tr_class;?>">
<td><a href="https://www.paketoff.ru/admin/shop/goods_list/?count_on_page=20&search_text=<?=$art_num;?>" target="_blank"><?=$art_num;?></a></td>
<td><?=$date_query;?><br><span style="font-size:10px;">номер заказа: <a href="/acc/query/query_send.php?show=<?=$uid;?>" target=_blank><?=$uid;?></a></span></td>
<td><?=$short;?><br>
</td>
<td><a href="/acc/query/query_send.php?show=<?=$uid;?>" target=_blank><?=$title;?></a></td>
<td align=center class=qty><?=$qty;?></td>
<td><?=round($price, 2);?></td>
<td align=center class=total_cost><?=$total_cost;?></td>
<td align=center><?=$prdm_opl_txt;?></td>
<td><?if ($deliv_id == "1") echo "самовывоз";
if ($deliv_id == "2") echo "по Мск";
if ($deliv_id == "8") echo "до ТК";
if ($deliv_id == "3") echo "срочная";
if ($deliv_id == "5") echo "СДЭК";
if ($deliv_id == "12") echo "Самовывоз из шоурума";
if ($deliv_id == "15") echo "Дел. линии";
if($courier_task_id > 0) {echo "<br><a href=/acc/logistic/courier_tasks.php?id=$courier_task_id target=_blank><b>стоит на доставке</b></a><br><span style='font-size:9px;'>$plan_shipping</span>";}
?></td>
<td style="<?=$td_style_spipping;?>"><?=$data_otgr_txt;?></td>
<td><?=$r["name"];?> <?=$r["surname"];?></td>
</tr>
<?}?>
<tr class=itog_tr>
<td class="tab_query_tit"></td>
<td class="tab_query_tit"></td>
<td class="tab_query_tit"></td>
<td class="tab_query_tit" align=center width=200></td>
<td class="tab_query_tit" align=center id="total_qty_td"><?=$zakazano_itog;?></td>
<td class="tab_query_tit"></td>
<td class="tab_query_tit" align=center id="total_cost_td"><?=$total_cost_itog;?></td>
<td class="tab_query_tit"></td>
<td class="tab_query_tit"></td>
<td class="tab_query_tit"></td>
<td class="tab_query_tit"></td>
</tr>
</table>


<span id=stat_info_full style="display:none;">
<?$plan_arts = mysql_fetch_assoc(mysql_query("SELECT sklad, booked, in_work FROM plan_arts WHERE art_id = '$art_num'"));
if($zakazano_itog > 0){
?>
<b>остаток артикула <a href="https://www.paketoff.ru/shop?search_text=<?=$art_num;?>" target="_blank"><?=$art_num;?></a> на складе:</b> <?=$plan_arts[sklad];?>
<br><strong>жесткая бронь (оплачены или имеется действующая бронь):</strong> <span style='color:green;font-weight:bold;'> <?=$plan_arts[booked];?>шт.</span>
<br><strong>в работе:</strong> <a href="https://crm.upak.me/acc/applications/?art_num=<?=$art_num;?>" target="_blank"><?=$plan_arts[in_work];?>шт.</a>
<br><strong>просроченная бронь:</strong> <span style='color:red;font-weight:bold;'><?=$booked_expire_total;?>шт.</span>
<br><strong>доступно к продаже:</strong> <?=$plan_arts[sklad]-$booked_total;?>шт.
<br><strong>заказано за период:</strong> <?=$zakazano_itog;?>шт.
<br><strong>выставлено на сумму:</strong> <?=$total_cost_itog;?>руб.

<br><br>
показать только где:
<span onclick="hide_show_tr('booked_existing')" style="text-decoration: underline; cursor: pointer" id="booked_existing">действующая бронь</span>  |
<span onclick="hide_show_tr('booked_expired')" style="text-decoration: underline; cursor: pointer" id="booked_expired">просроченная бронь</span>  |
<span onclick="hide_show_tr('shipping_maintained')" style="text-decoration: underline; cursor: pointer" id="shipping_maintained">накладная проведена</span>  |
<span onclick="hide_show_tr('shipping_done')" style="text-decoration: underline; cursor: pointer" id="shipping_done">отгруженные</span> |
<span onclick="hide_show_tr('all')" style="text-decoration: underline; cursor: pointer" id="all">все</span><br><br>
<?}?>
</span>

<?
} else {$errmes = "Введите артикул или сразу несколько, через запятую!";}
}
echo "<br><br><strong>".$errmes."</strong>";?>
  </td>
</tr>
</table>
</td>
</tr>
</table> <script type="text/javascript">
$("#art_num").focus();

<?if($_GET[act] == "show_bookings"){?>
hide_show_tr('booked_existing')
<?}?>

function hide_show_tr(trc){

    trs = ['shipping_done', 'shipping_maintained', 'booked_existing', 'booked_expired', 'all'];
    var total_qty_itog = 0
    var total_cost_itog = 0

    trs.forEach(function(t) {




        if(t == trc || trc == "all")
            {
                 $("."+t).show();
                 if(t == trc) $("#"+t).css('font-weight', 'bold');
                 $(".itog_tr").show();
            }
        else
            {

                 $("."+t).hide();
                 $("#"+t).css('font-weight', 'normal');
                // $(".itog_tr").hide();
            }

       });

            $('.qty').each(function(){
            if($(this).is(':visible'))
            total_qty_itog += parseInt($(this).html());
            });
            $("#total_qty_td").html(total_qty_itog);

            $('.total_cost').each(function(){
            if($(this).is(':visible'))
            total_cost_itog += parseInt($(this).html());
            });
            $("#total_cost_td").html(total_cost_itog);

}


/*<![CDATA[*/
function paste_totals(){
totals = $("#stat_info_full").html();
$("#stat_info_full_paste").html(totals);
}
 paste_totals()

function hide_show(coloumns_to_hide, act){

var coloumns_to_hide = coloumns_to_hide.split(',');
//var coloumns_to_hide = ["num_col", "oklad_hour_col"];

if($.isArray(coloumns_to_hide)){
$.each(coloumns_to_hide,function(index,value){
hide_cols(value,act)
});
}
else{
hide_cols(coloumns_to_hide,act)
}

}

function hide_cols(value,act){
if(act == "show"){
$('td[name^='+value+']').fadeIn(500);
$('colgroup[name^='+value+']').fadeIn(500);

}else{
$('td[name^='+value+']').fadeOut(500);
$('colgroup[name^='+value+']').fadeOut(500);
}
}

hide_show('col_in_pack,viruchka,potracheno,nacenka,marja')




/*]]>*/
</script>
</body>
</html>
<? ob_end_flush(); ?>