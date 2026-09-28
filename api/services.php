<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$lang = ($_GET['lang'] ?? 'fr') === 'en' ? 'en' : 'fr';
$columns = $pdo->query('SHOW COLUMNS FROM services')->fetchAll(PDO::FETCH_COLUMN);
$selectParts = ['id'];
foreach (['title','title_fr','title_en','description','description_fr','description_en','requirements','requirements_fr','requirements_en','required_documents','required_documents_fr','required_documents_en','fees','fees_fr','fees_en','processing_time','processing_time_fr','processing_time_en','office_hours','office_hours_fr','office_hours_en','download_forms','download_forms_fr','download_forms_en'] as $column) {
    if (in_array($column, $columns, true)) {
        $selectParts[] = $column;
    }
}

$stmt = $pdo->prepare('SELECT ' . implode(', ', $selectParts) . ' FROM services WHERE status = ? ORDER BY id DESC');
$stmt->execute(['published']);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($services as &$service) {
    $service['title'] = localizedContentValue($service, 'title', $lang);
    $service['description'] = localizedContentValue($service, 'description', $lang);
    $service['requirements'] = localizedContentValue($service, 'requirements', $lang);
    $service['required_documents'] = localizedContentValue($service, 'required_documents', $lang);
    $service['fees'] = localizedContentValue($service, 'fees', $lang);
    $service['processing_time'] = localizedContentValue($service, 'processing_time', $lang);
    $service['office_hours'] = localizedContentValue($service, 'office_hours', $lang);
    $service['download_forms'] = localizedContentValue($service, 'download_forms', $lang);
}
unset($service);

echo json_encode(['success' => true, 'data' => $services]);
