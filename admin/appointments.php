<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

$flash = getFlash();
$allowedStatuses = ['pending', 'approved', 'rejected', 'rescheduled'];
$appointments = $pdo->query('SELECT * FROM appointments ORDER BY created_at DESC')->fetchAll();
$appointmentStatusLabels = ['pending' => __admin('common.pending'), 'approved' => __admin('common.approved'), 'rejected' => __admin('common.rejected'), 'rescheduled' => __admin('common.rescheduled')];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $status = trim((string) ($_POST['status'] ?? 'pending'));

    if ($id === false || $id <= 0) {
        flash('danger', __admin('flash.appointment_invalid'));
        header('Location: appointments.php');
        exit;
    }

    if (!in_array($status, $allowedStatuses, true)) {
        flash('danger', __admin('flash.appointment_invalid'));
        header('Location: appointments.php');
        exit;
    }

    $existing = $pdo->prepare('SELECT id FROM appointments WHERE id = ?');
    $existing->execute([$id]);
    if (!$existing->fetch()) {
        flash('danger', __admin('flash.appointment_not_found'));
        header('Location: appointments.php');
        exit;
    }

    $pdo->prepare('UPDATE appointments SET status=? WHERE id=?')->execute([$status, $id]);
    flash('success', __admin('flash.appointment_updated'));
    header('Location: appointments.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(adminCurrentLanguage()) ?>">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= htmlspecialchars(__admin('appointments.title')) ?> - Embassy CMS</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="../assets/css/admin.css"></head>
<body>
  <div class="d-flex min-vh-100">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light">
      <?php include __DIR__ . '/partials/header.php'; ?>
      <main class="p-4">
        <h3 class="fw-bold mb-3"><?= htmlspecialchars(__admin('appointments.title')) ?></h3>
        <?php if ($flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <div class="card p-4 shadow-sm border-0">
          <?php if (empty($appointments)): ?>
            <p class="text-muted mb-0"><?= htmlspecialchars(__admin('appointments.empty')) ?></p>
          <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th><?= htmlspecialchars(__admin('appointments.applicant')) ?></th>
                  <th><?= htmlspecialchars(__admin('appointments.email')) ?></th>
                  <th><?= htmlspecialchars(__admin('appointments.phone')) ?></th>
                  <th><?= htmlspecialchars(__admin('appointments.nationality')) ?></th>
                  <th><?= htmlspecialchars(__admin('appointments.service')) ?></th>
                  <th><?= htmlspecialchars(__admin('appointments.date')) ?></th>
                  <th><?= htmlspecialchars(__admin('appointments.time')) ?></th>
                  <th><?= htmlspecialchars(__admin('appointments.status')) ?></th>
                  <th><?= htmlspecialchars(__admin('appointments.action')) ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                  <td><?= htmlspecialchars((string) ($appointment['applicant_name'] ?? '')) ?></td>
                  <td><?= htmlspecialchars((string) ($appointment['email'] ?? '')) ?></td>
                  <td><?= htmlspecialchars((string) ($appointment['phone'] ?? '')) ?></td>
                  <td><?= htmlspecialchars((string) ($appointment['nationality'] ?? '')) ?></td>
                  <td><?= htmlspecialchars((string) ($appointment['requested_service'] ?? '')) ?></td>
                  <td><?= htmlspecialchars((string) ($appointment['preferred_date'] ?? '')) ?></td>
                  <td><?= htmlspecialchars((string) ($appointment['preferred_time'] ?? '')) ?></td>
                  <td><?= htmlspecialchars($appointmentStatusLabels[(string) ($appointment['status'] ?? 'pending')] ?? ucfirst((string) ($appointment['status'] ?? 'pending'))) ?></td>
                  <td>
                    <form method="post" class="d-flex gap-2 align-items-center">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                      <input type="hidden" name="id" value="<?= (int) ($appointment['id'] ?? 0) ?>">
                      <select class="form-select form-select-sm" name="status">
                        <option value="pending" <?= (($appointment['status'] ?? 'pending') === 'pending') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.pending')) ?></option>
                        <option value="approved" <?= (($appointment['status'] ?? 'pending') === 'approved') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.approved')) ?></option>
                        <option value="rejected" <?= (($appointment['status'] ?? 'pending') === 'rejected') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.rejected')) ?></option>
                        <option value="rescheduled" <?= (($appointment['status'] ?? 'pending') === 'rescheduled') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.rescheduled')) ?></option>
                      </select>
                      <button class="btn btn-sm btn-primary" type="submit"><?= htmlspecialchars(__admin('appointments.update')) ?></button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
