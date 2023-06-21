<?php

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");

$name = '';
$result = [];
$userId = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : null;
$month = isset($_GET['month']) ? $_GET['month'] : null;
$year = isset($_GET['year']) ? $_GET['year'] : null;
if ($userId) {
    if ($action === 'take_amount') {
        // ��������� ����������� ����� �������
        $result  = mysql_query("SELECT SUM(amount) FROM user_fines WHERE user_id = {$userId} AND fine_month={$month} AND fine_year={$year}");
        if (!$result) {
            echo json_encode(['status' => 'error', 'error' => mysql_error()]);
            die();
        }
        $row = mysql_fetch_row($result);
        $result = ['status' => 'success', 'amount' => $row[0]];
    } else {
        $query = "SELECT id, reason, amount FROM user_fines WHERE user_id = {$userId} AND fine_month={$month} AND fine_year={$year}";
        $resource = mysql_query($query);
        while ($row = mysql_fetch_array($resource, MYSQL_ASSOC)) {
            $result[] = $row;
        }
    }
}

echo json_encode($result);
die();
