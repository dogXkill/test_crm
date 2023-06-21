<?php
header('Content-type: text/html; charset=windows-1251');
$auth = false;
require_once "../includes/smarty.php";
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");
require_once("../includes/lib.php");
?>

<?php


// если 1 -админ или бухгалтер, иначе 0 - менеджер
$tpacc = (($user_type == 'sup') || ($user_type == 'meg') || ($user_type == 'adm') || ($user_type == 'acc')) ? 1 : 0;


// ----- перейти на главную если доступ запрещен ---------
if(!$auth || (!$tpacc && $user_type != 'mng')) {
    header("Location: /");
    exit;
}

$smarty = new Smarty_project;
$smarty->caching = false;
if(isset($_GET["date"], $_GET["courier_id"]) and check_date($_GET["date"]) and isint($_GET["courier_id"]))
{
  $sql = "
SELECT
  COALESCE(CONCAT(u.surname, ' ', u.name, ' ', u.father, ' (', u.mobile, ')'), 'Удален') AS `user`,
  t.cash_payment,
  t.prdm_sum_acc,
  t.opl_voditel,
  t.address,
  t.address_real,
  t.text,
  t.contact_phone,
  t.contact_name,
  t.comment,
  t.first_point,
  (792 - t.map_x - 8) AS map_x,
  (784 - t.map_y - 7) AS map_y,
  ROUND((792 - t.map_x) / 4 - 8) AS map_x1,
  ROUND((784 - t.map_y) / 4 - 7) AS map_y1,
  m.name AS metro,
  t.date_last,
  t.id
FROM
  courier_tasks AS t
  LEFT JOIN users AS u ON t.user_id = u.uid
  LEFT JOIN metro AS m ON t.metro_id = m.id
WHERE
  t.date = '" . format_date($_GET["date"]) . "' AND
  t.courier_id = " . $_GET["courier_id"] . " AND
  t.done = 0
ORDER BY
  t.first_point DESC,
  t.date,
  `user`,
  u.uid";
  if($result = mysql_query($sql))
  {
    $num = 1;

    while($row = mysql_fetch_assoc($result))
    {
      $row["num"] = $num++;
      $row["user"] = $row["user"];
      $row["text"] = $row["text"];
      $row["contact_name"] = $row["contact_name"];
      $row["contact_phone"] = $row["contact_phone"];
	  $row["cash_payment"] = $row["cash_payment"];
      $row["prdm_sum_acc"] = $row["prdm_sum_acc"];
      $row["cash"] = $row["cash"];
      $row["opl_voditel"] = $row["opl_voditel"];
      $row["address"] = $row["address"];
      $row["address_real"] = $row["address_real"];
      $row["metro"] = $row["metro"];
      $row["comment"] = nl2br(trim($row["comment"]));
      $row["first_point"] = $row["first_point"];
		


$courier_task_id = $row["id"];

$get_order_info = mysql_query("SELECT uid AS query_id,typ_ord, prdm_sum_acc AS prdm_sum_acc, form_of_payment AS form_of_payment, prdm_dolg AS prdm_dolg FROM queries WHERE courier_task_id = '$courier_task_id'");

$ord_row = mysql_fetch_array($get_order_info);
$row["query_id"] = $ord_row["query_id"];
$row["typ_ord"]=$ord_row["typ_ord"];
 if ($ord_row["query_id"]!=null && $ord_row["query_id"]!=undefined){
	$id_temp=$ord_row["query_id"];
 }else{
	 $id_temp=0;
 }
 
$row["form_of_payment"] = $ord_row["form_of_payment"];

		//
	  //ловим товары
	  //echo "ID_TEMP".$id_temp."</br>";
	  if ($id_temp!=0){
		//$sql1 = "SELECT  `name`, num AS col, art_num AS art_num FROM obj_accounts WHERE query_id = " . $id_temp . " ORDER BY nn";
		$sql1="SELECT  `name`, num AS col, art_num AS art_num,p_l.art_id,p_l.num_sklad FROM obj_accounts 
LEFT JOIN plan_arts AS p_l ON p_l.art_id=art_num
WHERE query_id = ".$id_temp." ORDER BY nn;";
		$smarty->append("sql-test", $id_temp);
		if($res1 = mysql_query($sql1))
            {
				if (mysql_num_rows($res1) > 0){
				$k=0;
				
              while($row1 = mysql_fetch_assoc($res1))
              {
              if($row1["art_num"]!=="d" ){
                $row1["name"] = strip_tags($row1["name"]);
				//if ($row1
				$mas_towar[$id_temp][]=$row1;
				//$mas_towar[$query_id_load][$k]['name']=$row1['name'];
                //$mas_towar[$query_id_load][$k]['art_num']=$row1['art_num'];
				//$mas_towar[$query_id_load][$k]['col']=$row1['col'];
				$k++;
				}
				//запрос на num_sklad
				
              }
			  mysql_free_result($res1);
				}
              
            }
	  }else{
		  $smarty->append("sql-test", "none");
	  }
	  //доп запрос на адреса
	  $adres=$row['address'];
	  
		//include 'check_ymaps.php';
		$row['test']=$row['typ_ord'];
      $smarty->append("courier_tasks", $row);
	  if ($row['typ_ord']==1){
		  //под заказ
		  
	  }
	  
    }
    mysql_free_result($result);
	 
	  //echo $sql1."</br>";
            
			//echo "<pre>";
			//print_r($mas_towar);
			//echo "</pre>";
		//
    
	$smarty->append("queries", $mas_towar);
	//
  }

//сумма нала и оплат водителям
 $cash = "SELECT SUM(cash_payment), SUM(opl_voditel), COUNT(*) FROM courier_tasks WHERE date = '" . format_date($_GET["date"]) . "' AND  courier_id = " . $_GET["courier_id"] . " AND done = 0";
 $cash = mysql_query($cash);
 $cash = mysql_fetch_array($cash);
 $sdacha = $cash[0]-$cash[1];
 $smarty->assign("cash", $cash[0]);
 $smarty->assign("opl_voditel", $cash[1]);
 $smarty->assign("tochek", $cash[2]);
 $smarty->assign("sdacha", $sdacha);

//время формирования списка заданий
$vrem = "сформировано ".date("d.m.y H:i");
$smarty->assign("vrem", $vrem);


$smarty->assign("shipped_edit", $user_access['shipped_edit']);

  $sql = "SELECT name FROM couriers WHERE id = " . $_GET["courier_id"];
  if($result = mysql_query($sql))
  {
    if($row = mysql_fetch_assoc($result))
    {
      $smarty->assign("courier", $row["name"]);
    }
    mysql_free_result($result);
  }
$fdate = $_GET["date"];
$smarty->assign("date", $fdate);
}

$smarty->display("task_list1.tpl");




?>