<?php
	
	// необходимое старое говнецо
	require_once(dirname(dirname(__DIR__)) . '/acc/includes/db.inc.php');
	
	// инициализируем движок
	require_once(dirname(dirname(__DIR__)) . '/engine/init.php');
	
	$controller = new CustomersCallsController();
	$controller->runAction('index');
	