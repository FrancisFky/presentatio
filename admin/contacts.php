<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

$flash = getFlash();
$record = $pdo->query('SELECT * FROM emergency_contacts ORDER BY id DESC LIMIT 1')->fetch();
if (!$record) { $record = ['id' => null, 'hotline' => '', 'phone_numbers' => '', 'whatsapp' => '', 'embassy_email' => '', 'duty_officer' => '', 'google_maps' => '', 'office_hours' => '', 'emergency_instructions' => '']; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $hotline = trim($_POST['hotline'] ?? '');
    $phoneNumbers = trim($_POST['phone_numbers'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $embassyEmail = trim($_POST['embassy_email'] ?? '');
    $dutyOfficer = trim($_POST['duty_officer'] ?? '');
    $googleMaps = trim($_POST['google_maps'] ?? '');
    $officeHours = trim($_POST['office_hours'] ?? '');
    $instructions = trim($_POST['emergency_instructions'] ?? '');
    if ($record['id']) { $stmt = $pdo->prepare('UPDATE emergency_contacts SET hotline=?, phone_numbers=?, whatsapp=?, embassy_email=?, duty_officer=?, google_maps=?, office_hours=?, emergency_instructions=? WHERE id=?'); $stmt->execute([$hotline, $phoneNumbers, $whatsapp, $embassyEmail, $dutyOfficer, $googleMaps, $officeHours, $instructions, $record['id']]); } else { $stmt = $pdo->prepare('INSERT INTO emergency_contacts (hotline, phone_numbers, whatsapp, embassy_email, duty_officer, google_maps, office_hours, emergency_instructions) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'); $stmt->execute([$hotline, $phoneNumbers, $whatsapp, $embassyEmail, $dutyOfficer, $googleMaps, $officeHours, $instructions]); }
    flash('success', __admin('contacts.updated'));
    header('Location: contacts.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(adminCurrentLanguage()) ?>">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= htmlspecialchars(__admin('contacts.title')) ?> - Embassy CMS</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="../assets/css/admin.css"></head>
<body>
  <div class="d-flex min-vh-100">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light">
      <?php include __DIR__ . '/partials/header.php'; ?>
      <main class="p-4">
        <h3 class="fw-bold mb-3"><?= htmlspecialchars(__admin('contacts.title')) ?></h3>
        <?php if ($flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <form method="post" class="card p-4 shadow-sm border-0">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('contacts.hotline')) ?></label><input class="form-control" name="hotline" value="<?= htmlspecialchars($record['hotline'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('contacts.phone_numbers')) ?></label><input class="form-control" name="phone_numbers" value="<?= htmlspecialchars($record['phone_numbers'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('contacts.whatsapp')) ?></label><input class="form-control" name="whatsapp" value="<?= htmlspecialchars($record['whatsapp'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('contacts.embassy_email')) ?></label><input class="form-control" name="embassy_email" value="<?= htmlspecialchars($record['embassy_email'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('contacts.duty_officer')) ?></label><input class="form-control" name="duty_officer" value="<?= htmlspecialchars($record['duty_officer'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('contacts.google_maps')) ?></label><input class="form-control" name="google_maps" value="<?= htmlspecialchars($record['google_maps'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('contacts.office_hours')) ?></label><input class="form-control" name="office_hours" value="<?= htmlspecialchars($record['office_hours'] ?? '') ?>"></div>
            <div class="col-12"><label class="form-label"><?= htmlspecialchars(__admin('contacts.instructions')) ?></label><textarea class="form-control" rows="5" name="emergency_instructions"><?= htmlspecialchars($record['emergency_instructions'] ?? '') ?></textarea></div>
          </div>
          <button class="btn btn-primary mt-3" type="submit"><?= htmlspecialchars(__admin('contacts.save')) ?></button>
        </form>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
