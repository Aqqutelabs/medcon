<?php
require_once __DIR__ . '/links.php';

$whatsappNumber = '2347036961056';
$whatsappMessage = rawurlencode('Hello Medcon, I would like to make enquiries about studying medicine abroad.');
$whatsappUrl = "https://wa.me/{$whatsappNumber}?text={$whatsappMessage}";

$navItems = [
    ['label' => 'About', 'href' => 'about.php'],
    ['label' => 'Study Medicine in the Philippines', 'href' => 'study-medicine-in-philippines.php'],
    ['label' => 'Partner Colleges', 'href' => 'colleges.php'],
    ['label' => 'Contact', 'href' => 'contact.php'],
];

$topLinks = [
    ['label' => 'International students', 'href' => 'study-medicine-in-philippines.php'],
    ['label' => 'Work with us', 'href' => 'for-agents.php'],
    ['label' => 'Parents', 'href' => 'for-parents.php'],
    ['label' => 'Sign In', 'href' => 'app/login.php'],
];

$collegeData = [
    [
        'slug' => 'pltci-college-of-medicine',
        'name' => 'PLTCI College of Medicine',
        'shortName' => 'PLTCI',
        'location' => 'Solano, Nueva Vizcaya, Philippines',
        'country' => 'Philippines',
        'imageBadges' => ['WDOMS Listed', 'English-Medium', 'Clinical Training'],
        'programmes' => ['Doctor of Medicine (MD)'],
        'tuitionLabel' => 'Tuition / year',
        'tuition' => 'From $3,000',
        'durationLabel' => 'Duration',
        'duration' => '4-Year MD',
        'intakeLabel' => 'Intakes',
        'intakes' => 'Contact Admissions',
        'image' => 'img/PLTCI-img.png',
        'imageAlt' => 'PLTCI College of Medicine logo',
        'summary' => 'A structured medical programme combining academic foundations, clinical learning, faculty mentorship and practical patient exposure.',
        'additionalFeeNote' => 'Pre-Med: . Final fifth-year semester: ₱120,000.',
        'applicationSupport' => 'Admission, visa, travel and student settlement support provided by Medical Consultants Education Nigeria Limited.',
        'feeDisclaimer' => 'The published fees are indicative, and detailed fee structures are available.',
    ],
    [
        'slug' => 'the-manila-times-college-school-of-medicine',
        'name' => 'The Manila Times College School of Medicine',
        'shortName' => 'TMTC School of Medicine',
        'location' => 'Subic Bay Freeport Zone, Zambales, Philippines',
        'country' => 'Philippines',
        'imageBadges' => ['English-Medium', 'Clinical Internship', 'International Students'],
        'programmes' => ['Doctor of Medicine (MD)'],
        'tuitionLabel' => 'Tuition / year',
        'tuition' => 'From $4,000',
        'durationLabel' => 'Duration',
        'duration' => '4.5 Years + 1-Year Internship',
        'intakeLabel' => 'Intakes',
        'intakes' => 'Contact Admissions',
        'image' => 'img/SCIME-img.png',
        'imageAlt' => 'The Manila Times College School of Medicine building and student environment',
        'summary' => 'An English-medium medical programme offering structured academic learning, practical training and a one-year clinical internship.',
        'additionalFeeNote' => 'Pre-Med: . Final fifth-year semester: ₱120,000.',
        'applicationSupport' => 'Admission, visa, travel and student settlement support provided by Medical Consultants Education Nigeria Limited.',
        'feeDisclaimer' => 'Published fees are indicative and subject to confirmation by the institution.',
    ],
];

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function form_label($text, $required = false) {
?>
    <span class="form-label">
        <?= e($text) ?>
        <?php if ($required): ?>
            <span class="required-marker" aria-hidden="true">*</span><span class="sr-only"> (required)</span>
        <?php else: ?>
            <span class="optional-marker">(optional)</span>
        <?php endif; ?>
    </span>
<?php
}

