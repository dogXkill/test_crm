<html>
<head>
<title></title>
<style type="text/css">
<!--
body {
font-family:arial;
font-size: 12;
}
-->
</style>
</head>

<body>
Последние заявки по этому артикулу:<br>

<?
include_once($_SERVER['DOCUMENT_ROOT'].'/acc/includes/db.inc.php');
$art_num = $_GET["art_id"];

$res = mysql_query("SELECT uid,title,tiraz,dat_ord FROM applications WHERE art_num=$art_num");

while($row = mysql_fetch_assoc($res))
{
?>
<a href="http://192.168.1.100/acc/applications/edit.php?id=<?=$row['uid'];?>" target="_blank"><?=substr($row['title'],0,25);?>...</a> - <?=$row['tiraz'];?>шт. -
<?=date("Y-m-d",strtotime($row['dat_ord']));?><br>
<?}
echo mysql_error();?>

</body>

</html>