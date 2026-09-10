    </main>
    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <a class="footer-brand" href="<?= e(site_url('')) ?>" aria-label="Medcon home">
                    <img src="<?= e(site_url('img/logo.svg')) ?>" alt="Medcon">
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
                <a href="<?= e(site_url('accreditation')) ?>">Accreditation</a>
                <a href="<?= e(site_url('success-stories.php')) ?>">Success Stories</a>
                <a href="<?= e(site_url('privacy-policy.php')) ?>">Privacy Policy</a>
                <a href="<?= e(site_url('terms.php')) ?>">Terms of Use</a>
            </div>
            <div>
                <h2>Contact us</h2>
                <a href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener">Chat on WhatsApp</a>
                <a href="https://wa.me/2347088893414" target="_blank" rel="noopener">WhatsApp: +234 708 889 3414</a>
                <a href="https://wa.me/2348031605998" target="_blank" rel="noopener">WhatsApp: +234 803 160 5998</a>
                <a href="mailto:info@medconedu.org">info@medconedu.org</a>
                <a href="tel:+2347036961056">+234 703 696 1056</a>
                <a href="tel:+2347088893414">+234 708 889 3414</a>
                <a href="tel:+2348031605998">+234 803 160 5998</a>
                <nav class="footer-socials" aria-label="Follow Medcon on social media">
                    <a href="https://web.facebook.com/profile.php?id=61593932550773" target="_blank" rel="noopener noreferrer" aria-label="Medcon on Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4h-3c-3.3 0-5 2-5 5v2H6v4h3v7h4v-7h3.3l.7-4h-4V9c0-.7.3-1 1-1Z"/></svg></a>
                    <a href="https://x.com/Medconedu" target="_blank" rel="noopener noreferrer" aria-label="Medcon on X"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 3h4.8l4.1 5.8L18 3h2l-6.2 7.2L21 21h-4.8l-4.6-6.5L6 21H4l6.7-7.9L4 3Zm3.7 1.8 9.5 14.4h1.9L9.6 4.8H7.7Z"/></svg></a>
                    <a href="https://tiktok.com/medconedu" target="_blank" rel="noopener noreferrer" aria-label="Medcon on TikTok"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3h3c.3 2.1 1.6 3.5 4 4v3c-1.6 0-3-.5-4-1.3V15a6 6 0 1 1-6-6v3a3 3 0 1 0 3 3V3Z"/></svg></a>
                    <a href="https://www.instagram.com/medconedu/" target="_blank" rel="noopener noreferrer" aria-label="Medcon on Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4.2"/><circle cx="17.4" cy="6.7" r="1" class="footer-social-dot"/></svg></a>
                </nav>
            </div>
        </div>
        <div class="container footer-bottom">
            <span>&copy; <?= date('Y') ?> Medcon Educational Services and Consultancy Limited. RC: 9626491</span>
            <span>Privacy Policy | Terms of Use | Accessibility</span>
            <span>Crafted by <a href="https://aqqute.com" target="_blank" rel="noopener noreferrer">Aqqute</a></span>
        </div>
    </footer>
    <a class="whatsapp-float" href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener" aria-label="Chat with Medcon on WhatsApp">
        <svg viewBox="0 0 32 32" aria-hidden="true" focusable="false"><path d="M16.04 4.5c-6.25 0-11.34 5.02-11.34 11.2 0 2.12.6 4.18 1.74 5.96l-1.84 5.84 6.06-1.76a11.5 11.5 0 0 0 5.38 1.35c6.25 0 11.34-5.02 11.34-11.2S22.29 4.5 16.04 4.5Zm0 20.66c-1.74 0-3.45-.47-4.94-1.37l-.35-.21-3.55 1.03 1.08-3.42-.23-.36a9.22 9.22 0 0 1-1.43-4.94c0-5.12 4.22-9.29 9.42-9.29s9.42 4.17 9.42 9.29-4.22 9.27-9.42 9.27Zm5.16-6.95c-.28-.14-1.66-.81-1.92-.9-.26-.1-.45-.14-.64.14-.19.27-.73.9-.9 1.08-.17.18-.33.2-.61.07-.28-.14-1.18-.43-2.25-1.37-.83-.73-1.39-1.64-1.55-1.91-.16-.28-.02-.43.12-.57.13-.13.28-.33.42-.49.14-.17.19-.28.28-.46.09-.18.05-.34-.02-.48-.07-.14-.64-1.52-.88-2.08-.23-.55-.47-.47-.64-.48h-.55c-.19 0-.49.07-.75.34-.26.28-.99.96-.99 2.34s1.02 2.72 1.16 2.9c.14.18 2 3.02 4.85 4.23.68.29 1.21.46 1.62.59.68.21 1.3.18 1.79.11.55-.08 1.66-.67 1.9-1.32.23-.65.23-1.21.16-1.32-.07-.12-.26-.19-.54-.32Z"/></svg>
    </a>
</body>
</html>
