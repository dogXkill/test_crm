<?php
$auth = false;
require_once "../includes/smarty.php";
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

if(isset($_POST["name"], $_POST["address"], $_POST["passport1"], $_POST["passport2"], $_POST["phone"], $_POST["auto_number"], $_POST["comment"]))
{
  $url_end = "";

  if(get_magic_quotes_gpc())
  {
    $_POST["name"] = stripslashes($_POST["name"]);
    $_POST["address"] = stripslashes($_POST["address"]);
    $_POST["passport1"] = stripslashes($_POST["passport1"]);
    $_POST["passport2"] = stripslashes($_POST["passport2"]);
    $_POST["phone"] = stripslashes($_POST["phone"]);
    $_POST["auto_number"] = stripslashes($_POST["auto_number"]);
    $_POST["comment"] = stripslashes($_POST["comment"]);
    $_POST["base_tarif"] = stripslashes($_POST["base_tarif"]);
  }
  $_POST["name"] = trim($_POST["name"]);
  $_POST["address"] = trim($_POST["address"]);
  $_POST["passport1"] = trim($_POST["passport1"]);
  $_POST["passport2"] = trim($_POST["passport2"]);
  $_POST["phone"] = trim($_POST["phone"]);
  $_POST["auto_number"] = trim($_POST["auto_number"]);
  $_POST["comment"] = trim($_POST["comment"]);
  $_POST["base_tarif"] = trim($_POST["base_tarif"]);

  if(!empty($_POST["name"]))
  {
    if(isset($_GET["id"]) and isint($_GET["id"], 1))
    {
      $url_end = "?id=" . $_GET["id"];

      $sql = "UPDATE
  couriers
SET
  name = '" . mysql_real_escape_string($_POST["name"]) . "',
  address = '" . mysql_real_escape_string($_POST["address"]) . "',
  passport1 = '" . mysql_real_escape_string($_POST["passport1"]) . "',
  passport2 = '" . mysql_real_escape_string($_POST["passport2"]) . "',
  phone = '" . mysql_real_escape_string($_POST["phone"]) . "',
  auto_number = '" . mysql_real_escape_string($_POST["auto_number"]) . "',
  comment = '" . mysql_real_escape_string($_POST["comment"]) . "',
  priority = '" . mysql_real_escape_string($_POST["priority"]) . "',
  base_tarif = '" . mysql_real_escape_string($_POST["base_tarif"]) . "'
WHERE
  `id` = '" . $_GET["id"] . "'";
      if($result = mysql_query($sql))
      {
      }
    }
    else
    {
      $sql = "INSERT INTO
  couriers
SET
  name = '" . mysql_real_escape_string($_POST["name"]) . "',
  address = '" . mysql_real_escape_string($_POST["address"]) . "',
  passport1 = '" . mysql_real_escape_string($_POST["passport1"]) . "',
  passport2 = '" . mysql_real_escape_string($_POST["passport2"]) . "',
  phone = '" . mysql_real_escape_string($_POST["phone"]) . "',
  auto_number = '" . mysql_real_escape_string($_POST["auto_number"]) . "',
  priority = '" . mysql_real_escape_string($_POST["priority"]) . "',
  base_tarif = '" . mysql_real_escape_string($_POST["base_tarif"]) . "',
  comment = '" . mysql_real_escape_string($_POST["comment"]) . "' ";
      if($result = mysql_query($sql))
      {
        $id = mysql_insert_id();
        $url_end = "?id=" . $id;
      }
    }
  }

  header("Location: /acc/logistic/couriers.php" . $url_end);
}
else
{
  if(isset($_GET["del"]) and isint($_GET["del"], 1))
  {
    $sql = "DELETE couriers FROM couriers WHERE `id` = '" . $_GET["del"] . "'";
    if($result = mysql_query($sql))
    {
    }

    header("Location: /acc/logistic/couriers.php");
  }
  else
  {
    $smarty = new Smarty_project;

    if(isset($_GET["id"]) and isint($_GET["id"], -1))
    {
      $sql = "SELECT * FROM couriers WHERE `id` = '" . $_GET["id"] . "'";
      if($result = mysql_query($sql))
      {
        if($row = mysql_fetch_assoc($result))
        {
          $row["name"] = trim($row["name"]);
          $row["address"] = trim($row["address"]);
          $row["passport1"] = trim($row["passport1"]);
          $row["passport2"] = trim($row["passport2"]);
          $row["phone"] = trim($row["phone"]);
          $row["auto_number"] = trim($row["auto_number"]);
          $row["comment"] = trim($row["comment"]);
		  $row["priority"] = trim($row["priority"]);
		  $row["base_tarif"] = trim($row["base_tarif"]);
          $smarty->assign("content", $row);
        }
        mysql_free_result($result);
      }
    }
    else
    {
      $sql = "SELECT id, `name`, phone, priority, auto_number, base_tarif FROM couriers ORDER BY `priority`";
      if($result = mysql_query($sql))
      {
        while($row = mysql_fetch_assoc($result))
        {
          $row["name"] = $row["name"];
          $row["phone"] = $row["phone"];
          $row["priority"] = $row["priority"];
          $row["auto_number"] = $row["auto_number"];
          $row["base_tarif"] = $row["base_tarif"];
          $smarty->append("couriers", $row);
        }
        mysql_free_result($result);
      }
    }

    $smarty->assign("page_name", "Исполнители");
    $smarty->assign("page", "couriers");
    $smarty->assign("menu_type", "courier");

    require_once "main_template.php";
  }
}
?>