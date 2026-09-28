<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

$flash = getFlash();
$users = $pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? 'Administrator');
    $status = trim($_POST['status'] ?? 'active');
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $pdo->prepare('INSERT INTO users (full_name, email, username, password_hash, role, status) VALUES (?, ?, ?, ?, ?, ?)')->execute([$fullName, $email, $username, $hash, $role, $status]);
    flash('success', __admin('users.created'));
    header('Location: users.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(adminCurrentLanguage()) ?>">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= htmlspecialchars(__admin('users.title')) ?> - Embassy CMS</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="../assets/css/admin.css"></head>
<body>
  <div class="d-flex min-vh-100">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light">
      <?php include __DIR__ . '/partials/header.php'; ?>
      <main class="p-4">
        <h3 class="fw-bold mb-3"><?= htmlspecialchars(__admin('users.title')) ?></h3>
        <?php if ($flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <form method="post" class="card p-4 shadow-sm border-0 mb-4">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('users.full_name')) ?></label><input class="form-control" name="full_name" required></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('users.email')) ?></label><input class="form-control" type="email" name="email" required></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('users.username')) ?></label><input class="form-control" name="username" required></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('users.password')) ?></label><input class="form-control" type="password" name="password" required></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('users.role')) ?></label><select class="form-select" name="role"><option value="Administrator"><?= htmlspecialchars(__admin('users.role.administrator')) ?></option><option value="Content Editor"><?= htmlspecialchars(__admin('users.role.content_editor')) ?></option><option value="Consular Officer"><?= htmlspecialchars(__admin('users.role.consular_officer')) ?></option><option value="Communications Officer"><?= htmlspecialchars(__admin('users.role.communications_officer')) ?></option></select></div>
            <div class="col-md-6"><label class="form-label"><?= htmlspecialchars(__admin('users.status')) ?></label><select class="form-select" name="status"><option value="active"><?= htmlspecialchars(__admin('users.status.active')) ?></option><option value="inactive"><?= htmlspecialchars(__admin('users.status.inactive')) ?></option></select></div>
          </div>
          <button class="btn btn-primary mt-3" type="submit"><?= htmlspecialchars(__admin('users.create_user')) ?></button>
        </form>
        <div class="card p-4 shadow-sm border-0"><h5 class="fw-bold mb-3"><?= htmlspecialchars(__admin('users.system_users')) ?></h5><div class="table-responsive"><table class="table table-hover"><thead><tr><th><?= htmlspecialchars(__admin('users.full_name')) ?></th><th><?= htmlspecialchars(__admin('users.email')) ?></th><th><?= htmlspecialchars(__admin('users.role')) ?></th><th><?= htmlspecialchars(__admin('users.status')) ?></th></tr></thead><tbody><?php foreach ($users as $user): ?><tr><td><?= htmlspecialchars($user['full_name']) ?></td><td><?= htmlspecialchars($user['email']) ?></td><td><?= htmlspecialchars($user['role']) ?></td><td><?= htmlspecialchars($user['status']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
