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

$row["user_id"] = $user_id;

if(isset($_POST["contact_phone"], $_POST["user_id"], $_POST["date"]) and isint($_POST["user_id"], 1) and check_date($_POST["date"]))
  {
  $url_end = "";
/*
  if(get_magic_quotes_gpc())
  {
    $_POST["prdm_sum_acc"] = stripslashes($_POST["prdm_sum_acc"]);
    $_POST["opl_voditel"] = stripslashes($_POST["opl_voditel"]);
    $_POST["cash_payment"] = stripslashes($_POST["cash_payment"]);
    $_POST["address"] = stripslashes($_POST["address"]);
    $_POST["text"] = stripslashes($_POST["text"]);
    $_POST["contact_phone"] = stripslashes($_POST["contact_phone"]);
    $_POST["contact_name"] = stripslashes($_POST["contact_name"]);
    $_POST["address_real"] = stripslashes($_POST["address_real"]);
    $_POST["comment"] = strip_tags(stripslashes($_POST["comment"]));
    $_POST["first_point"] = stripslashes($_POST["first_point"]);
  }
  $_POST["prdm_sum_acc"] = trim($_POST["prdm_sum_acc"]);
  $_POST["opl_voditel"] = trim($_POST["opl_voditel"]);
  $_POST["cash_payment"] = trim($_POST["cash_payment"]);
  $_POST["address"] = trim($_POST["address"]);
  $_POST["text"] = trim($_POST["text"]);
  $_POST["contact_phone"] = trim($_POST["contact_phone"]);
  $_POST["contact_name"] = trim($_POST["contact_name"]);
  $_POST["address_real"] = trim($_POST["address_real"]);
  $_POST["comment"] = strip_tags(trim($_POST["comment"]));
  $_POST["first_point"] = trim($_POST["first_point"]);
*/

  if(!empty($_POST["address"]) and !empty($_POST["text"]))
  {
    $done = (int) isset($_POST["done"]);

    if(isint($_POST["metro_id"], 1))
    {
      $metro_id = $_POST["metro_id"];
    }
    else
    {
      $metro_id = "NULL";
    }

    if(isint($_POST["map_x"], 1) and isint($_POST["map_y"], 1))
    {
      $map_x = $_POST["map_x"];
      $map_y = $_POST["map_y"];
    }
    else
    {
      $map_x = "NULL";
      $map_y = "NULL";
    }


    if($_GET["id"] and $_GET["id"] > "0")
    {
       $url_end = "?id=" . $_GET["id"];
       $sql = "UPDATE
  courier_tasks
SET
  cash_payment = '" . $_POST["cash_payment"] . "',
  prdm_sum_acc = '" . $_POST["prdm_sum_acc"] . "',
  opl_voditel = '" . $_POST["opl_voditel"] . "',
  address = '" . mysql_real_escape_string($_POST["address"]) . "',
  metro_id = " . $metro_id . ",
  text = '" . mysql_real_escape_string($_POST["text"]) . "',
  contact_phone = '" . mysql_real_escape_string($_POST["contact_phone"]) . "',
  contact_name = '" . mysql_real_escape_string($_POST["contact_name"]) . "',
  address_real = '" . mysql_real_escape_string($_POST["address_real"]) . "',
  comment = '" . strip_tags(mysql_real_escape_string($_POST["comment"])) . "',
  date = '" . format_date($_POST["date"]) . "',
  user_id = " . $_POST["user_id"] . ",
  courier_id = " . $_POST["courier_id"] . ",
  done = " . $done . ",
  first_point = '" . mysql_real_escape_string($_POST["first_point"]) . "',
  map_x = " . $map_x . ",
  map_y = " . $map_y . "
WHERE
  `id` = '" . $_GET["id"] . "'";
      if($result = mysql_query($sql))
      {
  //echo "UPDATE";
      }
      echo mysql_error();
//echo "test ".$_POST["cash_payment"];
    }
    else
    {
       // echo "insert!";
      $sql = "INSERT INTO
  courier_tasks
SET
  cash_payment = '" . mysql_real_escape_string($_POST["cash_payment"]) . "',
  prdm_sum_acc = '" . mysql_real_escape_string($_POST["prdm_sum_acc"]) . "',
  opl_voditel = '" . mysql_real_escape_string($_POST["opl_voditel"]) . "',
  address = '" . mysql_real_escape_string($_POST["address"]) . "',
  metro_id = " . $metro_id . ",
  text = '" . mysql_real_escape_string($_POST["text"]) . "',
  contact_phone = '" . mysql_real_escape_string($_POST["contact_phone"]) . "',
  contact_name = '" . mysql_real_escape_string($_POST["contact_name"]) . "',
  address_real = '" . mysql_real_escape_string($_POST["address_real"]) . "',
  comment = '" . strip_tags(mysql_real_escape_string($_POST["comment"])) . "',
  date = '" . format_date($_POST["date"]) . "',
  user_id = " . $_POST["user_id"] . ",
  courier_id = " . $_POST["courier_id"] . ",
  done = " . $done . ",
  first_point = '" . $_POST["first_point"] . "',
  map_x = " . $map_x . ",
  map_y = " . $map_y;


  //echo mysql_insert_id();
      if($result = mysql_query($sql))
      {
        $id = mysql_insert_id();
        $url_end = "?id=" . $id;
         // echo "result".mysql_insert_id();
        if(isset($_POST["query_id"]) and isint($_POST["query_id"], 1))
        {
          $sql = "UPDATE queries SET courier_task_id = " . $id  . " WHERE uid = " . $_POST["query_id"];
          if($result = mysql_query($sql))
          {
          }

        }
      }
      //  echo mysql_error();
    }
  }
	
  header("Location: /acc/logistic/courier_tasks.php" . $url_end);
}
else
{
  if(isset($_GET["del"]) and isint($_GET["del"], 1))
  {
    $sql = "DELETE courier_tasks FROM courier_tasks WHERE `id` = '" . $_GET["del"] . "'";
    if($result = mysql_query($sql))
    {
      $sql = "UPDATE queries SET courier_task_id = '0' WHERE courier_task_id = " . $_GET["del"];
      if($result = mysql_query($sql))
      {
      }
    }

    if(isset($_GET["r"]) and $_GET["r"] == 1)
    {
      header("Location: /acc/logistic/index.php");
    }
    else
    {
      header("Location: /acc/logistic/courier_tasks.php");
    }
  }
  else
  {
    $smarty = new Smarty_project;

    if(isset($_GET["id"]) and isint($_GET["id"], 1))
    {
$task_id = $_GET["id"];

	$check_is_order_attached = "SELECT uid AS query_id,typ_ord, prdm_sum_acc AS prdm_sum_acc, form_of_payment AS form_of_payment, courier_task_id AS courier_task_id, prdm_opl AS prdm_opl, prdm_dolg AS prdm_dolg, deliv_id AS deliv_id FROM queries WHERE courier_task_id = '$task_id'";

$chk_att = mysql_query($check_is_order_attached);
$chk_row = mysql_fetch_assoc($chk_att);
//$form_of_payment = $ord_row["form_of_payment"];

      $sql = "SELECT * FROM courier_tasks WHERE `id` = '" . $_GET["id"] . "'";
      if($result = mysql_query($sql))
      {
        if($row = mysql_fetch_assoc($result))
        {
          $row["cash_payment"] = $row["cash_payment"];
          $row["opl_voditel"] = $row["opl_voditel"];
          $row["address"] = $row["address"];
          $row["comment"] = strip_tags($row["comment"]);
          $row["address_real"] = $row["address_real"];
          $row["contact_name"] = $row["contact_name"];
          $row["contact_phone"] = $row["contact_phone"];
          $row["first_point"] = $row["first_point"];
	      $row["date"] = unformat_date($row["date"]);
		  $row["query_id"] = $chk_row["query_id"];
		  //$query_id_load=$chk_row['query_id'];
		  $query_id_load=$chk_row['query_id'];
		  $row["prdm_sum_acc"] = $chk_row["prdm_sum_acc"];
		  $row["prdm_opl"] = $chk_row["prdm_opl"];
		  $row["prdm_dolg"] = $chk_row["prdm_dolg"];
		  $row["form_of_payment"] = $chk_row["form_of_payment"];
		  $row["deliv_id"] = $chk_row["deliv_id"];
		  $row["typ_ord"]=$chk_row["typ_ord"];

			
//echo 'TYP:'.$row["typ_ord"];
          $smarty->assign("content", $row);
        }
		//1
			$sql = "SELECT nn AS number, `name`, num AS col, art_num AS art_num FROM obj_accounts WHERE query_id = " . $query_id_load . " ORDER BY nn";
			//echo $sql;
            if($res = mysql_query($sql))
            {
              while($row = mysql_fetch_assoc($res))
              {
              if($row["art_num"]!=="d"){
                $row["name"] = strip_tags($row["name"]);
                $smarty->append("queries", $row);
				
				}
              }
              mysql_free_result($res);
            }
		//
        mysql_free_result($result);
      }
    }
    else
    {
			//создание
        //заполняем поле дата, если пользователь нажал создать задание на опред день
 if($_GET["st_date"]){
 $smarty->assign("st_date", $_GET["st_date"]);
}else{
      $smarty->assign("date", date("d-m-Y"));}


      if(isset($_GET["query_id"]) and isint($_GET["query_id"], 1))
      {

        $sql = "SELECT
  c.short AS text,
  c.cont_tel AS contact_phone,
  c.cont_pers AS contact_name,
  c.postal_address AS address,
  c.deliv_address AS deliv_address
FROM
  queries AS q
  INNER JOIN clients AS c ON q.client_id = c.uid
WHERE
  q.uid = " . $_GET["query_id"];
        if($result = mysql_query($sql))
        {


$query_id = $_GET["query_id"];

$task_id = $_GET["id"];

//echo "query_id:".$query_id." task_id:".$task_id;


$get_order_info = "SELECT uid AS query_id,typ_ord, prdm_sum_acc AS prdm_sum_acc, form_of_payment AS form_of_payment, deliv_id AS deliv_id, prdm_dolg AS prdm_dolg, prdm_opl AS prdm_opl, note AS note FROM queries WHERE courier_task_id  = '$query_id'";
$res = mysql_query($get_order_info);
$ord_row = mysql_fetch_assoc($res);


          if($row = mysql_fetch_assoc($result))
          {
			  //echo $query_id;
$row["query_id"] = $query_id;
$uid=$ord_row['uid'];
$row["form_of_payment"] = $ord_row["form_of_payment"];
$row["deliv_id"] = $ord_row["deliv_id"];
$row["prdm_sum_acc"] = $ord_row["prdm_sum_acc"];
$row["prdm_opl"] = $ord_row["prdm_opl"];
$row["prdm_dolg"] = $ord_row["prdm_dolg"];
$row["text"] = $row["text"];
$row["note"] = $ord_row["note"];
 $row["typ_ord"]=$ord_row["typ_ord"];
	 if($row["deliv_address"]){ $row["address"] = $row["deliv_address"];}
			else{$row["address"] = $row["address"];}
            $row["contact_name"] = $row["contact_name"];
            $row["contact_phone"] = $row["contact_phone"];
			$address=$row['address'];
				$adres_coord = mysql_query("SELECT * FROM maps_histori WHERE text LIKE '{$address}%' ");
				
				if (mysql_num_rows($adres_coord)>=1){
					$adress_row = mysql_fetch_array($adres_coord);
					$row['coord_yandex']=explode(",",$adress_row['kord']);
					
					$row['coord_yandex']=$row['coord_yandex'][1].",".$row['coord_yandex'][0];
				}
            $smarty->assign("content", $row);
			//print_r($row);
			
            $sql = "SELECT nn AS number, `name`, num AS col, art_num AS art_num FROM obj_accounts WHERE query_id = " . $uid . " ORDER BY nn";
			//echo $sql;
            if($res = mysql_query($sql))
            {
              while($row = mysql_fetch_assoc($res))
              {
              if($row["art_num"]!=="d"){
                $row["name"] = strip_tags($row["name"]);
                $smarty->append("queries", $row);
				//print_r($row);
				}
              }
			  
              mysql_free_result($res);
			  
            }
          }
          mysql_free_result($result);
        }
      }
    }
	


    $sql = "SELECT id, `name`, base_tarif FROM couriers ORDER BY priority ASC";
    if($result = mysql_query($sql))
    {
      while($row = mysql_fetch_assoc($result))
      {
        $row["name"] = $row["name"];
        $row["base_tarif"] = $row["base_tarif"];
        $smarty->append("couriers", $row);
      }
      mysql_free_result($result);
    }

    $sql = "SELECT uid AS id, CONCAT(surname,' ',name ) AS `name`, login FROM users WHERE (type = 'mng' OR type = 'adm' OR type='sup' OR type = 'meg' OR type = 'acc') AND archive <> '1' ORDER BY `name`, id";
    if($result = mysql_query($sql))
    {
      while($row = mysql_fetch_assoc($result))
      {
        $row["name"] = $row["name"];
//echo $row["login"]."<br>";
        $smarty->append("users", $row);
      }
      mysql_free_result($result);
    }

    $sql = "SELECT id, `name` FROM metro ORDER BY `order`, `name`";
    if($result = mysql_query($sql))
    {
      while($row = mysql_fetch_assoc($result))
      {
        $row["name"] = $row["name"];

        $smarty->append("metro", $row);
      }
      mysql_free_result($result);
    }


    $sql_where = array();
    $sql_where[] = 1;

    if(isset($_POST["date_from"], $_POST["date_to"], $_POST["user_id"], $_POST["courier_id"], $_POST["done"]) and in_array($_POST["done"], array("", "1", "0")))
    {
      $search = array();

      if(isint($_POST["user_id"], 1))
      {
        $search["user_id"] = $_POST["user_id"];
        $sql_where[] = "t.user_id = " . $_POST["user_id"];
      }

      if(isint($_POST["courier_id"], 1))
      {
        $search["courier_id"] = $_POST["courier_id"];
        $sql_where[] = "t.courier_id = " . $_POST["courier_id"];
      }

      $search["done"] = (string) $_POST["done"];
      if($_POST["done"] != "")
      {
        $sql_where[] = "t.done = " . $_POST["done"];
      }

      if(check_date($_POST["date_from"]))
      {
        $search["date_from"] = $_POST["date_from"];

        $sql_where[] = "t.date >= '" . format_date($_POST["date_from"]) . "'";
      }

      if(check_date($_POST["date_to"]))
      {
        $search["date_to"] = $_POST["date_to"];

        $sql_where[] = "t.date <= '" . format_date($_POST["date_to"]) . "'";
      }
    }
    else
    {
      $search["date_from"] = date("01-m-Y");
      $search["date_to"] = date("01-m-Y", time() + 30 * 60 * 60 * 24);
      $sql_where[] = "t.date >= '" . format_date($search["date_from"]) . "'";
      $sql_where[] = "t.date <= '" . format_date($search["date_to"]) . "'";

      $search["done"] = "0";
      $sql_where[] = "t.done = 0";
    }

    $sql = "SELECT
  t.id,
  t.done,
  COALESCE(c.name, 'Удален') AS courier,
  COALESCE(CONCAT(u.surname, ' ', u.name, ' ', u.father), 'Удален') AS `user`,
  DATE_FORMAT(t.date, '%d-%m-%Y') AS date,
  t.text
FROM
  courier_tasks AS t
  LEFT JOIN couriers AS c ON t.courier_id = c.id
  LEFT JOIN users AS u ON t.user_id = u.uid
WHERE
  " . implode(" AND ", $sql_where) . "

ORDER BY
  t.date DESC,
  user,
  u.uid,
  c.name";
    if($result = mysql_query($sql))
    {
      while($row = mysql_fetch_assoc($result))
      {
        $row["courier"] = $row["courier"];
        $row["user"] = $row["user"];
        $row["text"] = $row["text"];

        if(strlen($row["text"]) > 80)
        {
          $row["text_small"] = substr($row["text"], 0, 80) . "...";
        }
        else
        {
          $row["text_small"] = $row["text"];
        }

        $smarty->append("courier_tasks", $row);
      }
      mysql_free_result($result);
    }

    $smarty->assign("search", $search);
    $smarty->assign("page_name", "Задания");
    $smarty->assign("page", "courier_tasks");
    $smarty->assign("menu_type", "task_edit");



    require_once "main_template.php";
  }
}



?>