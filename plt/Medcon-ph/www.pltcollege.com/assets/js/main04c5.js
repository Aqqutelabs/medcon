/* ================================================================
 * pltcollege.com — Common JavaScript
 * PLTCI Asia Pacific College of Medicine & Research
 * ================================================================ */

/* ── Mobile hamburger toggle ── */
function toggleMob(btn) {
  const nav = document.getElementById('mobnav');
  btn.classList.toggle('open');
  nav.classList.toggle('open');
}

/* ── Mobile sub-menu accordion ── */
function toggleMobSec(btn) {
  const sec = btn.parentElement;
  const isOpen = sec.classList.contains('open');
  document.querySelectorAll('.mob-sec').forEach(s => s.classList.remove('open'));
  if (!isOpen) sec.classList.add('open');
}

/* ── FAQ accordion ── */
function toggleFaq(item) {
  const isOpen = item.classList.contains('open');
  document.querySelectorAll('.fqi').forEach(el => el.classList.remove('open'));
  if (!isOpen) item.classList.add('open');
}

/* ── Sidebar scroll-spy ── */
function initScrollSpy() {
  const sections = document.querySelectorAll('.content-block[id]');
  const sideLinks = document.querySelectorAll('.sidebar-link');
  if (!sections.length || !sideLinks.length) return;

  const secObs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const id = entry.target.id;
        sideLinks.forEach(l => l.classList.remove('active'));
        const active = document.querySelector('.sidebar-link[href="#' + id + '"]');
        if (active) active.classList.add('active');
      }
    });
  }, { rootMargin: '-15% 0px -70% 0px' });

  sections.forEach(s => secObs.observe(s));
}

/* ── Scroll reveal animations ── */
function initReveal() {
  const obs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('vis');
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.08 });
  document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
}

/* ── Smooth scroll with header offset ── */
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href === '#') return;
      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        const offset = window.innerWidth < 768 ? 80 : 90;
        window.scrollTo({
          top: target.getBoundingClientRect().top + window.pageYOffset - offset,
          behavior: 'smooth'
        });
      }
    });
  });
}

/* ── Blog category filter ── */
function filterCat(btn, cat) {
  document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.blog-card').forEach(card => {
    if (cat === 'all' || (card.dataset.cat && card.dataset.cat.includes(cat))) {
      card.style.display = '';
    } else {
      card.style.display = 'none';
    }
  });
}

/* ── Contact form submit ── */
function handleSubmit() {
  alert('Thank you! Our counsellor will contact you within 24 hours.\n\nOr WhatsApp us directly: +91 89393 30330');
}

/* ── Quick-nav tab active sync ── */
function initQuickNav() {
  const sections = document.querySelectorAll('.content-block[id]');
  const qnTabs = document.querySelectorAll('.qn-tab');
  if (!sections.length || !qnTabs.length) return;

  const obs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const id = entry.target.id;
        qnTabs.forEach(t => t.classList.remove('active'));
        const qt = document.querySelector('.qn-tab[href="#' + id + '"]');
        if (qt) {
          qt.classList.add('active');
          qt.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
      }
    });
  }, { rootMargin: '-15% 0px -70% 0px' });

  sections.forEach(s => obs.observe(s));
}

/* ── PHP contact form AJAX handler ── */
function submitContactForm(formId) {
  const form = document.getElementById(formId);
  if (!form) return;

  form.addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn ? btn.textContent : '';
    if (btn) { btn.textContent = 'Sending...'; btn.disabled = true; btn.style.opacity = '0.7'; }

    // Basic client-side validation
    const name = form.querySelector('input[name="name"]');
    const mobile = form.querySelector('input[name="mobile"]');
    if (!name || !name.value.trim()) {
      alert('Please enter your name');
      if (name) name.focus();
      if (btn) { btn.textContent = originalText; btn.disabled = false; btn.style.opacity = '1'; }
      return;
    }
    if (!mobile || mobile.value.replace(/\D/g, '').length < 10) {
      alert('Please enter a valid 10-digit mobile number');
      if (mobile) mobile.focus();
      if (btn) { btn.textContent = originalText; btn.disabled = false; btn.style.opacity = '1'; }
      return;
    }

    const data = new FormData(form);

    fetch('/form-handler.php', {
      method: 'POST',
      body: data,
      credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        showFormSuccess(form, btn, originalText);
      } else {
        alert(d.message || 'Please try again or WhatsApp us: +91 89393 30330');
        if (btn) { btn.textContent = originalText; btn.disabled = false; btn.style.opacity = '1'; }
      }
    })
    .catch((err) => {
      console.error('Form submit error:', err);
      // Fallback — show WhatsApp option
      const ok = confirm('Submission had an issue. Click OK to message us on WhatsApp instead, or Cancel to try again later.');
      if (ok) {
        window.open('https://wa.me/918939330330?text=Hi%20I%20want%20to%20enquire%20about%20MBBS%20Philippines', '_blank');
      }
      if (btn) { btn.textContent = originalText; btn.disabled = false; btn.style.opacity = '1'; }
    });
  });
}

function showFormSuccess(form, btn, originalText) {
  // Replace form with success message
  const wrapper = form.parentElement;
  const successHtml = `
    <div style="text-align:center; padding:40px 20px;">
      <div style="font-size:3rem; margin-bottom:12px;">✅</div>
      <h3 style="color:#0a1f5c; font-family:'Plus Jakarta Sans',sans-serif; font-size:1.3rem; font-weight:700; margin-bottom:10px;">Thank You! Enquiry Received</h3>
      <p style="color:#5d6b82; font-size:.95rem; line-height:1.65; margin-bottom:20px; max-width:380px; margin-left:auto; margin-right:auto;">Our admission counsellor will contact you within 24 hours. For faster response, message us on WhatsApp.</p>
      <a href="https://wa.me/918939330330" target="_blank" style="display:inline-block; background:#25d366; color:#fff; padding:13px 26px; border-radius:10px; font-weight:700; text-decoration:none; font-size:.92rem;">💬 WhatsApp Us Now</a>
    </div>
  `;
  form.innerHTML = successHtml;
  form.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

/* ── DOMContentLoaded — init all ── */
document.addEventListener('DOMContentLoaded', function() {
  initReveal();
  initSmoothScroll();
  initScrollSpy();
  initQuickNav();
  submitContactForm('contact-form');
  submitContactForm('main-enquiry-form');
  submitContactForm('sidebar-form');
});
