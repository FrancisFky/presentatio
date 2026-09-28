<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$lang = ($_GET['lang'] ?? 'fr') === 'en' ? 'en' : 'fr';
$columns = $pdo->query('SHOW COLUMNS FROM announcements')->fetchAll(PDO::FETCH_COLUMN);
$selectParts = ['id', 'category', 'priority', 'publish_date', 'pin_to_homepage'];
foreach (['title', 'title_fr', 'title_en', 'description', 'description_fr', 'description_en'] as $column) {
    if (in_array($column, $columns, true)) {
        $selectParts[] = $column;
    }
}

$stmt = $pdo->prepare('SELECT ' . implode(', ', $selectParts) . ' FROM announcements WHERE status = ? ORDER BY pin_to_homepage DESC, publish_date DESC LIMIT 6');
$stmt->execute(['published']);
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($announcements as &$announcement) {
    $requestedTitle = trim((string) ($announcement['title_' . $lang] ?? ''));
    if ($requestedTitle === '') {
        $otherLang = $lang === 'fr' ? 'en' : 'fr';
        $requestedTitle = trim((string) ($announcement['title_' . $otherLang] ?? ''));
    }
    if ($requestedTitle === '') {
        $requestedTitle = trim((string) ($announcement['title'] ?? ''));
    }
    $announcement['title'] = $requestedTitle;

    $requestedDescription = trim((string) ($announcement['description_' . $lang] ?? ''));
    if ($requestedDescription === '') {
        $otherLang = $lang === 'fr' ? 'en' : 'fr';
        $requestedDescription = trim((string) ($announcement['description_' . $otherLang] ?? ''));
    }
    if ($requestedDescription === '') {
        $requestedDescription = trim((string) ($announcement['description'] ?? ''));
    }
    $announcement['description'] = $requestedDescription;
}
unset($announcement);

echo json_encode(['success' => true, 'data' => $announcements]);
