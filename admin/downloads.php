<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

$flash = getFlash();
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$deleteId = isset($_GET['delete']) ? (int)$_GET['delete'] : 0;
if ($deleteId) {
    $pdo->prepare('DELETE FROM downloads WHERE id = ?')->execute([$deleteId]);
    flash('success', __admin('downloads.deleted'));
    header('Location: downloads.php');
    exit;
}

$record = ['id' => null, 'title' => '', 'category' => 'Visa Forms', 'file_path' => '', 'file_size' => '', 'status' => 'draft'];
if ($editId) { $stmt = $pdo->prepare('SELECT * FROM downloads WHERE id = ?'); $stmt->execute([$editId]); $record = $stmt->fetch(); if (!$record) { $record = ['id' => null, 'title' => '', 'category' => 'Visa Forms', 'file_path' => '', 'file_size' => '', 'status' => 'draft']; }}
$downloads = $pdo->query('SELECT * FROM downloads ORDER BY id DESC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? 'Visa Forms');
    $status = trim($_POST['status'] ?? 'draft');
    $filePath = $record['file_path'] ?? '';
    $fileSize = $record['file_size'] ?? '';
    if (!empty($_FILES['file']['name'])) { $upload = handleUpload('file', __DIR__ . '/../uploads/documents', 'document'); if ($upload['success']) { $filePath = str_replace(__DIR__ . '/../', '', $upload['path']); $fileSize = number_format($_FILES['file']['size'] / 1024, 1) . ' KB'; } else { flash('error', $upload['message']); header('Location: downloads.php'); exit; }}
    if ($id) { $stmt = $pdo->prepare('UPDATE downloads SET title=?, category=?, file_path=?, file_size=?, status=? WHERE id=?'); $stmt->execute([$title, $category, $filePath, $fileSize, $status, $id]); } else { $stmt = $pdo->prepare('INSERT INTO downloads (title, category, file_path, file_size, status) VALUES (?, ?, ?, ?, ?)'); $stmt->execute([$title, $category, $filePath, $fileSize, $status]); }
    flash('success', __admin('downloads.saved'));
    header('Location: downloads.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(adminCurrentLanguage()) ?>">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= htmlspecialchars(__admin('downloads.title')) ?> - Embassy CMS</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="../assets/css/admin.css"></head>
<body>
  <div class="d-flex min-vh-100">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light">
      <?php include __DIR__ . '/partials/header.php'; ?>
      <main class="p-4">
        <h3 class="fw-bold mb-3"><?= htmlspecialchars(__admin('downloads.title')) ?></h3>
        <?php if ($flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="card p-4 shadow-sm border-0 mb-4">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <input type="hidden" name="id" value="<?= htmlspecialchars($record['id'] ?? '') ?>">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('news.title_label')) ?></label><input class="form-control" name="title" value="<?= htmlspecialchars($record['title'] ?? '') ?>" required></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('announcements.category')) ?></label><select class="form-select" name="category"><option value="Visa Forms" <?= (($record['category'] ?? '') === 'Visa Forms') ? 'selected' : '' ?>>Visa Forms</option><option value="Passport Forms" <?= (($record['category'] ?? '') === 'Passport Forms') ? 'selected' : '' ?>>Passport Forms</option><option value="Application Forms" <?= (($record['category'] ?? '') === 'Application Forms') ? 'selected' : '' ?>>Application Forms</option><option value="Official Publications" <?= (($record['category'] ?? '') === 'Official Publications') ? 'selected' : '' ?>>Official Publications</option></select></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('downloads.upload_file')) ?></label><input class="form-control" type="file" name="file"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('common.status')) ?></label><select class="form-select" name="status"><option value="draft" <?= (($record['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.draft')) ?></option><option value="published" <?= (($record['status'] ?? 'draft') === 'published') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.published')) ?></option><option value="archived" <?= (($record['status'] ?? 'draft') === 'archived') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.archived')) ?></option></select></div>
          </div>
          <button class="btn btn-primary mt-3" type="submit"><?= htmlspecialchars(__admin('downloads.upload_document')) ?></button>
        </form>
        <div class="card p-4 shadow-sm border-0"><h5 class="fw-bold mb-3"><?= htmlspecialchars(__admin('downloads.list_title')) ?></h5><div class="table-responsive"><table class="table table-hover"><thead><tr><th><?= htmlspecialchars(__admin('news.title_label')) ?></th><th><?= htmlspecialchars(__admin('announcements.category')) ?></th><th><?= htmlspecialchars(__admin('common.status')) ?></th><th><?= htmlspecialchars(__admin('news.actions')) ?></th></tr></thead><tbody><?php foreach ($downloads as $item): ?><tr><td><?= htmlspecialchars($item['title']) ?></td><td><?= htmlspecialchars($item['category']) ?></td><td><?= htmlspecialchars($item['status']) ?></td><td><a class="btn btn-sm btn-outline-primary" href="downloads.php?edit=<?= $item['id'] ?>"><?= htmlspecialchars(__admin('common.edit')) ?></a> <a class="btn btn-sm btn-outline-danger" href="downloads.php?delete=<?= $item['id'] ?>" onclick="return confirm('<?= htmlspecialchars(__admin('downloads.delete_confirm')) ?>')"><?= htmlspecialchars(__admin('common.delete')) ?></a></td></tr><?php endforeach; ?></tbody></table></div></div>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
