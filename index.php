<?php
require_once __DIR__ . '/config/database.php';

$defaultSettings = [
  'embassy_name' => 'Embassy of the Republic of Congo in Kenya',
  'address' => 'United Crescent, Gigiri, Nairobi, Kenya',
  'phone_numbers' => '+254 707 786 276',
  'emails' => 'embacoken.diplomatic@gmail.com',
  'google_maps' => '',
  'working_hours' => 'Monday - Friday: 09:00 - 17:00',
  'footer_info' => 'Official embassy website for information, services and public notices.',
  'copyright' => '© 2026 Embassy of the Republic of Congo in Kenya',
  'seo_title' => 'Embassy of the Republic of Congo in Kenya',
  'seo_description' => 'Official embassy website for news, services and public information.'
];

$defaultHomepage = [
  'hero_title' => 'Welcome to the Embassy of the Republic of Congo in Kenya',
  'hero_subtitle' => 'Serving citizens, partners and the diplomatic community with professionalism and care.',
  'hero_image' => '',
  'welcome_message' => 'The embassy is committed to strengthening bilateral ties and serving the Congolese community in Kenya.',
  'mission' => 'To represent the Republic of Congo with dignity, professionalism and dedication.',
  'vision' => 'To build strong partnerships and provide trusted consular and diplomatic support.',
  'objectives' => 'To support citizens, strengthen diplomacy and promote cooperation across all sectors.'
];

$defaultAmbassador = [
  'name' => 'H.E. Léon François Yendouma',
  'position' => 'Ambassador',
  'welcome_message' => 'It is my pleasure to serve the people and strengthen relations between our nations.'
];

$defaultContacts = [
  'hotline' => '+254 707 786 276',
  'phone_numbers' => '+254 707 786 276',
  'embassy_email' => 'embacoken.diplomatic@gmail.com',
  'office_hours' => 'Monday - Friday: 09:00 - 17:00'
];

