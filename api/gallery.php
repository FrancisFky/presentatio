<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->prepare('SELECT id, title, image_path, caption FROM gallery WHERE featured = ? ORDER BY id DESC LIMIT 12');
$stmt->execute([1]);
$gallery = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $gallery]);
