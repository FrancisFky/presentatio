<?php
require_once __DIR__ . '/config/database.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    header('Content-Type: application/json');
    verifyCsrf();

    $name = trim((string) ($_POST['applicant_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $nationality = trim((string) ($_POST['nationality'] ?? ''));
    $service = trim((string) ($_POST['requested_service'] ?? ''));
    $date = trim((string) ($_POST['preferred_date'] ?? ''));
    $time = trim((string) ($_POST['preferred_time'] ?? ''));

    if ($name === '' || strlen($name) > 150) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Please provide a valid full name.']);
        exit;
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
        exit;
    }

    if ($service === '' || strlen($service) > 255) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Please choose a valid service.']);
        exit;
    }

    if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || strtotime($date) === false) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Please provide a valid preferred date.']);
        exit;
    }

    if (strtotime($date) < strtotime(date('Y-m-d'))) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Appointment dates in the past are not allowed.']);
        exit;
    }

    if ($time === '' || !preg_match('/^\d{2}:\d{2}$/', $time)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Please provide a valid preferred time.']);
        exit;
    }

    if ($phone !== '' && strlen($phone) > 50) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Phone number is too long.']);
        exit;
    }

    if ($nationality !== '' && strlen($nationality) > 100) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Nationality is too long.']);
        exit;
    }

    $statement = $pdo->prepare('INSERT INTO appointments (applicant_name, email, phone, nationality, requested_service, preferred_date, preferred_time, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $statement->execute([$name, $email, $phone, $nationality, $service, $date, $time, 'pending']);

    echo json_encode([
        'success' => true,
        'message' => 'Your appointment request has been submitted successfully. Your request is currently pending confirmation from the Embassy.'
    ]);
    exit;
}

$defaultSettings = [
  'embassy_name' => 'Embassy of the Republic of Congo in Kenya',
  'address' => 'United Crescent, Gigiri, Nairobi, Kenya',
  'phone_numbers' => '+254 707 786 276',
  'emails' => 'embacoken.diplomatic@gmail.com',
  'google_maps' => '',
  'working_hours' => 'Monday - Friday: 09:00 - 17:00',
  'footer_info' => 'Official embassy website for information, services and public notices.',
  'copyright' => '© 2026 Embassy of the Republic of Congo in Kenya',
  'seo_title' => 'Book an Appointment',
  'seo_description' => 'Request an appointment with the Embassy of the Republic of Congo in Kenya.'
];

