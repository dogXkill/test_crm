<?
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");

$inp = $_POST["inp"];
$fld_val = $_POST["fld_val"];

if($inp !== '' and $fld_val !== ''){
$q = "SELECT uid FROM `clients` WHERE $inp = '$fld_val' AND del = '0'";

$num = mysql_num_rows(mysql_query($q));
//if($inp == 'email'){}
echo mysql_error();
echo $num;
}
 ?>