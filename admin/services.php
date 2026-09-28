<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

$flash = getFlash();
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$deleteId = isset($_GET['delete']) ? (int)$_GET['delete'] : 0;

if ($deleteId) {
    $pdo->prepare('DELETE FROM services WHERE id = ?')->execute([$deleteId]);
    flash('success', __admin('services.deleted'));
    header('Location: services.php');
    exit;
}

$serviceColumns = [];
if (dbReady()) {
    $serviceColumns = $pdo->query('SHOW COLUMNS FROM services')->fetchAll(PDO::FETCH_COLUMN);
}
$hasServiceBilingualFields = in_array('title_fr', $serviceColumns, true)
    && in_array('title_en', $serviceColumns, true)
    && in_array('description_fr', $serviceColumns, true)
    && in_array('description_en', $serviceColumns, true)
    && in_array('requirements_fr', $serviceColumns, true)
    && in_array('requirements_en', $serviceColumns, true)
    && in_array('required_documents_fr', $serviceColumns, true)
    && in_array('required_documents_en', $serviceColumns, true)
    && in_array('fees_fr', $serviceColumns, true)
    && in_array('fees_en', $serviceColumns, true)
    && in_array('processing_time_fr', $serviceColumns, true)
    && in_array('processing_time_en', $serviceColumns, true)
    && in_array('office_hours_fr', $serviceColumns, true)
    && in_array('office_hours_en', $serviceColumns, true)
    && in_array('download_forms_fr', $serviceColumns, true)
    && in_array('download_forms_en', $serviceColumns, true);

