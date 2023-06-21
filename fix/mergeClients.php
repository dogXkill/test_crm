<?php
	
	// ����������� ������ �������
	require_once('../acc/includes/db.inc.php');

	set_time_limit(0);
	
	// �������������� ������
	require_once('../engine/init.php');
	
	$controller = new FixController();
	
	$by = isset($_GET['by']) ? $_GET['by'] : false;
	
	if ($by == 'inn') {
		$controller->runAction('mergeClientsByInn');
	} elseif ($by == 'temp_phone') {
		$controller->runAction('mergeClientsByTempPhone');
	} elseif ($by == 'email') {
		$controller->runAction('mergeClientsByEmail');
	} else {
		echo '��� ������ ����';
	}
	
	
	