<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

$flash = getFlash();
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$deleteId = isset($_GET['delete']) ? (int)$_GET['delete'] : 0;

if ($deleteId) {
    $pdo->prepare('DELETE FROM news WHERE id = ?')->execute([$deleteId]);
    $pdo->prepare('INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)')->execute([$_SESSION['user_id'], 'Deleted news article', 'Deleted article ID ' . $deleteId]);
    flash('success', __admin('flash.article_deleted'));
    header('Location: news.php');
    exit;
}

$record = ['id' => null, 'title' => '', 'title_fr' => '', 'title_en' => '', 'slug' => '', 'category_id' => '', 'short_description' => '', 'short_description_fr' => '', 'short_description_en' => '', 'content' => '', 'content_fr' => '', 'content_en' => '', 'featured_image' => '', 'author' => '', 'publication_date' => date('Y-m-d'), 'status' => 'draft', 'tags' => '', 'seo_title' => '', 'seo_description' => '', 'pdf_attachment' => ''];
if ($editId) {
    $record = $pdo->prepare('SELECT * FROM news WHERE id = ?');
    $record->execute([$editId]);
    $record = $record->fetch();
    if (!$record) { $record = ['id' => null, 'title' => '', 'title_fr' => '', 'title_en' => '', 'slug' => '', 'category_id' => '', 'short_description' => '', 'short_description_fr' => '', 'short_description_en' => '', 'content' => '', 'content_fr' => '', 'content_en' => '', 'featured_image' => '', 'author' => '', 'publication_date' => date('Y-m-d'), 'status' => 'draft', 'tags' => '', 'seo_title' => '', 'seo_description' => '', 'pdf_attachment' => '']; }
}