function site_url($path = '') {
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    $basePath = $basePath === '.' ? '' : $basePath;

    // Some shared-hosting configurations expose the project folder twice
    // (for example /MedCon/MedCon/index.php). Collapse adjacent duplicate
    // directory names so generated links always point to a single site root.
    $segments = array_values(array_filter(explode('/', $basePath), 'strlen'));
    $normalizedSegments = [];
    foreach ($segments as $segment) {
        $previous = end($normalizedSegments);
        if ($previous !== false && strcasecmp($previous, $segment) === 0) {
            continue;
        }
        $normalizedSegments[] = $segment;
    }
    $basePath = $normalizedSegments ? '/' . implode('/', $normalizedSegments) : '';

    // School microsite pages live two levels below the public site root.
    // Keep shared navigation and assets rooted at Medcon instead of the
    // current school directory.
    $schoolIndex = array_search('schools', $normalizedSegments, true);
    if ($schoolIndex !== false) {
        $rootSegments = array_slice($normalizedSegments, 0, $schoolIndex);
        $basePath = $rootSegments ? '/' . implode('/', $rootSegments) : '';
    }

    return ($basePath === '' ? '' : $basePath) . '/' . ltrim((string) $path, '/');
}

function render_header($title, $description, $active = '', $bodyClass = '') {
    global $topLinks, $navItems, $whatsappUrl;
    require __DIR__ . '/header.php';
}

function render_breadcrumb($current, $parent = null, $parentHref = null) {
?>
        <div class="breadcrumb-bar">
            <div class="container">
                <a href="<?= e(site_url('index.php')) ?>">Home</a>
                <?php if ($parent && $parentHref): ?>
                    <span><a href="<?= e(site_url($parentHref)) ?>"><?= e($parent) ?></a></span>
                <?php endif; ?>
                <strong><?= e($current) ?></strong>
            </div>
        </div>
<?php
}

function render_page_hero($badge, $headline, $body, $primary = null, $secondary = null, $visual = 'default') {
?>
        <section class="page-hero<?= $visual === 'philippines-bento' ? ' page-hero-bento' : '' ?>">
            <div class="container page-hero-grid">
                <div>
                    <span class="eyebrow"><?= e($badge) ?></span>
                    <h1><?= e($headline) ?></h1>
                    <p><?= e($body) ?></p>
                    <?php if ($primary || $secondary): ?>
                        <div class="hero-actions">
                            <?php if ($primary): ?><a class="btn btn-primary" href="<?= e($primary['href']) ?>"><?= e($primary['label']) ?></a><?php endif; ?>
                            <?php if ($secondary): ?><a class="btn btn-secondary" href="<?= e($secondary['href']) ?>"<?= !empty($secondary['external']) ? ' target="_blank" rel="noopener"' : '' ?>><?= e($secondary['label']) ?></a><?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ($visual === 'philippines-bento'): ?>
                    <?php
                    $bentoColumns = [
                        ['Phi-hero-0.jpg', 'Phi-hero-3.jpg', 'Phi-hero-6.jpg', 'Phi-hero-9.jpg'],
                        ['Phi-hero-1.jpg', 'Phi-hero-4.jpg', 'Phi-hero-7.jpg', 'Phi-hero-11.jpg'],
                        ['Phi-hero-2.jpg', 'Phi-hero-5.jpg', 'Phi-hero-8.jpg', 'Phi-hero-12.png'],
                    ];
                    ?>
                    <div class="hero-bento" aria-label="Student life and medical education in the Philippines">
                        <?php foreach ($bentoColumns as $columnIndex => $images): ?>
                            <div class="hero-bento-column hero-bento-column-<?= $columnIndex + 1 ?>">
                                <div class="hero-bento-track">
                                    <?php for ($copy = 0; $copy < 2; $copy++): ?>
                                        <?php foreach ($images as $image): ?>
                                            <img src="img/<?= e($image) ?>" alt="" loading="<?= $copy === 0 ? 'eager' : 'lazy' ?>" aria-hidden="true">
                                        <?php endforeach; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <?php
                    $heroImages = [
                        'colleges' => ['src' => 'img/colleges.png', 'alt' => 'Medcon partner medical colleges'],
                        'contact' => ['src' => 'img/counsel.jpg', 'alt' => 'Medical education counselling and admissions support'],
                    ];
                    $heroImage = $heroImages[$visual] ?? ['src' => 'img/hero-img.png', 'alt' => 'Medical students receiving education guidance'];
                    ?>
                    <img src="<?= e($heroImage['src']) ?>" alt="<?= e($heroImage['alt']) ?>">
                <?php endif; ?>
            </div>
        </section>
<?php
}

