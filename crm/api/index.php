<?php
require_once __DIR__.'/../../acc/includes/db.inc.php';

require_once __DIR__.'/Api.php';

try {
    echo json_encode((new Api(file_get_contents('php://input')))->exec());
} catch (Exception $e) {
}




