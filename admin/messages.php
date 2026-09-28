<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

$flash = getFlash();
$allowedStatuses = ['unread', 'read', 'archived'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $status = trim((string) ($_POST['status'] ?? 'unread'));

    if ($id === false || $id <= 0) {
        flash('danger', __admin('flash.message_invalid'));
        header('Location: messages.php');
        exit;
    }

    if (!in_array($status, $allowedStatuses, true)) {
        flash('danger', __admin('flash.message_invalid'));
        header('Location: messages.php');
        exit;
    }

    $existing = $pdo->prepare('SELECT id FROM contact_messages WHERE id = ?');
    $existing->execute([$id]);
    if (!$existing->fetch()) {
        flash('danger', __admin('flash.message_not_found'));
        header('Location: messages.php');
        exit;
    }

    $pdo->prepare('UPDATE contact_messages SET status = ? WHERE id = ?')->execute([$status, $id]);
    flash('success', __admin('flash.message_updated'));
    header('Location: messages.php');
    exit;
}

$messages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(adminCurrentLanguage()) ?>">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= htmlspecialchars(__admin('messages.title')) ?> - Embassy CMS</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="../assets/css/admin.css"></head>
<body>
  <div class="d-flex min-vh-100">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light">
      <?php include __DIR__ . '/partials/header.php'; ?>
      <main class="p-4">
        <h3 class="fw-bold mb-3"><?= htmlspecialchars(__admin('messages.title')) ?></h3>
        <?php if ($flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <div class="card p-4 shadow-sm border-0">
          <?php if (empty($messages)): ?>
            <p class="text-muted mb-0"><?= htmlspecialchars(__admin('messages.empty')) ?></p>
          <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th><?= htmlspecialchars(__admin('messages.sender')) ?></th>
                  <th><?= htmlspecialchars(__admin('messages.subject')) ?></th>
                  <th><?= htmlspecialchars(__admin('messages.date')) ?></th>
                  <th><?= htmlspecialchars(__admin('common.status')) ?></th>
                  <th><?= htmlspecialchars(__admin('messages.action')) ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($messages as $message): ?>
                <tr>
                  <td><?= htmlspecialchars((string) ($message['sender_name'] ?? '')) ?></td>
                  <td><?= htmlspecialchars((string) ($message['subject'] ?? '')) ?></td>
                  <td><?= htmlspecialchars(date('d M Y H:i', strtotime((string) ($message['created_at'] ?? 'now')))) ?></td>
                  <td><?= htmlspecialchars((string) ($message['status'] ?? 'unread')) ?></td>
                  <td>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                      <a class="btn btn-sm btn-outline-primary" href="message-view.php?id=<?= (int) ($message['id'] ?? 0) ?>"><?= htmlspecialchars(__admin('messages.view')) ?></a>
                      <form method="post" class="d-flex gap-2 align-items-center mb-0">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                        <input type="hidden" name="id" value="<?= (int) ($message['id'] ?? 0) ?>">
                        <select class="form-select form-select-sm" name="status">
                          <option value="unread" <?= (($message['status'] ?? 'unread') === 'unread') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.unread')) ?></option>
                          <option value="read" <?= (($message['status'] ?? 'unread') === 'read') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.read')) ?></option>
                          <option value="archived" <?= (($message['status'] ?? 'unread') === 'archived') ? 'selected' : '' ?>><?= htmlspecialchars(__admin('common.archived')) ?></option>
                        </select>
                        <button class="btn btn-sm btn-primary" type="submit"><?= htmlspecialchars(__admin('messages.update')) ?></button>
                      </form>
                    </div>
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
