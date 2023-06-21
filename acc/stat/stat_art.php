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
}
.hightlight tr:hover{
background-color:#E6E6E6;
}
.task_link_bold{
	font-weight: bold;
}
/*]]>*/
</style>
</head>
<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
<script type="text/javascript" src="../includes/js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="../includes/js/jscalendar/lang/calendar-ru.js"></script>
<script type="text/javascript" src="../includes/js/jscalendar/calendar-setup-art-stat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="../includes/js/jscalendar/calendar-blue.css" />

<body>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>
<?
$name_curr_page = 'query_list';
require_once("../templates/top.php");
$tit = 'Статистика поступлений на склад';
require_once("../templates/main_menu.php");?>
<table width=1100 border=0 cellpadding=0 cellspacing=0 align=center bgcolor="#F6F6F6">
	<tr>
		<td align="center" class="title_razd">Статистика поступлений на склад</td>
	</tr>
 	<tr>
	<td valign="top">
 <?require_once("../templates/stat_menu.php"); ?>



     </td>
</tr>
<tr>
  <td align="center">


<form action="" method=get>
Артикул: <input type="text" name="art_num" id="art_num" style="width: 130px; height: 25px; font-size: 20px;" value="<?=$_GET["art_num"];?>"/>
дата от: <input type="text" id="date_from"  name="date_from" style="width: 130px; height: 25px; font-size: 20px;" value="<?=$_GET["date_from"];?>"/>
до: <input type="text" id="date_to"  name="date_to" style="width: 130px; height: 25px; font-size: 20px;" value="<?=$_GET["date_to"];?>"/>
<input type="hidden" name=type value="stat_art"/>
<input type=submit style="width: 130px; height: 30px; font-size: 20px;" value="показать!"></form>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "date_from",      // id of the input field
        button         :    "button_from"   // trigger for the calendar (button ID)
    });
    Calendar.setup({
        inputField     :    "date_to",      // id of the input field
        button         :    "button_to"   // trigger for the calendar (button ID)
    });
</script>
<?
if($_GET["art_num"]){
 $art_num = $_GET["art_num"];

 if($_GET["date_from"]){$date_from = "AND job.cur_time > '".$_GET["date_from"]." 00:00:00'"; }
 if($_GET["date_to"]){$date_to = "AND job.cur_time < '".$_GET["date_to"]." 23:59:59'"; }

 $shop_history = mysql_query("SELECT
 applications.art_num,
 job.cur_time,
 applications.title,
 job.num_of_work,
 applications.uid,
 job.num_ord FROM applications, job
 WHERE
 applications.num_ord = job.num_ord AND
 applications.art_num = '$art_num'
 ".$date_from."
 ".$date_to."
 AND job.job = '11'
 ORDER BY job.cur_time DESC LIMIT 0,200");
?>

<table width=1000 border=0 cellpadding=5>
<tr>
<td class="tab_query_tit">Артикул</td>
<td class="tab_query_tit">Дата поступления на склад</td>
<td class="tab_query_tit">Название</td>
<td class="tab_query_tit">Количество</td>
<td class="tab_query_tit">Номер заявки</td>
</tr>
 <?
 while($r = mysql_fetch_array($shop_history)) {
 if(!$r){$errmes = "Ничего не найдено!";}
 ?>
<tr onmouseover="this.style.background='#BDCDFF';" onmouseout="this.style.background='';">
<td><a href="http://www.paketoff.ru/admin/shop/goods_list/?count_on_page=20&search_type=by_text&izd_w=&izd_v=&izd_b=&search_text=<?=$r["0"];?>&kolvo=any&quantity=" target="_blank"><?=$r["0"];?></a></td>
<td><?
$fd = 'd.m.Y';
$d = $r['1'];
$d = date($fd, strtotime($d));
echo $d;?></td>
<td align=center><a href="/acc/applications/edit.php?id=<?=$r["4"];?>" target="_blank"><?=$r["2"];?></a></td>
<td align=center><?$post=$r["3"]; $tpost = $post + $tpost; echo $post;?></td>
<td align=center><a href="/acc/applications/edit.php?id=<?=$r["4"];?>" target="_blank"><?=$r["5"];?></a></td>
</tr>
<?}?>
<tr>
<td class="tab_query_tit"></td>
<td class="tab_query_tit"></td>
<td class="tab_query_tit" align=center>ИТОГО</td>
<td class="tab_query_tit" align=center><?=$tpost;?></td>
<td class="tab_query_tit"></td>
</tr>

</table><? } else {echo "<br><br><strong>Введите артикул!</strong>"; }  ?>

</td>
</tr>
</table>
</td>
</tr>
</table>
<script type="text/javascript">
/*<![CDATA[*/

function focus_pass(){$("#art_num").focus()}
focus_pass()

/*]]>*/
</script>
</body>
</html>
<? ob_end_flush(); ?>