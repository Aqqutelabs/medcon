<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($description) ?>">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="img/hero-img.png">
    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css">
    <link rel="preload" href="assets/css/site.css" as="style">
    <link rel="stylesheet" href="assets/css/site.css">
    <script defer src="scripts/site.js"></script>
</head>
<body<?= $bodyClass !== '' ? ' class="' . e($bodyClass) . '"' : '' ?>>
    <a class="skip-link" href="#main">Skip to content</a>
    <header class="site-header" data-header>
        <div class="top-bar">
            <div class="container top-bar-inner">
                <span>Study Medicine Abroad | Philippines | Admissions Support</span>
                <nav class="top-links" aria-label="Utility navigation">
                    <?php foreach ($topLinks as $item): ?>
                        <a href="<?= e(site_url($item['href'])) ?>"><?= e($item['label']) ?></a>
                    <?php endforeach; ?>
                </nav>
            </div>
        </div>
        <div class="container header-inner">
            <a class="brand" href="<?= e(site_url('index.php')) ?>" aria-label="MedCon home">
                <img src="img/logo.svg" alt="MedCon" class="brand-logo">
            </a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" data-menu-toggle>
                <span></span><span></span><span></span><span class="sr-only">Open menu</span>
            </button>
            <nav class="primary-nav" id="primary-nav" data-nav>
                <?php foreach ($navItems as $item): ?>
                    <a class="<?= $active === $item['href'] ? 'active' : '' ?>" href="<?= e(site_url($item['href'])) ?>"><?= e($item['label']) ?></a>
                <?php endforeach; ?>
                <a class="btn btn-primary nav-cta" href="<?= e(site_url('apply.php')) ?>">Apply Now</a>
            </nav>
        </div>
    </header>
    <main id="main">
        <a class="announcement-strip" href="<?= e(site_url('apply.php')) ?>">2026 Admissions: Register your interest for priority guidance now</a>
