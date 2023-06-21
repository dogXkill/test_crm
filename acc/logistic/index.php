<?php
$auth = false;


require_once "../includes/smarty.php";
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");

if ($user_access['logistics_access'] == '0' || empty($user_access['logistics_access'])) {
  header('Location: /');
}


$lim = $_GET["lim"];
if (!$lim){$lim="20";}

$smarty = new Smarty_project;

$sql = "
SELECT
  t.id,
  t.cash_payment,
  t.opl_voditel,
  t.courier_id,
  COALESCE(c.name, 'Удален') AS courier,
  COALESCE(CONCAT(u.surname, ' ', u.name, ' ', u.father), 'Удален') AS `user`,
  DATE_FORMAT(t.date, '%d-%m-%Y') AS date,
  t.text,
  q.uid AS query_id,
  t.address,
  t.contact_name,
  t.contact_phone,
  t.comment,
  t.first_point,
  ROUND((792 - t.map_x) / 4 - 8) AS map_x,
  ROUND((784 - t.map_y) / 4 - 7) AS map_y
FROM
  courier_tasks AS t
  LEFT JOIN couriers AS c ON t.courier_id = c.id
  LEFT JOIN users AS u ON t.user_id = u.uid
  LEFT JOIN queries AS q ON t.id = q.courier_task_id
WHERE
  t.done = 0
ORDER BY
  t.date DESC
LIMIT 0, $lim
  ";
if($result = mysql_query($sql))
{
  $num = 2;

  while($row = mysql_fetch_assoc($result))
  {
    $row["opl_voditel"] = $row["opl_voditel"];
    $row["cash_payment"] = $row["cash_payment"];
    $row["courier"] = $row["courier"];
    $row["user"] = $row["user"];
    $row["text"] = $row["text"];
    $row["contact_name"] = $row["contact_name"];
    $row["contact_phone"] = $row["contact_phone"];
    $row["address"] = $row["address"];
    $row["comment"] = nl2br($row["comment"]);
    $row["first_point"] = $row["first_point"];

//$query_id = $_GET["query_id"];

$courier_task_id = $row["id"];

$get_order_info = mysql_query("SELECT uid AS query_id, prdm_sum_acc AS prdm_sum_acc, form_of_payment AS form_of_payment, prdm_dolg AS prdm_dolg FROM queries WHERE courier_task_id = '$courier_task_id'");

$ord_row = mysql_fetch_array($get_order_info);

$row["form_of_payment"] = $ord_row["form_of_payment"];
$row["prdm_sum_acc"] = $ord_row["prdm_sum_acc"];
$row["prdm_dolg"] = $ord_row["prdm_dolg"];
$row["query_id"] = $ord_row["query_id"];

    if($num == 2)
    {
      $row["num"] = 1;
      $num = 1;
    }
    else
    {
      $row["num"] = 2;
      $num = 2;
    }
	//ловим координаты по адресу
	$address=$row['address'];
	if ($address!=undefined && $address!='' && $adress!=null){ 
	$adres_coord = mysql_query("SELECT * FROM maps_histori WHERE text = '$address'");
	
	if (mysql_num_rows($adres_coord)>=1){
		$adress_row = mysql_fetch_array($adres_coord);
		$row['coord_yandex']=explode(",",$adress_row['kord']);
		$row['coord_yandex']=$row['coord_yandex'][1].",".$row['coord_yandex'][0];
	}
	}else{
		
	}
    $smarty->append("courier_tasks", $row);
  }
  mysql_free_result($result);
}

$sql = "SELECT id, `name` FROM couriers ORDER BY priority ASC";
if($result = mysql_query($sql))
{
  while($row = mysql_fetch_assoc($result))
  {
    $row["name"] = $row["name"];

    $smarty->append("couriers", $row);
  }
  mysql_free_result($result);
}



$smarty->assign("lim", $lim);
$smarty->assign("date", date("d-m-Y"));
$smarty->assign("page_name", "Текущие задания");
$smarty->assign("page", "index");
$smarty->assign("menu_type", "task");
require_once "main_template.php";

?>
