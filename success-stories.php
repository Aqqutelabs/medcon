<?php
require __DIR__ . '/includes/site.php';
render_header('Success Stories | Medcon', 'Verified Medcon student stories and testimonials will be published as they become available.', 'success-stories.php');
render_breadcrumb('Success Stories');
render_page_hero('SUCCESS STORIES', 'Student journeys supported by Medcon', 'Read stories from students and families who received guidance through their medical education admission journey.', ['label' => 'Start Your Enquiry', 'href' => 'apply.php']);
?>
<section class="section">
    <div class="container empty-state">
        <span class="eyebrow">Verified testimonials</span>
        <h2>Student stories coming soon</h2>
        <p>Medcon will share verified student stories and testimonials as they become available.</p>
        <a class="btn btn-primary" href="apply.php">Start Your Enquiry</a>
    </div>
</section>
<?php render_footer(); ?>
