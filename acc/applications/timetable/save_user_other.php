﻿<?php

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");

$name = '';
$result = [];
$userId = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
$reason = isset($_POST['reason']) ? mysql_real_escape_string($_POST['reason']) : '';
$amount = isset($_POST['amount']) ? intval($_POST['amount']) : 0;
$month = isset($_POST['month']) ? intval($_POST['month']) : 0;
$year = isset($_POST['year']) ? intval($_POST['year']) : 0;
$userIdAdded = intval($user_access['uid']);

// �� ������� ������� ������
if (empty($reason)) {
    echo json_encode(['error' => 'empty reason']);
    die();
}

// �� ������� ������� ������
if (!$amount) {
    echo json_encode(['error' => 'empty amount']);
    die();
}

// ��� ID ����������
if (!$userId) {
    echo json_encode(['error' => 'empty user_id']);
    die();
}

$query = "INSERT INTO user_other (reason, amount, fine_month,  fine_year, user_id, user_id_added) ";
$query .= "VALUES('$reason', $amount, $month, $year, $userId, $userIdAdded)";
$result = mysql_query($query);

// ������
if (!$result) {
    echo json_encode(['status' => 'error', 'error' => mysql_error()]);
    die();
}

// �������� ��������, ��������� ����������� ����� �������
$result  = mysql_query("SELECT SUM(amount) FROM user_other WHERE user_id = {$userId}");
if (!$result) {
    echo json_encode(['status' => 'error', 'error' => mysql_error()]);
    die();
}
$row = mysql_fetch_row($result);
echo json_encode(['status' => 'success', 'amount' => $row[0]]);
die();
