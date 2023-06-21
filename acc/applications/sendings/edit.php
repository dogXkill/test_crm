<?

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); // HTTP/1.1
header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");

ob_start();

define('IMG_PATCH', '/i/users/');
$auth = false;

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");
require_once("../../includes/lib.php");
require_once("../../includes/im_rez.inc.php");

if ($user_access['accounting_user'] == "0" || $user_access['is_in_division'] == "1") {
	header('Location: /');
}

$rand = microtime(true).rand();

$id = $_GET['id'];

if ($user_access['edit_shipments'] == 0) {
  header('Location: index.php');
}

?>
<html>
<head>
<meta http-equiv="Expires" content="Mon, 26 Jul 1997 05:00:00 GMT" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
<title>Редактирование отправки</title>
<link href="../../style.css?cache=<?=$rand?>" rel="stylesheet" type="text/css" />
<link href="style.css?cache=<?=$rand?>" rel="stylesheet" type="text/css" />
<link href="../../includes/new.css?cache=<?=$rand?>" rel="stylesheet" type="text/css"/>
</head>
<body>
  <script src="../../includes/js/react/react.development.js?cache=<?=$rand?>" charset="utf-8"></script>
  <script src="../../includes/js/react/react-dom.development.js?cache=<?=$rand?>" charset="utf-8"></script>
  <script src="../../includes/js/react/babel.min.js?cache=<?=$rand?>" charset="utf-8"></script>

	<script type="text/javascript" src="../../includes/js/jquery-1.11.3.min.js" charset="utf-8"></script>
	<script type="text/javascript" src="../../includes/js/jquery-ui.js" charset="utf-8"></script>
	<script type="text/javascript" src="../../includes/jquery.cookie.js" charset="utf-8"></script>
	<?require_once("../../templates/top.php");
	$name_curr_page = 'apl_list';
	require_once("../../templates/main_menu.php");?>
  <div style="display: none;" id="data"></div>
  <div id="app"></div>
  <script type="text/babel" data-id="<?=$id?>" id="editScript" src="js/edit.js?id=<?=$id?>&cache=<?=$rand?>" charset="utf-8"></script>
</body>
</html>
<? ob_end_flush() ?>
