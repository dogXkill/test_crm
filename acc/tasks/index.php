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
if ($user_access['tasks_access'] == '0' || empty($user_access['tasks_access'])) {
  header('Location: /');
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Задачи</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<link rel="stylesheet" type="text/css" href="../includes/fonts/css/all.min.css">
<link rel="stylesheet" type="text/css" media="all" href="../includes/js/calendar/calendar-blue.css" title="Aqua" />

<style type="text/css">
<!--
.table_tasks tr:hover{
background-color:#E6E6E6;

}
.table_tasks{
border: #C0C0C0 solid 1px;
border-collapse:collapse;
}
.surname{
  font-size: 10px;
}

.ui-state-default {
  border: 1px solid #A0A0A0;
  background-color: #D8D8D8;
  font:arial;
  font-size:14px;
  cursor:pointer;
  border-radius:4px;
  padding-left: 3px;
  padding-top: 3px;
  padding-right: 3px;
  padding-bottom: 3px;
  margin-bottom: 3px;
  margin-left: 3px;
  margin-right: 3px;
  margin-top: 3px;
  list-style:none;
  width: 300px;
  vertical-align:top;
  opacity:1.0 !important;
}

.ui-state-done {
  border: 1px solid #A0A0A0;
  background-color: #D0D0D0;
  font:arial;
  font-size:14px;
  cursor:pointer;
  border-radius:4px;
  padding-left: 3px;
  padding-top: 3px;
  padding-right: 3px;
  padding-bottom: 3px;
  margin-bottom: 3px;
  margin-left: 3px;
  margin-right: 3px;
  margin-top: 3px;
  list-style:none;
  width: 300px;
  vertical-align:top;
  opacity: 0.5;
}

.task_list_td{
  border-radius:6px;
  border: 1px solid #666666;
  background-color: white;
  padding-left: 5px;
  padding-top: 5px;
  padding-right: 5px;
  padding-bottom: 5px;
}
.fade-out-tasks{
position:absolute;
z-index:1000;
display:block;
background-color: #FFFFFF;
border: 1px solid #A0A0A0;
border-radius: 4px;
display:none;
  padding-left: 5px;
  padding-top: 5px;
  padding-right: 5px;
  padding-bottom: 5px;
  opacity:1.0 important;
}

.tasks_table_hdr{
font:arial;
  font-size:15px;
  font-weight: bold;
}

#task_done_td td{
  opacity:0.5;
}



.task_link{
  font:arial;
  font-size:14px;
}
.task_link_bold{
  font:arial;
  font-size:14px;
  font-weight: bold;
}

#sortable1 li span, #sortable2 li span { position: absolute; margin-left: -1.3em; }
-->
</style>
</head>

<body>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>
<? require_once("../templates/top.php"); ?>
<?
$name_curr_page = 'tasks';
      require_once("../templates/main_menu.php");
       $typ_ord = $_GET["typ_ord"];
       if ($typ_ord == ""){$typ_ord = "all";}
       ?>
<table align="center" width="1100" border="1">
  <tr>
    <td>
    <table width=1100 align=center><tr><td><a href="?typ_ord=1" class="task_link<?if($typ_ord == "1"){echo "_bold";}?>">заказы</a> | <a href="?typ_ord=2" class="task_link<?if($typ_ord == "2"){echo "_bold";}?>">магазин</a> | <a href="?typ_ord=all" class="task_link<?if($typ_ord == "all"){echo "_bold";}?>">все</a></td></tr></table>
       <br>
  <?
  if($user_type  == "sup"){$user_id_vstavka="";}else{$user_id_vstavka = "AND queries.user_id = '$user_id'";}
    if (is_numeric($typ_ord)){
    $typ_ord_vstavka = "AND queries.typ_ord = '$typ_ord'";} else {$typ_ord_vstavka="";}
    $tasks = mysql_query("SELECT tasks.query_id, clients.short, queries.typ_ord, queries.prdm_sum_acc, queries.prdm_dolg, tasks.task_ids, tasks.done_ids, queries.user_id, obj_accounts.num, obj_accounts.name, queries.date_query FROM tasks, queries, clients, obj_accounts WHERE tasks.query_id=queries.uid AND queries.uid=obj_accounts.query_id AND queries.client_id=clients.uid AND status!='0' ".$user_id_vstavka." " .$typ_ord_vstavka." AND tasks.task_ids <> '' GROUP BY tasks.query_id ORDER BY queries.date_query DESC LIMIT 0,150");
   ?>

    <table width=1100 cellpadding=5 border=1 class="table_tasks" cellspacing=0>
    <tr>
    <td align=center class=tasks_table_hdr width=300>Клиент</td>
    <td align=center class=tasks_table_hdr>Что делаем</td>
    <td align=center class=tasks_table_hdr>Тираж</td>
    <td align=center class=tasks_table_hdr>Тип проекта</td>
    <td align=center class=tasks_table_hdr>Сумма заказа</td>
    <td align=center class=tasks_table_hdr>Долг клиента</td>
    <td align=center class=tasks_table_hdr>Текущий статус</td>
    <td align=center class=tasks_table_hdr>% выполнения</td>
    <td align=center class=tasks_table_hdr></td>
    </tr>
