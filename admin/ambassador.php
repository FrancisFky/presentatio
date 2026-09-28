<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

$flash = getFlash();
$record = [
    'id' => null,
    'name' => '',
    'name_fr' => '',
    'name_en' => '',
    'position' => '',
    'position_fr' => '',
    'position_en' => '',
    'biography' => '',
    'biography_fr' => '',
    'biography_en' => '',
    'welcome_message' => '',
    'welcome_message_fr' => '',
    'welcome_message_en' => '',
    'signature' => '',
    'signature_fr' => '',
    'signature_en' => '',
    'photo' => '',
    'published' => 1,
];
$existingRecord = $pdo->query('SELECT * FROM ambassador ORDER BY id DESC LIMIT 1')->fetch();
if ($existingRecord) {
    $record = array_merge($record, $existingRecord);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $nameFr = trim($_POST['name_fr'] ?? '');
    $nameEn = trim($_POST['name_en'] ?? '');
    $positionFr = trim($_POST['position_fr'] ?? '');
    $positionEn = trim($_POST['position_en'] ?? '');
    $bioFr = trim($_POST['biography_fr'] ?? '');
    $bioEn = trim($_POST['biography_en'] ?? '');
    $welcomeFr = trim($_POST['welcome_message_fr'] ?? '');
    $welcomeEn = trim($_POST['welcome_message_en'] ?? '');
    $signatureFr = trim($_POST['signature_fr'] ?? '');
    $signatureEn = trim($_POST['signature_en'] ?? '');

    $legacyName = $nameEn !== '' ? $nameEn : $nameFr;
    $legacyPosition = $positionEn !== '' ? $positionEn : $positionFr;
    $legacyBio = $bioEn !== '' ? $bioEn : $bioFr;
    $legacyWelcome = $welcomeEn !== '' ? $welcomeEn : $welcomeFr;
    $legacySignature = $signatureEn !== '' ? $signatureEn : $signatureFr;

    $published = isset($_POST['published']) ? 1 : 0;
    $photo = $record['photo'] ?? '';

    if (!empty($_FILES['photo']['name'])) {
        $upload = handleUpload('photo', __DIR__ . '/../uploads/ambassador', 'image');
        if ($upload['success']) {
            $photo = str_replace(__DIR__ . '/../', '', $upload['path']);
        } else {
            flash('error', $upload['message']);
            header('Location: ambassador.php');
            exit;
        }
    }

    if ($record['id']) {
        $stmt = $pdo->prepare('UPDATE ambassador SET name=?, name_fr=?, name_en=?, position=?, position_fr=?, position_en=?, biography=?, biography_fr=?, biography_en=?, welcome_message=?, welcome_message_fr=?, welcome_message_en=?, signature=?, signature_fr=?, signature_en=?, photo=?, published=? WHERE id=?');
        $stmt->execute([
            $legacyName,
            $nameFr,
            $nameEn,
            $legacyPosition,
            $positionFr,
            $positionEn,
            $legacyBio,
            $bioFr,
            $bioEn,
            $legacyWelcome,
            $welcomeFr,
            $welcomeEn,
            $legacySignature,
            $signatureFr,
            $signatureEn,
            $photo,
            $published,
            $record['id'],
        ]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO ambassador (name, name_fr, name_en, position, position_fr, position_en, biography, biography_fr, biography_en, welcome_message, welcome_message_fr, welcome_message_en, signature, signature_fr, signature_en, photo, published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $legacyName,
            $nameFr,
            $nameEn,
            $legacyPosition,
            $positionFr,
            $positionEn,
            $legacyBio,
            $bioFr,
            $bioEn,
            $legacyWelcome,
            $welcomeFr,
            $welcomeEn,
            $legacySignature,
            $signatureFr,
            $signatureEn,
            $photo,
            $published,
        ]);
    }

    $pdo->prepare('INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)')->execute([$_SESSION['user_id'], 'Updated ambassador profile', 'Ambassador profile updated']);
    flash('success', __admin('flash.ambassador_saved'));
    header('Location: ambassador.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(adminCurrentLanguage()) ?>">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars(__admin('ambassador.title')) ?> - Embassy CMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <div class="d-flex min-vh-100">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light">
      <?php include __DIR__ . '/partials/header.php'; ?>
      <main class="p-4">
        <h3 class="fw-bold mb-3"><?= htmlspecialchars(__admin('ambassador.title')) ?></h3>
        <?php if ($flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="card p-4 shadow-sm border-0">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <div class="row g-4">
            <div class="col-12"><h5 class="fw-bold mb-0"><?= htmlspecialchars(__admin('ambassador.french')) ?></h5></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('ambassador.name_fr')) ?></label><input class="form-control" name="name_fr" value="<?= htmlspecialchars($record['name_fr'] ?? $record['name'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('ambassador.position_fr')) ?></label><input class="form-control" name="position_fr" value="<?= htmlspecialchars($record['position_fr'] ?? $record['position'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('ambassador.biography_fr')) ?></label><textarea class="form-control rich-text" rows="5" name="biography_fr"><?= htmlspecialchars($record['biography_fr'] ?? $record['biography'] ?? '') ?></textarea></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('ambassador.welcome_message_fr')) ?></label><textarea class="form-control rich-text" rows="5" name="welcome_message_fr"><?= htmlspecialchars($record['welcome_message_fr'] ?? $record['welcome_message'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('ambassador.signature_fr')) ?></label><input class="form-control" name="signature_fr" value="<?= htmlspecialchars($record['signature_fr'] ?? $record['signature'] ?? '') ?>"></div>

            <div class="col-12 mt-3"><h5 class="fw-bold mb-0"><?= htmlspecialchars(__admin('ambassador.english')) ?></h5></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('ambassador.name_en')) ?></label><input class="form-control" name="name_en" value="<?= htmlspecialchars($record['name_en'] ?? $record['name'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('ambassador.position_en')) ?></label><input class="form-control" name="position_en" value="<?= htmlspecialchars($record['position_en'] ?? $record['position'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('ambassador.biography_en')) ?></label><textarea class="form-control rich-text" rows="5" name="biography_en"><?= htmlspecialchars($record['biography_en'] ?? $record['biography'] ?? '') ?></textarea></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('ambassador.welcome_message_en')) ?></label><textarea class="form-control rich-text" rows="5" name="welcome_message_en"><?= htmlspecialchars($record['welcome_message_en'] ?? $record['welcome_message'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('ambassador.signature_en')) ?></label><input class="form-control" name="signature_en" value="<?= htmlspecialchars($record['signature_en'] ?? $record['signature'] ?? '') ?>"></div>

            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('ambassador.photo')) ?></label><input class="form-control" type="file" name="photo"></div>
            <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="published" <?= !empty($record['published']) ? 'checked' : '' ?>><label class="form-check-label"><?= htmlspecialchars(__admin('ambassador.published')) ?></label></div></div>
          </div>
          <button class="btn btn-primary mt-3" type="submit"><?= htmlspecialchars(__admin('ambassador.save')) ?></button>
        </form>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
  <script>document.querySelectorAll('.rich-text').forEach((el)=>{ClassicEditor.create(el).catch(console.error);});</script>
</body>
</html>
