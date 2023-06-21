<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

ob_start();


$auth = false;


$error = '';
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");


$tpus = $user_type;		// тип пользователя

// ----- перейти на главную если доступ запрещен ---------
if(!$auth || ($tpus != 'sup' && $tpus != 'meg' && $tpus != 'acc')) {
	header("Location: /");
	exit;
}


// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'acc') || ($user_type == 'adm')) ? 1 : 0;

//получаем в массив список должностей
            $get_doljnost = mysql_query("SELECT * FROM doljnost");
            //$d = mysql_fetch_row($get_doljnost);
            while ( $row = mysql_fetch_array($get_doljnost) ) {
            unset($row[0]);
            unset($row[1]);
           $d[$row[id]] = $row;
                      }

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Expires" content="Mon, 26 Jul 1997 05:00:00 GMT" /> 
<meta http-equiv="Pragma" content="no-cache" /> 
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.2.0</title>
<link href="../style.css?cache=<?=rand(1,1000000);?>" rel="stylesheet" type="text/css" />
<link href="../includes/new.css?cache=<?=rand(1,1000000);?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
</head>
<script language="JavaScript" type="text/javascript">
<!--
function set_y_m(){
var date = new Date(),
year = date.getFullYear(),
month = date.getMonth()+1;
if (month<10) {month='0'+month;}
$("select#year").val(year)
$("select#month").val(month)

}
function get_timetable(){
year = $('#year').val();
month = $('#month').val();
window.location.href = '../applications/timetable/report.php?type=proizvodstvo&year='+year+'&month='+month;
}
function timetable_div(){
$('#timetable_div').toggle(250)
set_y_m()
}

function arch_user(id, act) {
    if(act == "del_archive"){msg = "Удалить пользователя в архив?";}
    if(act == "restore"){msg = "Восстановить пользователя из архива?";}
    if(act == "del_final"){msg = "Удалить пользователя окончательно и навсегда?";}
	if(confirm(msg))
		document.location = 'users.php?nadomn=<?=$_GET["nadomn"];?>&proizv=<?=$_GET["proizv"];?>&archive=' +act+ '&del=' + id;
	}


//-->
</script>


<body>

<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>

<? require_once("../templates/top.php"); ?>
<table align=center width=750 border=0>

<tr>
<td colspan=3>
<br>
<?
$name_curr_page = 'users';
require_once("../templates/main_menu.php");?>
<table width=1200 border=0 cellpadding="5" cellspacing="0" bgcolor="#F6F6F6">

	<tr>
		<td align="center" class="title_razd">Список сотрудников</td>
	</tr>
	<tr>
		<td valign="top" align="center">
			<table border="0" cellspacing="10" cellpadding="0">
				<tr>
					<td width="450">
