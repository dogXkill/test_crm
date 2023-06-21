<?php
require_once __DIR__.'/Common.php';

class Customer extends Common
{
    public static $tableName = 'clients';
    public static $idField = 'uid';
}
