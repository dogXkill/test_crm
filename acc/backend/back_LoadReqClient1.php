<?

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once("../includes/db.inc.php");
function clear_str($str){
	$str=str_replace('\\', '', $str);
	return stripslashes($str);
}
$act = $_GET["act"];
$uid = $_GET["uid"];
$short = $_GET["short"];
$inp = $_GET["inp"];
$fld_val = $_GET["fld_val"];
//пишем запрос на поиск по короткому названию
if($act == "search"){

if($inp == "inn"){$where = " inn = '$fld_val' ";}
else if($inp == "email"){$where = " email = '$fld_val' ";}
else if($inp == "cont_tel" or $inp == "firm_tel"){$fld_val = str_replace(" ", "", $fld_val); $where = "  cont_tel LIKE '%$fld_val%' OR firm_tel LIKE '%$fld_val%' ";}
else if($inp == ''){$where = "  short LIKE '$short%' ";}
  	$q = mysql_query("SELECT uid, short FROM clients WHERE $where ORDER BY short LIMIT 15");
    //echo "SELECT uid, short FROM clients WHERE $where ORDER BY short LIMIT 15";
	while ($client = mysql_fetch_assoc($q)) {
		echo '<option value="' . $client['uid'] . '">' . $client['short'] . '</option>';
	}

}

/*if($act == "search_uniq"){
	$short_name = mysql_query("SELECT uid, short FROM clients WHERE uid IN () ORDER BY short LIMIT 15");
	while ($client = mysql_fetch_assoc($short_name)) {
		echo '<option value="' . $client['uid'] . '">' . $client['short'] . '</option>';
	}

}
      */


if($act == "get_req" and is_numeric($uid)){
$req = mysql_query("SELECT * FROM clients WHERE uid='$uid' LIMIT 1");
$r = mysql_fetch_array($req);
$check_email=2;
$sql="SELECT * FROM `client_email_status` WHERE `client_id` = '{$uid}'";
	$result  = mysql_query($sql);
		if (mysql_num_rows($result) >= 1){
			$row = mysql_fetch_row($result);
			$check_email=$row[2];
		}else{
			$check_email=2;
		}
$sfer_dey=0;
$sql="SELECT * FROM `client_sfera` WHERE `id_client` = '{$uid}'";
	$result  = mysql_query($sql);
		if (mysql_num_rows($result) >= 1){
			$row = mysql_fetch_row($result);
			$sfer_dey=$row[1];
		}else{
			$sfer_dey=0;
		}
$r['bank']=clear_str($r['bank']);
$r['name']=clear_str($r['name']);
$r['short']=clear_str($r['short']);

print(
$r['uid']."[,]".
$r['short']."[,]".
$r['name']."[,]".
$r['postal_address']."[,]".
$r['deliv_address']."[,]".
$r['inn']."[,]".
$r['kpp']."[,]".
$r['okpo']."[,]".
$r['comment']."[,]".
$r['rs_acc']."[,]".
$r['bank']."[,]".
$r['bik']."[,]".
$r['firm_tel']."[,]".
$r['email']."[,]".
$r['cont_pers']."[,]".
$r['cont_tel']."[,]".
$check_email."[,]".
$sfer_dey."[,]"
);
}
?>