$settings = $defaultSettings;
if (dbReady()) {
    $settings = $pdo->query('SELECT * FROM website_settings ORDER BY id DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC) ?: $defaultSettings;
}

$homepage = $defaultHomepage;
if (dbReady()) {
    $homepage = getLatestHomepageContent();
    $homepage = array_merge($defaultHomepage, $homepage);
}

$lang = ($_GET['lang'] ?? 'fr') === 'en' ? 'en' : 'fr';
$heroTitle = homepageContentValue($homepage, 'hero_title', $lang);
$heroSubtitle = homepageContentValue($homepage, 'hero_subtitle', $lang);
$welcomeMessage = homepageContentValue($homepage, 'welcome_message', $lang);
$mission = homepageContentValue($homepage, 'mission', $lang);
$vision = homepageContentValue($homepage, 'vision', $lang);
$objectives = homepageContentValue($homepage, 'objectives', $lang);

$heroImage = trim($homepage['hero_image'] ?? '') ?: 'assets/images/congo%20flag.jpg';
$logoPath = trim($settings['logo_path'] ?? '') ?: 'assets/images/armoiries-congo.jpg';
$faviconPath = trim($settings['favicon_path'] ?? '');
$heroButtons = json_decode((string) ($homepage['homepage_buttons'] ?? ''), true);
if (!is_array($heroButtons)) {
    $heroButtons = [];
}

$ambassador = $defaultAmbassador;
if (dbReady()) {
    $ambassador = $pdo->query('SELECT * FROM ambassador WHERE published = 1 ORDER BY id DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC) ?: $defaultAmbassador;
}

$ambassadorName = trim((string) ($ambassador['name_' . $lang] ?? ''));
if ($ambassadorName === '') {
    $otherLang = $lang === 'fr' ? 'en' : 'fr';
    $ambassadorName = trim((string) ($ambassador['name_' . $otherLang] ?? ''));
}
if ($ambassadorName === '') {
    $ambassadorName = trim((string) ($ambassador['name'] ?? ''));
}

$ambassadorPosition = trim((string) ($ambassador['position_' . $lang] ?? ''));
if ($ambassadorPosition === '') {
    $otherLang = $lang === 'fr' ? 'en' : 'fr';
    $ambassadorPosition = trim((string) ($ambassador['position_' . $otherLang] ?? ''));
}
if ($ambassadorPosition === '') {
    $ambassadorPosition = trim((string) ($ambassador['position'] ?? ''));
}

$ambassadorBiography = trim((string) ($ambassador['biography_' . $lang] ?? ''));
if ($ambassadorBiography === '') {
    $otherLang = $lang === 'fr' ? 'en' : 'fr';
    $ambassadorBiography = trim((string) ($ambassador['biography_' . $otherLang] ?? ''));
}
if ($ambassadorBiography === '') {
    $ambassadorBiography = trim((string) ($ambassador['biography'] ?? ''));
}

$ambassadorWelcome = trim((string) ($ambassador['welcome_message_' . $lang] ?? ''));
if ($ambassadorWelcome === '') {
    $otherLang = $lang === 'fr' ? 'en' : 'fr';
    $ambassadorWelcome = trim((string) ($ambassador['welcome_message_' . $otherLang] ?? ''));
}
if ($ambassadorWelcome === '') {
    $ambassadorWelcome = trim((string) ($ambassador['welcome_message'] ?? ''));
}

$ambassadorSignature = trim((string) ($ambassador['signature_' . $lang] ?? ''));
if ($ambassadorSignature === '') {
    $otherLang = $lang === 'fr' ? 'en' : 'fr';
    $ambassadorSignature = trim((string) ($ambassador['signature_' . $otherLang] ?? ''));
}
if ($ambassadorSignature === '') {
    $ambassadorSignature = trim((string) ($ambassador['signature'] ?? ''));
}

$announcements = [];
$news = [];
$services = [];
$holidays = [];
$events = [];
$downloads = [];
$gallery = [];
$contacts = $defaultContacts;
$featuredServiceLimit = 6;

if (dbReady()) {
    $announcementColumns = $pdo->query('SHOW COLUMNS FROM announcements')->fetchAll(PDO::FETCH_COLUMN);
    $announcementSelectParts = ['a.id', 'a.category', 'a.priority', 'a.image', 'a.pdf_attachment', 'a.publish_date', 'a.pin_to_homepage'];
    foreach (['title', 'title_fr', 'title_en', 'description', 'description_fr', 'description_en'] as $column) {
        if (in_array($column, $announcementColumns, true)) {
            $announcementSelectParts[] = 'a.' . $column;
        }
    }
    $announcements = $pdo->query('SELECT ' . implode(', ', $announcementSelectParts) . ' FROM announcements a WHERE a.status = "published" AND (a.expiry_date IS NULL OR a.expiry_date = "" OR a.expiry_date >= CURDATE()) ORDER BY a.pin_to_homepage DESC, a.publish_date DESC LIMIT 4')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($announcements as &$announcement) {
        $requestedTitle = '';
        $titleField = 'title_' . $lang;
        if (array_key_exists($titleField, $announcement) && trim((string) $announcement[$titleField]) !== '') {
            $requestedTitle = trim((string) $announcement[$titleField]);
        } elseif (array_key_exists('title_' . ($lang === 'fr' ? 'en' : 'fr'), $announcement) && trim((string) $announcement['title_' . ($lang === 'fr' ? 'en' : 'fr')]) !== '') {
            $requestedTitle = trim((string) $announcement['title_' . ($lang === 'fr' ? 'en' : 'fr')]);
        } elseif (isset($announcement['title']) && trim((string) $announcement['title']) !== '') {
            $requestedTitle = trim((string) $announcement['title']);
        }
        $announcement['title'] = $requestedTitle;

        $requestedDescription = '';
        $descriptionField = 'description_' . $lang;
        if (array_key_exists($descriptionField, $announcement) && trim((string) $announcement[$descriptionField]) !== '') {
            $requestedDescription = trim((string) $announcement[$descriptionField]);
        } elseif (array_key_exists('description_' . ($lang === 'fr' ? 'en' : 'fr'), $announcement) && trim((string) $announcement['description_' . ($lang === 'fr' ? 'en' : 'fr')]) !== '') {
            $requestedDescription = trim((string) $announcement['description_' . ($lang === 'fr' ? 'en' : 'fr')]);
        } elseif (isset($announcement['description']) && trim((string) $announcement['description']) !== '') {
            $requestedDescription = trim((string) $announcement['description']);
        }
        $announcement['description'] = $requestedDescription;
    }
    unset($announcement);

    $newsColumns = $pdo->query('SHOW COLUMNS FROM news')->fetchAll(PDO::FETCH_COLUMN);
    $newsSelectParts = [
        'n.id',
        'n.title',
        'n.short_description',
        'n.content',
        'n.featured_image',
        'n.pdf_attachment',
        'n.publication_date',
        'c.name AS category'
    ];
    foreach (['title_fr', 'title_en', 'short_description_fr', 'short_description_en', 'content_fr', 'content_en'] as $column) {
        if (in_array($column, $newsColumns, true)) {
            $newsSelectParts[] = 'n.' . $column;
        }
    }

    $news = $pdo->query('SELECT ' . implode(', ', $newsSelectParts) . ' FROM news n LEFT JOIN news_categories c ON c.id = n.category_id WHERE n.status = "published" ORDER BY n.publication_date DESC, n.id DESC LIMIT 6')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($news as &$article) {
        $requestedTitle = '';
        $titleField = 'title_' . $lang;
        if (array_key_exists($titleField, $article) && trim((string) $article[$titleField]) !== '') {
            $requestedTitle = trim((string) $article[$titleField]);
        } elseif (array_key_exists('title_' . ($lang === 'fr' ? 'en' : 'fr'), $article) && trim((string) $article['title_' . ($lang === 'fr' ? 'en' : 'fr')]) !== '') {
            $requestedTitle = trim((string) $article['title_' . ($lang === 'fr' ? 'en' : 'fr')]);
        } elseif (isset($article['title']) && trim((string) $article['title']) !== '') {
            $requestedTitle = trim((string) $article['title']);
        }
        $article['title'] = $requestedTitle;

        $requestedShort = '';
        $shortField = 'short_description_' . $lang;
        if (array_key_exists($shortField, $article) && trim((string) $article[$shortField]) !== '') {
            $requestedShort = trim((string) $article[$shortField]);
        } elseif (array_key_exists('short_description_' . ($lang === 'fr' ? 'en' : 'fr'), $article) && trim((string) $article['short_description_' . ($lang === 'fr' ? 'en' : 'fr')]) !== '') {
            $requestedShort = trim((string) $article['short_description_' . ($lang === 'fr' ? 'en' : 'fr')]);
        } elseif (isset($article['short_description']) && trim((string) $article['short_description']) !== '') {
            $requestedShort = trim((string) $article['short_description']);
        }
        $article['short_description'] = $requestedShort;

        $requestedContent = '';
        $contentField = 'content_' . $lang;
        if (array_key_exists($contentField, $article) && trim((string) $article[$contentField]) !== '') {
            $requestedContent = trim((string) $article[$contentField]);
        } elseif (array_key_exists('content_' . ($lang === 'fr' ? 'en' : 'fr'), $article) && trim((string) $article['content_' . ($lang === 'fr' ? 'en' : 'fr')]) !== '') {
            $requestedContent = trim((string) $article['content_' . ($lang === 'fr' ? 'en' : 'fr')]);
        } elseif (isset($article['content']) && trim((string) $article['content']) !== '') {
            $requestedContent = trim((string) $article['content']);
        }
        $article['content'] = $requestedContent;
    }
    unset($article);
    $serviceColumns = $pdo->query('SHOW COLUMNS FROM services')->fetchAll(PDO::FETCH_COLUMN);
    $serviceSelectParts = ['s.id'];
    foreach (['title','title_fr','title_en','description','description_fr','description_en','requirements','requirements_fr','requirements_en','required_documents','required_documents_fr','required_documents_en','fees','fees_fr','fees_en','processing_time','processing_time_fr','processing_time_en','office_hours','office_hours_fr','office_hours_en','download_forms','download_forms_fr','download_forms_en'] as $column) {
        if (in_array($column, $serviceColumns, true)) {
            $serviceSelectParts[] = 's.' . $column;
        }
    }
    $services = $pdo->query('SELECT ' . implode(', ', $serviceSelectParts) . ' FROM services s WHERE s.status = "published" ORDER BY s.id DESC LIMIT ' . (int) $featuredServiceLimit)->fetchAll(PDO::FETCH_ASSOC);
    foreach ($services as &$service) {
        $service['title'] = localizedContentValue($service, 'title', $lang);
        $service['description'] = localizedContentValue($service, 'description', $lang);
        $service['requirements'] = localizedContentValue($service, 'requirements', $lang);
        $service['required_documents'] = localizedContentValue($service, 'required_documents', $lang);
        $service['fees'] = localizedContentValue($service, 'fees', $lang);
        $service['processing_time'] = localizedContentValue($service, 'processing_time', $lang);
        $service['office_hours'] = localizedContentValue($service, 'office_hours', $lang);
        $service['download_forms'] = localizedContentValue($service, 'download_forms', $lang);
    }
    unset($service);

    $eventColumns = $pdo->query('SHOW COLUMNS FROM events')->fetchAll(PDO::FETCH_COLUMN);
    $eventSelectParts = ['e.id', 'e.event_date', 'e.event_time', 'e.cover_image', 'e.registration_link', 'e.status'];
    foreach (['title','title_fr','title_en','description','description_fr','description_en','venue','venue_fr','venue_en'] as $column) {
        if (in_array($column, $eventColumns, true)) {
            $eventSelectParts[] = 'e.' . $column;
        }
    }
    $events = $pdo->query('SELECT ' . implode(', ', $eventSelectParts) . ' FROM events e WHERE e.status = "published" ORDER BY e.event_date ASC, e.event_time ASC LIMIT 6')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($events as &$event) {
        $event['title'] = localizedContentValue($event, 'title', $lang);
        $event['description'] = localizedContentValue($event, 'description', $lang);
        $event['venue'] = localizedContentValue($event, 'venue', $lang);
    }
    unset($event);

    $holidays = $pdo->query("SELECT holiday_name, holiday_date, description FROM holidays WHERE status = 'published' ORDER BY holiday_date ASC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
    $downloads = $pdo->query("SELECT title, category, file_path, file_size FROM downloads WHERE status = 'published' ORDER BY id DESC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);
    $gallery = $pdo->query("SELECT title, caption, alt_text, image_path FROM gallery WHERE image_path <> '' ORDER BY featured DESC, created_at DESC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);
    $contacts = $pdo->query('SELECT * FROM emergency_contacts ORDER BY id DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC) ?: $defaultContacts;
}
?><!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($settings['seo_title'] ?: $settings['embassy_name']) ?></title>
  <meta name="description" content="<?= htmlspecialchars($settings['seo_description'] ?: 'Official embassy website') ?>">
  <?php if ($faviconPath !== ''): ?><link rel="icon" href="<?= htmlspecialchars($faviconPath) ?>"><?php endif; ?>
  <link rel="stylesheet" href="styles.css?v=4">
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
          <img src="<?= htmlspecialchars($logoPath) ?>" alt="Armoiries de la République du Congo" class="logo-img">
        </div>
        <div class="brand-text">
          <p class="eyebrow">République du Congo</p>
          <h1 class="logo">Ambassade au Kenya</h1>
        </div>
      </div>
      <button class="nav-toggle" type="button" aria-label="Toggle navigation">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <nav class="navigation">
        <a href="#accueil" data-i18n="nav_home">Accueil</a>
        <div class="nav-dropdown">
          <button class="nav-dropdown-toggle" type="button" aria-expanded="false" aria-controls="about-menu" data-i18n="nav_about">À Propos</button>
          <div class="nav-dropdown-menu" id="about-menu">
            <a href="about-congo.php" data-i18n="nav_about_congo">À propos du Congo</a>
            <a href="about-embassy.php" data-i18n="nav_about_embassy">À propos de l’Ambassade</a>
            <a href="invest-in-congo.php" data-i18n="nav_invest_congo">Investir au Congo</a>
          </div>
        </div>
        <a href="#services" data-i18n="nav_services">Services</a>
        <a href="#actualites" data-i18n="nav_news">Actualités</a>
        <a href="appointment.php" data-i18n="nav_appointment">Book an Appointment</a>
        <?php if ($events): ?><a href="#events">Events</a><?php endif; ?>
        <?php if ($gallery): ?><a href="#gallery">Gallery</a><?php endif; ?>
        <?php if ($downloads): ?><a href="#downloads">Documents</a><?php endif; ?>
        <a href="#contact" data-i18n="nav_contact">Contact</a>
      </nav>
      <div class="lang">
        <button class="active" id="fr-btn" type="button">FR</button>
        <button id="en-btn" type="button">EN</button>
      </div>
    </div>
  </header>

  <main>
    <section class="hero" id="accueil" style="<?= htmlspecialchars("background-image: linear-gradient(rgba(118, 121, 124, 0.35), rgba(17, 34, 51, 0.45)), url('{$heroImage}')", ENT_QUOTES) ?>">
      <div class="hero-content">
        <p class="hero-label" data-i18n="hero_label">Représentation Diplomatique</p>
        <!-- These values are managed in the CMS. Do not mark them for
             JavaScript translation, otherwise the saved database content is
             replaced as soon as the page loads. -->
        <h2><?= htmlspecialchars($heroTitle) ?></h2>
        <p class="hero-copy"><?= htmlspecialchars($heroSubtitle) ?></p>
        <?php if (!empty($heroButtons)): ?>
          <?php foreach ($heroButtons as $button): ?>
            <?php $buttonLabel = is_array($button) ? ($button['label'] ?? '') : (string) $button; ?>
            <?php $buttonUrl = is_array($button) ? ($button['url'] ?? '#contact') : '#contact'; ?>
            <?php if ($buttonLabel !== '' && (str_starts_with($buttonUrl, '#') || filter_var($buttonUrl, FILTER_VALIDATE_URL))): ?>
            <a class="btn" href="<?= htmlspecialchars($buttonUrl) ?>"><?= htmlspecialchars($buttonLabel) ?></a>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php else: ?>
        <a class="btn" href="#contact" data-i18n="btn">Prendre Rendez-vous</a>
        <?php endif; ?>
      </div>
    </section>

    <?php if (!empty($services)): ?>
    <section class="quick-access" aria-label="Quick access">
      <div class="quick-access-inner">
        <?php foreach (array_slice($services, 0, 4) as $service): ?>
        <a href="#services"><span class="quick-access-icon" aria-hidden="true">◆</span><span><?= htmlspecialchars($service['title']) ?></span></a>
        <?php endforeach; ?>
        <a href="#appointment"><span class="quick-access-icon" aria-hidden="true">◆</span><span>Appointment</span></a>
      </div>
    </section>
    <?php endif; ?>

    <?php if (trim($welcomeMessage) !== ''): ?>
    <section class="section welcome-section">
      <div class="empty-state">
        <p><?= nl2br(htmlspecialchars($welcomeMessage)) ?></p>
      </div>
    </section>
    <?php endif; ?>

    <section class="section ambassador-section" id="apropos">
      <div class="section-header">
        <span class="section-number">01</span>
        <div>
          <h2 data-i18n="ambassador_title">Message de l’Ambassade</h2>
          <p data-i18n="ambassador_desc">Une représentation officielle au service de la diplomatie, de la diaspora et des citoyens.</p>
        </div>
      </div>
      <div class="ambassador-block">
        <div class="ambassador-img-container">
          <img src="<?= htmlspecialchars(trim($ambassador['photo'] ?? '') ?: 'assets/images/embassador.jpeg') ?>" alt="<?= htmlspecialchars($ambassador['name']) ?>" class="ambassador-img">
        </div>
        <div class="ambassador-content">
          <?php if (trim($ambassadorPosition) !== ''): ?><p class="ambassador-role"><?= htmlspecialchars($ambassadorPosition) ?></p><?php endif; ?>
          <h3 class="ambassador-name"><?= htmlspecialchars($ambassadorName) ?></h3>
          <?php if (trim($ambassadorWelcome) !== ''): ?>
          <div class="ambassador-quote rich-content">&laquo; <?= renderRichText($ambassadorWelcome) ?> &raquo;</div>
          <?php endif; ?>
          <?php if (trim($ambassadorBiography) !== ''): ?>
          <div class="rich-content"><?= renderRichText($ambassadorBiography) ?></div>
          <?php endif; ?>
          <?php if (trim($ambassadorSignature) !== ''): ?><p class="content-meta"><?= htmlspecialchars($ambassadorSignature) ?></p><?php endif; ?>
          <p class="ambassador-quote">« <?= htmlspecialchars($ambassadorWelcome) ?> »</p>
        </div>
      </div>
    </section>

    <section class="section info-section">
      <div class="section-header">
        <span class="section-number">02</span>
        <div>
          <h2 data-i18n="about_title">Mission & Coordonnées</h2>
          <p data-i18n="about_desc">Les services consulaires, l’assistance aux citoyens et les informations officielles de l’ambassade.</p>
        </div>
      </div>
      <div class="info-grid">
        <article class="info-card">
          <p class="info-label">Mission</p>
          <p><?= htmlspecialchars($mission) ?></p>
        </article>
        <?php if (trim($vision) !== ''): ?>
        <article class="info-card">
          <p class="info-label">Vision</p>
          <p><?= htmlspecialchars($vision) ?></p>
        </article>
        <?php endif; ?>
        <?php if (trim($objectives) !== ''): ?>
        <article class="info-card">
          <p class="info-label">Objectives</p>
          <p><?= nl2br(htmlspecialchars($objectives)) ?></p>
        </article>
        <?php endif; ?>
        <article class="info-card">
          <p class="info-label">Coordonnées</p>
          <p><?= htmlspecialchars($settings['address']) ?></p>
          <p><?= htmlspecialchars($contacts['phone_numbers'] ?: $settings['phone_numbers']) ?></p>
          <p><?= htmlspecialchars($contacts['embassy_email'] ?: $settings['emails']) ?></p>
        </article>
        <article class="info-card">
          <p class="info-label">Horaires</p>
          <p><?= htmlspecialchars($contacts['office_hours'] ?: $settings['working_hours']) ?></p>
        </article>
      </div>
    </section>

    <section class="section services-section" id="services">
      <div class="section-header">
        <span class="section-number">03</span>
        <div>
          <h2 data-i18n="services_title">Services consulaires</h2>
          <p data-i18n="services_desc">Consultez les services proposés par l’ambassade.</p>
        </div>
      </div>
      <?php if (!empty($services)): ?>
      <div class="services-grid">
        <?php foreach ($services as $service): ?>
          <article class="service-card hover-card">
            <h3><?= htmlspecialchars($service['title']) ?></h3>
            <div class="rich-content"><?= renderRichText($service['description']) ?></div>
            <?php if (!empty($service['fees'])): ?><p><strong>Fees:</strong> <?= htmlspecialchars($service['fees']) ?></p><?php endif; ?>
            <?php if (!empty($service['processing_time'])): ?><p><strong>Processing time:</strong> <?= htmlspecialchars($service['processing_time']) ?></p><?php endif; ?>
            <?php if (!empty($service['office_hours'])): ?><p><strong>Office hours:</strong> <?= htmlspecialchars($service['office_hours']) ?></p><?php endif; ?>
            <?php if (!empty($service['requirements'])): ?><p><strong>Requirements:</strong> <?= nl2br(htmlspecialchars($service['requirements'])) ?></p><?php endif; ?>
            <?php if (!empty($service['required_documents'])): ?><p><strong>Documents:</strong> <?= nl2br(htmlspecialchars($service['required_documents'])) ?></p><?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <p>No published services are available at the moment.</p>
      <?php endif; ?>
    </section>

    <section class="section news-section" id="actualites">
      <div class="section-header">
        <span class="section-number">04</span>
        <div>
          <h2 data-i18n="news_title">Actualités & annonces</h2>
          <p data-i18n="news_desc">Retrouvez les dernières informations publiées par l’ambassade.</p>
        </div>
      </div>
      <?php if (empty($news) && empty($announcements)): ?>
      <div class="empty-state">
        <p>No published news is available at the moment.</p>
      </div>
      <?php else: ?>
      <div class="news-grid">
        <?php foreach ($news as $article): ?>
          <article class="news-card hover-card">
            <?php if (!empty($article['featured_image'])): ?>
            <img class="news-img" src="<?= htmlspecialchars($article['featured_image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
            <?php endif; ?>
            <div class="news-content">
              <p class="news-tag"><?= htmlspecialchars($article['category'] ?: 'News') ?></p>
              <h3><?= htmlspecialchars($article['title']) ?></h3>
              <div class="rich-content"><?= renderRichText($article['short_description']) ?></div>
              <?php if (trim($article['content'] ?? '') !== ''): ?>
              <details class="content-details"><summary>Read more</summary><div class="rich-content"><?= renderRichText($article['content']) ?></div></details>
              <?php endif; ?>
              <?php if (!empty($article['pdf_attachment'])): ?>
              <a class="text-link" href="<?= htmlspecialchars($article['pdf_attachment']) ?>" target="_blank" rel="noopener">Read attached document</a>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
        <?php foreach ($announcements as $announcement): ?>
          <article class="news-card hover-card">
            <?php if (!empty($announcement['image'])): ?>
            <img class="news-img" src="<?= htmlspecialchars($announcement['image']) ?>" alt="<?= htmlspecialchars($announcement['title']) ?>">
            <?php endif; ?>
            <div class="news-content">
              <p class="news-tag">Announcement</p>
              <h3><?= htmlspecialchars($announcement['title']) ?></h3>
              <div class="rich-content"><?= renderRichText($announcement['description']) ?></div>
              <?php if (!empty($announcement['pdf_attachment'])): ?>
              <a class="text-link" href="<?= htmlspecialchars($announcement['pdf_attachment']) ?>" target="_blank" rel="noopener">Read attached document</a>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </section>

    <?php if (!empty($events)): ?>
    <section class="section" id="events">
      <div class="section-header">
        <span class="section-number">05</span>
        <div><h2>Upcoming Events</h2><p>Official embassy events and public activities.</p></div>
      </div>
      <div class="news-grid">
        <?php foreach ($events as $event): ?>
        <article class="news-card hover-card">
          <?php if (!empty($event['cover_image'])): ?><img class="news-img" src="<?= htmlspecialchars($event['cover_image']) ?>" alt="<?= htmlspecialchars($event['title']) ?>"><?php endif; ?>
          <div class="news-content">
            <p class="news-tag"><?= htmlspecialchars(formatDate($event['event_date'])) ?> · <?= htmlspecialchars(substr($event['event_time'], 0, 5)) ?></p>
            <h3><?= htmlspecialchars($event['title']) ?></h3>
            <div class="rich-content"><?= renderRichText($event['description']) ?></div>
            <?php if (!empty($event['venue'])): ?><p class="content-meta"><?= htmlspecialchars($event['venue']) ?></p><?php endif; ?>
            <?php if (!empty($event['registration_link'])): ?><a class="text-link" href="<?= htmlspecialchars($event['registration_link']) ?>" target="_blank" rel="noopener">Register for this event</a><?php endif; ?>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($downloads)): ?>
    <section class="section" id="downloads">
      <div class="section-header">
        <span class="section-number">06</span>
        <div><h2>Documents & Downloads</h2><p>Official forms and publications available for download.</p></div>
      </div>
      <div class="services-grid">
        <?php foreach ($downloads as $download): ?>
        <article class="service-card hover-card">
          <p class="news-tag"><?= htmlspecialchars($download['category']) ?></p>
          <h3><?= htmlspecialchars($download['title']) ?></h3>
          <?php if (!empty($download['file_size'])): ?><p class="content-meta"><?= htmlspecialchars($download['file_size']) ?></p><?php endif; ?>
          <?php if (!empty($download['file_path'])): ?><a class="text-link" href="<?= htmlspecialchars($download['file_path']) ?>" target="_blank" rel="noopener">Download file</a><?php endif; ?>
        </article>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($gallery)): ?>
    <section class="section" id="gallery">
      <div class="section-header">
        <span class="section-number">07</span>
        <div><h2>Photo Gallery</h2><p>Featured moments from the embassy.</p></div>
      </div>
      <div class="gallery-grid">
        <?php foreach ($gallery as $image): ?>
        <figure class="gallery-item hover-card">
          <img src="<?= htmlspecialchars($image['image_path']) ?>" alt="<?= htmlspecialchars($image['alt_text'] ?: $image['title']) ?>">
          <?php if (!empty($image['title']) || !empty($image['caption'])): ?><figcaption><strong><?= htmlspecialchars($image['title']) ?></strong><?php if (!empty($image['caption'])): ?><span><?= htmlspecialchars($image['caption']) ?></span><?php endif; ?></figcaption><?php endif; ?>
        </figure>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <section class="section holidays-section">
      <div class="section-header">
        <span class="section-number">08</span>
        <div>
          <h2 data-i18n="holidays_title">Jours fériés</h2>
          <p data-i18n="holidays_desc">Les dates officielles à connaître pour les démarches et les visites.</p>
        </div>
      </div>
      <div class="info-grid">
        <?php foreach ($holidays as $holiday): ?>
          <article class="info-card">
            <p class="info-label"><?= htmlspecialchars($holiday['holiday_name']) ?></p>
            <p><?= htmlspecialchars($holiday['holiday_date']) ?></p>
            <p><?= htmlspecialchars($holiday['description']) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section contact-section" id="contact">
      <div class="section-header">
        <span class="section-number">09</span>
        <div>
          <h2 data-i18n="contact_title">Contact & rendez-vous</h2>
          <p data-i18n="contact_desc">Contactez l’ambassade pour toute demande consulaire ou administrative.</p>
        </div>
      </div>
      <div class="contact-grid">
        <form class="contact-form" id="contact-form">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <label>
            <span>Nom</span>
            <input type="text" name="name" required>
          </label>
          <label>
            <span>Email</span>
            <input type="email" name="email" required>
          </label>
          <label>
            <span>Message</span>
            <textarea name="message" rows="5" required></textarea>
          </label>
          <button class="btn" type="submit">Envoyer</button>
        </form>
        <div class="contact-info">
          <div class="contact-card">
            <h3>Téléphone</h3>
            <p><?= htmlspecialchars($contacts['hotline'] ?: $settings['phone_numbers']) ?></p>
          </div>
          <div class="contact-card">
            <h3>Email</h3>
            <p><?= htmlspecialchars($contacts['embassy_email'] ?: $settings['emails']) ?></p>
          </div>
          <div class="contact-card">
            <h3>Horaires</h3>
            <p><?= htmlspecialchars($contacts['office_hours'] ?: $settings['working_hours']) ?></p>
          </div>
          <?php if (!empty($contacts['whatsapp'])): ?><div class="contact-card"><h3>WhatsApp</h3><p><?= htmlspecialchars($contacts['whatsapp']) ?></p></div><?php endif; ?>
          <?php if (!empty($contacts['duty_officer'])): ?><div class="contact-card"><h3>Duty Officer</h3><p><?= htmlspecialchars($contacts['duty_officer']) ?></p></div><?php endif; ?>
          <?php if (!empty($contacts['emergency_instructions'])): ?><div class="contact-card"><h3>Emergency Information</h3><p><?= nl2br(htmlspecialchars($contacts['emergency_instructions'])) ?></p></div><?php endif; ?>
          <?php $mapLink = trim(($contacts['google_maps'] ?? '') ?: ($settings['google_maps'] ?? '')); ?>
          <?php if ($mapLink !== '' && filter_var($mapLink, FILTER_VALIDATE_URL)): ?><div class="contact-card"><h3>Location</h3><a class="text-link" href="<?= htmlspecialchars($mapLink) ?>" target="_blank" rel="noopener">Open map</a></div><?php endif; ?>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="footer-inner">
      <p><?= htmlspecialchars($settings['copyright']) ?></p>
      <p><?= htmlspecialchars($settings['footer_info']) ?></p>
      <?php $socialLinks = array_filter(['Facebook' => $settings['facebook'] ?? '', 'Instagram' => $settings['instagram'] ?? '', 'Twitter' => $settings['twitter'] ?? '', 'LinkedIn' => $settings['linkedin'] ?? ''], static fn($url) => filter_var($url, FILTER_VALIDATE_URL)); ?>
      <?php if ($socialLinks): ?><p class="social-links"><?php foreach ($socialLinks as $label => $url): ?><a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($label) ?></a><?php endforeach; ?></p><?php endif; ?>
    </div>
  </footer>

  <script src="script.js?v=2"></script>
  <script>
    document.querySelectorAll('img').forEach((image) => image.addEventListener('error', () => {
      if (!image.dataset.fallbackApplied) { image.dataset.fallbackApplied = 'true'; image.src = 'assets/images/congo%20flag.jpg'; }
    }));
    document.getElementById('contact-form')?.addEventListener('submit', async function (event) {
      event.preventDefault();
      const form = event.currentTarget;
      const data = new FormData(form);
      const response = await fetch('api/contact.php', { method: 'POST', body: data });
      const result = await response.json();
      alert(result.message || 'Message sent');
      if (result.success) form.reset();
    });
  </script>
</body>
</html>
