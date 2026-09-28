<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->prepare('SELECT id, holiday_name, holiday_date, description FROM holidays WHERE status = ? ORDER BY holiday_date ASC LIMIT 12');
$stmt->execute(['published']);
$holidays = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $holidays]);
