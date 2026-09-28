<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

$flash = getFlash();
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$deleteId = isset($_GET['delete']) ? (int)$_GET['delete'] : 0;

if ($deleteId) {
    $pdo->prepare('DELETE FROM events WHERE id = ?')->execute([$deleteId]);
    flash('success', __admin('events.deleted'));
    header('Location: events.php');
    exit;
}

$eventColumns = [];
if (dbReady()) {
    $eventColumns = $pdo->query('SHOW COLUMNS FROM events')->fetchAll(PDO::FETCH_COLUMN);
}
$hasEventBilingualFields = in_array('title_fr', $eventColumns, true)
    && in_array('title_en', $eventColumns, true)
    && in_array('description_fr', $eventColumns, true)
    && in_array('description_en', $eventColumns, true)
    && in_array('venue_fr', $eventColumns, true)
    && in_array('venue_en', $eventColumns, true);

$record = ['id' => null, 'title' => '', 'title_fr' => '', 'title_en' => '', 'description' => '', 'description_fr' => '', 'description_en' => '', 'venue' => '', 'venue_fr' => '', 'venue_en' => '', 'event_date' => date('Y-m-d'), 'event_time' => '09:00', 'organizer' => '', 'speaker' => '', 'cover_image' => '', 'registration_link' => '', 'status' => 'draft'];
if ($editId) { $stmt = $pdo->prepare('SELECT * FROM events WHERE id = ?'); $stmt->execute([$editId]); $record = $stmt->fetch(PDO::FETCH_ASSOC) ?: $record; }
$events = $pdo->query('SELECT * FROM events ORDER BY event_date DESC')->fetchAll(PDO::FETCH_ASSOC);
foreach ($events as &$item) {
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
    $venueFr = trim($_POST['venue_fr'] ?? '');
    $venueEn = trim($_POST['venue_en'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? date('Y-m-d'));
    $eventTime = trim($_POST['event_time'] ?? '09:00');
    $organizer = trim($_POST['organizer'] ?? '');
    $speaker = trim($_POST['speaker'] ?? '');
    $registrationLink = trim($_POST['registration_link'] ?? '');
    $status = trim($_POST['status'] ?? 'draft');
    $coverImage = $record['cover_image'] ?? '';
    if (!empty($_FILES['cover_image']['name'])) { $upload = handleUpload('cover_image', __DIR__ . '/../uploads/events', 'image'); if ($upload['success']) { $coverImage = str_replace(__DIR__ . '/../', '', $upload['path']); } else { flash('error', $upload['message']); header('Location: events.php'); exit; }}
    if (!$hasEventBilingualFields) {
        flash('error', 'Event bilingual columns are missing. Run the event migration before saving.');
        header('Location: events.php');
        exit;
    }
    if ($id) {
        $stmt = $pdo->prepare('UPDATE events SET title_fr=?, title_en=?, description_fr=?, description_en=?, venue_fr=?, venue_en=?, event_date=?, event_time=?, organizer=?, speaker=?, cover_image=?, registration_link=?, status=? WHERE id=?');
        $stmt->execute([$titleFr, $titleEn, $descriptionFr, $descriptionEn, $venueFr, $venueEn, $eventDate, $eventTime, $organizer, $speaker, $coverImage, $registrationLink, $status, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO events (title, description, venue, title_fr, title_en, description_fr, description_en, venue_fr, venue_en, event_date, event_time, organizer, speaker, cover_image, registration_link, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([null, null, null, $titleFr, $titleEn, $descriptionFr, $descriptionEn, $venueFr, $venueEn, $eventDate, $eventTime, $organizer, $speaker, $coverImage, $registrationLink, $status]);
    }
    $pdo->prepare('INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)')->execute([$_SESSION['user_id'], 'Managed event', 'Event saved']);
    flash('success', __admin('events.saved'));
    header('Location: events.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(adminCurrentLanguage()) ?>">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= htmlspecialchars(__admin('events.title')) ?> - Embassy CMS</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="../assets/css/admin.css"></head>
<body>
  <div class="d-flex min-vh-100">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light">
      <?php include __DIR__ . '/partials/header.php'; ?>
      <main class="p-4">
        <h3 class="fw-bold mb-3"><?= htmlspecialchars(__admin('events.title')) ?></h3>
        <?php if ($flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="card p-4 shadow-sm border-0 mb-4">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <input type="hidden" name="id" value="<?= htmlspecialchars($record['id'] ?? '') ?>">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('events.date')) ?></label><input class="form-control" type="date" name="event_date" value="<?= htmlspecialchars($record['event_date'] ?? date('Y-m-d')) ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('events.time')) ?></label><input class="form-control" type="time" name="event_time" value="<?= htmlspecialchars($record['event_time'] ?? '09:00') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('events.organizer')) ?></label><input class="form-control" name="organizer" value="<?= htmlspecialchars($record['organizer'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('events.speaker')) ?></label><input class="form-control" name="speaker" value="<?= htmlspecialchars($record['speaker'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('events.cover_image')) ?></label><input class="form-control" type="file" name="cover_image"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('events.registration_link')) ?></label><input class="form-control" name="registration_link" value="<?= htmlspecialchars($record['registration_link'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('common.status')) ?></label><select class="form-select" name="status"><option value="draft" <?= (($record['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.draft')) ?></option><option value="published" <?= (($record['status'] ?? 'draft') === 'published') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.published')) ?></option><option value="archived" <?= (($record['status'] ?? 'draft') === 'archived') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.archived')) ?></option></select></div>

            <div class="col-12 mt-3"><h5 class="fw-bold mb-0"><?= htmlspecialchars(__admin('events.french')) ?></h5></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('events.title_fr')) ?></label><input class="form-control" name="title_fr" value="<?= htmlspecialchars($record['title_fr'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('events.venue_fr')) ?></label><input class="form-control" name="venue_fr" value="<?= htmlspecialchars($record['venue_fr'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('events.description_fr')) ?></label><textarea class="form-control rich-text" rows="5" name="description_fr"><?= htmlspecialchars($record['description_fr'] ?? '') ?></textarea></div>

            <div class="col-12 mt-3"><h5 class="fw-bold mb-0"><?= htmlspecialchars(__admin('events.english')) ?></h5></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('events.title_en')) ?></label><input class="form-control" name="title_en" value="<?= htmlspecialchars($record['title_en'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('events.venue_en')) ?></label><input class="form-control" name="venue_en" value="<?= htmlspecialchars($record['venue_en'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('events.description_en')) ?></label><textarea class="form-control rich-text" rows="5" name="description_en"><?= htmlspecialchars($record['description_en'] ?? '') ?></textarea></div>
          </div>
          <button class="btn btn-primary mt-3" type="submit"><?= htmlspecialchars(__admin('events.save')) ?></button>
        </form>
        <div class="card p-4 shadow-sm border-0">
          <h5 class="fw-bold mb-3"><?= htmlspecialchars(__admin('events.list_title')) ?></h5>
          <div class="table-responsive"><table class="table table-hover"><thead><tr><th><?= htmlspecialchars(__admin('news.title_label')) ?></th><th><?= htmlspecialchars(__admin('events.date')) ?></th><th><?= htmlspecialchars(__admin('common.status')) ?></th><th><?= htmlspecialchars(__admin('news.actions')) ?></th></tr></thead><tbody><?php foreach ($events as $item): ?><tr><td><?= htmlspecialchars($item['display_title'] ?? ($item['title'] ?? '')) ?></td><td><?= htmlspecialchars($item['event_date']) ?></td><td><?= htmlspecialchars($item['status']) ?></td><td><a class="btn btn-sm btn-outline-primary" href="events.php?edit=<?= $item['id'] ?>"><?= htmlspecialchars(__admin('common.edit')) ?></a> <a class="btn btn-sm btn-outline-danger" href="events.php?delete=<?= $item['id'] ?>" onclick="return confirm('<?= htmlspecialchars(__admin('events.delete_confirm')) ?>')"><?= htmlspecialchars(__admin('common.delete')) ?></a></td></tr><?php endforeach; ?></tbody></table></div>
        </div>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
  <script>document.querySelectorAll('.rich-text').forEach((el)=>{ClassicEditor.create(el).catch(console.error);});</script>
</body>
</html>
