<?php
require_once __DIR__ . '/site.php';
require_once __DIR__ . '/school-data.php';

$catalogue = school_catalogue();
$schoolKey = $schoolKey ?? '';
$pageKey = $pageKey ?? 'landing';
if (!isset($catalogue[$schoolKey]['pages'][$pageKey])) { http_response_code(404); exit('School page not found.'); }
$school = $catalogue[$schoolKey];
$page = $school['pages'][$pageKey];
$schoolBase = 'schools/' . $schoolKey . '/';
$applicationSlug = $schoolKey === 'pltci' ? 'pltci-college-of-medicine' : 'the-manila-times-college-school-of-medicine';

render_header($page['title'] . ' | ' . $school['short'] . ' | Medcon', $page['intro'], 'colleges.php', 'school-page');
?>
<nav class="school-tabs" aria-label="<?= e($school['short']) ?> pages">
  <div class="container school-tabs-inner">
    <a class="school-tab-brand" href="<?= e(site_url($schoolBase . 'index.php')) ?>"><img src="<?= e(site_url($school['logo'])) ?>" alt=""><span><?= e($school['short']) ?></span></a>
    <div class="school-tab-links">
      <?php foreach ($school['pages'] as $key => $tab): ?><a class="<?= $key === $pageKey ? 'active' : '' ?>" href="<?= e(site_url($schoolBase . ($key === 'landing' ? 'index.php' : $key . '.php'))) ?>"><?= e($tab['label']) ?></a><?php endforeach; ?>
    </div>
  </div>
</nav>
<nav class="school-breadcrumb" aria-label="Breadcrumb">
  <div class="container"><a href="<?= e(site_url('index.php')) ?>">Home</a><span aria-hidden="true">/</span><a href="<?= e(site_url('colleges.php')) ?>">Partner colleges</a><span aria-hidden="true">/</span><a href="<?= e(site_url($schoolBase . 'index.php')) ?>"><?= e($school['short']) ?></a><span aria-hidden="true">/</span><strong aria-current="page"><?= e($page['label']) ?></strong></div>
</nav>
<section class="school-hero">
  <img src="<?= e(site_url($school['hero'])) ?>" alt="<?= e($school['name']) ?> campus" class="school-hero-image">
  <div class="school-hero-shade"></div>
  <div class="container school-hero-content">
    <div class="school-hero-meta"><span class="school-live"><i></i> September 2026 intake open</span><span class="school-programme-tag">Doctor of Medicine</span></div>
    <p class="school-location"><?= e($school['location']) ?></p>
    <h1><?= e($page['title']) ?></h1>
    <p><?= e($page['intro']) ?></p>
    <div class="hero-actions"><a class="btn btn-primary" href="<?= e(site_url('apply.php?college=' . $applicationSlug)) ?>">Apply now</a><a class="btn btn-secondary school-whatsapp" href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener">WhatsApp us</a><?php if ($pageKey === 'landing'): ?><a class="school-text-link" href="<?= e(site_url($schoolBase . 'cost.php')) ?>">View fees</a><?php endif; ?></div>
  </div>
</section>
<?php if ($pageKey === 'about'): ?>
<div class="school-accreditation-link"><div class="container"><a href="<?= e(site_url('accreditation')) ?>">View Our Accreditation &amp; Authorization <span aria-hidden="true">→</span></a></div></div>
<?php endif; ?>
<div class="container school-layout">
  <aside class="school-pagemap" aria-label="On this page">
    <details open><summary>On this page</summary><nav><?php foreach ($page['sections'] as $id => $section): ?><a href="#<?= e($id) ?>" data-section-link><?= e($section['title']) ?></a><?php endforeach; ?></nav></details>
  </aside>
  <div class="school-sections">
  <?php foreach ($page['sections'] as $id => $section): ?>
    <section class="school-section" id="<?= e($id) ?>" data-school-section>
      <div class="school-section-heading"><span class="school-section-number" aria-hidden="true"></span><div><span class="eyebrow"><?= e($section['kicker'] ?? $school['short']) ?></span>
      <h2><?= e($section['title']) ?></h2>
      </div></div>
      <?php if (!empty($section['body'])): ?><p class="school-lead"><?= e($section['body']) ?></p><?php endif; ?>
      <?php if (!empty($section['stats'])): ?><div class="school-stats"><?php foreach ($section['stats'] as $stat): ?><div><strong><?= e($stat[0]) ?></strong><span><?= e($stat[1]) ?></span></div><?php endforeach; ?></div><?php endif; ?>
      <?php if (!empty($section['items'])): ?><div class="school-card-grid"><?php foreach ($section['items'] as $item): ?><article><span class="school-card-mark"></span><h3><?= e($item) ?></h3></article><?php endforeach; ?></div><?php endif; ?>
      <?php if (!empty($section['gallery'])): ?><div class="school-gallery"><?php foreach ($school['gallery'] as $index => $image): ?><img src="<?= e(site_url($image)) ?>" alt="<?= e($school['short']) ?> campus view <?= $index + 1 ?>" loading="lazy"><?php endforeach; ?></div><?php endif; ?>
      <?php if (!empty($section['faqs'])): ?><?php render_faqs($section['faqs']); ?><?php endif; ?>
      <?php if (!empty($section['table'])): ?><?php $tableHeaders = $section['headers'] ?? ['Destination', 'Key considerations', 'Relative overall cost']; ?><div class="table-wrap"><table class="info-table"><thead><tr><?php foreach ($tableHeaders as $header): ?><th><?= e($header) ?></th><?php endforeach; ?></tr></thead><tbody><?php foreach ($section['table'] as $row): ?><tr><?php foreach ($row as $cell): ?><td><?= e($cell) ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
      <?php if (in_array($id, ['apply','contact'], true)): ?><div class="school-inline-actions"><a class="btn btn-primary" href="<?= e(site_url('apply.php?college=' . $applicationSlug)) ?>">Start your application</a><a class="btn btn-outline" href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener">Chat on WhatsApp</a></div><?php endif; ?>
    </section>
  <?php endforeach; ?>
  </div>
</div>
<?php if ($pageKey === 'about'): require_once __DIR__ . '/accreditation.php'; $schoolDocument = accreditation_documents()[$schoolKey]; ?>
<section class="section soft school-authorization"><div class="container split"><div><span class="verification-status"><?= e($schoolDocument['status']) ?></span><span class="eyebrow"><?= $schoolKey === 'pltci' ? 'Authorized student support in Nigeria' : 'Student recruitment and support authorization' ?></span><h2><?= e($schoolDocument['title']) ?></h2><p><?= e($schoolDocument['explanation']) ?></p><?php if (!empty($schoolDocument['wdoms_url'])): ?><p><a class="about-accreditation-link" href="<?= e($schoolDocument['wdoms_url']) ?>" target="_blank" rel="noopener noreferrer"><?= e($schoolDocument['wdoms_label']) ?> <span aria-hidden="true">↗</span></a></p><?php endif; ?><p><a class="school-text-link" href="<?= e(site_url('accreditation')) ?>">View all Medcon Edu accreditations and authorizations</a></p></div><?php render_certificate_preview($schoolKey, $schoolDocument, 'View Authorization Certificate'); ?></div></section>
<?php render_certificate_dialog(); ?><script defer src="<?= e(site_url('scripts/accreditation.js')) ?>"></script>
<?php endif; ?>
<?php render_footer(); ?>
