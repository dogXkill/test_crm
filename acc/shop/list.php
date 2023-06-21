<?

//die();
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");


ob_start();

$auth = false;

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");

if ($user_access['shop_access'] == '0' || empty($user_access['shop_access'])) {
	header('Location: /');
}

$tpus = $user_type;		// тип пользователя

// ----- перейти на главную если доступ запрещен ---------
if(!$auth) {
	header("Location: /");
	exit;
}
// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'acc') || ($user_type == 'meg')) ? 1 : 0;

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Магазин</title>
<link href="../style.css?cache=<?=rand(1,1000000);?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>
</head>
<body>
<script type="text/javascript" src="../includes/js/wz_tooltip.js"></script>

<?require_once("../templates/top.php"); ?>
<?$name_curr_page = 'shop';
require_once("../templates/main_menu.php");?>

<?php

$shop_login_id = @md5(@$_COOKIE['user_des'].@md5($_COOKIE['pass_des'])); // .date("Hdwm")

?>

<table width=1400 border=0 align=center cellpadding="0" cellspacing="0" bgcolor="#F6F6F6">
        <tr>
          <td>
 <?

            //точно такой же код генерится в includes/auth.php на сайте. Раньше приходилось создавать учетки каждому пользователю на сайте, что коряво
        $d = date("d");
        $m = date("m");
        $y = date("Y");

        //просто генерим код , точно такой же герерим в CRM. Действует только сутки, а затем нужно заново кликать по ссылке в CRM
        $shop_login_code = ($d + $m + $y) * 334;
        $shop_login_code = $shop_login_code."748265193";

        ?>
            <iframe src="https://www.paketoff.ru/admin/shop/orders/?shop_login_code=<?=$shop_login_code;?>&notemplate=0" frameborder="0" height="650" width="1400" scrolling="auto"></iframe>

       </td>
        </tr>
      </table>
    </td>
  </tr>
   <form name=list_f action="" method="post">
  <input name="subm" type="hidden" value="1" /></form>
</table>


</body>
</html>
<? ob_end_flush(); ?>
