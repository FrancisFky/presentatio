<?php
require_once __DIR__ . '/../config/database.php';
$aboutSlug = $aboutSlug ?? '';
$allowedSlugs = ['about-congo', 'about-embassy', 'invest-in-congo'];
if (!in_array($aboutSlug, $allowedSlugs, true)) { http_response_code(404); exit('Page not found.'); }
$lang = ($_GET['lang'] ?? 'fr') === 'en' ? 'en' : 'fr';
$fallbackTitles = ['about-congo' => ['fr' => 'À propos du Congo', 'en' => 'About Congo'], 'about-embassy' => ['fr' => 'À propos de l’Ambassade', 'en' => 'About the Embassy'], 'invest-in-congo' => ['fr' => 'Investir au Congo', 'en' => 'Invest in Congo']];
$page = null;
$aboutPagesAvailable = false;
if (dbReady()) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM about_pages WHERE slug = ? AND status = 'published' LIMIT 1");
        $stmt->execute([$aboutSlug]);
        $page = $stmt->fetch();
        $aboutPagesAvailable = true;
    } catch (PDOException $e) {
        // The site remains available until the CMS migration is applied.
        $page = null;
    }
}
$title = $page['title_' . $lang] ?? $fallbackTitles[$aboutSlug][$lang];
$content = $page['content_' . $lang] ?? '';
$hero = trim($page['hero_image'] ?? '') ?: 'assets/images/embassy photo.png';
$settings = dbReady() ? ($pdo->query('SELECT * FROM website_settings ORDER BY id DESC LIMIT 1')->fetch() ?: []) : [];
$labels = $lang === 'en' ? ['home'=>'Home','about'=>'About','services'=>'Services','news'=>'News','contact'=>'Contact','crumb'=>'About'] : ['home'=>'Accueil','about'=>'À Propos','services'=>'Services','news'=>'Actualités','contact'=>'Contact','crumb'=>'À Propos'];
?>
<!doctype html><html lang="<?= $lang ?>"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= htmlspecialchars($title) ?> | <?= htmlspecialchars($settings['embassy_name'] ?? 'Embassy of the Republic of Congo in Kenya') ?></title><link rel="stylesheet" href="styles.css?v=3"><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"></head>
<body><div class="top-bar"><?= htmlspecialchars($settings['embassy_name'] ?? 'Embassy of the Republic of Congo in Kenya') ?></div><header class="header"><div class="nav-container"><a class="brand" href="index.php"><div class="logo-container"><img src="<?= htmlspecialchars($settings['logo_path'] ?? 'assets/images/armoiries-congo.jpg') ?>" alt="Coat of arms" class="logo-img"></div><div class="brand-text"><p class="eyebrow">République du Congo</p><h1 class="logo">Ambassade au Kenya</h1></div></a><button class="nav-toggle" type="button" aria-label="Toggle navigation"><span></span><span></span><span></span></button><nav class="navigation"><a href="index.php"><?= $labels['home'] ?></a><div class="nav-dropdown"><button class="nav-dropdown-toggle" type="button" aria-expanded="false" aria-controls="about-menu"><?= $labels['about'] ?></button><div class="nav-dropdown-menu" id="about-menu"><a href="about-congo.php?lang=<?= $lang ?>" class="<?= $aboutSlug === 'about-congo' ? 'active' : '' ?>"><?= $lang === 'en' ? 'About Congo' : 'À propos du Congo' ?></a><a href="about-embassy.php?lang=<?= $lang ?>" class="<?= $aboutSlug === 'about-embassy' ? 'active' : '' ?>"><?= $lang === 'en' ? 'About the Embassy' : 'À propos de l’Ambassade' ?></a><a href="invest-in-congo.php?lang=<?= $lang ?>" class="<?= $aboutSlug === 'invest-in-congo' ? 'active' : '' ?>"><?= $lang === 'en' ? 'Invest in Congo' : 'Investir au Congo' ?></a></div></div><a href="index.php#services"><?= $labels['services'] ?></a><a href="index.php#actualites"><?= $labels['news'] ?></a><a href="index.php#contact"><?= $labels['contact'] ?></a></nav><div class="lang"><a class="<?= $lang === 'fr' ? 'active' : '' ?>" href="<?= htmlspecialchars(basename($_SERVER['PHP_SELF'])) ?>?lang=fr">FR</a><a class="<?= $lang === 'en' ? 'active' : '' ?>" href="<?= htmlspecialchars(basename($_SERVER['PHP_SELF'])) ?>?lang=en">EN</a></div></div></header><main><section class="about-hero" style="background-image:linear-gradient(rgba(11,21,33,.72),rgba(17,34,51,.68)),url('<?= htmlspecialchars($hero, ENT_QUOTES) ?>')"><div><p class="breadcrumb"><a href="index.php"><?= $labels['home'] ?></a> / <?= $labels['crumb'] ?></p><h2><?= htmlspecialchars($title) ?></h2></div></section><section class="about-content section"><div class="about-content-card"><?php if ($content !== ''): ?><div class="rich-content"><?= renderRichText($content) ?></div><?php else: ?><div class="empty-state"><p><?= $lang === 'en' ? 'This page is being prepared. Please check back soon.' : 'Cette page est en cours de préparation. Revenez bientôt.' ?></p></div><?php endif; ?></div></section></main><footer class="footer"><p><?= htmlspecialchars($settings['copyright'] ?? '© Embassy of the Republic of Congo in Kenya') ?></p><p><?= htmlspecialchars($settings['footer_info'] ?? '') ?></p></footer><script src="script.js"></script></body></html>
