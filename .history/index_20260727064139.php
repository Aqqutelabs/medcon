<?php
$whatsappNumber = '2340000000000';
$whatsappMessage = rawurlencode('Hello MedCon, I would like to make enquiries about studying medicine abroad.');
$whatsappUrl = "https://wa.me/{$whatsappNumber}?text={$whatsappMessage}";

$topLinks = [
    
    ['label' => 'Work with us', 'href' => 'for-agents.php'],
    ['label' => 'Parents', 'href' => 'for-parents.php'],
    ['label' => 'Contact', 'href' => 'contact.php'],
];

$navItems = [
    ['label' => 'Home', 'href' => 'index.php'],
    ['label' => 'About', 'href' => 'about.php'],
    ['label' => 'Study Medicine in the Philippines', 'href' => 'study-medicine-in-philippines.php'],
    ['label' => 'Partner Colleges', 'href' => 'colleges.php'],
    ['label' => 'For Parents', 'href' => 'for-parents.php'],
    ['label' => 'Contact', 'href' => 'contact.php'],
];

$benefits = [
    ['title' => 'English-based instruction', 'body' => 'Explore medical education options delivered in an international learning environment.'],
    ['title' => 'Affordable study options', 'body' => 'Review tuition, accommodation, living expenses, and processing cost expectations early.'],
    ['title' => 'International student support', 'body' => 'Prepare for study abroad with structured guidance for students and parents.'],
    ['title' => 'Document preparation', 'body' => 'Understand academic, identity, and travel documents before applying.'],
    ['title' => 'Parent consultation', 'body' => 'Give families a clear channel for cost, safety, accommodation, and communication questions.'],
    ['title' => 'Admissions pathway', 'body' => 'Move from enquiry to eligibility review, college choice, documents, and admission guidance.'],
];

$featuredColleges = [
    [
        'name' => "St. Luke's Medical Center College of Medicine",
        'location' => 'Quezon City, Philippines',
        'badge' => 'Top Ranked',
        'imageBadges' => ['CHED Accredited', 'WHO Listed', 'Level IV Hospital'],
        'programmes' => ['Medicine (MD)', 'Nursing BSN', 'Pharmacy'],
        'tuition' => '$4,500 - $5,200',
        'duration' => '5.5 Years',
        'intakes' => 'June, November',
        'image' => 'img/hero-img.png',
        'href' => 'college-st-lukes-medical-center-college-of-medicine.php',
    ],
    [
        'name' => 'University of Perpetual Help - JONELTA',
        'location' => 'Las Pinas City, Philippines',
        'badge' => 'Most Popular',
        'imageBadges' => ['CHED Accredited', 'FAIMER Listed', 'Teaching Hospital'],
        'programmes' => ['Medicine (MD)', 'Nursing BSN', 'Physical Therapy', 'Pharmacy'],
        'tuition' => '$3,800 - $4,600',
        'duration' => '5 Years',
        'intakes' => 'June, November',
        'image' => 'img/hero-img.png',
        'href' => 'college-university-of-perpetual-help-jonelta.php',
    ],
];

$steps = [
    'Speak with MedCon',
    'Check eligibility',
    'Choose preferred college',
    'Submit application enquiry',
    'Prepare documents',
    'Receive admission guidance',
];

