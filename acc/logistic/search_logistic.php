<?
$auth = false;
require_once("../includes/db.inc.php");
require_once("../includes/auth.php");

$val = $_GET["val"];

$val = str_replace(" ","|",$val);
$search = mysql_query("SELECT id, text, date FROM courier_tasks WHERE text LIKE  '%$val%' OR address LIKE  '%$val%' OR contact_name  LIKE  '%$val%' OR comment  LIKE  '%$val%' OR address_real  LIKE  '%$val%'  ORDER BY date DESC LIMIT 0 , 30");

while($row = mysql_fetch_assoc($search))
{?>
<span onmouseover="this.style.background='#BDCDFF';" onmouseout="this.style.background='';" target="_blank"><a href="courier_tasks.php?id=<?echo $row['id'];?>"><b><?echo $row['text'];?></b> <?echo $row['date'];?></a> <a href="/acc/logistic/courier_tasks.php?del=<?echo $row['id'];?>&r=1" onclick="return confirm('Вы уверены, что хотите удалить заявку?');"><img width="20" height="20" src="../i/del.gif" onmouseover="Tip('Удалить')"></a></span><br>
<?}
echo mysql_error()
?>