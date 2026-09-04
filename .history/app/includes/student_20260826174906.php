<?php

require_once __DIR__ . '/auth.php';

function require_student(PDO $pdo): array
{
    require_role(['student']);
    $user = current_user();
    $stmt = $pdo->prepare('SELECT s.*, u.first_name, u.last_name, u.email, u.phone, u.country FROM students s JOIN users u ON u.id = s.user_id WHERE s.user_id = ? LIMIT 1');
    $stmt->execute([(int) $user['id']]);
    $student = $stmt->fetch();
    if (!$student) {
        http_response_code(403);
        exit('Student profile not found.');
    }
    return $student;
}

function application_status_label(?string $status): string
{
    return $status ? ucwords(str_replace('_', ' ', $status)) : 'Not started';
}

function student_portal_start(string $title, string $active, array $student): void
{
    $nav = [
        'dashboard' => [app_url('student/dashboard.php'), 'Overview', 'home'],
        'notifications' => [app_url('student/notifications.php'), 'Notifications', 'bell'],
        'profile' => [app_url('student/profile.php'), 'Profile', 'user'],
        'application' => [app_url('student/application.php'), 'Application', 'file'],
        'documents' => [app_url('student/documents.php'), 'Documents', 'folder'],
    ];
    $icons = [
        'home' => '<svg viewBox="0 0 24 24"><path d="M3 10.8 12 3l9 7.8v9.7a.5.5 0 0 1-.5.5H15v-6H9v6H3.5a.5.5 0 0 1-.5-.5z"/></svg>',
        'bell' => '<svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>',
        'user' => '<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>',
        'file' => '<svg viewBox="0 0 24 24"><path d="M6 2h8l4 4v16H6zM14 2v5h5M9 12h6M9 16h6"/></svg>',
        'folder' => '<svg viewBox="0 0 24 24"><path d="M3 6h7l2 2h9v12H3z"/></svg>',
        'calendar' => '<svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>',
        'chat' => '<svg viewBox="0 0 24 24"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/></svg>',
    ];
    $unreadStmt = $GLOBALS['pdo']->prepare('SELECT COUNT(*) FROM messages WHERE recipient_user_id=? AND read_at IS NULL');
    $unreadStmt->execute([(int)$student['user_id']]);
    $unreadCount = (int)$unreadStmt->fetchColumn();
    ?>
    <!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <!-- Google tag (gtag.js) --><script async src="https://www.googletagmanager.com/gtag/js?id=G-FZ8QPG3B9T"></script><script>window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'G-FZ8QPG3B9T');</script>
    <title><?= esc($title) ?> | Medcon Student Portal</title><link rel="stylesheet" href="<?= esc(app_url('assets/css/app.css')) ?>"></head>
    <body class="medcon-app mc-portal-body"><a class="mc-skip" href="#main-content">Skip to content</a>
    <div class="mc-portal-shell">
    <aside class="mc-portal-sidebar" id="portal-sidebar"><a class="mc-portal-logo" href="<?= esc(app_url('student/dashboard.php')) ?>"><img src="<?= esc(site_url('img/logo.svg')) ?>" alt="Medcon"><span>Student portal</span></a><nav class="mc-portal-nav" aria-label="Student portal"><small>Workspace</small><?php foreach ($nav as $key => [$href, $label, $icon]): ?><a href="<?= esc($href) ?>"<?= $active === $key ? ' aria-current="page"' : '' ?>><span class="mc-nav-icon" aria-hidden="true"><?= $icons[$icon] ?></span><?= $label ?><?php if($key === 'notifications' && $unreadCount): ?><span class="mc-nav-count"><?= $unreadCount > 99 ? '99+' : $unreadCount ?></span><?php endif; ?></a><?php endforeach; ?><small>Support</small><a href="<?= esc(MEDCON_CONSULTATION_URL) ?>" target="_blank" rel="noopener noreferrer"><span class="mc-nav-icon" aria-hidden="true"><?= $icons['calendar'] ?></span>Book consultation</a><a href="<?= esc(MEDCON_WHATSAPP_URL) ?>" target="_blank" rel="noopener noreferrer"><span class="mc-nav-icon" aria-hidden="true"><?= $icons['chat'] ?></span>Send WhatsApp message</a></nav><div class="mc-sidebar-account"><span class="mc-user-avatar" aria-hidden="true"><?= esc(strtoupper(substr($student['first_name'],0,1).substr($student['last_name'],0,1))) ?></span><div><strong><?= esc($student['first_name'].' '.$student['last_name']) ?></strong><small>Student</small></div></div></aside>
    <div class="mc-portal-workspace"><header class="mc-portal-header"><button class="mc-menu-toggle" type="button" aria-expanded="false" aria-controls="portal-sidebar"><span aria-hidden="true">☰</span><span class="mc-sr-only">Open navigation</span></button><div><span class="mc-topbar-context">Medcon Edu</span><strong><?= esc($title) ?></strong></div><div class="mc-portal-user"><a href="<?= esc(site_url('contact.php')) ?>">Help</a><a href="<?= esc(app_url('student/notifications.php')) ?>" class="mc-notification-bell" aria-label="Notifications<?= $unreadCount ? ', '.$unreadCount.' unread' : '' ?>"><?= $icons['bell'] ?><?php if($unreadCount): ?><span><?= $unreadCount > 99 ? '99+' : $unreadCount ?></span><?php endif; ?></a><a href="<?= esc(app_url('logout.php')) ?>">Sign out</a></div></header>
    <main id="main-content" class="mc-portal-main">
    <?php
}

function student_portal_end(): void
{
    echo '</main><footer class="mc-portal-footer">Crafted by <a href="https://aqqute.com" target="_blank" rel="noopener noreferrer">Aqqute</a></footer></div></div><script>(function(){var button=document.querySelector(".mc-menu-toggle"),sidebar=document.querySelector(".mc-portal-sidebar");if(!button||!sidebar)return;button.addEventListener("click",function(){var open=document.body.classList.toggle("mc-nav-open");button.setAttribute("aria-expanded",open?"true":"false");});document.addEventListener("keydown",function(event){if(event.key==="Escape"){document.body.classList.remove("mc-nav-open");button.setAttribute("aria-expanded","false");}});})();</script></body></html>';
}
