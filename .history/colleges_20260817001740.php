<?php
require __DIR__ . '/includes/site.php';
render_header('PLTCI and The Manila Times College School of Medicine | Medcon', 'Compare the two verified Medcon partner medical colleges in the Philippines and request admission support.', 'colleges.php');
render_breadcrumb('Partner Colleges');
render_page_hero('PARTNER COLLEGES', 'Verified medical college partners in the Philippines', 'Compare PLTCI College of Medicine and The Manila Times College School of Medicine, then request admission and student-support guidance from Medcon.', ['label' => 'Apply Now', 'href' => 'apply.php'], null, 'colleges');
?>
<section class="section featured-colleges"><div class="container"><div class="featured-heading"><span class="partner-pill">Our partner institutions</span><h2>Partner Medical Colleges</h2><p>Admission, visa, travel and student settlement support provided by Medical Consultants Education Nigeria Limited.</p></div><div class="featured-college-grid"><?php foreach ($collegeData as $college) { render_college_card($college); } ?></div></div></section>
<section class="section soft"><div class="container"><div class="section-heading"><span class="eyebrow">Compare colleges</span><h2>Quick planning comparison</h2></div><div class="table-wrap"><table class="info-table"><thead><tr><th>College</th><th>Location</th><th>Tuition / yr</th><th>Duration</th><th>Intakes</th></tr></thead><tbody><?php foreach ($collegeData as $college): ?><tr><td><?= e($college['name']) ?></td><td><?= e($college['location']) ?></td><td><?= e($college['tuition']) ?></td><td><?= e($college['duration']) ?></td><td><?= e($college['intakes']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></section>
<?php render_footer(); ?>
