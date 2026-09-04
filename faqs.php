<?php
require __DIR__ . '/includes/site.php';
render_header('Frequently Asked Questions | Medcon', 'Answers to common questions about Medcon, studying medicine in the Philippines, admissions, documents, parents, and agents.', 'faqs.php');
render_breadcrumb('FAQs');
render_page_hero('FAQS', 'Frequently asked questions', 'Find answers to common questions about Medcon, studying medicine in the Philippines, admissions guidance, documents, parents, and agent partnerships.');
$faqs = [
    ['question' => 'What does Medcon do?', 'answer' => 'Medcon helps African students and families understand medical education opportunities abroad and provides guided admissions support through partner institutions.'],
    ['question' => 'Which countries do you support?', 'answer' => 'Medcon is focused on supporting African students seeking medical education abroad, beginning with partner institutions in the Philippines.'],
    ['question' => 'How do I start the process?', 'answer' => 'Start by submitting the Apply Now form or chatting with Medcon on WhatsApp. Our team will review your enquiry and guide you on the next step.'],
    ['question' => 'Do I need to know my preferred college before applying?', 'answer' => 'No. If you are unsure, Medcon can explain the available partner colleges and help you understand which option may fit your goals.'],
    ['question' => 'What documents do I need?', 'answer' => 'Common documents include academic results, passport, birth certificate, passport photograph, and transcripts where applicable. Final requirements depend on the selected college.'],
    ['question' => 'Can I apply without a passport?', 'answer' => 'You may begin an enquiry without a passport, but passport readiness may be required before progressing through later admission and travel steps.'],
    ['question' => 'What are the tuition costs?', 'answer' => 'Tuition varies by college and programme. Medcon will provide available tuition ranges and explain other cost areas such as accommodation and living expenses.'],
    ['question' => 'Can parents speak with Medcon?', 'answer' => 'Yes. Parents can request a consultation or chat with the team on WhatsApp before making decisions.'],
    ['question' => 'How do agents register?', 'answer' => 'Agents can complete the agent registration form on the For Agents page. Medcon will review the information and contact approved partners.'],
    ['question' => 'Are agents paid commission?', 'answer' => 'Commission opportunities may be available based on Medcon agent policy and confirmed student outcomes.'],
];
?>
<section class="section"><div class="container"><div class="section-heading"><span class="eyebrow">General, admissions, parents, and agents</span><h2>Answers for common enquiries</h2></div><?php render_faqs($faqs); ?></div></section>
<script type="application/ld+json">
<?= json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(fn($faq) => ['@type' => 'Question', 'name' => $faq['question'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']]], $faqs)], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
</script>
<?php render_footer(); ?>
