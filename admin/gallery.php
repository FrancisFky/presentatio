<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

$flash = getFlash();
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$deleteId = isset($_GET['delete']) ? (int)$_GET['delete'] : 0;

if ($deleteId) {
    $pdo->prepare('DELETE FROM gallery WHERE id = ?')->execute([$deleteId]);
    flash('success', __admin('gallery.deleted'));
    header('Location: gallery.php');
    exit;
}

$albums = $pdo->query('SELECT id, name FROM gallery_albums ORDER BY name ASC')->fetchAll();
$images = $pdo->query('SELECT g.*, a.name AS album_name FROM gallery g LEFT JOIN gallery_albums a ON a.id = g.album_id ORDER BY g.created_at DESC')->fetchAll();
$record = ['id' => null, 'album_id' => '', 'title' => '', 'caption' => '', 'alt_text' => '', 'image_path' => '', 'featured' => 0];
if ($editId) { $stmt = $pdo->prepare('SELECT * FROM gallery WHERE id = ?'); $stmt->execute([$editId]); $record = $stmt->fetch(); if (!$record) { $record = ['id' => null, 'album_id' => '', 'title' => '', 'caption' => '', 'alt_text' => '', 'image_path' => '', 'featured' => 0]; }}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $id = (int)($_POST['id'] ?? 0);
    $albumIdRaw = trim((string)($_POST['album_id'] ?? ''));
    $title = trim($_POST['title'] ?? '');
    $caption = trim($_POST['caption'] ?? '');
    $altText = trim($_POST['alt_text'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;
    $imagePath = $record['image_path'] ?? '';

    if ($albumIdRaw === '' || !preg_match('/^\d+$/', $albumIdRaw)) {
        flash('error', 'Please select a valid album.');
        header('Location: gallery.php');
        exit;
    }

    $albumId = (int) $albumIdRaw;
    $albumExists = $pdo->prepare('SELECT id FROM gallery_albums WHERE id = ? LIMIT 1');
    $albumExists->execute([$albumId]);
    if (!$albumExists->fetch()) {
        flash('error', 'Please select a valid album.');
        header('Location: gallery.php');
        exit;
    }

    if (!empty($_FILES['image']['name'])) { $upload = handleUpload('image', __DIR__ . '/../uploads/gallery', 'image'); if ($upload['success']) { $imagePath = str_replace(__DIR__ . '/../', '', $upload['path']); } else { flash('error', $upload['message']); header('Location: gallery.php'); exit; }}

    try {
        if ($id) { $stmt = $pdo->prepare('UPDATE gallery SET album_id=?, title=?, caption=?, alt_text=?, image_path=?, featured=? WHERE id=?'); $stmt->execute([$albumId, $title, $caption, $altText, $imagePath, $featured, $id]); } else { $stmt = $pdo->prepare('INSERT INTO gallery (album_id, title, caption, alt_text, image_path, featured) VALUES (?, ?, ?, ?, ?, ?)'); $stmt->execute([$albumId, $title, $caption, $altText, $imagePath, $featured]); }
    } catch (PDOException $e) {
        error_log('Gallery save failed: ' . $e->getMessage());
        flash('error', 'Unable to save the gallery image. Please select a valid album.');
        header('Location: gallery.php');
        exit;
    }

    $pdo->prepare('INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)')->execute([$_SESSION['user_id'], 'Managed gallery', 'Gallery item saved']);
    flash('success', __admin('gallery.saved'));
    header('Location: gallery.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(adminCurrentLanguage()) ?>">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= htmlspecialchars(__admin('gallery.title')) ?> - Embassy CMS</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="../assets/css/admin.css"></head>
<body>
  <div class="d-flex min-vh-100">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light">
      <?php include __DIR__ . '/partials/header.php'; ?>
      <main class="p-4">
        <h3 class="fw-bold mb-3"><?= htmlspecialchars(__admin('gallery.title')) ?></h3>
        <?php if ($flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <?php if (empty($albums)): ?><div class="alert alert-warning"><?= htmlspecialchars(__admin('gallery.no_albums')) ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="card p-4 shadow-sm border-0 mb-4">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <input type="hidden" name="id" value="<?= htmlspecialchars($record['id'] ?? '') ?>">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('gallery.album')) ?></label>
              <select class="form-select" name="album_id" required <?= empty($albums) ? 'disabled' : '' ?>>
                <option value=""><?= empty($albums) ? htmlspecialchars(__admin('gallery.no_albums_select')) : htmlspecialchars(__admin('gallery.select_album')) ?></option>
                <?php foreach ($albums as $album): ?><option value="<?= $album['id'] ?>" <?= (($record['album_id'] ?? '') == $album['id']) ? 'selected' : '' ?>><?= htmlspecialchars($album['name']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('news.title_label')) ?></label><input class="form-control" name="title" value="<?= htmlspecialchars($record['title'] ?? '') ?>" required></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('gallery.caption')) ?></label><input class="form-control" name="caption" value="<?= htmlspecialchars($record['caption'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('gallery.alt_text')) ?></label><input class="form-control" name="alt_text" value="<?= htmlspecialchars($record['alt_text'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('gallery.image')) ?></label><input class="form-control" type="file" name="image"></div>
            <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="featured" <?= !empty($record['featured']) ? 'checked' : '' ?>><label class="form-check-label"><?= htmlspecialchars(__admin('gallery.featured_image')) ?></label></div></div>
          </div>
          <button class="btn btn-primary mt-3" type="submit"><?= htmlspecialchars(__admin('gallery.upload_image')) ?></button>
        </form>
        <div class="card p-4 shadow-sm border-0">
          <h5 class="fw-bold mb-3"><?= htmlspecialchars(__admin('gallery.list_title')) ?></h5>
          <div class="row g-3">
            <?php foreach ($images as $image): ?><div class="col-md-4"><div class="border rounded p-2"><img src="../<?= htmlspecialchars($image['image_path']) ?>" alt="" class="img-fluid rounded mb-2" style="height:150px;object-fit:cover;width:100%;"><div class="small fw-semibold"><?= htmlspecialchars($image['title']) ?></div><div class="small text-muted"><?= htmlspecialchars($image['album_name']) ?></div><a class="btn btn-sm btn-outline-primary mt-2" href="gallery.php?edit=<?= $image['id'] ?>"><?= htmlspecialchars(__admin('news.action.edit')) ?></a> <a class="btn btn-sm btn-outline-danger mt-2" href="gallery.php?delete=<?= $image['id'] ?>" onclick="return confirm('<?= htmlspecialchars(__admin('gallery.delete_confirm')) ?>')"><?= htmlspecialchars(__admin('news.action.delete')) ?></a></div></div><?php endforeach; ?>
          </div>
        </div>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
