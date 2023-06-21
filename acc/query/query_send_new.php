<?php
	
	// необходимое старое говнецо
	require_once("../includes/db.inc.php");
	
	// инициализируем движок
	require_once(dirname(dirname(__DIR__)) . '/engine/init.php');
	
	$controller = new QueryController();
	$controller->runAction('send');
	