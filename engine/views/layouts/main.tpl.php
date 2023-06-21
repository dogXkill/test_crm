<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta charset='utf-8'>

	<link href="/acc/style.css?cache=<?=rand(1,1000000);?>" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" media="all" href="../../../acc/includes/fonts/css/all.min.css" title="Aqua" />

	<?=$app_header;?>

</head>
<body>

<?
	//Говнокодим, чтобы поддерживать старое говнецо
	call_user_func_array(function($userObj, $name_curr_page) {
		ob_start();
		// Инициализируем нужные переменные
		$auth = $userObj->isLogged();
		$user_type = $userObj->getType();
		$user = $userObj->getLogin();
		$user_id = (string) $userObj->getId();
		$tpacc = $userObj->getAccountType();

		// Подключаем файл шапки
		require_once(dirname(dirname(dirname(__DIR__))) . "/acc/templates/top.php");
		require_once(dirname(dirname(dirname(__DIR__))) . "/acc/templates/main_menu.php");

		echo iconv('Windows-1251', 'UTF-8//IGNORE', ob_get_clean());
	}, array($user, $old_name_curr_page));

?>

	<?=$app_content; ?>
	<?=$app_footer; ?>

</body>
</html>
