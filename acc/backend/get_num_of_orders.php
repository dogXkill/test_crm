<?

if ($_GET['code'] == "fdsfds8fu883832ije99089fs"){
require_once("../includes/db.inc.php");
$uid = $_GET['uid'];

$num_of_orders = mysql_query("SELECT print, izd_w, izd_v FROM shop_goods WHERE uid='$id'");
$num_of_orders = mysql_fetch_array($num_of_orders);



if ($set_status == "true"){echo $num_of_orders[0];}else{echo mysql_error();}


}else{

echo "неправильный код";
}

 ?>