<?php

	// необходимое старое говнецо
	require_once("../includes/db.inc.php");
   	require_once("../includes/auth.php");
	if ($user_access['order_access'] == '0' || empty($user_access['order_access'])) {
		header('Location: /');
	}

	// инициализируем движок
	require_once('../../engine/init.php');

	$controller = new QueryController();
	$controller->runAction('index');
