<<<<<<< HEAD
<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
header('Content-Type: text/html; charset=windows-1251');
list($msec,$sec)=explode(chr(32),microtime());
$mTimeStart=$sec+$msec;
$auth = false;
require_once("acc/includes/db.inc.php");
require_once("acc/includes/auth.php");
// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'meg') || ($user_type == 'adm') || ($user_type == 'acc')) ? 1 : 0;
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<script type="text/javascript" src="/acc/includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="/acc/includes/js/jquery-ui.js"></script>
<title>Printfolio intranet v.2</title>
<link href="/acc/style.css?5" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="/acc/includes/fonts/css/all.min.css">
</head>

<body>
<script type="text/javascript" src="/acc/includes/js/wz_tooltip.js"></script>
<?require_once("acc/templates/top.php");
require_once("acc/templates/main_menu.php");?>
<table width=1100 border=0 align=center cellpadding="0" cellspacing="0" bgcolor="#F6F6F6"><tr><td>
    <? if(@$auth) { ?>
<table width=1100 border=0 align=center cellpadding="0" cellspacing="0">
<tr>
<td width=500 valign=top>
<br>
<?
if ($_COOKIE['main_info_access'] == '1') {
  ?>
  <ul style="text-align: left">
  <li><a href="https://upakme.rsuquant.ru/tasks/list/my" target="_blank"><font face=tahoma size=2>Квант</font></a></li>


  <li><a href="docs/new/изготовление_ИП_шаблон.doc"><font face=tahoma size=2>Договор на изготовление заказной продукции</font></a></li>
  <li><a href="docs/new/купля_продажа_ИП_шаблон.doc"><font face=tahoma size=2>Договор купли продажи готовой продукции</font></a></li>
  <li><a href="docs/new/агентский_договор_шаблон.doc"><font face=tahoma size=2>Агентский договор на привлечение клиентов</font></a></li>

  <li><a href="docs/shablon_bum_paket.cdr" target="_blank"><font face=tahoma size=2>Бумажный пакет клиенту на утверждение (шаблон)</font></a></li>

  <li><a href="docs/blank_ip.doc" target="_blank"><font face=tahoma size=2>Бланк ИП Москвин</font></a></li>

  <br>
  <li><a href="https://www.fefco.org/sites/default/files/files/2022-04-21_FEFCO%20Code_WEB_unprotected.pdf" target="_blank"><font face=tahoma size=2>Каталог fefco</font></a></li>
  <li><a href="https://docs.google.com/document/d/10T8wr0b9Vpe5xPP9yaDrHsGFEppLfUrNEfoM5ZXj9a8/edit" target="_blank"><font face=tahoma size=2>Шпаргалка для менеджеров</font></a></li>
  <li><a href="https://upakme.amocrm.ru/leads/pipeline/" target="_blank"><font face=tahoma size=2>АМО для менеджеров</font></a></li>


  <?
}
?>

</ul>


</td>

<td valign=top align=left width=500><br>


<br />
<br />

<?
if ($user_access['order_access'] == '1') {
  ?>
  <a href="/acc/query/query_send.php" class="sublink"><!--<img src="/i/invoice.png" width="32" height="32" alt="" align=absmiddle />--><i class="fa-duotone fa-file icon_btn_r30 icon_home" style="--fa-primary-color: #000000; --fa-primary-opacity: 0.6; --fa-secondary-color: #cec7bc; --fa-secondary-opacity: 1;"></i></a> <a href="/acc/query/query_send.php" style="font-size: 20px;vertical-align: inherit;">запросить счет</a><br><br>
  <?
}
if ($user_access['proizv_access'] == '1') {
  ?>
  <a href="/acc/applications/edit.php" class="sublink "><!--<img src="/i/manufacture32.png" width="32" height="32" alt="" align=absmiddle />--><i class="fa-duotone fa-file-pen icon_btn_r30 icon_home" style="--fa-primary-color: #000000; --fa-primary-opacity: 0.6; --fa-secondary-color: #e8b04f; --fa-secondary-opacity: 1;"></i></a> <a href="/acc/applications/edit.php" style="font-size: 20px;vertical-align: inherit;">заявка на производство</a><br>

  <?
}

if ($user_access['accounting_user'] == 1 && $user_access['jobs_access'] !== '0') {
    ?>
    <br>
    <a href="/acc/applications/count/add.php" target=_blank class="sublink"><!--<img src="/i/add_job.png" width="32" height="32" alt="" align="absmiddle">--><i class="fa-solid fa-square-plus icon_btn_r30 icon_btn_blue icon_home"></i></a> <a href="/acc/applications/count/add.php" style="font-size: 20px;vertical-align: inherit;" target=_blank>добавить работу</a>
    <?
}
?>

<br />


<?
if ($user_access['order_access'] == '1') {
  require_once("acc/templates/search_form.php");
} ?>
<br />


</td>
</tr>
</table>


</td>
</tr>
<tr>
<td bgcolor="#F6F6F6">
<table width=800 border=0 align=center cellpadding="0" cellspacing="0">
<tr>
<td valign=top width=350>



<?
if ($_COOKIE['main_info_access'] == '1') {
?>
  <span onclick="show_ip()" id=ip_tit style="background-color: #D8D8D8; cursor: pointer;">ИП Москвин</span>
  <table border=0 cellpadding="7" cellspacing="0" id=ip  style="display: yes; background-color: #D8D8D8;"><tr><td>
    <textarea  name="" id="" cols="50" rows="18">
    Индивидуальный предприниматель Москвин Павел Дмитриевич
    ОГРНИП 312501735300010
    ИНН   501703416801
    Свидетельство  50№013006060
    143532,  Московская область, г. Дедовск, ул. 1-ая Волоколамская, д.60
    Почтовый адрес:
    143432 Московская обл., Красногорский р-н, п. Нахабино, ул Институтская, д. 11, абонентский ящик 705
    Расчетный счет	40802810900000051534
    Банк	АО "ТИНЬКОФФ БАНК"
    БИК банка	044525974
    Корр.счет банка	30101810145250000974
    </textarea>
    <br />
  </td></tr></table>
<?
}
?>

<script type="text/javascript">
/*<![CDATA[*/

function art_search(){

art_num = $('#art_num').val()

if($.isNumeric(art_num)){

 if ($('#postupl').prop('checked'))
 {
   window.open('/acc/stat/stat_art.php?art_num='+art_num+'&type=stat_art', '_blank');
 }
 if ($('#realiz').prop('checked'))
 {
   window.open('/acc/stat/stat_shop.php?art_num='+art_num+'&type=shop_history', '_blank');
 }
 if ($('#site').prop('checked'))
 {
   //window.open('https://www.paketoff.ru/admin/shop/goods_list/?count_on_page=20&search_type=by_text&izd_w=&izd_v=&izd_b=&search_text='+art_num+'', '_blank');
   window.open('https://www.paketoff.ru/shop?search_text='+art_num, '_blank');
 }
 if ($('#apps').prop('checked'))
 {
   window.open('/acc/applications/?zakaz_id='+art_num, '_blank');
 }

}else{
  alert("Введите артикул в виде числа!")
  $('#art_num').focus()
}

}
/*]]>*/
</script>


</td><td valign=top align=center>


<table><tr><td>
 <h2>Поиск артикула</h2>
<!--<input type="radio" name="art_search" id="postupl"/><label for="postupl" style="cursor:pointer;">поступления на склад</label><br />-->
<!--<input type="radio" name="art_search" id="realiz"/><label for="realiz" style="cursor:pointer;">реализация</label><br />-->
<input type="radio" name="art_search" id="site"/><label for="site" style="cursor:pointer;">на сайте</label><br />
<?
if ($user_access['proizv_access'] == '1') {
  ?>
  <input type="radio" name="art_search" id="apps"/><label for="apps" style="cursor:pointer;">заявки на производстве</label><br />
  <?
}
?>
<input id="art_num" name="art_num" value="" style="width: 120px; height: 27px; font-size: 20px;" type="text"/>
<input name="button" type="button" style="width: 70px; height: 32px; font-size:18px;" value=">>>" onclick="art_search()"/>
</td></tr></table>



</td></tr></table><br><br>
   <? }?>
