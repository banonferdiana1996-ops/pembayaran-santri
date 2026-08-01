<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

$db = getDbConnection();

$status = 'ok';
$dbName = (string) $db->query('SELECT DATABASE()')->fetchColumn();

header('Content-Type: application/json');
echo json_encode([
    'app' => 'Pembayaran Santri',
    'php_version' => PHP_VERSION,
    'db_status' => $status,
    'db_name' => $dbName,
    'time' => date('c'),
], JSON_PRETTY_PRINT);
