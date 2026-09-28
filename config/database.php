<?php
if (session_status() === PHP_SESSION_NONE) {
    // Admin pages contain authenticated content and must never be restored
    // from the browser cache after the visitor closes the session or logs out.
    session_cache_limiter('nocache');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once __DIR__ . '/../admin/includes/translations.php';
adminApplyLanguagePreference();

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'embassy_cms');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('UPLOAD_ROOT', __DIR__ . '/../uploads');
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024);
define('UPLOAD_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
define('UPLOAD_DOCUMENT_EXTENSIONS', ['pdf', 'docx', 'xlsx', 'zip', 'jpg', 'jpeg', 'png', 'webp']);

$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);

$pdoOptions = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$pdo = null;

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $pdoOptions);
} catch (PDOException $e) {
    $pdo = null;
}

function dbReady(): bool
{
    global $pdo;
    return $pdo instanceof PDO;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function enforceSessionTimeout(int $seconds = 900): void
{
    if (!isLoggedIn()) {
        return;
    }

    $lastActivity = $_SESSION['last_activity'] ?? time();
    if ((time() - (int) $lastActivity) > $seconds) {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }

    $_SESSION['last_activity'] = time();
}

function requireLogin(): void
{
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');

    enforceSessionTimeout();

    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function homepageContentValue(array $content, string $field, string $lang): string
{
    $langField = $field . '_' . $lang;
    $candidate = trim((string) ($content[$langField] ?? ''));
    if ($candidate !== '') {
        return $candidate;
    }

    $otherLang = $lang === 'fr' ? 'en' : 'fr';
    $otherCandidate = trim((string) ($content[$field . '_' . $otherLang] ?? ''));
    if ($otherCandidate !== '') {
        return $otherCandidate;
    }

    return trim((string) ($content[$field] ?? ''));
}

function localizedContentValue(array $row, string $field, string $lang): string
{
    $requestedField = $field . '_' . $lang;
    $requested = trim((string) ($row[$requestedField] ?? ''));
    if ($requested !== '') {
        return $requested;
    }

    $otherLang = $lang === 'fr' ? 'en' : 'fr';
    $otherField = $field . '_' . $otherLang;
    $other = trim((string) ($row[$otherField] ?? ''));
    if ($other !== '') {
        return $other;
    }

    return trim((string) ($row[$field] ?? ''));
}

function getLatestHomepageContent(): array
{
    global $pdo;

    $defaults = [
        'id' => null,
        'hero_title' => '',
        'hero_title_fr' => '',
        'hero_title_en' => '',
        'hero_subtitle' => '',
        'hero_subtitle_fr' => '',
        'hero_subtitle_en' => '',
        'hero_image' => '',
        'welcome_message' => '',
        'welcome_message_fr' => '',
        'welcome_message_en' => '',
        'mission' => '',
        'mission_fr' => '',
        'mission_en' => '',
        'vision' => '',
        'vision_fr' => '',
        'vision_en' => '',
        'objectives' => '',
        'objectives_fr' => '',
        'objectives_en' => '',
        'homepage_buttons' => ''
    ];

    if (!$pdo) {
        return $defaults;
    }

    $stmt = $pdo->query('SELECT * FROM homepage_content ORDER BY id DESC LIMIT 1');
    $content = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$content) {
        return $defaults;
    }

    foreach (['hero_title', 'hero_subtitle', 'welcome_message', 'mission', 'vision', 'objectives'] as $field) {
        if (!array_key_exists($field . '_fr', $content) || !array_key_exists($field . '_en', $content)) {
            $content[$field . '_fr'] = homepageContentValue($content, $field, 'fr');
            $content[$field . '_en'] = homepageContentValue($content, $field, 'en');
        }
    }

    return $content;
}

/** Render formatting created by the CMS rich-text editor safely on public pages. */
function renderRichText(?string $content): string
{
    $content = trim((string) $content);
    if ($content === '') {
        return '';
    }

    $content = strip_tags($content, '<p><br><strong><b><em><i><ul><ol><li><h2><h3><h4><blockquote>');
    return preg_replace('/<([a-z0-9]+)\\b[^>]*>/i', '<$1>', $content);
}

function requireRole(string $role): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }

    global $pdo;
    $stmt = $pdo->prepare('SELECT role FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || $user['role'] !== $role) {
        http_response_code(403);
        die('Access denied.');
    }
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void
{
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function isActive(string $page, string $current): bool
{
    return $page === $current;
}

function handleUpload(string $fieldName, string $targetDir, string $type = 'image'): array
{
    if (!isset($_FILES[$fieldName])) {
        return ['success' => false, 'message' => 'No file uploaded'];
    }

    $file = $_FILES[$fieldName];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error (' . (int) $file['error'] . ')'];
    }
    $allowed = $type === 'document' ? UPLOAD_DOCUMENT_EXTENSIONS : UPLOAD_IMAGE_EXTENSIONS;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed, true)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }

    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return ['success' => false, 'message' => 'File exceeds 5MB limit'];
    }

    if ($type === 'image') {
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mime, $allowedMimes, true) || @getimagesize($file['tmp_name']) === false) {
            return ['success' => false, 'message' => 'Invalid image file'];
        }
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $filename = uniqid('file_', true) . '.' . $ext;
    $destination = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'message' => 'Upload failed'];
    }

    return ['success' => true, 'path' => $destination, 'filename' => $filename];
}

function getUserName(PDO $pdo, int $userId): string
{
    $stmt = $pdo->prepare('SELECT full_name FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    return $user['full_name'] ?? 'Staff';
}

function formatDate(string $date): string
{
    return date('d M Y', strtotime($date));
}

function getStats(PDO $pdo): array
{
    $stats = [];
    $stats['news'] = (int) $pdo->query('SELECT COUNT(*) FROM news')->fetchColumn();
    $stats['announcements'] = (int) $pdo->query('SELECT COUNT(*) FROM announcements')->fetchColumn();
    $stats['gallery'] = (int) $pdo->query('SELECT COUNT(*) FROM gallery')->fetchColumn();
    $stats['events'] = (int) $pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
    $stats['downloads'] = (int) $pdo->query('SELECT COUNT(*) FROM downloads')->fetchColumn();
    $stats['messages'] = (int) $pdo->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
    $stats['appointments'] = (int) $pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn();
    $stats['users'] = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    return $stats;
}
