<?php
require __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/inquiries.php';
require_once __DIR__ . '/app/includes/cloudflare.php';
handle_inquiry_submission('apply');
render_header('Apply to Study Medicine Abroad | Medcon', 'Start your medical education enquiry with Medcon and receive guidance on partner colleges and next steps.', 'apply.php');
render_breadcrumb('Apply Now');
render_page_hero('APPLY NOW', 'Start your medical education enquiry', 'Submit your details and Medcon will contact you with guidance on partner colleges, programme options, requirements, and the next step.');
$steps = ['Your enquiry is received', 'The Medcon team reviews your details', 'You are contacted for clarification where needed', 'You receive guidance on college options and requirements', 'You are advised on the next step'];
$selectedCollege = isset($_GET['college']) ? (string) $_GET['college'] : '';
$approvedCollegeSlugs = array_column($collegeData, 'slug');
if (!in_array($selectedCollege, $approvedCollegeSlugs, true)) {
    $selectedCollege = '';
}
?>
<section class="section"><div class="container split"><?php render_inquiry_form($collegeData,$selectedCollege); ?><div class="apply-guidance-column"><aside class="guidance-panel"><span class="eyebrow">Information to prepare</span><h2>Before you apply</h2><ul class="check-list"><li>Academic results</li><li>Passport status</li><li>Intended programme</li><li>Preferred intake</li><li>Parent or guardian contact</li><li>Estimated family budget</li><li>Questions you want answered</li></ul></aside><figure class="apply-page-image"><img src="<?= e(site_url('img/apply-img.png')) ?>" alt="Prospective medical student completing an application form beside a laptop"></figure></div></div></section>
<section class="section soft"><div class="container"><div class="section-heading"><span class="eyebrow">What happens next</span><h2>After you submit your enquiry</h2></div><?php render_steps($steps); ?></div></section>
<?php render_footer(); ?>