function render_cards($items, $class = 'feature-grid') {
?>
        <div class="<?= e($class) ?>">
            <?php foreach ($items as $item): ?>
                <article class="feature-card">
                    <h3><?= e($item['title']) ?></h3>
                    <p><?= e($item['body']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
<?php
}

function render_steps($steps) {
?>
        <ol class="process-list">
            <?php foreach ($steps as $index => $step): ?>
                <li>
                    <span><?= e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                    <strong><?= e($step) ?></strong>
                </li>
            <?php endforeach; ?>
        </ol>
<?php
}

function render_faqs($faqs) {
?>
        <div class="accordion-list faq-list">
            <?php foreach ($faqs as $index => $faq): ?>
                <details <?= $index === 0 ? 'open' : '' ?>>
                    <summary><?= e($faq['question']) ?></summary>
                    <p><?= e($faq['answer']) ?></p>
                </details>
            <?php endforeach; ?>
        </div>
<?php
}

function render_college_card($college) {
    $schoolRoutes = [
        'pltci-college-of-medicine' => 'schools/pltci/index.php',
        'the-manila-times-college-school-of-medicine' => 'schools/tmtcs/index.php',
    ];
    $collegeHref = $schoolRoutes[$college['slug']] ?? ('college-' . $college['slug'] . '.php');
?>
        <article class="featured-college-card">
            <div class="college-photo">
                <img src="<?= e($college['image']) ?>" alt="<?= e($college['imageAlt']) ?>" loading="lazy">
                <div class="image-badges">
                    <?php foreach ($college['imageBadges'] as $imageBadge): ?>
                        <span><?= e($imageBadge) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="featured-college-body">
                <p class="college-location"><?= e($college['location']) ?></p>
                <h3><?= e($college['name']) ?></h3>
                <p><?= e($college['summary']) ?></p>
                <div class="programme-tags" aria-label="Programmes">
                    <?php foreach ($college['programmes'] as $programme): ?>
                        <span><?= e($programme) ?></span>
                    <?php endforeach; ?>
                </div>
                <dl class="college-stats">
                    <div><dt><?= e($college['tuitionLabel']) ?></dt><dd><?= e($college['tuition']) ?></dd></div>
                    <div><dt><?= e($college['durationLabel']) ?></dt><dd><?= e($college['duration']) ?></dd></div>
                    <div><dt><?= e($college['intakeLabel']) ?></dt><dd><?= e($college['intakes']) ?></dd></div>
                </dl>
                <?php if (!empty($college['additionalFeeNote'])): ?><p class="college-fee-note"><?= e($college['additionalFeeNote']) ?></p><?php endif; ?>
                <p class="college-fee-disclaimer"><?= e($college['feeDisclaimer']) ?></p>
                <p class="college-support-note"><?= e($college['applicationSupport']) ?></p>
                <div class="college-actions">
                    <a class="btn btn-outline college-button" href="<?= e(site_url($collegeHref)) ?>">View College</a>
                    <a class="btn btn-primary college-button" href="apply.php?college=<?= e($college['slug']) ?>">Apply Now</a>
                </div>
            </div>
        </article>
<?php
}

function render_footer() {
    global $navItems, $whatsappUrl;
    require __DIR__ . '/footer.php';
}
?>
