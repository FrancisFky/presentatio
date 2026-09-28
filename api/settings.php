<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->query('SELECT * FROM website_settings ORDER BY id DESC LIMIT 1');
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$settings) {
    echo json_encode(['success' => false, 'message' => 'No settings found']);
    exit;
}

echo json_encode(['success' => true, 'data' => $settings]);