<?
$curr_task_name = array( "0" => "Заключение договора (бланка заказа)",
"1" => "Выставление счета",
"2" => "Ожидание оплаты",
"3" => "Утверждение превью",
"4" => "Утвеждение спуска клиентом",
"5" => "Составить заявку на производство",
"6" => "Заказ печати",
"7" => "Вывоз листов из типографии",
"8" => "Контроль качества",
"9" => "Организация отгрузки",
"10" => "Заказ штампа",
"11" => "Заказ бумаги",
"12" => "Заказ клише",
"13" => "Заказ ручек",
"14" => "Посещение приладки",
"15" => "Заказ люверсов",
"16" => "Отвезти пакеты на нанесение",
"17" => "Изготовление сигнальника",
"18" => "Цветопроба" );

//получаем массив с фамилиями сотрудников
$users = mysql_query("SELECT uid, surname FROM users WHERE archive!='1' AND administration='1'");
$user = array();
while($r = mysql_fetch_array($users)){$user[$r[uid]] = $r[surname];}



        while(@$r = mysql_fetch_array($tasks)) {
        $tasks_ar = explode(",", $r["5"]);
        $done_tasks_ar = explode(",", $r["6"]);
        $done_tasks_ar = array_diff($done_tasks_ar, array( '' ));
        $tasks_ar_num = count($tasks_ar);
        $done_tasks_ar_num = count($done_tasks_ar);
        //массив невыполненных заданий
        $undone = array_diff($tasks_ar, $done_tasks_ar);
        $undone = array_values($undone);
        //$undone = implode(",", $undone);
        $done_tasks_ar = implode(",", $done_tasks_ar);
 ?>
    <tr id="tr_<?=$r["0"];?>" <?if(!is_numeric($undone[0])){?>class="task_done_td"<?}?>>
    <td><a href="../query/query_send.php?show=<?=$r["0"];?>"><?=$r["1"];?></a> <br><span class=surname><? if($user_type  == "sup"){echo $user[$r["7"]];} echo "(".$r["10"].")";?></span></td>

    <td style="font-size:10px;"><?=$r["9"];?></td>
    <td><?=$r["8"];?></td>
    <td align=center><?if($r["2"]==2){?>магазин<?} if($r["2"]==1){?>заказ<?} if($r["2"]==3){?>прочее<?}?></td>
    <td align=center><?=round($r["3"]);?></td>
    <td align=center><?=round($r["4"]);?></td>
    <td>

    <div id="curr_task_<?=$r["0"];?>" onclick="show_tasks('<?=$r["0"];?>')"><?if(is_numeric($undone[0])){?><li class="ui-state-default"><?=$curr_task_name[$undone[0]]?>
    <?if ($undone[0] == "5"){?><a href="/acc/applications/edit.php?type=1&uid=<?=$r["0"];?>" target=_blank>
	<!--<img width="20" height="20" src="../../i/manufacture_pr.png" onmouseover="Tip('Создать заявку на производство')">-->
	<i class="fa-solid fa-file-pen icon_btn_r21 icon_btn_blue" onmouseover="Tip('Создать заявку на производство')"></i>
	</a><? } ?>
    <?if ($undone[0] == "9"){?><a href="/acc/logistic/courier_tasks.php?query_id=<?=$r["0"];?>" target=_blank>
	<!--<img width="20" height="20" src="../i/logistic.png" onmouseover="Tip('Просмотреть заявку на курьера')">-->
	<i class="fa-solid fa-truck icon_btn_r21 icon_btn_blue" onmouseover="Tip('Просмотреть заявку на курьера')"></i>
	</a><? } ?>
    </li><?}else{?><li class="ui-state-default">Завершено!</li><?}?></div>
    <div id="done_tasks_<?=$r["0"];?>" class="fade-out-tasks"></div>

    </td>
       <?if ($done_tasks_ar_num == "0"){$procent = "0";} else {$procent=round($done_tasks_ar_num/$tasks_ar_num*100);}?>
    <td align=center style="color:<?if($procent <= "20"){echo "#D60004";}if($procent <= "40" and $procent > "20"){echo "#D6851B";}if($procent <= "60" and $procent > "40"){echo "#83A130";}if($procent <= "80" and $procent > "60"){echo "#57A12F";}if($procent <= "100" and $procent > "80"){echo "#17A10D";}?>">

    <?=$procent;?>%</td>
    <td align=center><img src="../i/del.gif" width="20" height="20" alt="" id="del_<?=$r["0"];?>" style="cursor:pointer;" onclick="hide_task(<?=$r["0"];?>)"></td>
    </tr>
       <? } ?>
    </table>


		</td>
	</tr>
