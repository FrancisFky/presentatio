<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar bg-dark text-white d-flex flex-column p-3">
  <div class="mb-4">
    <h5 class="fw-bold"><i class="fa-solid fa-landmark me-2"></i><?= htmlspecialchars(__admin('portal_name')) ?></h5>
    <small class="text-white-50"><?= htmlspecialchars(__admin('portal_subtitle')) ?></small>
  </div>
  <nav class="nav nav-pills flex-column gap-1">
    <a class="nav-link text-white <?= isActive('dashboard.php', $currentPage) ? 'active' : '' ?>" href="dashboard.php"><i class="fa-solid fa-gauge me-2"></i><?= htmlspecialchars(__admin('sidebar.dashboard')) ?></a>
    <a class="nav-link text-white <?= isActive('homepage.php', $currentPage) ? 'active' : '' ?>" href="homepage.php"><i class="fa-solid fa-house me-2"></i><?= htmlspecialchars(__admin('sidebar.homepage')) ?></a>
    <div class="text-uppercase text-white-50 small mt-2 mb-1 px-2"><?= htmlspecialchars(__admin('sidebar.about')) ?></div>
    <a class="nav-link text-white sidebar-subnav <?= isActive('about.php', $currentPage) ? 'active' : '' ?>" href="about.php?page=about-congo"><i class="fa-solid fa-angle-right me-2"></i><?= htmlspecialchars(__admin('sidebar.about_congo')) ?></a>
    <a class="nav-link text-white sidebar-subnav <?= isActive('about.php', $currentPage) ? 'active' : '' ?>" href="about.php?page=about-embassy"><i class="fa-solid fa-angle-right me-2"></i><?= htmlspecialchars(__admin('sidebar.about_embassy')) ?></a>
    <a class="nav-link text-white sidebar-subnav <?= isActive('about.php', $currentPage) ? 'active' : '' ?>" href="about.php?page=invest-in-congo"><i class="fa-solid fa-angle-right me-2"></i><?= htmlspecialchars(__admin('sidebar.invest_congo')) ?></a>
    <a class="nav-link text-white <?= isActive('ambassador.php', $currentPage) ? 'active' : '' ?>" href="ambassador.php"><i class="fa-solid fa-user-tie me-2"></i><?= htmlspecialchars(__admin('sidebar.ambassador')) ?></a>
    <a class="nav-link text-white <?= isActive('news.php', $currentPage) ? 'active' : '' ?>" href="news.php"><i class="fa-solid fa-newspaper me-2"></i><?= htmlspecialchars(__admin('sidebar.news')) ?></a>
    <a class="nav-link text-white <?= isActive('announcements.php', $currentPage) ? 'active' : '' ?>" href="announcements.php"><i class="fa-solid fa-bullhorn me-2"></i><?= htmlspecialchars(__admin('sidebar.announcements')) ?></a>
    <a class="nav-link text-white <?= isActive('gallery.php', $currentPage) ? 'active' : '' ?>" href="gallery.php"><i class="fa-solid fa-images me-2"></i><?= htmlspecialchars(__admin('sidebar.gallery')) ?></a>
    <a class="nav-link text-white <?= isActive('events.php', $currentPage) ? 'active' : '' ?>" href="events.php"><i class="fa-solid fa-calendar-alt me-2"></i><?= htmlspecialchars(__admin('sidebar.events')) ?></a>
    <a class="nav-link text-white <?= isActive('services.php', $currentPage) ? 'active' : '' ?>" href="services.php"><i class="fa-solid fa-handshake me-2"></i><?= htmlspecialchars(__admin('sidebar.services')) ?></a>
    <a class="nav-link text-white <?= isActive('downloads.php', $currentPage) ? 'active' : '' ?>" href="downloads.php"><i class="fa-solid fa-download me-2"></i><?= htmlspecialchars(__admin('sidebar.downloads')) ?></a>
    <a class="nav-link text-white <?= isActive('holidays.php', $currentPage) ? 'active' : '' ?>" href="holidays.php"><i class="fa-solid fa-calendar me-2"></i><?= htmlspecialchars(__admin('sidebar.holidays')) ?></a>
    <a class="nav-link text-white <?= isActive('contacts.php', $currentPage) ? 'active' : '' ?>" href="contacts.php"><i class="fa-solid fa-phone me-2"></i><?= htmlspecialchars(__admin('sidebar.contacts')) ?></a>
    <a class="nav-link text-white <?= isActive('appointments.php', $currentPage) ? 'active' : '' ?>" href="appointments.php"><i class="fa-solid fa-calendar-check me-2"></i><?= htmlspecialchars(__admin('sidebar.appointments')) ?></a>
    <a class="nav-link text-white <?= isActive('messages.php', $currentPage) ? 'active' : '' ?>" href="messages.php"><i class="fa-solid fa-envelope me-2"></i><?= htmlspecialchars(__admin('sidebar.messages')) ?></a>
    <a class="nav-link text-white <?= isActive('users.php', $currentPage) ? 'active' : '' ?>" href="users.php"><i class="fa-solid fa-users me-2"></i><?= htmlspecialchars(__admin('sidebar.users')) ?></a>
    <a class="nav-link text-white <?= isActive('settings.php', $currentPage) ? 'active' : '' ?>" href="settings.php"><i class="fa-solid fa-gear me-2"></i><?= htmlspecialchars(__admin('sidebar.settings')) ?></a>
    <a class="nav-link text-white" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i><?= htmlspecialchars(__admin('logout')) ?></a>
  </nav>
</div>
