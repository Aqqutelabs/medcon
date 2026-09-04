<?php
require __DIR__ . '/includes/site.php';
render_header('Parent Guidance for Medical Studies Abroad | MedCon', 'Parent-focused guidance for families considering medical studies abroad through MedCon.', 'for-parents.php');
render_breadcrumb('For Parents');
render_page_hero('FOR PARENTS', 'Clear information for families making a major education decision', 'Choosing a medical school abroad is a serious family decision. MedCon provides parents with clear, practical information on cost, safety, accommodation, admission steps, documentation, and communication.', ['label' => 'Book a Parent Consultation', 'href' => '#parent-form'], ['label' => 'Chat on WhatsApp', 'href' => $whatsappUrl, 'external' => true]);
$points = [
    ['title' => 'Cost Clarity', 'body' => 'Understand expected tuition ranges, living cost considerations, and other study-related expenses before making decisions.'],
    ['title' => 'Accommodation Guidance', 'body' => 'Receive information on available student accommodation options and what families should confirm before travel.'],
    ['title' => 'Document Support', 'body' => 'Know what documents are needed and how to prepare them properly for the admission process.'],
    ['title' => 'Process Transparency', 'body' => 'Parents can speak with our team to understand the student journey before, during, and after application.'],
];
$faqs = [
    ['question' => 'How much does it cost to study medicine in the Philippines?', 'answer' => 'Costs vary by college, programme, accommodation choice, and personal living needs. MedCon will provide available tuition ranges and explain the major cost areas before you proceed.'],
    ['question' => 'Can parents speak with the team?', 'answer' => 'Yes. Parents can request a consultation through the website or chat with MedCon on WhatsApp.'],
    ['question' => 'Is accommodation available?', 'answer' => 'Accommodation options depend on the selected institution and location. Families should confirm available options before making final plans.'],
];
?>
<section class="section"><div class="container"><div class="section-heading"><span class="eyebrow">Parent assurance</span><h2>Practical answers before the family commits</h2></div><?php render_cards($points, 'feature-grid two-col'); ?></div></section>
<section class="section soft" id="parent-form"><div class="container split"><div><span class="eyebrow">Consultation request</span><h2>Book a parent consultation</h2><p>Share your questions and preferred consultation channel. MedCon will contact you with the next step.</p></div><form class="form-card" data-form data-success="Thank you. Your parent consultation request has been received. Our team will contact you with the next step."><h3>Parent consultation form</h3><div class="form-grid"><label>Parent name<input required type="text" name="parent_name"></label><label>Student name<input required type="text" name="student_name"></label><label>Email<input required type="email" name="email"></label><label>Phone<input required type="tel" name="phone"></label><label>Country<input required type="text" name="country"></label><label>Student education level<input type="text" name="education_level"></label><label>Main concern<input type="text" name="main_concern"></label><label>Preferred channel<select name="channel"><option>WhatsApp</option><option>Phone</option><option>Email</option></select></label></div><button class="btn btn-primary" type="submit">Book Consultation</button><p class="form-status" role="status" aria-live="polite"></p></form></div></section>
<section class="section"><div class="container"><div class="section-heading"><span class="eyebrow">Parent FAQs</span><h2>Common family questions</h2></div><?php render_faqs($faqs); ?></div></section>
<?php render_footer(); ?>
