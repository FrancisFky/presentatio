<?php
require_once __DIR__ . '/../config/database.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($identifier !== '' && $password !== '') {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE (email = ? OR username = ?) AND status = "active"');
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['last_activity'] = time();
            $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')->execute([$user['id']]);
            $pdo->prepare('INSERT INTO login_logs (user_id, ip_address, user_agent) VALUES (?, ?, ?)')->execute([$user['id'], $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
            flash('success', 'Welcome back!');
            header('Location: dashboard.php');
            exit;
        }

        flash('error', 'Invalid credentials.');
    } else {
        flash('error', 'Please provide your credentials.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    body { background: linear-gradient(135deg, #112233, #1d3a5d); min-height: 100vh; }
    .login-card { max-width: 450px; border-radius: 18px; box-shadow: 0 20px 50px rgba(0,0,0,.2); }
    .brand { color: #c5a059; font-size: 1.2rem; }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center p-4">
  <div class="card login-card w-100 p-4">
    <div class="text-center mb-4">
      <div class="brand fw-bold mb-2"><i class="fa-solid fa-landmark me-2"></i>Embassy Administration</div>
      <h3 class="fw-bold text-dark">Secure Staff Login</h3>
      <p class="text-muted">Access the embassy content management system</p>
    </div>
    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
      <div class="mb-3">
        <label class="form-label">Username or Email</label>
        <input name="identifier" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="remember">
          <label class="form-check-label">Remember me</label>
        </div>
        <a href="#" class="small">Forgot password?</a>
      </div>
      <button class="btn btn-primary w-100" type="submit">Login</button>
    </form>
  </div>
</body>
</html>
