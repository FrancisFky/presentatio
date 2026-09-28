<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

$flash = getFlash();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id <= 0) {
    flash('danger', 'Invalid message ID.');
    header('Location: messages.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM contact_messages WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$message = $stmt->fetch();

if (!$message) {
    flash('danger', 'Message not found.');
    header('Location: messages.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $status = trim((string) ($_POST['status'] ?? 'unread'));
    $allowedStatuses = ['unread', 'read', 'archived'];

    if (!in_array($status, $allowedStatuses, true)) {
        flash('danger', 'Invalid message status.');
        header('Location: message-view.php?id=' . $id);
        exit;
    }

    $pdo->prepare('UPDATE contact_messages SET status = ? WHERE id = ?')->execute([$status, $id]);
    flash('success', 'Message status updated.');
    header('Location: message-view.php?id=' . $id);
    exit;
}

if (($message['status'] ?? 'unread') === 'unread') {
    $pdo->prepare('UPDATE contact_messages SET status = ? WHERE id = ?')->execute(['read', $id]);
    $message['status'] = 'read';
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Message Details - Embassy CMS</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="../assets/css/admin.css"></head>
<body>
  <div class="d-flex min-vh-100">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light">
      <?php include __DIR__ . '/partials/header.php'; ?>
      <main class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="fw-bold mb-0">Message Details</h3>
          <a href="messages.php" class="btn btn-outline-secondary btn-sm">Back to Messages</a>
        </div>
        <?php if ($flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <div class="card p-4 shadow-sm border-0">
          <div class="row g-3">
            <div class="col-md-6"><strong>Sender:</strong> <?= htmlspecialchars((string) ($message['sender_name'] ?? '')) ?></div>
            <div class="col-md-6"><strong>Email:</strong> <?= htmlspecialchars((string) ($message['email'] ?? '')) ?></div>
            <div class="col-md-6"><strong>Phone:</strong> <?= htmlspecialchars((string) ($message['phone'] ?? '')) ?></div>
            <div class="col-md-6"><strong>Subject:</strong> <?= htmlspecialchars((string) ($message['subject'] ?? '')) ?></div>
            <div class="col-md-6"><strong>Date:</strong> <?= htmlspecialchars(date('d M Y H:i', strtotime((string) ($message['created_at'] ?? 'now')))) ?></div>
            <div class="col-md-6"><strong>Status:</strong> <?= htmlspecialchars((string) ($message['status'] ?? 'unread')) ?></div>
          </div>

          <hr>

          <div class="mt-3">
            <h5 class="fw-bold mb-2">Message</h5>
            <div class="border rounded p-3 bg-light">
              <?= nl2br(htmlspecialchars((string) ($message['message'] ?? ''))) ?>
            </div>
          </div>

          <div class="mt-4 d-flex gap-2 align-items-center">
            <form method="post" class="d-flex gap-2 align-items-center mb-0">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
              <select class="form-select form-select-sm" name="status">
                <option value="unread" <?= (($message['status'] ?? 'unread') === 'unread') ? 'selected' : '' ?>>Unread</option>
                <option value="read" <?= (($message['status'] ?? 'unread') === 'read') ? 'selected' : '' ?>>Read</option>
                <option value="archived" <?= (($message['status'] ?? 'unread') === 'archived') ? 'selected' : '' ?>>Archived</option>
              </select>
              <button class="btn btn-primary btn-sm" type="submit">Update Status</button>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
