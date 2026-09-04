    </main>
    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <a class="footer-brand" href="<?= e(site_url('index.php')) ?>" aria-label="Medcon home">
                    <img src="img/logo.svg" alt="Medcon">
                </a>
                <p>Medcon helps African students and families access clear guidance for international medical education opportunities through partner institution pathways.</p>
            </div>
            <div>
                <h2>Main pages</h2>
                <?php foreach ($navItems as $item): ?>
                    <a href="<?= e(site_url($item['href'])) ?>"><?= e($item['label']) ?></a>
                <?php endforeach; ?>
                <a href="<?= e(site_url('apply.php')) ?>">Apply Now</a>
            </div>
            <div>
                <h2>Information</h2>
                <a href="<?= e(site_url('faqs.php')) ?>">FAQs</a>
                <a href="<?= e(site_url('success-stories.php')) ?>">Success Stories</a>
                <a href="<?= e(site_url('privacy-policy.php')) ?>">Privacy Policy</a>
                <a href="<?= e(site_url('terms.php')) ?>">Terms of Use</a>
            </div>
            <div>
                <h2>Contact us</h2>
                <a href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener">Chat on WhatsApp</a>
                <a href="mailto:info@medconedu.org">info@medconedu.org</a>
                <a href="tel:+2347036961056">+234 703 696 1056</a>
            </div>
        </div>
        <div class="container footer-bottom">
            <span>&copy; <?= date('Y') ?> Medcon Educational Services and Consultancy Limited.</span>
            <span>Privacy Policy | Terms of Use | Accessibility</span>
        </div>
    </footer>
    <a class="whatsapp-float" href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener" aria-label="Chat with Medcon on WhatsApp">
        <svg viewBox="0 0 32 32" aria-hidden="true" focusable="false"><path d="M16.04 4.5c-6.25 0-11.34 5.02-11.34 11.2 0 2.12.6 4.18 1.74 5.96l-1.84 5.84 6.06-1.76a11.5 11.5 0 0 0 5.38 1.35c6.25 0 11.34-5.02 11.34-11.2S22.29 4.5 16.04 4.5Zm0 20.66c-1.74 0-3.45-.47-4.94-1.37l-.35-.21-3.55 1.03 1.08-3.42-.23-.36a9.22 9.22 0 0 1-1.43-4.94c0-5.12 4.22-9.29 9.42-9.29s9.42 4.17 9.42 9.29-4.22 9.27-9.42 9.27Zm5.16-6.95c-.28-.14-1.66-.81-1.92-.9-.26-.1-.45-.14-.64.14-.19.27-.73.9-.9 1.08-.17.18-.33.2-.61.07-.28-.14-1.18-.43-2.25-1.37-.83-.73-1.39-1.64-1.55-1.91-.16-.28-.02-.43.12-.57.13-.13.28-.33.42-.49.14-.17.19-.28.28-.46.09-.18.05-.34-.02-.48-.07-.14-.64-1.52-.88-2.08-.23-.55-.47-.47-.64-.48h-.55c-.19 0-.49.07-.75.34-.26.28-.99.96-.99 2.34s1.02 2.72 1.16 2.9c.14.18 2 3.02 4.85 4.23.68.29 1.21.46 1.62.59.68.21 1.3.18 1.79.11.55-.08 1.66-.67 1.9-1.32.23-.65.23-1.21.16-1.32-.07-.12-.26-.19-.54-.32Z"/></svg>
    </a>
</body>
</html>
