<?php
require __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/inquiries.php';
require_once __DIR__ . '/app/includes/cloudflare.php';
handle_inquiry_submission('contact');
render_header('Contact Medcon | Medical Education Admissions Support', 'Contact Medcon for medical education admissions, parent consultations, agent partnerships, and general enquiries.', 'contact.php');
render_breadcrumb('Contact');
render_page_hero('CONTACT Medcon', 'Speak with our medical education admissions team', 'Have questions about studying medicine abroad, partner colleges, parent consultations, or agent partnerships? Send us a message and our team will respond.', ['label' => 'Chat on WhatsApp', 'href' => $whatsappUrl], null, 'contact');
?>
<section class="section"><div class="container"><div class="contact-grid"><article class="feature-card"><h3>WhatsApp</h3><p>Chat with Medcon for quick enquiries.</p><a class="btn btn-primary" href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener">Chat on WhatsApp</a></article><article class="feature-card"><h3>Email</h3><p>Send us your questions and our team will respond.</p><a href="mailto:info@medconedu.org">info@medconedu.org</a></article><article class="feature-card"><h3>Phone</h3><p>Speak directly with our admissions support team.</p><p><a href="tel:+2347036961056">+234 703 696 1056</a></p></article><article class="feature-card"><h3>Office</h3><p>Visit or contact our office where available.</p><p>Office address to be confirmed</p></article></div></div></section>
<section class="section soft"><div class="container split"><div><span class="eyebrow">General enquiry</span><h2>Send us a message</h2><p>Students, parents, and agents can use the same enquiry form. Every submission is saved for follow-up.</p></div><?php render_inquiry_form($collegeData); ?></div></section>
<?php render_footer(); ?>
