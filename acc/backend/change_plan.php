<?
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");


if($user_id == '12' || $user_id == '11' || $user_id == '199' || $user_id == '332'){
$str = $_SERVER['QUERY_STRING'];
parse_str($str);

if($month_num !== "" and $year_num !== "" and $usid !== ""){


if($act == "get_form"){
$g = mysql_fetch_assoc(mysql_query("SELECT * FROM plan_users WHERE year='$year_num' AND month='$month_num' AND user_id='$usid'"));
$summ = $g[summ];
$prem = $g[prem];
$summ2 = $g[summ2];
$prem2 = $g[prem2];

?>
<form id="frm_<?=$usid;?><?=$month_num;?><?=$year_num;?>" name="frm_<?=$usid;?><?=$month_num;?><?=$year_num;?>">
<table class="plan_tbl">
<tr>
  <td>План:</td>
  <td><input type="text" name="summ" id="summ" value="<?=$summ;?>" class="short_inp"/></td>
</tr>
<tr>
  <td>Премия:</td>
  <td><input type="text" name="prem" id="prem" value="<?=$prem;?>" class="short_inp"/></td>
</tr>
<tr>
  <td>План2:</td>
  <td><input type="text" name="summ2" id="summ2" value="<?=$summ2;?>" class="short_inp"/></td>
</tr>
<tr>
  <td>Премия2:</td>
  <td><input type="text" name="prem2" id="prem2" value="<?=$prem2;?>" class="short_inp"/></td>
</tr>
<tr>
  <td></td>
  <td><input type="submit" value="Изменить!"  onclick="change_plan('<?=$usid;?>', '<?=$month_num;?>', '<?=$year_num;?>', 'update_plan'); return false;"/> </td>
</tr>
</table></form>

<?
}else{
   // echo $usid."test".$summ;
$check = mysql_num_rows(mysql_query("SELECT * FROM plan_users WHERE year='$year_num' AND month='$month_num' AND user_id='$usid'"));
if($check > 0)
{$upd = mysql_query("UPDATE plan_users SET summ = '$summ', prem = '$prem', summ2 = '$summ2', prem2 = '$prem2' WHERE year='$year_num' AND month='$month_num' AND user_id='$usid'");}
if($check == 0)
{$ins = mysql_query("INSERT INTO plan_users (year, month, user_id, summ, prem, summ2, prem2) VALUES ('$year_num', '$month_num', '$usid', '$summ', '$prem', '$summ2', '$prem2')");}


if($upd == true OR $ins == true ){echo "план: $summ";
//echo "UPDATE plan_users SET summ='$summ' WHERE year='$year_num' AND month='$month_num' AND user_id='$user_id'";
}
else{echo mysql_error();}}

}else{echo "Не все параметры переданы";} }
?>