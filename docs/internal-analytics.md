# Internal website analytics

Open **Admin → Website analytics** (`app/admin/calculator.php`). Website reports and the existing calculator counters share this page.

The website report counts unique anonymous browsers, page views, and button/link clicks for today or the last 7, 30, or 90 days. Visitors are deduplicated within the chosen period, globally and per page. Totals across page rows must not be added to derive the site visitor total. Calculator counters retain their separate, earlier definitions and all-time history.

## Deployment

- Deploy `site-event.php`, `includes/site-activity.php`, `scripts/internal-analytics.js`, and the updated shared analytics and admin files.
- Enable PHP **PDO SQLite** and allow PHP to write to `data/`. The database is created automatically; no MySQL migration is needed.
- Preserve `data/.htaccess` (`Require all denied`) to deny public access to analytics files. On servers other than Apache, configure an equivalent deny rule for `/data/`.
- Keep `/site-event.php` out of CDN caches. Responses include `Cache-Control: no-store`.
- Retain the SQLite database when deploying future updates; it contains both event history and the cookie-signing secret.

## Counting rules

- One visible page-load event per document, protected against replay using a server-issued event ID. Reloads add page views, but reuse the visitor cookie.
- A signed, HttpOnly, SameSite first-party cookie lasts one year. Only its pseudonymous hash is stored with events. Cookies identify browsers, not people; blocked cookies prevent event recording. Clearing cookies, different devices, and different browser profiles can increase visitor counts.
- Public site pages, including school microsites, are included. Portal/admin pages, collectors, and unknown paths are excluded. PHP suffixes, directory index paths, query strings, and fragments are normalized out of page reports.
- Only browser-reported trusted clicks are sent. Known bot user agents and browser automation are excluded, but these checks cannot prove every visitor is human. Per-browser rate limiting limits bursts to 120 accepted events per minute.
- Button labels use public text plus control position. Add a stable `data-analytics-id="apply-primary"` to distinguish important controls across layout changes. Add `data-analytics-ignore` to a control or parent to exclude its clicks. Form values and destination URLs are not collected.
- Events older than 90 days are removed on subsequent writes. No old visits are estimated or backfilled. Reports can undercount when scripts, cookies, referrer headers, or network requests are blocked.
- Browser delivery is best effort; a click count is not proof that the destination opened or a form completed. Use the separate calculator events for native-share completion and clipboard-copy success.

## Verification

Run `node scripts/test-internal-analytics.cjs <php-executable>`. Tests start an isolated local PHP server and temporary database; they do not add events to the website's actual counters.
