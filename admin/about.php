<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

$pages = [
    'about-congo' => ['fr' => 'À propos du Congo', 'en' => 'About Congo'],
    'about-embassy' => ['fr' => 'À propos de l’Ambassade', 'en' => 'About the Embassy'],
    'invest-in-congo' => ['fr' => 'Investir au Congo', 'en' => 'Invest in Congo'],
];
$slug = $_GET['page'] ?? $_POST['slug'] ?? 'about-congo';
if (!isset($pages[$slug])) { http_response_code(404); exit('Unknown page.'); }
if (!dbReady()) { http_response_code(503); exit('Database connection is unavailable.'); }

$aboutTableReady = true;
try {
    $stmt = $pdo->prepare('SELECT * FROM about_pages WHERE slug = ? LIMIT 1');
    $stmt->execute([$slug]);
    $record = $stmt->fetch() ?: ['id' => null, 'slug' => $slug, 'title_fr' => $pages[$slug]['fr'], 'title_en' => $pages[$slug]['en'], 'content_fr' => '', 'content_en' => '', 'hero_image' => '', 'status' => 'draft'];
} catch (PDOException $e) {
    $aboutTableReady = false;
    $record = ['id' => null, 'slug' => $slug, 'title_fr' => $pages[$slug]['fr'], 'title_en' => $pages[$slug]['en'], 'content_fr' => '', 'content_en' => '', 'hero_image' => '', 'status' => 'draft'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $aboutTableReady) {
    verifyCsrf();
    $titleFr = trim($_POST['title_fr'] ?? '');
    $titleEn = trim($_POST['title_en'] ?? '');
    $contentFr = trim($_POST['content_fr'] ?? '');
    $contentEn = trim($_POST['content_en'] ?? '');
    $status = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';
    if ($titleFr === '' || $titleEn === '') {
        flash('error', __admin('flash.about_titles_required'));
        header('Location: about.php?page=' . rawurlencode($slug)); exit;
    }
    $heroImage = $record['hero_image'] ?? '';
    if (!empty($_FILES['hero_image']['name'])) {
        $upload = handleUpload('hero_image', __DIR__ . '/../uploads/about', 'image');
        if (!$upload['success']) { flash('error', $upload['message']); header('Location: about.php?page=' . rawurlencode($slug)); exit; }
        $heroImage = str_replace(__DIR__ . '/../', '', $upload['path']);
    }
    if ($record['id']) {
        $stmt = $pdo->prepare('UPDATE about_pages SET title_fr=?, title_en=?, content_fr=?, content_en=?, hero_image=?, status=? WHERE id=?');
        $stmt->execute([$titleFr, $titleEn, $contentFr, $contentEn, $heroImage, $status, $record['id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO about_pages (slug, title_fr, title_en, content_fr, content_en, hero_image, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$slug, $titleFr, $titleEn, $contentFr, $contentEn, $heroImage, $status]);
    }
    $pdo->prepare('INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)')->execute([$_SESSION['user_id'], 'Updated about page', $slug]);
    flash('success', __admin('flash.about_saved'));
    header('Location: about.php?page=' . rawurlencode($slug)); exit;
}
$flash = getFlash();
$currentLanguage = adminCurrentLanguage();
?>
<!doctype html>
<html lang="<?= htmlspecialchars($currentLanguage) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars(__admin('about.title')) ?> - Embassy CMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body><div class="d-flex min-vh-100"><?php include __DIR__ . '/partials/sidebar.php'; ?><div class="flex-grow-1 bg-light"><?php include __DIR__ . '/partials/header.php'; ?><main class="p-4"><div class="d-flex justify-content-between align-items-center mb-4"><div><h3 class="fw-bold mb-1"><?= htmlspecialchars(__admin('about.title')) ?></h3><p class="text-muted mb-0"><?= htmlspecialchars(__admin('about.description')) ?></p></div><a class="btn btn-outline-secondary" href="../<?= htmlspecialchars($slug) ?>.php" target="_blank"><?= htmlspecialchars(__admin('about.preview')) ?> <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i></a></div>
<?php if (!$aboutTableReady): ?><div class="alert alert-warning"><?= htmlspecialchars(__admin('about.missing_table')) ?></div><?php endif; ?>
<?php if ($flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
<ul class="nav nav-pills mb-4"><?php foreach ($pages as $pageSlug => $labels): ?><li class="nav-item"><a class="nav-link <?= $pageSlug === $slug ? 'active' : '' ?>" href="about.php?page=<?= htmlspecialchars($pageSlug) ?>"><?= htmlspecialchars($labels[$currentLanguage] ?? $labels['en']) ?></a></li><?php endforeach; ?></ul>
<form method="post" enctype="multipart/form-data" class="card p-4 shadow-sm border-0"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>"><input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>"><div class="row g-4"><div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('about.title_fr')) ?></label><input class="form-control" name="title_fr" value="<?= htmlspecialchars($record['title_fr']) ?>" required></div><div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('about.title_en')) ?></label><input class="form-control" name="title_en" value="<?= htmlspecialchars($record['title_en']) ?>" required></div><div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('about.hero_image')) ?></label><input class="form-control" type="file" name="hero_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><?php if ($record['hero_image']): ?><div class="mt-2"><img class="img-thumbnail" style="max-height:180px" src="../<?= htmlspecialchars($record['hero_image']) ?>" alt="Current hero image"></div><?php endif; ?></div><div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('about.content_fr')) ?></label><textarea class="form-control rich-text" rows="12" name="content_fr"><?= htmlspecialchars($record['content_fr']) ?></textarea></div><div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('about.content_en')) ?></label><textarea class="form-control rich-text" rows="12" name="content_en"><?= htmlspecialchars($record['content_en']) ?></textarea></div><div class="col-md-4"><label class="form-label"><?= htmlspecialchars(__admin('about.status')) ?></label><select class="form-select" name="status"><option value="draft" <?= $record['status'] === 'draft' ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.draft')) ?></option><option value="published" <?= $record['status'] === 'published' ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.published')) ?></option></select></div></div><button class="btn btn-primary mt-4" type="submit"><i class="fa-solid fa-save me-2"></i><?= htmlspecialchars(__admin('about.save')) ?></button></form></main></div></div><script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script><script>document.querySelectorAll('.rich-text').forEach((el) => ClassicEditor.create(el).catch(console.error));</script></body></html>