$settings = $defaultSettings;
if (dbReady()) {
    $settings = $pdo->query('SELECT * FROM website_settings ORDER BY id DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC) ?: $defaultSettings;
}

$lang = strtolower((string)($_GET['lang'] ?? 'en'));
if (!in_array($lang, ['fr', 'en'], true)) {
    $lang = 'en';
}

$services = [];
if (dbReady()) {
    $serviceColumns = $pdo->query('SHOW COLUMNS FROM services')->fetchAll(PDO::FETCH_COLUMN);
    $serviceSelectParts = ['id'];
    foreach (['title', 'title_fr', 'title_en'] as $column) {
        if (in_array($column, $serviceColumns, true)) {
            $serviceSelectParts[] = $column;
        }
    }
    $rows = $pdo->query('SELECT ' . implode(', ', $serviceSelectParts) . ' FROM services WHERE status = "published" ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $serviceTitle = localizedContentValue($row, 'title', $lang);
        if ($serviceTitle !== '') {
            $services[] = ['title' => $serviceTitle];
        }
    }
}

if (!in_array($lang, ['fr', 'en'], true)) {
    $lang = 'en';
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($settings['seo_title'] ?: 'Book an Appointment') ?></title>
  <meta name="description" content="<?= htmlspecialchars($settings['seo_description'] ?: 'Request an appointment with the Embassy of the Republic of Congo in Kenya.') ?>">
  <?php if (!empty($settings['favicon_path'] ?? '')): ?><link rel="icon" href="<?= htmlspecialchars($settings['favicon_path']) ?>"><?php endif; ?>
  <link rel="stylesheet" href="styles.css?v=5">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="top-bar"><?= htmlspecialchars($settings['embassy_name']) ?> • Official Portal</div>
  <header class="header">
    <div class="nav-container">
      <div class="brand">
        <div class="logo-container">
          <img src="<?= htmlspecialchars(trim($settings['logo_path'] ?? '') ?: 'assets/images/armoiries-congo.jpg') ?>" alt="Armoiries de la République du Congo" class="logo-img">
        </div>
        <div class="brand-text">
          <p class="eyebrow">République du Congo</p>
          <h1 class="logo">Ambassade au Kenya</h1>
        </div>
      </div>
      <button class="nav-toggle" type="button" aria-label="Toggle navigation">
        <span></span><span></span><span></span>
      </button>
      <nav class="navigation">
        <a href="index.php" data-i18n="nav_home">Accueil</a>
        <div class="nav-dropdown">
          <button class="nav-dropdown-toggle" type="button" aria-expanded="false" aria-controls="about-menu" data-i18n="nav_about">À Propos</button>
          <div class="nav-dropdown-menu" id="about-menu">
            <a href="about-congo.php" data-i18n="nav_about_congo">À propos du Congo</a>
            <a href="about-embassy.php" data-i18n="nav_about_embassy">À propos de l’Ambassade</a>
            <a href="invest-in-congo.php" data-i18n="nav_invest_congo">Investir au Congo</a>
          </div>
        </div>
        <a href="index.php#services" data-i18n="nav_services">Services</a>
        <a href="index.php#actualites" data-i18n="nav_news">Actualités</a>
        <a href="appointment.php" data-i18n="nav_appointment">Book an Appointment</a>
        <a href="index.php#contact" data-i18n="nav_contact">Contact</a>
      </nav>
      <div class="lang">
        <button class="active" id="fr-btn" type="button">FR</button>
        <button id="en-btn" type="button">EN</button>
      </div>
    </div>
  </header>

  <main>
    <section class="section appointment-section">
      <div class="section-header">
        <span class="section-number">10</span>
        <div>
          <h2 data-i18n="appointment_title">Book an Appointment</h2>
          <p data-i18n="appointment_subtitle">Request a meeting with the Embassy for consular or administrative services.</p>
        </div>
      </div>

      <div class="appointment-layout">
        <div class="appointment-info">
          <div class="info-card appointment-card">
            <p class="info-label" data-i18n="appointment_information">Appointment Information</p>
            <h3><?= htmlspecialchars($settings['embassy_name']) ?></h3>
            <p><?= htmlspecialchars($settings['address']) ?></p>
            <p><?= htmlspecialchars($settings['phone_numbers']) ?></p>
            <p><?= htmlspecialchars($settings['emails']) ?></p>
            <p><?= htmlspecialchars($settings['working_hours']) ?></p>
          </div>
        </div>

        <form class="contact-form appointment-form" id="appointment-form" method="post" action="appointment.php" novalidate>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <div id="appointment-status" class="form-status" aria-live="polite"></div>

          <label>
            <span data-i18n="label_full_name">Full Name</span>
            <input type="text" name="applicant_name" required maxlength="150" autocomplete="name">
          </label>

          <label>
            <span data-i18n="label_email">Email Address</span>
            <input type="email" name="email" required maxlength="150" autocomplete="email">
          </label>

          <label>
            <span data-i18n="label_phone">Phone</span>
            <input type="tel" name="phone" maxlength="50" autocomplete="tel">
          </label>

          <label>
            <span data-i18n="label_nationality">Nationality</span>
            <input type="text" name="nationality" maxlength="100">
          </label>

          <label>
            <span data-i18n="label_service">Requested Service</span>
            <?php if (!empty($services)): ?>
              <select name="requested_service" required>
                <option value="" data-i18n="option_select_service">Select a service</option>
                <?php foreach ($services as $service): ?>
                  <?php $serviceTitle = trim((string)($service['title'] ?? '')); ?>
                  <?php if ($serviceTitle !== ''): ?>
                    <option value="<?= htmlspecialchars($serviceTitle) ?>"><?= htmlspecialchars($serviceTitle) ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
            <?php else: ?>
              <input type="text" name="requested_service" required maxlength="255" placeholder="Passport, Visa, Consular assistance...">
            <?php endif; ?>
          </label>

          <div class="appointment-split">
            <label>
              <span data-i18n="label_date">Preferred Date</span>
              <input type="date" name="preferred_date" required>
            </label>
            <label>
              <span data-i18n="label_time">Preferred Time</span>
              <input type="time" name="preferred_time" required>
            </label>
          </div>

          <button class="btn" type="submit" data-i18n="appointment_submit">Book Appointment</button>
        </form>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="footer-inner">
      <p><?= htmlspecialchars($settings['copyright']) ?></p>
      <p><?= htmlspecialchars($settings['footer_info']) ?></p>
    </div>
  </footer>

  <script src="script.js?v=2"></script>
  <script>
    const appointmentForm = document.getElementById('appointment-form');
    const appointmentStatus = document.getElementById('appointment-status');

    appointmentForm?.addEventListener('submit', async function (event) {
      event.preventDefault();
      const form = event.currentTarget;
      const formData = new FormData(form);
      appointmentStatus.className = 'form-status';
      appointmentStatus.textContent = '';

      try {
        const response = await fetch(form.action, {
          method: 'POST',
          body: formData,
          headers: { 'Accept': 'application/json' }
        });

        const result = await response.json();

        if (!response.ok || result.success === false) {
          appointmentStatus.classList.add('error');
          appointmentStatus.textContent = result.message || 'Please correct the form and try again.';
          return;
        }

        appointmentStatus.classList.add('success');
        appointmentStatus.textContent = result.message || 'Your appointment request has been submitted successfully. Your request is currently pending confirmation from the Embassy.';
        form.reset();
      } catch (error) {
        appointmentStatus.classList.add('error');
        appointmentStatus.textContent = 'Unable to submit your appointment request. Please try again.';
      }
    });
  </script>
</body>
</html>
