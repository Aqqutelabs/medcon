<?php
require __DIR__ . '/includes/site.php';
render_header('Study Medicine in the Philippines | Medcon', 'Understand medical study options in the Philippines, including benefits, requirements, costs, timelines, and application guidance.', 'study-medicine-in-philippines.php');
render_breadcrumb('Study Medicine in the Philippines');
render_page_hero('STUDY MEDICINE IN THE PHILIPPINES', 'Begin your medical education journey in the Philippines', 'The Philippines provides an accessible international study route for students seeking medical education abroad. Medcon helps African students and parents understand colleges, requirements, costs, timelines, and next steps.', ['label' => 'Apply Now', 'href' => 'apply.php'], ['label' => 'Chat on WhatsApp', 'href' => $whatsappUrl, 'external' => true], 'philippines-bento');
$benefits = [
    ['title' => 'English-Based Learning', 'body' => 'Many programmes are delivered in English, making the academic transition easier for international students.'],
    ['title' => 'International Student Environment', 'body' => 'Students study alongside peers from different countries and backgrounds.'],
    ['title' => 'Accessible Tuition Planning', 'body' => 'Families can plan when they understand expected annual ranges and related costs.'],
    ['title' => 'Medical Training Culture', 'body' => 'Students enter a healthcare-focused academic environment with clinical learning exposure.'],
    ['title' => 'Clear Admission Pathway', 'body' => 'Medcon explains the steps, documents, timelines, and requirements before applying.'],
    ['title' => 'Support for Families', 'body' => 'Parents receive guidance on process, cost, accommodation, and student welfare considerations.'],
];
$steps = ['Speak with Medcon', 'Check eligibility', 'Choose preferred college', 'Submit application enquiry', 'Prepare documents', 'Receive admission guidance'];
$faqs = [
    ['question' => 'Can I study medicine in English?', 'answer' => 'Many medical education pathways in the Philippines use English-based instruction. Medcon will confirm programme-specific details during enquiry.'],
    ['question' => 'When should students apply?', 'answer' => 'Students should begin enquiries early enough to review requirements, confirm intake periods, prepare documents, and plan costs.'],
    ['question' => 'What documents are commonly needed?', 'answer' => 'Common documents include academic results, passport, birth certificate, passport photograph, and transcripts where applicable.'],
];
?>
<section class="section"><div class="container"><div class="section-heading"><span class="eyebrow">Why students choose the Philippines</span><h2>A practical option for medical education abroad</h2></div><?php render_cards($benefits); ?></div></section>
<section class="section soft"><div class="container"><div class="section-heading"><span class="eyebrow">Admission planning</span><h2>What families should review before applying</h2></div><div class="table-wrap"><table class="info-table"><thead><tr><th>Area</th><th>What to prepare</th></tr></thead><tbody><tr><td>Academic readiness</td><td>Results, transcripts where applicable, and current education level.</td></tr><tr><td>Identity documents</td><td>Passport status, birth certificate, and passport photograph.</td></tr><tr><td>Financial planning</td><td>Tuition range, living costs, accommodation, local transport, and study materials.</td></tr><tr><td>Timing</td><td>Preferred intake, document readiness, and family decision timeline.</td></tr></tbody></table></div></div></section>
<section class="section"><div class="container"><div class="section-heading"><span class="eyebrow">How the process works</span><h2>From first enquiry to admission guidance</h2></div><?php render_steps($steps); ?></div></section>
<section class="section soft"><div class="container"><div class="section-heading"><span class="eyebrow">FAQs</span><h2>Common questions about the Philippines route</h2></div><?php render_faqs($faqs); ?></div></section>
<?php render_footer(); ?>
