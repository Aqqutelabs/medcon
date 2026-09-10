<?php
header('Location: schools/pltci/', true, 302);
exit;
require __DIR__ . '/includes/site.php';
$college = $collegeData[0];
render_header(
    'PLTCI College of Medicine | Medcon',
    'Review the four-year Doctor of Medicine programme, indicative tuition and admissions support for PLTCI College of Medicine.',
    'colleges.php'
);
render_breadcrumb($college['name'], 'Partner Colleges', 'colleges.php');
render_page_hero(
    'COLLEGE DETAIL',
    $college['name'],
    $college['summary'],
    ['label' => 'Apply to This College', 'href' => 'apply.php?college=' . $college['slug']]
);
include __DIR__ . '/includes/college-detail-body.php';
render_footer();
