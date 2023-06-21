<?php

require_once("../../includes/db.inc.php");
require_once("../../includes/auth.php");

$name = '';
$result = '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id) {
    $query = "DELETE FROM user_other WHERE id = {$id}";
    if (mysql_query($query)) {
        $result = 'success';
    } else {
        $result = mysql_error();
    }
}

echo $result;
die();
