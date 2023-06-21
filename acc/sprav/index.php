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

if ($user_access['sprav_access'] == '0' || empty($user_access['sprav_access'])) {
  header('Location: /');
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Справочники</title>
<link href="../style.css?cache=<?=rand(1,1000000);?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>

<style type="text/css">
<!--
#vendors_table td, #tbl td  {
border: 1px solid #336699;
padding: 4px;
}
.spans{
border: 1px solid #336699;
background-color:white;
padding: 10px;
display:none;
}
#vendors_table tr:hover, #tbl tr:hover{
background-color: #E6E6E6;
}
</style>
</head>


<body>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>
<? require_once("../templates/top.php");
$name_curr_page = 'sprav';
require_once("../templates/main_menu.php");
$part = $_GET["part"];
?>
<table align="center" width="1100" border="0" cellpadding=0 bgcolor="#F6F6F6">
<tr><td>
<table cellpadding=10>
<tr>
<td>

<li><a href="/acc/users/users.php" class=sublink>сотрудники</a></li>
<li><a href="/acc/sprav/user_groups" class=sublink>группы пользователей</a></li>
<li><a href="/acc/sprav/user_posts" class=sublink>должности пользователей</a></li>
<li><a href="/acc/sprav/user_departments" class=sublink>отделы </a></li>
<li><a href="/acc/logistic/couriers.php" class=sublink>водители</a></li>
<li><a href="/acc/sprav/vendors/?part=vendor_types" class=sublink <?if($part=="vendor_types"){?>style="font-weight:bold;"<?}?>>типы поставщиков</a></li>
<li><a href="/acc/query/clients_list.php " class=sublink>клиенты</a></li>

<?php if ($user_access['proizv_access']){?>
<li><a href="/acc/sprav/stamps" class=sublink>реестр штампов</a></li>
<?php }?>
<li><a href="https://docs.google.com/spreadsheets/d/1VmhyCSLaUTK322fYc1mepDKxGOeLo5BfqqcY6w9jHfE/edit#gid=0" target=_blank class=sublink>лист ожидания</a></li>
<li><a href="https://docs.google.com/spreadsheets/d/167ipSXKXRoKcSSM01JWD8cG70fIuy6w0WBqjMmV4Qa0/edit#gid=0" target=_blank class=sublink>регистрация счетов</a></li>
<li><a href="https://docs.google.com/document/d/10T8wr0b9Vpe5xPP9yaDrHsGFEppLfUrNEfoM5ZXj9a8/edit" target=_blank class=sublink>шпаргалка для менеджеров</a></li>
<li><a href="https://docs.google.com/spreadsheets/d/1JusKaGQDgIaaiqSok6dgYWc5R6TMRWesbeO4FNKXicA/edit#gid=0" target=_blank class=sublink>остатки материала для клиентов</a></li>

</td>
</tr>
</table>



</td></tr>
</table>


</body>
</html>
<? ob_end_flush(); ?>
<!--<li><a href="/acc/sprav/vendors/?part=vendors" class=sublink <?if($part=="vendors"){?>style="font-weight:bold;"<?}?>>поставщики</a></li>-->
<!--<li><a href="/acc/plan/?part=groups " class=sublink>группы товаров</a></li>-->