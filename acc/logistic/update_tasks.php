<?php
$auth = false;

require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");

// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'meg') || ($user_type == 'adm') || ($user_type == 'acc')) ? 1 : 0;

// ----- перейти на главную если доступ запрещен ---------
if(!$auth || (!$tpacc && $user_type != 'mng')) {
    header("Location: /");
    exit;
}

if(isset($_POST["courier_id"], $_POST["date"]) and is_array($_POST["courier_id"]) and is_array($_POST["date"]))
{
  foreach($_POST["courier_id"] as $k => $v)
  {
    if(isint($k, 1) and isint($v, 1) and isset($_POST["date"][$k]) and check_date($_POST["date"][$k]))
    {
//echo "id курьера".$k."<br>";
//echo "id новое курьера".$v."<br>";
//echo "новая дата".$_POST["date"][$k]."<br>";
      $sql = "UPDATE courier_tasks SET courier_id = " . $v . ", date = '" . format_date($_POST["date"][$k]) . "' WHERE id = " . $k;
      if($result = mysql_query($sql))
      {
      }
    }
  }
}

header("Location: /acc/logistic/index.php");
?>