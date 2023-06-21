<?
require_once("../includes/db.inc.php");
$uid = $_GET["uid"];
$act = $_GET["act"];
$log_out = mysql_query("SELECT auto_logout FROM users WHERE uid=$uid");
$log_out_status = mysql_fetch_array($log_out);
$log_out_status = $log_out_status[0];

//if($act == "check"){
$log_out = mysql_query("SELECT auto_logout FROM users WHERE uid=$uid");
$log_out_st = mysql_fetch_array($log_out);
$log_out_status = $log_out_st[0];

if($act == "check"){
    echo $log_out_status;
}

if($act == "change"){
if($log_out_status == "1"){$act_vst = " SET auto_logout = '0' ";}
else{$act_vst = " SET auto_logout = '1' ";}
$q = mysql_query("UPDATE users ".$act_vst." WHERE uid = '$uid'");
//echo $log_out_status;
echo mysql_error();
}
?>