$faqs = [
    ['question' => 'What does MedCon do?', 'answer' => 'MedCon supports African students and families with medical education admissions guidance, beginning with partner institutions in the Philippines.'],
    ['question' => 'Can parents speak with the team?', 'answer' => 'Yes. Parent consultation is part of the support process so families can discuss cost, documents, accommodation, safety, and communication.'],
    ['question' => 'What documents do students usually need?', 'answer' => 'Common documents include academic results, passport, birth certificate, passport photograph, transcripts where applicable, and a completed application form. Exact requirements depend on the college.'],
    ['question' => 'Are tuition costs shown on the website?', 'answer' => 'Confirmed tuition ranges should be supplied by each partner college. Until then, MedCon can provide cost guidance after a direct enquiry.'],
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MedCon | Medical Education Admissions Support</title>
    <meta name="description" content="MedCon helps African students access medical education opportunities through verified partner institutions and structured admissions support.">
    <meta property="og:title" content="MedCon | Study Medicine Abroad">
    <meta property="og:description" content="Trusted medical education admissions guidance for students and parents.">
    <meta property="og:type" content="website">
    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css">
    <link rel="preload" href="assets/css/site.css" as="style">
    <link rel="stylesheet" href="assets/css/site.css">
    <script defer src="scripts/site.js"></script>
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>

    <header class="site-header" data-header>
        <div class="top-bar">
            <div class="container top-bar-inner">
                <span>Study Medicine in the Philippines | Admissions Support</span>
                <nav class="top-links" aria-label="Utility navigation">
                    <?php foreach ($topLinks as $item): ?>
                        <a href="<?= htmlspecialchars($item['href']) ?>"><?= htmlspecialchars($item['label']) ?></a>
                    <?php endforeach; ?>
                </nav>
            </div>
        </div>

        <div class="container header-inner">
            <a class="brand" href="index.php" aria-label="MedCon home">
                <img src="img/logo.svg" alt="MedCon" class="brand-logo">
            </a>

            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" data-menu-toggle>
                <span></span>
                <span></span>
                <span></span>
                <span class="sr-only">Open menu</span>
            </button>

            <nav class="primary-nav" id="primary-nav" data-nav>
                <?php foreach ($navItems as $item): ?>
                    <a href="<?= htmlspecialchars($item['href']) ?>"><?= htmlspecialchars($item['label']) ?></a>
                <?php endforeach; ?>
                <a class="btn btn-primary nav-cta" href="apply.php">Apply Now</a>
            </nav>
        </div>
    </header>

    <main id="main">
        <div class="breadcrumb-bar">
            <div class="container">
                <a href="index.php">Home</a>
                <span>College of Health and Life Sciences</span>
                <strong>MedCon Medical Admissions</strong>
            </div>
        </div>

        <a class="announcement-strip" href="apply.php">2026 Admissions: Register your interest for priority guidance now</a>

        <section class="hero" id="home">
            <div class="container">
                <h1>MedCon Medical Admissions</h1>
                <div class="hero-stage">
                    <img src="img/hero-img.png" alt="Medical students learning with an anatomy model">
                    <div class="hero-card">
                        <h2>Study Medicine Abroad</h2>
                        <p>Clear medical admissions guidance for African students applying to partner colleges in the Philippines.</p>
                        <div class="hero-actions">
                            <a class="btn btn-primary" href="apply.php">Apply Now</a>
                            <a class="btn btn-secondary" href="<?= htmlspecialchars($whatsappUrl) ?>" target="_blank" rel="noopener">Chat on WhatsApp</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="about">
            <div class="container intro-grid">
                <div>
                    <span class="eyebrow">Medical education admissions support</span>
                    <h2>Clear admissions guidance for students and families</h2>
                </div>
                <div>
                    <p>MedCon presents medical study options in a structured, university-style format so students and parents can understand the route before taking the next step.</p>
                    <p>Our support covers college guidance, admissions questions, document preparation, parent consultation, and student onboarding.</p>
                </div>
            </div>
        </section>

        <section class="section soft" id="study">
            <div class="container">
                <div class="admissions-slider" data-admissions-slider>
                    <div class="tabs" role="tablist" aria-label="Medical admissions information">
                        <button class="tab active" id="curriculum-tab" type="button" role="tab" aria-selected="true" aria-controls="curriculum-panel" data-slide-tab="0">Our curriculum</button>
                        <button class="tab" id="process-tab" type="button" role="tab" aria-selected="false" aria-controls="process-panel" data-slide-tab="1">Our Process</button>
                    </div>
                    <div class="slide-viewport" data-slide-viewport>
                        <div class="slide-track" data-slide-track>
                            <article class="slide-panel" id="curriculum-panel" role="tabpanel" aria-labelledby="curriculum-tab">
                                <div class="academic-panel">
                                    <p>At MedCon, admissions guidance is designed with a student-centred approach. Students receive clear direction on eligibility, college options, document preparation, parent questions, and the steps required to begin a medical education pathway abroad.</p>
                                    <p>Families can review likely requirements before committing to a preferred college or application route.</p>
                                </div>
                                <div class="feature-grid">
                                    <?php foreach ($benefits as $item): ?>
                                        <article class="feature-card">
                                            <h3><?= htmlspecialchars($item['title']) ?></h3>
                                            <p><?= htmlspecialchars($item['body']) ?></p>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            </article>

                            <article class="slide-panel process-slide" id="process-panel" role="tabpanel" aria-labelledby="process-tab">
                                <div class="section-heading">
                                    <span class="eyebrow">How the process works</span>
                                    <h2>From first enquiry to admission guidance</h2>
                                </div>
                                <ol class="process-list">
                                    <?php foreach ($steps as $index => $step): ?>
                                        <li>
                                            <span><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                                            <strong><?= htmlspecialchars($step) ?></strong>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section featured-colleges" id="colleges">
            <div class="container">
                <div class="featured-heading">
                    <span class="partner-pill">Our partner institutions</span>
                    <h2>Featured Medical Colleges</h2>
                    <p>As the exclusive African representative for these two institutions, we provide direct, streamlined admissions with dedicated support.</p>
                </div>

                <div class="featured-college-grid">
                    <?php foreach ($featuredColleges as $college): ?>
                        <article class="featured-college-card">
                            <div class="college-photo">
                                <img src="<?= htmlspecialchars($college['image']) ?>" alt="<?= htmlspecialchars($college['name']) ?>" loading="lazy">
                                <span class="top-badge"><?= htmlspecialchars($college['badge']) ?></span>
                                <div class="image-badges">
                                    <?php foreach ($college['imageBadges'] as $imageBadge): ?>
                                        <span><?= htmlspecialchars($imageBadge) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="featured-college-body">
                                <p class="college-location"><span aria-hidden="true">âŒ–</span><?= htmlspecialchars($college['location']) ?></p>
                                <h3><?= htmlspecialchars($college['name']) ?></h3>

                                <div class="programme-tags" aria-label="Programmes">
                                    <?php foreach ($college['programmes'] as $programme): ?>
                                        <span><?= htmlspecialchars($programme) ?></span>
                                    <?php endforeach; ?>
                                </div>

                                <dl class="college-stats">
                                    <div>
                                        <dt>Tuition / yr</dt>
                                        <dd><?= htmlspecialchars($college['tuition']) ?></dd>
                                    </div>
                                    <div>
                                        <dt>Duration</dt>
                                        <dd><?= htmlspecialchars($college['duration']) ?></dd>
                                    </div>
                                    <div>
                                        <dt>Intakes</dt>
                                        <dd><?= htmlspecialchars($college['intakes']) ?></dd>
                                    </div>
                                </dl>

                                <div class="college-actions">
                                    <a class="btn btn-outline college-button" href="<?= htmlspecialchars($college['href']) ?>">View College</a>
                                    <a class="btn btn-primary college-button" href="apply.php">Apply Now</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <a class="compare-link" href="colleges.php">Compare both colleges <span aria-hidden="true">-></span></a>
            </div>
        </section>

        <section class="advice-zone">
            <div class="advice-grid">
                <img src="img/hero-img.png" alt="Students receiving medical education support">
                <div class="advice-content">
                    <span class="eyebrow">Application Advice Zone</span>
                    <h2>Medical admissions guidance</h2>
                    <p>We help students understand the application process, required documents, eligibility questions, and the best next step before applying.</p>
                    <a class="btn btn-primary" href="apply.php">Find out more</a>
                </div>
            </div>
        </section>

        <section class="section" id="parents">
            <div class="container split">
                <div>
                    <span class="eyebrow">For parents</span>
                    <h2>Clear guidance for families supporting a medical education journey</h2>
                    <p>Parents can speak with the MedCon team about school legitimacy, cost expectations, accommodation, documents, student support, and application updates.</p>
                    <a class="btn btn-primary" href="for-parents.php#parent-form">Book a Parent Consultation</a>
                </div>
                <div class="accordion-list">
                    <details open><summary>Cost and tuition guidance</summary><p>Families receive a clearer picture of likely tuition, accommodation, processing, travel, and living cost areas.</p></details>
                    <details><summary>Accommodation and safety</summary><p>MedCon helps parents ask the right questions before a student commits to a study route.</p></details>
                    <details><summary>Documents and timelines</summary><p>Students receive guidance on common academic, identity, and travel documents.</p></details>
                </div>
            </div>
        </section>

        <section class="section soft" id="agents">
            <div class="container split">
                <div>
                    <span class="eyebrow">For agents</span>
                    <h2>Partner with MedCon as a student recruitment agent</h2>
                    <p>Education consultants, school counsellors, student recruiters, and community representatives can submit interest and discuss responsible student recruitment support.</p>
                </div>
                <form class="form-card" id="agent-form" data-form>
                    <h3>Agent interest form</h3>
                    <div class="form-grid">
                        <label>Full name<input required type="text" name="name"></label>
                        <label>Email<input required type="email" name="email"></label>
                        <label>Phone<input required type="tel" name="phone"></label>
                        <label>Country<input required type="text" name="country"></label>
                    </div>
                    <label>Recruitment experience<textarea name="message" rows="4"></textarea></label>
                    <button class="btn btn-primary" type="submit">Apply to Become an Agent</button>
                    <p class="form-status" role="status" aria-live="polite"></p>
                </form>
            </div>
        </section>

        <section class="section" id="apply">
            <div class="container split">
                <div>
                    <span class="eyebrow">Apply now</span>
                    <h2>Start your medical education enquiry</h2>
                    <p>Share your details and preferred study route. A MedCon adviser can follow up with next steps, document guidance, and available college information.</p>
                    <a class="btn btn-secondary" href="<?= htmlspecialchars($whatsappUrl) ?>" target="_blank" rel="noopener">Continue on WhatsApp</a>
                </div>
                <form class="form-card" data-form>
                    <h3>Student enquiry form</h3>
                    <div class="form-grid">
                        <label>Full name<input required type="text" name="full_name"></label>
                        <label>Email<input required type="email" name="email"></label>
                        <label>Phone<input required type="tel" name="phone"></label>
                        <label>Country<input required type="text" name="country"></label>
                        <label>Education level<input type="text" name="education_level"></label>
                        <label>Preferred intake<input type="text" name="intake"></label>
                    </div>
                    <label>Message<textarea name="message" rows="4"></textarea></label>
                    <label class="checkbox-line"><input required type="checkbox" name="consent"> I agree to be contacted about my enquiry.</label>
                    <button class="btn btn-primary" type="submit">Submit Application Enquiry</button>
                    <p class="form-status" role="status" aria-live="polite"></p>
                </form>
            </div>
        </section>

        <section class="section soft" id="faqs">
            <div class="container">
                <div class="section-heading">
                    <span class="eyebrow">FAQs</span>
                    <h2>Common questions from students and parents</h2>
                </div>
                <div class="accordion-list faq-list">
                    <?php foreach ($faqs as $index => $faq): ?>
                        <details <?= $index === 0 ? 'open' : '' ?>>
                            <summary><?= htmlspecialchars($faq['question']) ?></summary>
                            <p><?= htmlspecialchars($faq['answer']) ?></p>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section cta-band" id="contact">
            <div class="container cta-inner">
                <div>
                    <span class="eyebrow">Ready to start?</span>
                    <h2>Start your medical education journey</h2>
                    <p>Speak with MedCon about admissions guidance, parent consultation, college options, and application next steps.</p>
                </div>
                <div class="cta-actions">
                    <a class="btn btn-primary" href="apply.php">Apply Now</a>
                    <a class="btn btn-secondary" href="<?= htmlspecialchars($whatsappUrl) ?>" target="_blank" rel="noopener">Chat on WhatsApp</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <a class="footer-brand" href="#home" aria-label="MedCon home">
                    <img src="img/logo.svg" alt="MedCon">
                </a>
                <p> Medcon Educational Services and Consultancy Limited supports students and families with structured international medical education admissions guidance.</p>
            </div>
            <div>
                <h2>Schools and services</h2>
                <?php foreach ($navItems as $item): ?>
                    <a href="<?= htmlspecialchars($item['href']) ?>"><?= htmlspecialchars($item['label']) ?></a>
                <?php endforeach; ?>
            </div>
            <div>
                <h2>More information</h2>
                <a href="#study">Student support services</a>
                <a href="#parents">Parents</a>
                <a href="#agents">Agent partnerships</a>
                <a href="#faqs">FAQs</a>
            </div>
            <div>
                <h2>Contact us</h2>
                <a href="<?= htmlspecialchars($whatsappUrl) ?>" target="_blank" rel="noopener">Chat on WhatsApp</a>
                <a href="mailto:info@medconedu.com">info@medconedu.com</a>
                <span>Business details to be confirmed</span>
            </div>
        </div>
        <div class="container footer-bottom">
            <span>&copy; <?= date('Y') ?> Medical Consultants Education Nigeria Limited.</span>
            <span>Privacy Policy | Terms of Use | Accessibility</span>
        </div>
    </footer>

    <a class="whatsapp-float" href="<?= htmlspecialchars($whatsappUrl) ?>" target="_blank" rel="noopener" aria-label="Chat with MedCon on WhatsApp">
        <svg viewBox="0 0 32 32" aria-hidden="true" focusable="false">
            <path d="M16.04 4.5c-6.25 0-11.34 5.02-11.34 11.2 0 2.12.6 4.18 1.74 5.96l-1.84 5.84 6.06-1.76a11.5 11.5 0 0 0 5.38 1.35c6.25 0 11.34-5.02 11.34-11.2S22.29 4.5 16.04 4.5Zm0 20.66c-1.74 0-3.45-.47-4.94-1.37l-.35-.21-3.55 1.03 1.08-3.42-.23-.36a9.22 9.22 0 0 1-1.43-4.94c0-5.12 4.22-9.29 9.42-9.29s9.42 4.17 9.42 9.29-4.22 9.27-9.42 9.27Zm5.16-6.95c-.28-.14-1.66-.81-1.92-.9-.26-.1-.45-.14-.64.14-.19.27-.73.9-.9 1.08-.17.18-.33.2-.61.07-.28-.14-1.18-.43-2.25-1.37-.83-.73-1.39-1.64-1.55-1.91-.16-.28-.02-.43.12-.57.13-.13.28-.33.42-.49.14-.17.19-.28.28-.46.09-.18.05-.34-.02-.48-.07-.14-.64-1.52-.88-2.08-.23-.55-.47-.47-.64-.48h-.55c-.19 0-.49.07-.75.34-.26.28-.99.96-.99 2.34s1.02 2.72 1.16 2.9c.14.18 2 3.02 4.85 4.23.68.29 1.21.46 1.62.59.68.21 1.3.18 1.79.11.55-.08 1.66-.67 1.9-1.32.23-.65.23-1.21.16-1.32-.07-.12-.26-.19-.54-.32Z"/>
        </svg>
    </a>
</body>
</html>
