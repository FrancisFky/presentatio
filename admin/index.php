<?php
require_once __DIR__ . '/../config/database.php';

// Make /admin/ a safe entry point. New visitors always see the login form;
// authenticated staff can continue directly to their dashboard.
header('Location: ' . (isLoggedIn() ? 'dashboard.php' : 'login.php'));
exit;
