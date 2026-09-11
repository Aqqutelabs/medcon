(() => {
    'use strict';
    if (window.medconInternalAnalytics || navigator.webdriver) return;
    window.medconInternalAnalytics = true;
    const source = document.currentScript;
    if (!source) return;
    const endpoint = new URL('../site-event.php', source.src);
    const id = () => window.crypto?.randomUUID?.() || Date.now().toString(36) + Math.random().toString(36).slice(2) + Math.random().toString(36).slice(2);
    let tokenPromise;
    const token = () => tokenPromise || (tokenPromise = fetch(endpoint, {credentials: 'same-origin', cache: 'no-store'})
        .then(response => { if (!response.ok) throw new Error('Analytics unavailable'); return response.json(); }));
    const send = async (kind, button = '') => {
        try {
            const access = await token();
            await fetch(endpoint, {method: 'POST', credentials: 'same-origin', keepalive: true,
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({token: access.token, kind, button, id: id()})});
        } catch (_) { /* Never interrupt a visitor when analytics is unavailable. */ }
    };
    let viewed = false;
    const view = () => {
        if (!viewed && document.visibilityState === 'visible') { viewed = true; send('view'); }
    };
    view();
    document.addEventListener('visibilitychange', view);
    document.addEventListener('click', event => {
        if (!event.isTrusted || document.visibilityState !== 'visible') return;
        const element = event.target.closest('button, a[href], input[type="submit"], input[type="button"], [role="button"]');
        if (!element || element.disabled || element.closest('[data-analytics-ignore]')) return;
        const controls = [...document.querySelectorAll('button, a[href], input[type="submit"], input[type="button"], [role="button"]')];
        // Only public control labels; never collect form values or destination URLs.
        const known = ['share-estimate','download-estimate'].find(name => element.hasAttribute('data-' + name));
        const explicit = element.getAttribute('data-analytics-id');
        const text = (element.getAttribute('aria-label') || (element.tagName === 'INPUT' ? '' : element.textContent) || '')
            .replace(/[^a-zA-Z0-9_.:# -]/g, ' ').replace(/\s+/g, ' ').trim().slice(0, 100);
        let label = explicit || (known ? known : (text || element.tagName.toLowerCase()) + ' #' + (controls.indexOf(element) + 1));
        if (!/^[a-zA-Z0-9_.:# -]{1,160}$/.test(label)) label = 'control:' + (controls.indexOf(element) + 1);
        view(); send('click', label);
    }, true);
})();