<a href="users.php?administration=1" class="sublink" <? if($_GET['administration'] && !$_GET['oper'] && !$_GET['edit']) {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>>администрация</a> |
<a href="users.php?proizv=1" class="sublink" <? if($_GET['proizv'] == "1") {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>>производство</a> |
<a href="users.php?nadomn=1" class="sublink" <? if($_GET['nadomn'] == "1") {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>>надомники</a> |
<a href="users.php?oper=all" class="sublink" <? if($_GET["oper"]=="all") {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>>все</a> |
<a href="users.php?oper=archive" class="sublink" <? if($_GET["oper"]=="archive") {echo "style=\"font-weight:bold;border-bottom:none;\" ";} ?>>архив</a>

					</td>

<td width="250">
<?if ($user_type == "sup" || $user_type == "acc"){?>
<a href="#" target=_blank>
<img src="../../../i/vedomost.png" width="22" height="22" alt="" align="middle" onclick="timetable_div()"></a>
<a href="#" class=sublink onclick=timetable_div()>Ведомость</a>

<div id=timetable_div style="background-color:white; z-index:100; position:absolute;display:none; border: 1px black; border: 1px solid black;">

<table align=center cellpadding=10>
<tr>
<td>
Год: <select name=year id=year>
<option value="2014">2014</option>
<option value="2015">2015</option>
<option value="2016">2016</option>
<option value="2017">2017</option>
<option value="2018">2018</option>
<option value="2019">2019</option>
<option value="2020">2020</option>
</select>

Месяц:
<select name=month id=month>
<option value="01">январь</option>
<option value="02">февраль</option>
<option value="03">март</option>
<option value="04">апрель</option>
<option value="05">май</option>
<option value="06">июнь</option>
<option value="07">июль</option>
<option value="08">август</option>
<option value="09">сентябрь</option>
<option value="10">октябрь</option>
<option value="11">ноябрь</option>
<option value="12">декабрь</option>
</select>
</td>
<td><input type=button value=OK onclick="get_timetable()"></td>
<td>
<img src="../../i/del.gif" width="20" height="20" alt="" style="cursor:pointer" onclick=timetable_div()>
</td>

</tr>
</table>
</div>

<?}?>

				   </td>
					<? if( ($tpus == 'sup') || ($tpus == 'acc') ) { ?>
					<td width="130">
					 	<a href="user_edit.php?oper=new"><img src="../../i/add_user.png" width="32" height="32" alt="" valign=middle></a>
						<a href="user_edit.php?oper=new" class="sublink" style="font-weight:bold;border-bottom:none;">создать пользователя</a>
                    </td>

                    <td width="130">
						<a href="options.php?type=department_managment"><img src="../../i/depart.png" width="32" height="32" alt=""> </a>
						<a href="options.php?type=department_managment" class="sublink">подразделения</a>
					</td>

					<td width="130">
						<a href="options.php?type=email_managment"><img src="../../i/emails.png" width="32" height="32" alt="" valign=middle></a>
						<a href="options.php?type=email_managment">список E-mail рассылки</a>
					</td>

                    <?}?>
				</tr>
			</table></td>
</tr>



			<table width="1200" border="0" cellpadding="3" cellspacing="2" bordercolor="#999999">
				<tr class="tab_query_tit">
					<td align="center" class="tab_query_tit">Ф.И.О.</td>
					<td align="center" class="tab_query_tit">должность</td>
					<td align="center" class="tab_query_tit">база</td>
					<td align="center" class="tab_query_tit">id работника производства</td>
					<td align="center" class="tab_query_tit">Мобильный тел.</td>
					<td align="center" class="tab_query_tit">Логин, пароль</td>
					<td align="center" class="tab_query_tit">Тип доступа</td>
					<td align="center" class="tab_query_tit">Операция</td>
				</tr>
				<?
				if ($_GET["proizv"]=="1"){
				$proizv = " WHERE proizv = '1' AND  archive != '1'";
				}
				else if ($_GET["administration"]=="1"){
				$proizv = " WHERE administration = '1' AND  archive != '1'";
				}
				else if ($_GET["nadomn"]=="1"){
				$proizv = " WHERE nadomn = '1' AND  archive != '1'";
				}
				else if ($_GET["oper"]=="all"){
				$proizv = " WHERE archive != '1'";
				}
				else if ($_GET["oper"]=="archive"){
				$proizv = " WHERE archive = '1'";
				}
				else {$proizv = " WHERE administration = '1' AND  archive != '1'";}

				$query = "SELECT * FROM users ".$proizv." ORDER BY  surname";
				$res = mysql_query($query);
				while($r_us = mysql_fetch_array($res)) {
					$fio = $r_us['surname'].' '.$r_us['name'].' '.$r_us['father'];



						
					switch($r_us['type']) {
						case 'adm':
							$user_header = 'администратор';
							break;
						case 'mng':
							$user_header = 'менеджер';
							break;
						case 'acc':
							$user_header = 'бухгалтер';
							break;
						case 'meg':
							$user_header = 'мегаадмин';
							break;
						case 'sup':
							$user_header = 'суперадмин';
							break;
						default:
							$user_header = 'гость';
					}

					
				?>
				<tr>
					<td class="tab_td_marg"><a href="?edit=<?=$r_us['uid']?>&oper=edit" onmouseover="Tip('Редактировать')" class="user_fio_link"><?=$fio?></a></td>


                    <td align="center" class="tab_td_norm"><?=$d[$r_us['doljnost']][name];?></td>
                    <td align="center" class="tab_td_norm"><?=$r_us['oklad'];?></td>
                    <td  class="tab_td_norm"><strong><?=$r_us['job_id'];?></strong></td>


					<td align="center" class="tab_td_norm"><?=(trim($r_us['mobile'])) ? $r_us['mobile'] : '---'?></td>

					<?$sh_login = (trim($r_us['login']) && ($tpus=='sup')) ?  $r_us['login']."<br>".$r_us['pass'] : '---';?>

					<td align="center" class="tab_td_norm"><?if($user_id == '11'){?><?=$sh_login?><br><?=$sh_pass;}?></td>
					<td align="center" class="tab_td_norm"><?=$user_header?></td>
					<td class="tab_td_norm" align="center">
					<a href="../../acc/applications/count/exp_csv.php?num_sotr='.$r_us['job_id'].'"><img src="../../i/export.png" width="24" height="24" alt="" valign=middle/></a>
					<img  width="20" height="20" src="../i/edit2.gif" title="Редактировать" />
                    <img width="20" height="20" src="../i/pr_ok.gif" title="Восстановить из архива" />
                    <img width="20" height="20" src="../i/del.gif" title="Удалить окончательно" />
                    <img width="20" height="20" src="../i/del.gif" title="Удалить в архив" />

                    </td>
				</tr>
			<? }  ?>
			</table>
	</td>

</tr>
</table>
<br><br>

</td>

</tr>
</table>

</body>
</html>
<? ob_end_flush() ?>