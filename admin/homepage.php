<?php

require_once __DIR__ . '/../config/database.php';

requireLogin();

$flash = getFlash();

/*
|--------------------------------------------------------------------------
| Load Homepage Content
|--------------------------------------------------------------------------
| The homepage_content table is treated as a singleton.
| The latest record is used if multiple records exist.
*/
$content = getLatestHomepageContent();

$homepageHasLanguageColumns = false;
if (dbReady()) {
    try {
        $columnCheck = $pdo->query("SHOW COLUMNS FROM homepage_content LIKE 'hero_title_fr'")->fetch();
        $homepageHasLanguageColumns = $columnCheck !== false;
    } catch (PDOException $e) {
        $homepageHasLanguageColumns = false;
    }
}

/*
|--------------------------------------------------------------------------
| Handle Form Submission
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verifyCsrf();

    $heroTitleFr = trim($_POST['hero_title_fr'] ?? '');
    $heroTitleEn = trim($_POST['hero_title_en'] ?? '');
    $heroSubtitleFr = trim($_POST['hero_subtitle_fr'] ?? '');
    $heroSubtitleEn = trim($_POST['hero_subtitle_en'] ?? '');
    $welcomeMessageFr = trim($_POST['welcome_message_fr'] ?? '');
    $welcomeMessageEn = trim($_POST['welcome_message_en'] ?? '');
    $missionFr = trim($_POST['mission_fr'] ?? '');
    $missionEn = trim($_POST['mission_en'] ?? '');
    $visionFr = trim($_POST['vision_fr'] ?? '');
    $visionEn = trim($_POST['vision_en'] ?? '');
    $objectivesFr = trim($_POST['objectives_fr'] ?? '');
    $objectivesEn = trim($_POST['objectives_en'] ?? '');
    $homepageButtons = trim($_POST['homepage_buttons'] ?? '');

    $heroTitle = $heroTitleFr !== '' ? $heroTitleFr : $heroTitleEn;
    $heroSubtitle = $heroSubtitleFr !== '' ? $heroSubtitleFr : $heroSubtitleEn;
    $welcomeMessage = $welcomeMessageFr !== '' ? $welcomeMessageFr : $welcomeMessageEn;
    $mission = $missionFr !== '' ? $missionFr : $missionEn;
    $vision = $visionFr !== '' ? $visionFr : $visionEn;
    $objectives = $objectivesFr !== '' ? $objectivesFr : $objectivesEn;

    /*
    | Preserve the existing hero image unless a new image
    | is successfully uploaded.
    */
    $heroImage = $content['hero_image'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | Hero Image Upload
    |--------------------------------------------------------------------------
    */
    if (!empty($_FILES['hero_image']['name'])) {

        $upload = handleUpload(
            'hero_image',
            __DIR__ . '/../uploads/homepage',
            'image'
        );

        if ($upload['success']) {

            $heroImage = str_replace(
                __DIR__ . '/../',
                '',
                $upload['path']
            );

        } else {

            flash('error', $upload['message']);

            header('Location: homepage.php');
            exit;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Existing Homepage Record
    |--------------------------------------------------------------------------
    */
    if ($content && isset($content['id']) && $content['id']) {

        if ($homepageHasLanguageColumns) {
            $stmt = $pdo->prepare(
                'UPDATE homepage_content
                 SET
                    hero_title = ?,
                    hero_title_fr = ?,
                    hero_title_en = ?,
                    hero_subtitle = ?,
                    hero_subtitle_fr = ?,
                    hero_subtitle_en = ?,
                    hero_image = ?,
                    welcome_message = ?,
                    welcome_message_fr = ?,
                    welcome_message_en = ?,
                    mission = ?,
                    mission_fr = ?,
                    mission_en = ?,
                    vision = ?,
                    vision_fr = ?,
                    vision_en = ?,
                    objectives = ?,
                    objectives_fr = ?,
                    objectives_en = ?,
                    homepage_buttons = ?
                 WHERE id = ?'
            );

            $stmt->execute([
                $heroTitle,
                $heroTitleFr,
                $heroTitleEn,
                $heroSubtitle,
                $heroSubtitleFr,
                $heroSubtitleEn,
                $heroImage,
                $welcomeMessage,
                $welcomeMessageFr,
                $welcomeMessageEn,
                $mission,
                $missionFr,
                $missionEn,
                $vision,
                $visionFr,
                $visionEn,
                $objectives,
                $objectivesFr,
                $objectivesEn,
                $homepageButtons,
                $content['id']
            ]);
        } else {
            $stmt = $pdo->prepare(
                'UPDATE homepage_content
                 SET
                    hero_title = ?,
                    hero_subtitle = ?,
                    hero_image = ?,
                    welcome_message = ?,
                    mission = ?,
                    vision = ?,
                    objectives = ?,
                    homepage_buttons = ?
                 WHERE id = ?'
            );

            $stmt->execute([
                $heroTitle,
                $heroSubtitle,
                $heroImage,
                $welcomeMessage,
                $mission,
                $vision,
                $objectives,
                $homepageButtons,
                $content['id']
            ]);
        }

    /*
    |--------------------------------------------------------------------------
    | Create Homepage Record If None Exists
    |--------------------------------------------------------------------------
    */
    } else {

        if ($homepageHasLanguageColumns) {
            $stmt = $pdo->prepare(
                'INSERT INTO homepage_content
                    (
                        hero_title,
                        hero_title_fr,
                        hero_title_en,
                        hero_subtitle,
                        hero_subtitle_fr,
                        hero_subtitle_en,
                        hero_image,
                        welcome_message,
                        welcome_message_fr,
                        welcome_message_en,
                        mission,
                        mission_fr,
                        mission_en,
                        vision,
                        vision_fr,
                        vision_en,
                        objectives,
                        objectives_fr,
                        objectives_en,
                        homepage_buttons
                    )
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );

            $stmt->execute([
                $heroTitle,
                $heroTitleFr,
                $heroTitleEn,
                $heroSubtitle,
                $heroSubtitleFr,
                $heroSubtitleEn,
                $heroImage,
                $welcomeMessage,
                $welcomeMessageFr,
                $welcomeMessageEn,
                $mission,
                $missionFr,
                $missionEn,
                $vision,
                $visionFr,
                $visionEn,
                $objectives,
                $objectivesFr,
                $objectivesEn,
                $homepageButtons
            ]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO homepage_content
                    (
                        hero_title,
                        hero_subtitle,
                        hero_image,
                        welcome_message,
                        mission,
                        vision,
                        objectives,
                        homepage_buttons
                    )
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );

            $stmt->execute([
                $heroTitle,
                $heroSubtitle,
                $heroImage,
                $welcomeMessage,
                $mission,
                $vision,
                $objectives,
                $homepageButtons
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    */
    $pdo->prepare(
        'INSERT INTO activity_logs
            (user_id, action, details)
         VALUES (?, ?, ?)'
    )->execute([
        $_SESSION['user_id'],
        'Updated homepage content',
        'Homepage settings were updated'
    ]);

    flash(
        'success',
        __admin('flash.homepage_updated')
    );

    header('Location: homepage.php');
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);

?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars(adminCurrentLanguage()) ?>">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= htmlspecialchars(__admin('homepage.title')) ?> - Embassy CMS</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- Embassy CMS Admin Styles -->
    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >

</head>

<body>

<div class="d-flex min-vh-100">

    <!-- Sidebar -->
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-grow-1 bg-light">

        <!-- Header -->
        <?php include __DIR__ . '/partials/header.php'; ?>

        <!-- Page Content -->
        <main class="p-4">

            <!-- Page Heading -->
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h3 class="fw-bold mb-1">
                        <?= htmlspecialchars(__admin('homepage.title')) ?>
                    </h3>

                    <p class="text-muted mb-0">
                        <?= htmlspecialchars(__admin('homepage.description')) ?>
                    </p>
                </div>

            </div>

            <!-- Flash Message -->
            <?php if ($flash): ?>

                <div
                    class="alert alert-<?= htmlspecialchars($flash['type']) ?>"
                    role="alert"
                >
                    <?= htmlspecialchars($flash['message']) ?>
                </div>

            <?php endif; ?>

            <!-- Homepage Form -->
            <form
                method="post"
                enctype="multipart/form-data"
                class="card p-4 shadow-sm border-0"
            >

                <!-- CSRF Protection -->
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars(csrfToken()) ?>"
                >

                <div class="row g-4">

                    <!-- Hero Title (FR) -->
                    <div class="col-md-6">

                        <label
                            for="hero_title_fr"
                            class="form-label"
                        >
                            <?= htmlspecialchars(__admin('homepage.hero_title_fr')) ?>
                        </label>

                        <input
                            type="text"
                            id="hero_title_fr"
                            class="form-control"
                            name="hero_title_fr"
                            value="<?= htmlspecialchars($content['hero_title_fr'] ?? $content['hero_title'] ?? '') ?>"
                            required
                        >

                    </div>

                    <!-- Hero Title (EN) -->
                    <div class="col-md-6">

                        <label
                            for="hero_title_en"
                            class="form-label"
                        >
                            <?= htmlspecialchars(__admin('homepage.hero_title_en')) ?>
                        </label>

                        <input
                            type="text"
                            id="hero_title_en"
                            class="form-control"
                            name="hero_title_en"
                            value="<?= htmlspecialchars($content['hero_title_en'] ?? $content['hero_title'] ?? '') ?>"
                            required
                        >

                    </div>

                    <!-- Hero Subtitle (FR) -->
                    <div class="col-md-6">

                        <label
                            for="hero_subtitle_fr"
                            class="form-label"
                        >
                            <?= htmlspecialchars(__admin('homepage.hero_subtitle_fr')) ?>
                        </label>

                        <input
                            type="text"
                            id="hero_subtitle_fr"
                            class="form-control"
                            name="hero_subtitle_fr"
                            value="<?= htmlspecialchars($content['hero_subtitle_fr'] ?? $content['hero_subtitle'] ?? '') ?>"
                            required
                        >

                    </div>

                    <!-- Hero Subtitle (EN) -->
                    <div class="col-md-6">

                        <label
                            for="hero_subtitle_en"
                            class="form-label"
                        >
                            <?= htmlspecialchars(__admin('homepage.hero_subtitle_en')) ?>
                        </label>

                        <input
                            type="text"
                            id="hero_subtitle_en"
                            class="form-control"
                            name="hero_subtitle_en"
                            value="<?= htmlspecialchars($content['hero_subtitle_en'] ?? $content['hero_subtitle'] ?? '') ?>"
                            required
                        >

                    </div>

                    <!-- Hero Image -->
                    <div class="col-12">

                        <label
                            for="hero_image"
                            class="form-label"
                        >
                            <?= htmlspecialchars(__admin('homepage.hero_image')) ?>
                        </label>

                        <input
                            type="file"
                            id="hero_image"
                            class="form-control"
                            name="hero_image"
                            accept="image/*"
                        >

                        <?php if (!empty($content['hero_image'])): ?>

                            <div class="form-text">
                                <?= htmlspecialchars(__admin('homepage.current_image')) ?>
                                <?= htmlspecialchars($content['hero_image']) ?>
                            </div>

                        <?php endif; ?>

                    </div>

                    <!-- Welcome Message (FR) -->
                    <div class="col-md-6">

                        <label
                            for="welcome_message_fr"
                            class="form-label"
                        >
                            <?= htmlspecialchars(__admin('homepage.welcome_message_fr')) ?>
                        </label>

                        <textarea
                            id="welcome_message_fr"
                            class="form-control"
                            rows="4"
                            name="welcome_message_fr"
                        ><?= htmlspecialchars($content['welcome_message_fr'] ?? $content['welcome_message'] ?? '') ?></textarea>

                    </div>

                    <!-- Welcome Message (EN) -->
                    <div class="col-md-6">

                        <label
                            for="welcome_message_en"
                            class="form-label"
                        >
                            <?= htmlspecialchars(__admin('homepage.welcome_message_en')) ?>
                        </label>

                        <textarea
                            id="welcome_message_en"
                            class="form-control"
                            rows="4"
                            name="welcome_message_en"
                        ><?= htmlspecialchars($content['welcome_message_en'] ?? $content['welcome_message'] ?? '') ?></textarea>

                    </div>

                    <!-- Mission (FR) -->
                    <div class="col-md-6">

                        <label
                            for="mission_fr"
                            class="form-label"
                        >
                            <?= htmlspecialchars(__admin('homepage.mission_fr')) ?>
                        </label>

                        <textarea
                            id="mission_fr"
                            class="form-control"
                            rows="4"
                            name="mission_fr"
                        ><?= htmlspecialchars($content['mission_fr'] ?? $content['mission'] ?? '') ?></textarea>

                    </div>

                    <!-- Mission (EN) -->
                    <div class="col-md-6">

                        <label
                            for="mission_en"
                            class="form-label"
                        >
                            <?= htmlspecialchars(__admin('homepage.mission_en')) ?>
                        </label>

                        <textarea
                            id="mission_en"
                            class="form-control"
                            rows="4"
                            name="mission_en"
                        ><?= htmlspecialchars($content['mission_en'] ?? $content['mission'] ?? '') ?></textarea>

                    </div>

                    <!-- Vision (FR) -->
                    <div class="col-md-6">

                        <label
                            for="vision_fr"
                            class="form-label"
                        >
                            <?= htmlspecialchars(__admin('homepage.vision_fr')) ?>
                        </label>

                        <textarea
                            id="vision_fr"
                            class="form-control"
                            rows="4"
                            name="vision_fr"
                        ><?= htmlspecialchars($content['vision_fr'] ?? $content['vision'] ?? '') ?></textarea>

                    </div>

                    <!-- Vision (EN) -->
                    <div class="col-md-6">

                        <label
                            for="vision_en"
                            class="form-label"
                        >
                            <?= htmlspecialchars(__admin('homepage.vision_en')) ?>
                        </label>

                        <textarea
                            id="vision_en"
                            class="form-control"
                            rows="4"
                            name="vision_en"
                        ><?= htmlspecialchars($content['vision_en'] ?? $content['vision'] ?? '') ?></textarea>

                    </div>

                    <!-- Objectives (FR) -->
                    <div class="col-md-6">

                        <label
                            for="objectives_fr"
                            class="form-label"
                        >
                            <?= htmlspecialchars(__admin('homepage.objectives_fr')) ?>
                        </label>

                        <textarea
                            id="objectives_fr"
                            class="form-control"
                            rows="4"
                            name="objectives_fr"
                        ><?= htmlspecialchars($content['objectives_fr'] ?? $content['objectives'] ?? '') ?></textarea>

                    </div>

                    <!-- Objectives (EN) -->
                    <div class="col-md-6">

                        <label
                            for="objectives_en"
                            class="form-label"
                        >
                            <?= htmlspecialchars(__admin('homepage.objectives_en')) ?>
                        </label>

                        <textarea
                            id="objectives_en"
                            class="form-control"
                            rows="4"
                            name="objectives_en"
                        ><?= htmlspecialchars($content['objectives_en'] ?? $content['objectives'] ?? '') ?></textarea>

                    </div>

                    <!-- Homepage Buttons -->
                    <div class="col-md-6">

                        <label
                            for="homepage_buttons"
                            class="form-label"
                        >
                            <?= htmlspecialchars(__admin('homepage.buttons')) ?>
                        </label>

                        <textarea
                            id="homepage_buttons"
                            class="form-control"
                            rows="3"
                            name="homepage_buttons"
                        ><?= htmlspecialchars($content['homepage_buttons'] ?? '') ?></textarea>

                        <div class="form-text">
                            <?= htmlspecialchars(__admin('homepage.compatibility_text')) ?>
                        </div>

                    </div>

                </div>

                <!-- Submit -->
                <div class="mt-4">

                    <button
                        class="btn btn-primary"
                        type="submit"
                    >
                        <i class="fa-solid fa-save me-2"></i>
                        <?= htmlspecialchars(__admin('homepage.save')) ?>
                    </button>

                </div>

            </form>

        </main>

    </div>

</div>

<!-- Bootstrap JS -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>