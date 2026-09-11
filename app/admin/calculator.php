<?php
require_once __DIR__ . '/../includes/student.php';
require_once __DIR__ . '/../includes/admin-layout.php';
require_role(['admin','super_admin']);
require_once dirname(__DIR__, 2) . '/includes/calculator-activity.php';
$user = current_user();
$error = '';
try { $activity = calculator_activity(); }
catch (Throwable $exception) { $activity = ['totals'=>[], 'days'=>[], 'recent'=>[]]; $error = 'Calculator activity storage is unavailable. Please check server storage permissions.'; }
$labels = calculator_event_labels();
$today = $activity['days'][gmdate('Y-m-d')] ?? [];
require_once dirname(__DIR__, 2) . '/includes/site-activity.php';
$period = (string)($_GET['period'] ?? '30');
if (!in_array($period, ['today','7','30','90'], true)) $period = '30';
$since = $period === 'today' ? strtotime(gmdate('Y-m-d') . ' UTC') : time() - (int)$period * 86400;
$siteError = ''; $pages = []; $clicks = []; $siteTotals = ['visitors'=>0,'views'=>0,'clicks'=>0];
try {
    $siteDb = site_activity_db();
    $summary = $siteDb->prepare("SELECT COUNT(DISTINCT visitor) visitors, COALESCE(SUM(kind='view'),0) views, COALESCE(SUM(kind='click'),0) clicks FROM events WHERE at>=?");
    $summary->execute([$since]); $siteTotals = $summary->fetch(PDO::FETCH_ASSOC);
    $statement = $siteDb->prepare("SELECT page, COUNT(DISTINCT visitor) visitors, SUM(kind='view') views, SUM(kind='click') clicks FROM events WHERE at>=? GROUP BY page ORDER BY visitors DESC, page");
    $statement->execute([$since]); $pages = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = $siteDb->prepare("SELECT page, button, COUNT(*) clicks, COUNT(DISTINCT visitor) visitors FROM events WHERE at>=? AND kind='click' GROUP BY page,button ORDER BY clicks DESC, page, button LIMIT 200");
    $statement->execute([$since]); $clicks = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $exception) { $siteError = 'Website analytics are unavailable. Check that PHP PDO SQLite is enabled and the data directory is writable.'; }
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow">
<title>Website analytics and fee calculator | Medcon Admin</title>
<link rel="stylesheet" href="<?= esc(app_url('assets/css/app.css')) ?>">
</head><body class="medcon-app mc-role-body">
<?php admin_portal_header($user, 'calculator'); ?>
<main class="mc-role-main">
<div class="mc-portal-heading"><div><span class="mc-kicker">Website activity</span><h1>Website analytics</h1><p>Internal visitor and button-click counts, followed by fee calculator usage. Refresh for the latest data.</p></div></div>
<section class="mc-panel"><h2>Website visitors</h2>
<form method="get"><label for="analytics-period">Reporting period (UTC)</label> <select id="analytics-period" name="period"><?php foreach (['today'=>'Today','7'=>'Last 7 days','30'=>'Last 30 days','90'=>'Last 90 days'] as $value=>$label): ?><option value="<?= esc((string)$value) ?>"<?= $period===(string)$value?' selected':'' ?>><?= esc($label) ?></option><?php endforeach; ?></select> <button type="submit">Update</button></form>
<?php if ($siteError): ?><p role="alert"><?= esc($siteError) ?></p><?php else: ?>
<p><strong><?= number_format((int)$siteTotals['visitors']) ?></strong> unique browsers &middot; <strong><?= number_format((int)$siteTotals['views']) ?></strong> page views &middot; <strong><?= number_format((int)$siteTotals['clicks']) ?></strong> button/link clicks</p>
<?php endif; ?>
<p>Visitors are unique browsers identified by a first-party cookie, not verified people. Refreshes add page views but do not add visitors. One browser visiting several pages counts once in the site total and once on each page. Cookie deletion or another device counts separately. Query strings and PHP/index URL variants are combined.</p>
<p>Tracking begins when installed, with no estimated or backfilled counts. Only visible public pages and browser-reported user clicks are recorded. Portal pages, known bots and browser automation are excluded; sophisticated bots may still be counted. Blocked cookies or scripts can cause undercounting. Events are retained for 90 days. No IP addresses, form values or contact details are stored.</p>
</section>
<section class="mc-panel"><h2>Pages</h2>
<?php if (!$pages): ?><p><?= $siteError ? 'Counts unavailable.' : 'No page activity recorded in this period.' ?></p><?php else: ?>
<div class="mc-table-wrap"><table><thead><tr><th>Page</th><th>Visitors (unique browsers)</th><th>Page views</th><th>Button/link clicks</th></tr></thead><tbody>
<?php foreach ($pages as $row): ?><tr><td><?= esc($row['page']) ?></td><td><?= number_format((int)$row['visitors']) ?></td><td><?= number_format((int)$row['views']) ?></td><td><?= number_format((int)$row['clicks']) ?></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></section>
<section class="mc-panel"><h2>Button and link clicks</h2><p>Top 200 controls in this period. Labels include their position on the page to distinguish repeated buttons; these positions can change when a page is edited. Clicks do not prove that a form was submitted or a purchase completed.</p>
<?php if (!$clicks): ?><p><?= $siteError ? 'Counts unavailable.' : 'No clicks recorded in this period.' ?></p><?php else: ?>
<div class="mc-table-wrap"><table><thead><tr><th>Page</th><th>Button / link</th><th>Clicks</th><th>Visitors who clicked</th></tr></thead><tbody>
<?php foreach ($clicks as $row): ?><tr><td><?= esc($row['page']) ?></td><td><?= esc($row['button']) ?></td><td><?= number_format((int)$row['clicks']) ?></td><td><?= number_format((int)$row['visitors']) ?></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></section>
<?php if ($error): ?><p role="alert"><?= esc($error) ?></p><?php endif; ?>
<section class="mc-panel"><h2>Fee calculator activity counters</h2>
<div class="mc-table-wrap"><table><thead><tr><th>Activity</th><th>Today (UTC)</th><th>All time</th></tr></thead><tbody>
<?php foreach ($labels as $key=>$label): ?><tr><td><?= esc($label) ?></td><td><?= number_format($today[$key] ?? 0) ?></td><td><strong><?= number_format($activity['totals'][$key] ?? 0) ?></strong></td></tr><?php endforeach; ?>
</tbody></table></div>
<p>Visits count page loads, not unique people. Interaction means changing an option, sharing, or requesting a PDF. Cancelled shares count only as attempts. A completed native share means the browser reported success; a copied link does not prove it was sent. PDF requests do not confirm a saved file. Shared-link visits include reloads.</p>
</section>
<section class="mc-panel"><h2>Recent activity</h2><p>Latest 100 events. Selections are anonymous and are not verified personal details. Times are UTC.</p>
<?php if (!$activity['recent']): ?><p>No calculator activity recorded yet.</p><?php else: ?>
<div class="mc-table-wrap"><table><thead><tr><th>Time</th><th>Activity</th><th>College</th><th>Academic background</th><th>Package</th></tr></thead><tbody>
<?php foreach ($activity['recent'] as $event): ?><tr><td><?= esc(gmdate('j M Y H:i:s', strtotime($event['at']))) ?></td><td><?= esc($labels[$event['event']] ?? $event['event']) ?></td><td><?= esc(strtoupper($event['college'])) ?></td><td><?= esc(ucwords(str_replace('-', ' ', $event['background']))) ?></td><td><?= esc(ucfirst($event['package'])) ?></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></section>
</main><?php admin_portal_footer(); ?></body></html>