$categories = $pdo->query('SELECT id, name FROM news_categories ORDER BY name ASC')->fetchAll();
$news = $pdo->query('SELECT n.*, c.name AS category_name FROM news n LEFT JOIN news_categories c ON c.id = n.category_id ORDER BY n.created_at DESC')->fetchAll();
foreach ($news as &$item) {
    $legacyTitle = trim((string) ($item['title_fr'] ?? ''));
    if ($legacyTitle === '') {
        $legacyTitle = trim((string) ($item['title_en'] ?? ''));
    }
    if ($legacyTitle === '') {
        $legacyTitle = trim((string) ($item['title'] ?? ''));
    }
    $item['display_title'] = $legacyTitle;
}
unset($item);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $id = (int)($_POST['id'] ?? 0);
    $titleFr = trim($_POST['title_fr'] ?? '');
    $titleEn = trim($_POST['title_en'] ?? '');
    $legacyTitle = trim((string) ($record['title'] ?? ''));
    $slugTitle = $titleEn !== '' ? $titleEn : ($titleFr !== '' ? $titleFr : $legacyTitle);
    $slug = slugify($_POST['slug'] ?: $slugTitle);
    $categoryIdRaw = trim((string)($_POST['category_id'] ?? ''));
    $shortFr = trim($_POST['short_description_fr'] ?? '');
    $shortEn = trim($_POST['short_description_en'] ?? '');
    $contentFr = trim($_POST['content_fr'] ?? '');
    $contentEn = trim($_POST['content_en'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $publicationDate = trim($_POST['publication_date'] ?? date('Y-m-d'));
    $status = trim($_POST['status'] ?? 'draft');
    $tags = trim($_POST['tags'] ?? '');
    $seoTitle = trim($_POST['seo_title'] ?? '');
    $seoDescription = trim($_POST['seo_description'] ?? '');
    $featuredImage = $record['featured_image'] ?? '';
    $pdf = $record['pdf_attachment'] ?? '';

    if ($categoryIdRaw === '' || !preg_match('/^\d+$/', $categoryIdRaw)) {
        flash('error', __admin('flash.invalid_category'));
        header('Location: news.php');
        exit;
    }

    $categoryId = (int) $categoryIdRaw;
    $categoryExists = $pdo->prepare('SELECT id FROM news_categories WHERE id = ? LIMIT 1');
    $categoryExists->execute([$categoryId]);
    if (!$categoryExists->fetch()) {
        flash('error', __admin('flash.invalid_category'));
        header('Location: news.php');
        exit;
    }

    if (!empty($_FILES['featured_image']['name'])) {
        $upload = handleUpload('featured_image', __DIR__ . '/../uploads/news', 'image');
        if ($upload['success']) { $featuredImage = str_replace(__DIR__ . '/../', '', $upload['path']); } else { flash('error', $upload['message']); header('Location: news.php'); exit; }
    }
    if (!empty($_FILES['pdf_attachment']['name'])) {
        $upload = handleUpload('pdf_attachment', __DIR__ . '/../uploads/documents', 'document');
        if ($upload['success']) { $pdf = str_replace(__DIR__ . '/../', '', $upload['path']); } else { flash('error', $upload['message']); header('Location: news.php'); exit; }
    }

    try {
        if ($id) {
            $stmt = $pdo->prepare('UPDATE news SET title_fr=?, title_en=?, slug=?, category_id=?, short_description_fr=?, short_description_en=?, content_fr=?, content_en=?, featured_image=?, author=?, publication_date=?, status=?, tags=?, seo_title=?, seo_description=?, pdf_attachment=? WHERE id=?');
            $stmt->execute([$titleFr, $titleEn, $slug, $categoryId, $shortFr, $shortEn, $contentFr, $contentEn, $featuredImage, $author, $publicationDate, $status, $tags, $seoTitle, $seoDescription, $pdf, $id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO news (title, title_fr, title_en, slug, category_id, short_description, short_description_fr, short_description_en, content, content_fr, content_en, featured_image, author, publication_date, status, tags, seo_title, seo_description, pdf_attachment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute(['', $titleFr, $titleEn, $slug, $categoryId, '', $shortFr, $shortEn, '', $contentFr, $contentEn, $featuredImage, $author, $publicationDate, $status, $tags, $seoTitle, $seoDescription, $pdf]);
        }
    } catch (PDOException $e) {
        error_log('News save failed: ' . $e->getMessage());
        flash('error', __admin('flash.save_failed'));
        header('Location: news.php');
        exit;
    }

    $pdo->prepare('INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)')->execute([$_SESSION['user_id'], 'Managed news', 'News article saved']);
    flash('success', __admin('flash.article_saved'));
    header('Location: news.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(adminCurrentLanguage()) ?>">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= htmlspecialchars(__admin('news.title')) ?> - Embassy CMS</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="../assets/css/admin.css"></head>
<body>
  <div class="d-flex min-vh-100">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light">
      <?php include __DIR__ . '/partials/header.php'; ?>
      <main class="p-4">
        <h3 class="fw-bold mb-3"><?= htmlspecialchars(__admin('news.title')) ?></h3>
        <?php if ($flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="card p-4 shadow-sm border-0 mb-4">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <input type="hidden" name="id" value="<?= htmlspecialchars($record['id'] ?? '') ?>">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('news.slug')) ?></label><input class="form-control" name="slug" value="<?= htmlspecialchars($record['slug'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('news.category')) ?></label>
              <select class="form-select" name="category_id" required <?= empty($categories) ? 'disabled' : '' ?>>
                <option value=""><?= empty($categories) ? htmlspecialchars(__admin('news.no_categories')) : htmlspecialchars(__admin('news.select_category')) ?></option>
                <?php foreach ($categories as $cat): ?><option value="<?= $cat['id'] ?>" <?= (($record['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option><?php endforeach; ?>
              </select>
              <?php if (empty($categories)): ?><div class="form-text text-warning"><?= htmlspecialchars(__admin('news.category_warning')) ?></div><?php endif; ?>
            </div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('news.author')) ?></label><input class="form-control" name="author" value="<?= htmlspecialchars($record['author'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('news.publication_date')) ?></label><input class="form-control" type="date" name="publication_date" value="<?= htmlspecialchars($record['publication_date'] ?? date('Y-m-d')) ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('news.status')) ?></label><select class="form-select" name="status"><option value="draft" <?= (($record['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.draft')) ?></option><option value="published" <?= (($record['status'] ?? 'draft') === 'published') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.published')) ?></option><option value="archived" <?= (($record['status'] ?? 'draft') === 'archived') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.archived')) ?></option></select></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('news.featured_image')) ?></label><input class="form-control" type="file" name="featured_image"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('news.pdf_attachment')) ?></label><input class="form-control" type="file" name="pdf_attachment"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('news.tags')) ?></label><input class="form-control" name="tags" value="<?= htmlspecialchars($record['tags'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('news.seo_title')) ?></label><input class="form-control" name="seo_title" value="<?= htmlspecialchars($record['seo_title'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('news.seo_description')) ?></label><textarea class="form-control" rows="2" name="seo_description"><?= htmlspecialchars($record['seo_description'] ?? '') ?></textarea></div>

            <div class="col-12 mt-3"><h5 class="fw-bold mb-0"><?= htmlspecialchars(__admin('news.french')) ?></h5></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('news.title_fr')) ?></label><input class="form-control" name="title_fr" value="<?= htmlspecialchars($record['title_fr'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('news.short_description_fr')) ?></label><textarea class="form-control" rows="3" name="short_description_fr"><?= htmlspecialchars($record['short_description_fr'] ?? '') ?></textarea></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('news.content_fr')) ?></label><textarea class="form-control rich-text" rows="8" name="content_fr"><?= htmlspecialchars($record['content_fr'] ?? '') ?></textarea></div>

            <div class="col-12 mt-3"><h5 class="fw-bold mb-0"><?= htmlspecialchars(__admin('news.english')) ?></h5></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('news.title_en')) ?></label><input class="form-control" name="title_en" value="<?= htmlspecialchars($record['title_en'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('news.short_description_en')) ?></label><textarea class="form-control" rows="3" name="short_description_en"><?= htmlspecialchars($record['short_description_en'] ?? '') ?></textarea></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('news.content_en')) ?></label><textarea class="form-control rich-text" rows="8" name="content_en"><?= htmlspecialchars($record['content_en'] ?? '') ?></textarea></div>
          </div>
          <button class="btn btn-primary mt-3" type="submit"><?= htmlspecialchars(__admin('news.save')) ?></button>
        </form>
        <div class="card p-4 shadow-sm border-0">
          <h5 class="fw-bold mb-3"><?= htmlspecialchars(__admin('news.list_title')) ?></h5>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead><tr><th><?= htmlspecialchars(__admin('news.title_label')) ?></th><th><?= htmlspecialchars(__admin('news.category')) ?></th><th><?= htmlspecialchars(__admin('news.status')) ?></th><th><?= htmlspecialchars(__admin('news.actions')) ?></th></tr></thead><tbody>
                <?php foreach ($news as $item): ?><tr><td><?= htmlspecialchars($item['display_title'] ?? ($item['title'] ?? '')) ?></td><td><?= htmlspecialchars($item['category_name']) ?></td><td><?= htmlspecialchars($item['status']) ?></td><td><a class="btn btn-sm btn-outline-primary" href="news.php?edit=<?= $item['id'] ?>"><?= htmlspecialchars(__admin('news.action.edit')) ?></a> <a class="btn btn-sm btn-outline-danger" href="news.php?delete=<?= $item['id'] ?>" onclick="return confirm('<?= htmlspecialchars(__admin('flash.delete_confirm')) ?>')"><?= htmlspecialchars(__admin('news.action.delete')) ?></a></td></tr><?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
  <script>document.querySelectorAll('.rich-text').forEach((el)=>{ClassicEditor.create(el).catch(console.error);});</script>
</body>
</html>
