

<html>
<script type="text/javascript" src="/acc/includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="/acc/includes/js/jquery-ui.js"></script>
<head>
<?
require_once("../includes/db.inc.php");
$ago = date("Y-m-d h:i:s",strtotime("-1 year"));
$apps_q = "SELECT a.uid, a.num_ord, a.dat_ord, a.art_num, SUM(j.num_of_work), j.job FROM applications AS a, job AS j, job_names AS n WHERE a.type = '2' AND a.dat_ord > '$ago' AND a.num_ord=j.num_ord AND j.job = n.id  GROUP BY a.num_ord, j.job ORDER BY a.num_ord";

$apps = mysql_query($apps_q);
            while ( $r = mysql_fetch_array($apps) ) {
            $apps_arr = $apps_arr."&uid=$r[0]&$r[1]&$r[2]&$r[3]&$r[4]&$r[5]";
            }


$sales_q = "SELECT art_id, monthly_profit FROM plan_arts";

$sales  = mysql_query($sales_q);
            while ( $r = mysql_fetch_array($sales) ) {
              if($r[1]>'0'){$sales_arr = $sales_arr."&art_id=$r[0]&$r[1]";}
            }

$arr = $apps_arr."APPS_ARR_END".$sales_arr;

echo mysql_error();

?>
</head>

<body>
<form action="https://www.paketoff.ru/modules/synch/apps.php" method="post" name="synch_form" id="synch_form"><input type="text" value="<?=$arr;?>" name="arr" id="arr"/></form>
<script>
function synch_site(){
  app_arr = $("arr").val();
  if(arr !== ""){
$("#synch_form").submit();
return false;

}
}

synch_site()
</script>
</body>

</html>