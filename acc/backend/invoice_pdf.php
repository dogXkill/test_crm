<?php

// ������ � ��
require_once("../includes/db.inc.php");
// �������� ������� � ��������� pdf
require_once("../includes/auth.php");
if ($user_access['order_access'] == '0' || empty($user_access['order_access'])) {
    header('Location: /');
}

// �������������� ������
require_once('../../engine/init.php');

$controller = new PdfController();
$controller->runAction('invoice');