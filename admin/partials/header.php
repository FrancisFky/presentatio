<?php
$flash = getFlash();
$currentLanguage = adminCurrentLanguage();
?>
<header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
  <div>
    <h5 class="mb-0 fw-bold text-dark"><?= htmlspecialchars(__admin('app_title')) ?></h5>
    <small class="text-muted"><?= htmlspecialchars(__admin('app_subtitle')) ?></small>
  </div>
  <div class="d-flex align-items-center gap-3">
    <div class="d-flex align-items-center gap-2 border rounded-pill px-2 py-1 bg-light small fw-semibold">
      <a href="<?= htmlspecialchars(adminBuildSwitchUrl('en')) ?>" class="text-decoration-none <?= $currentLanguage === 'en' ? 'text-primary fw-bold' : 'text-muted' ?>">EN</a>
      <span class="text-muted">|</span>
      <a href="<?= htmlspecialchars(adminBuildSwitchUrl('fr')) ?>" class="text-decoration-none <?= $currentLanguage === 'fr' ? 'text-primary fw-bold' : 'text-muted' ?>">FR</a>
    </div>
    <span class="badge bg-primary"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
    <a href="logout.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-right-from-bracket me-2"></i><?= htmlspecialchars(__admin('logout')) ?></a>
  </div>
</header>
