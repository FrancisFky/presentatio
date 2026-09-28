<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

$flash = getFlash();
$settings = $pdo->query('SELECT * FROM website_settings ORDER BY id DESC LIMIT 1')->fetch();
if (!$settings) { $settings = ['id' => null, 'embassy_name' => '', 'logo_path' => '', 'favicon_path' => '', 'address' => '', 'phone_numbers' => '', 'emails' => '', 'google_maps' => '', 'working_hours' => '', 'facebook' => '', 'instagram' => '', 'twitter' => '', 'linkedin' => '', 'footer_info' => '', 'copyright' => '', 'seo_title' => '', 'seo_description' => '', 'google_analytics_code' => '']; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $embassyName = trim($_POST['embassy_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phoneNumbers = trim($_POST['phone_numbers'] ?? '');
    $emails = trim($_POST['emails'] ?? '');
    $googleMaps = trim($_POST['google_maps'] ?? '');
    $workingHours = trim($_POST['working_hours'] ?? '');
    $facebook = trim($_POST['facebook'] ?? '');
    $instagram = trim($_POST['instagram'] ?? '');
    $twitter = trim($_POST['twitter'] ?? '');
    $linkedin = trim($_POST['linkedin'] ?? '');
    $footerInfo = trim($_POST['footer_info'] ?? '');
    $copyright = trim($_POST['copyright'] ?? '');
    $seoTitle = trim($_POST['seo_title'] ?? '');
    $seoDescription = trim($_POST['seo_description'] ?? '');
    $analytics = trim($_POST['google_analytics_code'] ?? '');

    $logoPath = $settings['logo_path'] ?? '';
    $faviconPath = $settings['favicon_path'] ?? '';
    if (!empty($_FILES['logo']['name'])) { $upload = handleUpload('logo', __DIR__ . '/../uploads', 'image'); if ($upload['success']) { $logoPath = str_replace(__DIR__ . '/../', '', $upload['path']); } else { flash('error', $upload['message']); header('Location: settings.php'); exit; }}
    if (!empty($_FILES['favicon']['name'])) { $upload = handleUpload('favicon', __DIR__ . '/../uploads', 'image'); if ($upload['success']) { $faviconPath = str_replace(__DIR__ . '/../', '', $upload['path']); } else { flash('error', $upload['message']); header('Location: settings.php'); exit; }}
    if ($settings['id']) { $stmt = $pdo->prepare('UPDATE website_settings SET embassy_name=?, logo_path=?, favicon_path=?, address=?, phone_numbers=?, emails=?, google_maps=?, working_hours=?, facebook=?, instagram=?, twitter=?, linkedin=?, footer_info=?, copyright=?, seo_title=?, seo_description=?, google_analytics_code=? WHERE id=?'); $stmt->execute([$embassyName, $logoPath, $faviconPath, $address, $phoneNumbers, $emails, $googleMaps, $workingHours, $facebook, $instagram, $twitter, $linkedin, $footerInfo, $copyright, $seoTitle, $seoDescription, $analytics, $settings['id']]); } else { $stmt = $pdo->prepare('INSERT INTO website_settings (embassy_name, logo_path, favicon_path, address, phone_numbers, emails, google_maps, working_hours, facebook, instagram, twitter, linkedin, footer_info, copyright, seo_title, seo_description, google_analytics_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'); $stmt->execute([$embassyName, $logoPath, $faviconPath, $address, $phoneNumbers, $emails, $googleMaps, $workingHours, $facebook, $instagram, $twitter, $linkedin, $footerInfo, $copyright, $seoTitle, $seoDescription, $analytics]); }
    flash('success', __admin('flash.settings_saved'));
    header('Location: settings.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(adminCurrentLanguage()) ?>">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= htmlspecialchars(__admin('settings.title')) ?> - Embassy CMS</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="../assets/css/admin.css"></head>
<body>
  <div class="d-flex min-vh-100">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light">
      <?php include __DIR__ . '/partials/header.php'; ?>
      <main class="p-4">
        <h3 class="fw-bold mb-3"><?= htmlspecialchars(__admin('settings.title')) ?></h3>
        <?php if ($flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="card p-4 shadow-sm border-0">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.embassy_name')) ?></label><input class="form-control" name="embassy_name" value="<?= htmlspecialchars($settings['embassy_name'] ?? '') ?>" required></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.logo')) ?></label><input class="form-control" type="file" name="logo"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.favicon')) ?></label><input class="form-control" type="file" name="favicon"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.address')) ?></label><input class="form-control" name="address" value="<?= htmlspecialchars($settings['address'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.phone_numbers')) ?></label><input class="form-control" name="phone_numbers" value="<?= htmlspecialchars($settings['phone_numbers'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.emails')) ?></label><input class="form-control" name="emails" value="<?= htmlspecialchars($settings['emails'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.google_maps')) ?></label><input class="form-control" name="google_maps" value="<?= htmlspecialchars($settings['google_maps'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.working_hours')) ?></label><input class="form-control" name="working_hours" value="<?= htmlspecialchars($settings['working_hours'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.facebook')) ?></label><input class="form-control" name="facebook" value="<?= htmlspecialchars($settings['facebook'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.instagram')) ?></label><input class="form-control" name="instagram" value="<?= htmlspecialchars($settings['instagram'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.twitter')) ?></label><input class="form-control" name="twitter" value="<?= htmlspecialchars($settings['twitter'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.linkedin')) ?></label><input class="form-control" name="linkedin" value="<?= htmlspecialchars($settings['linkedin'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('settings.footer_info')) ?></label><textarea class="form-control" rows="3" name="footer_info"><?= htmlspecialchars($settings['footer_info'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.copyright')) ?></label><input class="form-control" name="copyright" value="<?= htmlspecialchars($settings['copyright'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.seo_title')) ?></label><input class="form-control" name="seo_title" value="<?= htmlspecialchars($settings['seo_title'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.seo_description')) ?></label><textarea class="form-control" rows="3" name="seo_description"><?= htmlspecialchars($settings['seo_description'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('settings.analytics')) ?></label><textarea class="form-control" rows="3" name="google_analytics_code"><?= htmlspecialchars($settings['google_analytics_code'] ?? '') ?></textarea></div>
          </div>
          <button class="btn btn-primary mt-3" type="submit"><?= htmlspecialchars(__admin('settings.save')) ?></button>
        </form>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
