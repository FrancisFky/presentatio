<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->prepare('SELECT id, title, short_description, content, featured_image, publication_date FROM news WHERE status = ? ORDER BY publication_date DESC, id DESC LIMIT 6');
$stmt->execute(['published']);
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $news]);
