<?php
require __DIR__ . '/includes/site.php';
render_header('Parent Guidance for Medical Studies Abroad | Medcon', 'Parent-focused guidance for families considering medical studies abroad through Medcon.', 'for-parents.php');
render_breadcrumb('For Parents');
render_page_hero('FOR PARENTS', 'Clear information for families making a major education decision', 'Choosing a medical school abroad is a serious family decision. Medcon provides parents with clear, practical information on cost, safety, accommodation, admission steps, documentation, and communication.', ['label' => 'Book a Parent Consultation', 'href' => MEDCON_CONSULTATION_URL, 'external' => true], ['label' => 'Chat on WhatsApp', 'href' => $whatsappUrl, 'external' => true]);
$points = [
    ['title' => 'Cost Clarity', 'body' => 'Understand expected tuition ranges, living cost considerations, and other study-related expenses before making decisions.'],
    ['title' => 'Accommodation Guidance', 'body' => 'Receive information on available student accommodation options and what families should confirm before travel.'],
    ['title' => 'Document Support', 'body' => 'Know what documents are needed and how to prepare them properly for the admission process.'],
    ['title' => 'Process Transparency', 'body' => 'Parents can speak with our team to understand the student journey before, during, and after application.'],
];
$faqs = [
    ['question' => 'How much does it cost to study medicine in the Philippines?', 'answer' => 'Costs vary by college, programme, accommodation choice, and personal living needs. Medcon will provide available tuition ranges and explain the major cost areas before you proceed.'],
    ['question' => 'Can parents speak with the team?', 'answer' => 'Yes. Parents can request a consultation through the website or chat with Medcon on WhatsApp.'],
    ['question' => 'Is accommodation available?', 'answer' => 'Accommodation options depend on the selected institution and location. Families should confirm available options before making final plans.'],
];
?>
<section class="section"><div class="container"><div class="section-heading"><span class="eyebrow">Parent assurance</span><h2>Practical answers before the family commits</h2></div><?php render_cards($points, 'feature-grid two-col'); ?></div></section>
<section class="section soft" id="parent-form"><div class="container split"><div><span class="eyebrow">Consultation booking</span><h2>Book a parent consultation</h2><p>Select a suitable date and time for a student or parent consultation with the Medcon admissions team.</p></div><div class="form-card"><h3>Choose your appointment</h3><p>Open the Medcon consultation calendar to see available times and confirm your booking.</p><a class="btn btn-primary" href="<?= e(MEDCON_CONSULTATION_URL) ?>" target="_blank" rel="noopener noreferrer">View Available Times</a></div></div></section>
<section class="section"><div class="container"><div class="section-heading"><span class="eyebrow">Parent FAQs</span><h2>Common family questions</h2></div><?php render_faqs($faqs); ?></div></section>
<?php render_footer(); ?>