$record = ['id' => null, 'title' => '', 'title_fr' => '', 'title_en' => '', 'description' => '', 'description_fr' => '', 'description_en' => '', 'requirements' => '', 'requirements_fr' => '', 'requirements_en' => '', 'required_documents' => '', 'required_documents_fr' => '', 'required_documents_en' => '', 'fees' => '', 'fees_fr' => '', 'fees_en' => '', 'processing_time' => '', 'processing_time_fr' => '', 'processing_time_en' => '', 'office_hours' => '', 'office_hours_fr' => '', 'office_hours_en' => '', 'download_forms' => '', 'download_forms_fr' => '', 'download_forms_en' => '', 'status' => 'draft'];
if ($editId) { $stmt = $pdo->prepare('SELECT * FROM services WHERE id = ?'); $stmt->execute([$editId]); $record = $stmt->fetch(PDO::FETCH_ASSOC) ?: $record; }
$services = $pdo->query('SELECT * FROM services ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
foreach ($services as &$item) {
    $displayTitle = trim((string) ($item['title_fr'] ?? ''));
    if ($displayTitle === '') { $displayTitle = trim((string) ($item['title_en'] ?? '')); }
    if ($displayTitle === '') { $displayTitle = trim((string) ($item['title'] ?? '')); }
    $item['display_title'] = $displayTitle;
}
unset($item);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $id = (int)($_POST['id'] ?? 0);
    $titleFr = trim($_POST['title_fr'] ?? '');
    $titleEn = trim($_POST['title_en'] ?? '');
    $descriptionFr = trim($_POST['description_fr'] ?? '');
    $descriptionEn = trim($_POST['description_en'] ?? '');
    $requirementsFr = trim($_POST['requirements_fr'] ?? '');
    $requirementsEn = trim($_POST['requirements_en'] ?? '');
    $requiredDocumentsFr = trim($_POST['required_documents_fr'] ?? '');
    $requiredDocumentsEn = trim($_POST['required_documents_en'] ?? '');
    $feesFr = trim($_POST['fees_fr'] ?? '');
    $feesEn = trim($_POST['fees_en'] ?? '');
    $processingTimeFr = trim($_POST['processing_time_fr'] ?? '');
    $processingTimeEn = trim($_POST['processing_time_en'] ?? '');
    $officeHoursFr = trim($_POST['office_hours_fr'] ?? '');
    $officeHoursEn = trim($_POST['office_hours_en'] ?? '');
    $downloadFormsFr = trim($_POST['download_forms_fr'] ?? '');
    $downloadFormsEn = trim($_POST['download_forms_en'] ?? '');
    $status = trim($_POST['status'] ?? 'draft');
    if (!$hasServiceBilingualFields) {
        flash('error', 'Service bilingual columns are missing. Run the service migration before saving.');
        header('Location: services.php');
        exit;
    }
    if ($id) {
        $stmt = $pdo->prepare('UPDATE services SET title_fr=?, title_en=?, description_fr=?, description_en=?, requirements_fr=?, requirements_en=?, required_documents_fr=?, required_documents_en=?, fees_fr=?, fees_en=?, processing_time_fr=?, processing_time_en=?, office_hours_fr=?, office_hours_en=?, download_forms_fr=?, download_forms_en=?, status=? WHERE id=?');
        $stmt->execute([$titleFr, $titleEn, $descriptionFr, $descriptionEn, $requirementsFr, $requirementsEn, $requiredDocumentsFr, $requiredDocumentsEn, $feesFr, $feesEn, $processingTimeFr, $processingTimeEn, $officeHoursFr, $officeHoursEn, $downloadFormsFr, $downloadFormsEn, $status, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO services (title, description, requirements, required_documents, fees, processing_time, office_hours, download_forms, title_fr, title_en, description_fr, description_en, requirements_fr, requirements_en, required_documents_fr, required_documents_en, fees_fr, fees_en, processing_time_fr, processing_time_en, office_hours_fr, office_hours_en, download_forms_fr, download_forms_en, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([null, null, null, null, null, null, null, null, $titleFr, $titleEn, $descriptionFr, $descriptionEn, $requirementsFr, $requirementsEn, $requiredDocumentsFr, $requiredDocumentsEn, $feesFr, $feesEn, $processingTimeFr, $processingTimeEn, $officeHoursFr, $officeHoursEn, $downloadFormsFr, $downloadFormsEn, $status]);
    }
    $pdo->prepare('INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)')->execute([$_SESSION['user_id'], 'Managed service', 'Service saved']);
    flash('success', __admin('services.saved'));
    header('Location: services.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(adminCurrentLanguage()) ?>">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= htmlspecialchars(__admin('services.title')) ?> - Embassy CMS</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="../assets/css/admin.css"></head>
<body>
  <div class="d-flex min-vh-100">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light">
      <?php include __DIR__ . '/partials/header.php'; ?>
      <main class="p-4">
        <h3 class="fw-bold mb-3"><?= htmlspecialchars(__admin('services.title')) ?></h3>
        <?php if ($flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <form method="post" class="card p-4 shadow-sm border-0 mb-4">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <input type="hidden" name="id" value="<?= htmlspecialchars($record['id'] ?? '') ?>">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('common.status')) ?></label><select class="form-select" name="status"><option value="draft" <?= (($record['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.draft')) ?></option><option value="published" <?= (($record['status'] ?? 'draft') === 'published') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.published')) ?></option><option value="archived" <?= (($record['status'] ?? 'draft') === 'archived') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.archived')) ?></option></select></div>

            <div class="col-12 mt-3"><h5 class="fw-bold mb-0"><?= htmlspecialchars(__admin('services.french')) ?></h5></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('services.title_fr')) ?></label><input class="form-control" name="title_fr" value="<?= htmlspecialchars($record['title_fr'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('services.description_fr')) ?></label><textarea class="form-control rich-text" rows="4" name="description_fr"><?= htmlspecialchars($record['description_fr'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('services.requirements_fr')) ?></label><textarea class="form-control" rows="3" name="requirements_fr"><?= htmlspecialchars($record['requirements_fr'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('services.required_documents_fr')) ?></label><textarea class="form-control" rows="3" name="required_documents_fr"><?= htmlspecialchars($record['required_documents_fr'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('services.fees_fr')) ?></label><input class="form-control" name="fees_fr" value="<?= htmlspecialchars($record['fees_fr'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('services.processing_time_fr')) ?></label><input class="form-control" name="processing_time_fr" value="<?= htmlspecialchars($record['processing_time_fr'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('services.office_hours_fr')) ?></label><input class="form-control" name="office_hours_fr" value="<?= htmlspecialchars($record['office_hours_fr'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('services.download_forms_fr')) ?></label><input class="form-control" name="download_forms_fr" value="<?= htmlspecialchars($record['download_forms_fr'] ?? '') ?>"></div>

            <div class="col-12 mt-3"><h5 class="fw-bold mb-0"><?= htmlspecialchars(__admin('services.english')) ?></h5></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('services.title_en')) ?></label><input class="form-control" name="title_en" value="<?= htmlspecialchars($record['title_en'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('services.description_en')) ?></label><textarea class="form-control rich-text" rows="4" name="description_en"><?= htmlspecialchars($record['description_en'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('services.requirements_en')) ?></label><textarea class="form-control" rows="3" name="requirements_en"><?= htmlspecialchars($record['requirements_en'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('services.required_documents_en')) ?></label><textarea class="form-control" rows="3" name="required_documents_en"><?= htmlspecialchars($record['required_documents_en'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('services.fees_en')) ?></label><input class="form-control" name="fees_en" value="<?= htmlspecialchars($record['fees_en'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('services.processing_time_en')) ?></label><input class="form-control" name="processing_time_en" value="<?= htmlspecialchars($record['processing_time_en'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('services.office_hours_en')) ?></label><input class="form-control" name="office_hours_en" value="<?= htmlspecialchars($record['office_hours_en'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('services.download_forms_en')) ?></label><input class="form-control" name="download_forms_en" value="<?= htmlspecialchars($record['download_forms_en'] ?? '') ?>"></div>
          </div>
          <button class="btn btn-primary mt-3" type="submit"><?= htmlspecialchars(__admin('services.save')) ?></button>
        </form>
        <div class="card p-4 shadow-sm border-0">
          <h5 class="fw-bold mb-3"><?= htmlspecialchars(__admin('services.list_title')) ?></h5>
          <div class="table-responsive"><table class="table table-hover"><thead><tr><th><?= htmlspecialchars(__admin('news.title_label')) ?></th><th><?= htmlspecialchars(__admin('common.status')) ?></th><th><?= htmlspecialchars(__admin('news.actions')) ?></th></tr></thead><tbody><?php foreach ($services as $item): ?><tr><td><?= htmlspecialchars($item['display_title'] ?? ($item['title'] ?? '')) ?></td><td><?= htmlspecialchars($item['status']) ?></td><td><a class="btn btn-sm btn-outline-primary" href="services.php?edit=<?= $item['id'] ?>"><?= htmlspecialchars(__admin('common.edit')) ?></a> <a class="btn btn-sm btn-outline-danger" href="services.php?delete=<?= $item['id'] ?>" onclick="return confirm('<?= htmlspecialchars(__admin('services.delete_confirm')) ?>')"><?= htmlspecialchars(__admin('common.delete')) ?></a></td></tr><?php endforeach; ?></tbody></table></div>
        </div>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
  <script>document.querySelectorAll('.rich-text').forEach((el)=>{ClassicEditor.create(el).catch(console.error);});</script>
</body>
</html>
