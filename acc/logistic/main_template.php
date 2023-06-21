<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Printfolio intranet v.2</title>
<link href="../style.css?cache=<?=rand(1,1000000);?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" media="all" href="../includes/js/jscalendar/calendar-blue.css" />
</head>

<body>
<script type="text/javascript" src="../includes/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="../includes/js/jquery-ui.js"></script>

<?require_once "../templates/top.php";?>
<table align="center" width="750" border="0">


<tr>
<td colspan="3">
<br>
<?
$name_curr_page = 'logistic';
require_once "../templates/main_menu.php";
$smarty->display("main.tpl");
?>
</td>
</tr>
</table>
</body>
</html>