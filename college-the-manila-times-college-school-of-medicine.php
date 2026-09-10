<?php
header('Location: schools/tmtcs/', true, 302);
exit;
require __DIR__ . '/includes/site.php';
$college = $collegeData[1];
render_header(
    'The Manila Times College School of Medicine | Medcon',
    'Review the BS–MD Medical Programme, indicative tuition, clinical internship and admissions support for The Manila Times College School of Medicine.',
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
