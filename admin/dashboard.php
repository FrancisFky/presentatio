<?php
require_once __DIR__ . '/../config/database.php';
requireLogin();

$stats = getStats($pdo);
$recentNews = $pdo->query('SELECT * FROM news ORDER BY created_at DESC LIMIT 4')->fetchAll();
$recentMessages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 4')->fetchAll();
$recentActivity = $pdo->query('SELECT a.*, u.full_name FROM activity_logs a LEFT JOIN users u ON u.id = a.user_id ORDER BY a.created_at DESC LIMIT 8')->fetchAll();
$flash = getFlash();
$currentLanguage = adminCurrentLanguage();
$dashboardCards = [
    ['key' => 'dashboard.card.news', 'value' => $stats['news'], 'icon' => 'fa-newspaper'],
    ['key' => 'dashboard.card.announcements', 'value' => $stats['announcements'], 'icon' => 'fa-bullhorn'],
    ['key' => 'dashboard.card.gallery', 'value' => $stats['gallery'], 'icon' => 'fa-images'],
    ['key' => 'dashboard.card.events', 'value' => $stats['events'], 'icon' => 'fa-calendar-alt'],
    ['key' => 'dashboard.card.downloads', 'value' => $stats['downloads'], 'icon' => 'fa-file-alt'],
    ['key' => 'dashboard.card.messages', 'value' => $stats['messages'], 'icon' => 'fa-envelope'],
    ['key' => 'dashboard.card.appointments', 'value' => $stats['appointments'], 'icon' => 'fa-calendar-check'],
    ['key' => 'dashboard.card.users', 'value' => $stats['users'], 'icon' => 'fa-users'],
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLanguage) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars(__admin('dashboard.title')) ?> - Embassy CMS</title>
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
        <?php if ($flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
        <div class="row g-4 mb-4">
          <?php foreach ($dashboardCards as $card): ?>
            <div class="col-12 col-sm-6 col-xl-3">
              <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                  <div>
                    <p class="text-muted mb-1"><?= htmlspecialchars(__admin($card['key'])) ?></p>
                    <h3 class="fw-bold mb-0"><?= $card['value'] ?></h3>
                  </div>
                  <div class="icon-box"><i class="fa-solid <?= $card['icon'] ?>"></i></div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="row g-4">
          <div class="col-lg-8">
            <div class="card shadow-sm border-0">
              <div class="card-header bg-white border-0 fw-bold"><?= htmlspecialchars(__admin('dashboard.recent_activity')) ?></div>
              <div class="card-body">
                <?php foreach ($recentActivity as $activity): ?>
                  <div class="d-flex justify-content-between py-2 border-bottom">
                    <div>
                      <div class="fw-semibold"><?= htmlspecialchars($activity['action']) ?></div>
                      <div class="small text-muted"><?= htmlspecialchars($activity['details']) ?></div>
                    </div>
                    <div class="small text-muted"><?= htmlspecialchars($activity['full_name'] ?? 'System') ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
              <div class="card-header bg-white border-0 fw-bold"><?= htmlspecialchars(__admin('dashboard.quick_actions')) ?></div>
              <div class="card-body d-grid gap-2">
                <a href="news.php" class="btn btn-outline-primary"><i class="fa-solid fa-plus me-2"></i><?= htmlspecialchars(__admin('dashboard.action.add_news')) ?></a>
                <a href="gallery.php" class="btn btn-outline-primary"><i class="fa-solid fa-images me-2"></i><?= htmlspecialchars(__admin('dashboard.action.upload_photos')) ?></a>
                <a href="announcements.php" class="btn btn-outline-primary"><i class="fa-solid fa-bullhorn me-2"></i><?= htmlspecialchars(__admin('dashboard.action.create_announcement')) ?></a>
                <a href="events.php" class="btn btn-outline-primary"><i class="fa-solid fa-calendar-plus me-2"></i><?= htmlspecialchars(__admin('dashboard.action.add_event')) ?></a>
                <a href="homepage.php" class="btn btn-outline-primary"><i class="fa-solid fa-edit me-2"></i><?= htmlspecialchars(__admin('dashboard.action.edit_homepage')) ?></a>
              </div>
            </div>
            <div class="card shadow-sm border-0">
              <div class="card-header bg-white border-0 fw-bold"><?= htmlspecialchars(__admin('dashboard.latest_messages')) ?></div>
              <div class="card-body">
                <?php foreach ($recentMessages as $message): ?>
                  <div class="small border-bottom py-2">
                    <div class="fw-semibold"><?= htmlspecialchars($message['sender_name']) ?></div>
                    <div class="text-muted"><?= htmlspecialchars($message['subject']) ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
