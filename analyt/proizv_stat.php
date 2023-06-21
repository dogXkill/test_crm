<!DOCTYPE HTML>
<?
$auth = false;
require_once("../acc/includes/db.inc.php");
require_once("../acc/includes/auth.php");

$months = "01,02,03,04,05,06,07,08,09,10,11,12";

$months = explode(",", $months);

?>
<html>

<head>
  <title>Untitled</title>
</head>

<body>
<h1>Количество изделий:</h1>

<table>
<tr>
  <td>ФИО</td>
  <?foreach ($months as $month) {
    echo "<td>$month</td>";
  }?>
</tr>

</table>

<?
$q = "SELECT u.job_id, u.name, u.surname, SUM(j.num_of_work) FROM users AS u, job AS j WHERE u.doljnost = '15' AND u.archive <> '1' AND u.nadomn <> '1' AND u.job_id = j.num_sotr AND j.cur_time LIKE '2018-09%' GROUP BY u.job_id";
$izd = mysql_query($q);

while($iz = mysql_fetch_array($izd)){?>

<?}?>
</body>

</html>