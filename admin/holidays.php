<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

$flash = getFlash();
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$deleteId = isset($_GET['delete']) ? (int)$_GET['delete'] : 0;
if ($deleteId) {
    $pdo->prepare('DELETE FROM holidays WHERE id = ?')->execute([$deleteId]);
    flash('success', __admin('holidays.deleted'));
    header('Location: holidays.php');
    exit;
}

$record = ['id' => null, 'holiday_name' => '', 'holiday_date' => date('Y-m-d'), 'description' => '', 'status' => 'draft'];
if ($editId) { $stmt = $pdo->prepare('SELECT * FROM holidays WHERE id = ?'); $stmt->execute([$editId]); $record = $stmt->fetch(); if (!$record) { $record = ['id' => null, 'holiday_name' => '', 'holiday_date' => date('Y-m-d'), 'description' => '', 'status' => 'draft']; }}
$holidays = $pdo->query('SELECT * FROM holidays ORDER BY holiday_date DESC')->fetchAll();
$holidayStatusLabels = ['draft' => __admin('common.draft'), 'published' => __admin('common.published'), 'archived' => __admin('common.archived')];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $id = (int)($_POST['id'] ?? 0);
    $holidayName = trim($_POST['holiday_name'] ?? '');
    $holidayDate = trim($_POST['holiday_date'] ?? date('Y-m-d'));
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'draft');
    if ($id) { $stmt = $pdo->prepare('UPDATE holidays SET holiday_name=?, holiday_date=?, description=?, status=? WHERE id=?'); $stmt->execute([$holidayName, $holidayDate, $description, $status, $id]); } else { $stmt = $pdo->prepare('INSERT INTO holidays (holiday_name, holiday_date, description, status) VALUES (?, ?, ?, ?)'); $stmt->execute([$holidayName, $holidayDate, $description, $status]); }
    flash('success', __admin('holidays.saved'));
    header('Location: holidays.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(adminCurrentLanguage()) ?>">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= htmlspecialchars(__admin('holidays.title')) ?> - Embassy CMS</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="../assets/css/admin.css"></head>
<body>
  <div class="d-flex min-vh-100">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light">
      <?php include __DIR__ . '/partials/header.php'; ?>
      <main class="p-4">
        <h3 class="fw-bold mb-3"><?= htmlspecialchars(__admin('holidays.title')) ?></h3>
        <?php if ($flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <form method="post" class="card p-4 shadow-sm border-0 mb-4">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <input type="hidden" name="id" value="<?= htmlspecialchars($record['id'] ?? '') ?>">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('holidays.name')) ?></label><input class="form-control" name="holiday_name" value="<?= htmlspecialchars($record['holiday_name'] ?? '') ?>" required></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('holidays.date')) ?></label><input class="form-control" type="date" name="holiday_date" value="<?= htmlspecialchars($record['holiday_date'] ?? date('Y-m-d')) ?>"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('holidays.description')) ?></label><textarea class="form-control" rows="4" name="description"><?= htmlspecialchars($record['description'] ?? '') ?></textarea></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('common.status')) ?></label><select class="form-select" name="status"><option value="draft" <?= (($record['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.draft')) ?></option><option value="published" <?= (($record['status'] ?? 'draft') === 'published') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.published')) ?></option><option value="archived" <?= (($record['status'] ?? 'draft') === 'archived') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.archived')) ?></option></select></div>
          </div>
          <button class="btn btn-primary mt-3" type="submit"><?= htmlspecialchars(__admin('holidays.save')) ?></button>
        </form>
        <div class="card p-4 shadow-sm border-0"><h5 class="fw-bold mb-3"><?= htmlspecialchars(__admin('holidays.list_title')) ?></h5><div class="table-responsive"><table class="table table-hover"><thead><tr><th><?= htmlspecialchars(__admin('holidays.name')) ?></th><th><?= htmlspecialchars(__admin('holidays.date')) ?></th><th><?= htmlspecialchars(__admin('common.status')) ?></th><th><?= htmlspecialchars(__admin('news.actions')) ?></th></tr></thead><tbody><?php foreach ($holidays as $item): ?><tr><td><?= htmlspecialchars($item['holiday_name']) ?></td><td><?= htmlspecialchars($item['holiday_date']) ?></td><td><?= htmlspecialchars($holidayStatusLabels[$item['status']] ?? ucfirst((string) ($item['status'] ?? '')) ) ?></td><td><a class="btn btn-sm btn-outline-primary" href="holidays.php?edit=<?= $item['id'] ?>"><?= htmlspecialchars(__admin('news.action.edit')) ?></a> <a class="btn btn-sm btn-outline-danger" href="holidays.php?delete=<?= $item['id'] ?>" onclick="return confirm('<?= htmlspecialchars(__admin('holidays.delete_confirm')) ?>')"><?= htmlspecialchars(__admin('common.delete')) ?></a></td></tr><?php endforeach; ?></tbody></table></div></div>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
