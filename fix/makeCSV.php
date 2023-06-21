<?php
	
	// необходимое старое говнецо
	require_once(dirname(__DIR__) . '/acc/includes/db.inc.php');
	
	// инициализируем движок
	require_once(dirname(__DIR__) . '/engine/init.php');
	
	$controller = new FixController();
	
	$controller->runAction('makeCSV');

	