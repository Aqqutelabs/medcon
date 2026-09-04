<?php

function admin_portal_header(array $user, string $active): void {
    $links = [
        'applications' => ['Applications', 'admin/dashboard.php'],
        'agents' => ['Agents', 'admin/agents.php'],
        'inquiries' => ['Inquiries', 'admin/inquiries.php'],
        'leads' => ['Eligibility leads', 'admin/leads.php'],
    ];
    ?>
    <header class="mc-role-topbar mc-admin-topbar">
        <a href="<?= esc(app_url('admin/dashboard.php')) ?>" class="mc-portal-logo" aria-label="Medcon admin dashboard">
            <img src="<?= esc(site_url('img/logo.svg')) ?>" alt="Medcon">
            <span>Admin portal</span>
        </a>
        <nav class="mc-admin-nav" aria-label="Admin portal">
            <?php foreach ($links as $key => [$label, $href]): ?>
                <a href="<?= esc(app_url($href)) ?>"<?= $active === $key ? ' aria-current="page"' : '' ?>><?= esc($label) ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="mc-portal-user mc-admin-user">
            <span><?= esc($user['full_name']) ?></span>
            <a href="<?= esc(app_url('logout.php')) ?>">Sign out</a>
        </div>
    </header>
    <?php
}

function admin_portal_footer(): void {
    ?>
    <footer class="mc-portal-footer"><span>&copy; <?= date('Y') ?> Medcon Educational Services and Consultancy Limited</span><span>Crafted by <a href="https://aqqute.com" target="_blank" rel="noopener noreferrer">Aqqute</a></span></footer>
    <?php
}