</td></tr></table></td></tr></table>
<br><br>






</body>
</html>
=======
<?
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
header('Content-Type: text/html; charset=windows-1251');
list($msec,$sec)=explode(chr(32),microtime());
$mTimeStart=$sec+$msec;
$auth = false;
require_once("acc/includes/db.inc.php");
require_once("acc/includes/auth.php");
// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'meg') || ($user_type == 'adm') || ($user_type == 'acc')) ? 1 : 0;
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<script type="text/javascript" src="/acc/includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="/acc/includes/js/jquery-ui.js"></script>
<title>Printfolio intranet v.2</title>
<link href="/acc/style.css?5" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="/acc/includes/fonts/css/all.min.css">
</head>

<body>
<script type="text/javascript" src="/acc/includes/js/wz_tooltip.js"></script>
<?require_once("acc/templates/top.php");
require_once("acc/templates/main_menu.php");?>
<table width=1100 border=0 align=center cellpadding="0" cellspacing="0" bgcolor="#F6F6F6"><tr><td>
    <? if(@$auth) { ?>
<table width=1100 border=0 align=center cellpadding="0" cellspacing="0">
<tr>
<td width=500 valign=top>
<br>
<?
if ($_COOKIE['main_info_access'] == '1') {
  ?>
  <ul style="text-align: left">
  <li><a href="https://upakme.rsuquant.ru/tasks/list/my" target="_blank"><font face=tahoma size=2>Квант</font></a></li>


  <li><a href="docs/new/изготовление_ИП_шаблон.doc"><font face=tahoma size=2>Договор на изготовление заказной продукции</font></a></li>
  <li><a href="docs/new/купля_продажа_ИП_шаблон.doc"><font face=tahoma size=2>Договор купли продажи готовой продукции</font></a></li>
  <li><a href="docs/new/агентский_договор_шаблон.doc"><font face=tahoma size=2>Агентский договор на привлечение клиентов</font></a></li>

  <li><a href="docs/shablon_bum_paket.cdr" target="_blank"><font face=tahoma size=2>Бумажный пакет клиенту на утверждение (шаблон)</font></a></li>

  <li><a href="docs/blank_ip.doc" target="_blank"><font face=tahoma size=2>Бланк ИП Москвин</font></a></li>

  <br>
  <li><a href="https://www.fefco.org/sites/default/files/files/2022-04-21_FEFCO%20Code_WEB_unprotected.pdf" target="_blank"><font face=tahoma size=2>Каталог fefco</font></a></li>
  <li><a href="https://docs.google.com/document/d/10T8wr0b9Vpe5xPP9yaDrHsGFEppLfUrNEfoM5ZXj9a8/edit" target="_blank"><font face=tahoma size=2>Шпаргалка для менеджеров</font></a></li>
  <li><a href="https://upakme.amocrm.ru/leads/pipeline/" target="_blank"><font face=tahoma size=2>АМО для менеджеров</font></a></li>


  <?
}
?>

</ul>


</td>

<td valign=top align=left width=500><br>


<br />
<br />

<?
if ($user_access['order_access'] == '1') {
  ?>
  <a href="/acc/query/query_send.php" class="sublink"><!--<img src="/i/invoice.png" width="32" height="32" alt="" align=absmiddle />--><i class="fa-duotone fa-file icon_btn_r30 icon_home" style="--fa-primary-color: #000000; --fa-primary-opacity: 0.6; --fa-secondary-color: #cec7bc; --fa-secondary-opacity: 1;"></i></a> <a href="/acc/query/query_send.php" style="font-size: 20px;vertical-align: inherit;">запросить счет</a><br><br>
  <?
}
if ($user_access['proizv_access'] == '1') {
  ?>
  <a href="/acc/applications/edit.php" class="sublink "><!--<img src="/i/manufacture32.png" width="32" height="32" alt="" align=absmiddle />--><i class="fa-duotone fa-file-pen icon_btn_r30 icon_home" style="--fa-primary-color: #000000; --fa-primary-opacity: 0.6; --fa-secondary-color: #e8b04f; --fa-secondary-opacity: 1;"></i></a> <a href="/acc/applications/edit.php" style="font-size: 20px;vertical-align: inherit;">заявка на производство</a><br>

  <?
}

if ($user_access['accounting_user'] == 1 && $user_access['jobs_access'] !== '0') {
    ?>
    <br>
    <a href="/acc/applications/count/add.php" target=_blank class="sublink"><!--<img src="/i/add_job.png" width="32" height="32" alt="" align="absmiddle">--><i class="fa-solid fa-square-plus icon_btn_r30 icon_btn_blue icon_home"></i></a> <a href="/acc/applications/count/add.php" style="font-size: 20px;vertical-align: inherit;" target=_blank>добавить работу</a>
    <?
}
?>

<br />


<?
if ($user_access['order_access'] == '1') {
  require_once("acc/templates/search_form.php");
} ?>
<br />


</td>
</tr>
</table>


</td>
</tr>
<tr>
<td bgcolor="#F6F6F6">
<table width=800 border=0 align=center cellpadding="0" cellspacing="0">
<tr>
<td valign=top width=350>



<?
if ($_COOKIE['main_info_access'] == '1') {
?>
  <span onclick="show_ip()" id=ip_tit style="background-color: #D8D8D8; cursor: pointer;">ИП Москвин</span>
  <table border=0 cellpadding="7" cellspacing="0" id=ip  style="display: yes; background-color: #D8D8D8;"><tr><td>
    <textarea  name="" id="" cols="50" rows="18">
    Индивидуальный предприниматель Москвин Павел Дмитриевич
    ОГРНИП 312501735300010
    ИНН   501703416801
    Свидетельство  50№013006060
    143532,  Московская область, г. Дедовск, ул. 1-ая Волоколамская, д.60
    Почтовый адрес:
    143432 Московская обл., Красногорский р-н, п. Нахабино, ул Институтская, д. 11, абонентский ящик 705
    Расчетный счет	40802810900000051534
    Банк	АО "ТИНЬКОФФ БАНК"
    БИК банка	044525974
    Корр.счет банка	30101810145250000974
    </textarea>
    <br />
  </td></tr></table>
<?
}
?>

<script type="text/javascript">
/*<![CDATA[*/

function art_search(){

art_num = $('#art_num').val()

if($.isNumeric(art_num)){

 if ($('#postupl').prop('checked'))
 {
   window.open('/acc/stat/stat_art.php?art_num='+art_num+'&type=stat_art', '_blank');
 }
 if ($('#realiz').prop('checked'))
 {
   window.open('/acc/stat/stat_shop.php?art_num='+art_num+'&type=shop_history', '_blank');
 }
 if ($('#site').prop('checked'))
 {
   //window.open('https://www.paketoff.ru/admin/shop/goods_list/?count_on_page=20&search_type=by_text&izd_w=&izd_v=&izd_b=&search_text='+art_num+'', '_blank');
   window.open('https://www.paketoff.ru/shop?search_text='+art_num, '_blank');
 }
 if ($('#apps').prop('checked'))
 {
   window.open('/acc/applications/?zakaz_id='+art_num, '_blank');
 }

}else{
  alert("Введите артикул в виде числа!")
  $('#art_num').focus()
}

}
/*]]>*/
</script>


</td><td valign=top align=center>


<table><tr><td>
 <h2>Поиск артикула</h2>
<!--<input type="radio" name="art_search" id="postupl"/><label for="postupl" style="cursor:pointer;">поступления на склад</label><br />-->
<!--<input type="radio" name="art_search" id="realiz"/><label for="realiz" style="cursor:pointer;">реализация</label><br />-->
<input type="radio" name="art_search" id="site"/><label for="site" style="cursor:pointer;">на сайте</label><br />
<?
if ($user_access['proizv_access'] == '1') {
  ?>
  <input type="radio" name="art_search" id="apps"/><label for="apps" style="cursor:pointer;">заявки на производстве</label><br />
  <?
}
?>
<input id="art_num" name="art_num" value="" style="width: 120px; height: 27px; font-size: 20px;" type="text"/>
<input name="button" type="button" style="width: 70px; height: 32px; font-size:18px;" value=">>>" onclick="art_search()"/>
</td></tr></table>



</td></tr></table><br><br>
   <? }?>
</td></tr></table></td></tr></table>
<br><br>






</body>
</html>
>>>>>>> parent of c0fc685 (Revert "t")