</table>

<script>


var task_list = [
['0','Заключение договора (бланка заказа)'],
['1','Выставление счета'],
['2','Ожидание оплаты'],
['3','Утверждение превью'],
['4','Утвеждение спуска клиентом'],
['5','Составить заявку на производство'],
['6','Заказ печати'],
['7','Вывоз листов из типографии'],
['8','Контроль качества'],
['9','Организация отгрузки'],
['10','Заказ штампа'],
['11','Заказ бумаги'],
['12','Заказ клише'],
['13','Заказ ручек'],
['14','Посещение приладки'],
['15','Заказ люверсов'],
['16','Отвезти пакеты на нанесение'],
['17','Изготовление сигнальника'],
['18','Цветопроба']
];

function show_tasks(id){
  $('#done_tasks_'+id).html("");
  geturl = $.ajax({
    type: "GET",
    url: '../backend/update_done_tasks.php',
	data : '&query_id='+id+'&act=get_tasks',
    success: function () {
var resp = geturl.responseText

$('#done_tasks_'+id).html(resp);
$('#done_tasks_'+id).slideDown(150)
$("#fade").show()
}})
      }


function hide_task_list(){
$("#fade").hide()
$(".fade-out-tasks").slideUp(250)
$(".fade-out-tasks").html("");
}


function set_view(query_id, task_id){
 if ($("#chk_"+query_id+"_"+task_id).prop("checked")){
 $( "#li_"+query_id+"_"+task_id).addClass( "ui-state-done" )
 $( "#li_"+query_id+"_"+task_id).removeClass( "ui-state-default" )
 } else {
 $( "#li_"+query_id+"_"+task_id).addClass( "ui-state-default" )
 $( "#li_"+query_id+"_"+task_id).removeClass( "ui-state-done" )  }
}

function save_tasks(id){
  a = ""
  var a = new Array();
  c = $("input[name='done']:checked");
  $.each(c, function (n, v) {
    a += v.value + ",";
});

  geturl = $.ajax({
    type: "GET",
    url: '../backend/update_done_tasks.php',
	data : '&query_id='+id+'&done_tasks='+a+'&act=update',
    success: function () {
var resp = geturl.responseText
 if (resp == "OK"){ document.location = '?typ_ord=<?=$typ_ord;?>'; }
}})

}


//проект не удаляется, а ему просто присваевается нулевой status=0
function hide_task(id){
 if (confirm("Точно удалить проект?")) {
  geturl = $.ajax({
    type: "GET",
    url: '../backend/update_done_tasks.php',
	data : '&query_id='+id+'&act=delete',
    success: function () {
var resp = geturl.responseText
if (resp == "OK"){
  $("#tr_"+id).hide()

}else{
alert('ошибка! '+resp) }
}})
} }
</script>
<div id="fade" style="position:fixed; top:0px; left:0px; height: 100%; width:100%; background-color: black; opacity: 0.4;z-index:999;display:none" onclick="hide_task_list()"></div>
</body>
</html>
<? ob_end_flush(); ?>
