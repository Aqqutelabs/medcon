<?php
require __DIR__ . '/includes/site.php';
render_header('About Medcon |  Medcon Educational Services and Consultancy Limited', 'Learn about Medcon Educational Services and Consultancy Limited and its guided medical education admissions support for African students.', 'about.php');
render_breadcrumb('About');
?>
<section class="about-hero">
    <div class="container about-hero-content">
        <div class="about-hero-panel">
            <span class="eyebrow">ABOUT MEDCON</span>
            <h1>Guiding African students toward international medical education</h1>
            <p> Medcon Educational Services and Consultancy Limited helps African students access medical education opportunities abroad through guided admissions support and verified partner institution pathways.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="contact.php">Speak With Our Team</a>
            </div>
        </div>
    </div>
</section>
<?php
$support = [
    ['title' => 'Medical College Selection', 'body' => 'Helping students compare partner colleges, programme options, tuition ranges, and intake periods.'],
    ['title' => 'Admissions Guidance', 'body' => 'Explaining the admission pathway and helping students understand what information is required.'],
    ['title' => 'Document Preparation Guidance', 'body' => 'Guiding students and parents on the basic documents commonly needed for medical school admission.'],
    ['title' => 'Parent Communication', 'body' => 'Providing families with clearer understanding of cost, accommodation, timelines, and support expectations.'],
    ['title' => 'Agent Collaboration', 'body' => 'Working with approved education agents and referral partners across Africa.'],
    ['title' => 'Student Journey Support', 'body' => 'Helping students move from enquiry to next-step admissions guidance with less confusion.'],
];
$trust = ['Verified partner institution information', 'Clear tuition and intake guidance', 'Parent-friendly communication', 'Structured enquiry and application forms', 'Professional education counselling approach', 'Transparent next-step guidance'];
?>
<section class="section">
    <div class="container intro-grid">
        <div><span class="eyebrow">Who we are</span><h2>A trusted medical education admissions partner</h2></div>
        <div>
            <p>Medcon exists to make the medical school admissions journey clearer for students and families. We provide practical guidance on college options, admission requirements, tuition expectations, required documents, and communication with partner institutions.</p>
            <p>Our work is built around clarity, trust, and structured support. We help students understand what is possible, what is required, and what steps to take next.</p>
        </div>
    </div>
</section>
<section class="section soft"><div class="container"><div class="section-heading"><span class="eyebrow">Our mission</span><h2>Clear guidance, structured support, reliable communication</h2></div><p class="narrow-copy">To connect qualified African students with trusted international medical education opportunities through clear guidance, structured admissions support, and reliable communication.</p></div></section>
<section class="section"><div class="container"><div class="section-heading"><span class="eyebrow">What we support</span><h2>Support across the enquiry journey</h2></div><?php render_cards($support); ?></div></section>
<section class="section soft"><div class="container split"><div><span class="eyebrow">Why families trust Medcon</span><h2>Built for clarity, trust, and responsible guidance</h2></div><ul class="check-list"><?php foreach ($trust as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul></div></section>
<section class="section cta-band"><div class="container cta-inner"><div><span class="eyebrow">Speak with a medical education adviser</span><h2>Have questions about studying medicine abroad?</h2><p>Our team can help you understand the available options and the next step.</p></div><div class="cta-actions"><a class="btn btn-primary" href="apply.php">Apply Now</a><a class="btn btn-secondary" href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener">Chat on WhatsApp</a></div></div></section>
<?php render_footer(); ?>
