<?php
function auth_page_start(string $title, string $eyebrow, string $headline, string $copy, string $pageClass = ''): void {
    $slides = [
        ['../img/college class.jpg', 'Medical students learning together in class'],
        ['../img/school/pltci-anatomy.jpg', 'Medical students studying anatomy'],
        ['../img/school/tmtcs-med-students.webp', 'Doctor of Medicine students on campus'],
        ['../img/graduation.png', 'Medical graduates celebrating their achievement'],
    ];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="theme-color" content="#16055D">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-FZ8QPG3B9T"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-FZ8QPG3B9T');
    </script>
    <title><?= esc($title) ?> | Medcon</title>
    <link rel="icon" href="../img/favicon/favicon.ico" sizes="any">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="assets/css/halftone.css">
</head>
<body class="mc-auth-page <?= esc($pageClass) ?>">
<a class="mc-skip" href="#auth-form">Skip to form</a>
<main class="mc-auth-shell">
    <section class="mc-auth-visual" aria-label="Medcon student community">
        <div class="mc-auth-slides" data-auth-slider>
            <?php foreach ($slides as $index => $slide): ?>
                <div class="mc-auth-slide mc-halftone<?= $index === 0 ? ' is-active' : '' ?>" data-halftone>
                    <img class="mc-halftone-source" src="<?= esc($slide[0]) ?>" alt="<?= esc($slide[1]) ?>"<?= $index === 0 ? '' : ' loading="lazy"' ?>>
                    <canvas class="mc-halftone-canvas" aria-hidden="true"></canvas>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mc-auth-visual-overlay"></div>
        <div class="mc-auth-story" tabindex="0"><span><?= esc($eyebrow) ?></span><h2><?= esc($headline) ?></h2><p><?= esc($copy) ?></p></div>
        <div class="mc-auth-slide-status" aria-hidden="true"><span data-auth-progress></span></div>
    </section>
    <section class="mc-auth-panel" id="auth-form">
        <a class="mc-auth-brand" href="../index.php" aria-label="Medcon home"><img src="../img/logo-icon-blue.svg" alt=""><strong>Medcon Edu</strong></a>
        <div class="mc-auth-panel-inner">
<?php }

function auth_page_end(): void { ?>
        </div>
        <footer class="mc-auth-footer"><span>Medical Consultants Education Nigeria Limited</span><div><a href="../privacy-policy.php">Privacy</a><a href="../terms.php">Terms</a><a href="../contact.php">Support</a></div></footer>
    </section>
</main>
<script src="assets/js/halftone.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
<?php }
