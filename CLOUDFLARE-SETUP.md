# Cloudflare Setup & Configuration Guide for MedCon

## Overview

Cloudflare has been integrated into MedCon with:
- **Protection**: Challenge/block threats on sign-up, application, contact, eligibility, and agent pages
- **Rate Limiting**: Prevents spam and abuse (5 signups/hour, 3 contact forms/hour, etc.)
- **Caching**: Improves performance on read-heavy pages
- **Security Headers**: HSTS, CSP, X-Frame-Options, etc.

## Installation Steps

### 1. Create Cloudflare Account & Add Domain
1. Sign up at https://www.cloudflare.com
2. Add your domain (medcon.com or equivalent)
3. Note your **Zone ID** and generate an **API Key** (if programmatic setup needed)
4. Update nameservers at your domain registrar

### 2. Configure Environment Variables
Copy `.env.cloudflare.example` to `.env.cloudflare` and add your credentials:

```bash
cp .env.cloudflare.example .env.cloudflare
```

Edit `.env.cloudflare`:
```
CLOUDFLARE_ENABLED=true
CLOUDFLARE_API_KEY=your_token_here
CLOUDFLARE_ZONE_ID=your_zone_id
CLOUDFLARE_EMAIL=your@email.com
```

### 3. Load Environment Variables
In your app's bootstrap (e.g., `config.php`):
```php
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();
```

## Cloudflare Dashboard Configuration

### Pages WITH Full Protection
✅ Application forms (`/app/student/*`)
✅ Sign up (`/app/signup`)
✅ Contact form (`/contact.php`)
✅ Eligibility checker (`/eligibility-checker.php`)
✅ Agent dashboards (`/app/admin/agents`, `/app/agent/*`)

### Pages WITHOUT Cloudflare Protection
❌ Login (`/app/login`) - Users should not encounter challenges
❌ Password reset routes (`/app/forgot-password`, `/app/reset-password`)
❌ Email verification (`/app/verify-email`)

### Recommended Cloudflare Settings

#### 1. **Firewall Rules** (Cloudflare Dashboard > Security > WAF)
Create rules to protect pages:

```
(cf.threat_score > 10 AND http.request.uri.path contains "/app/signup") → Challenge
(cf.threat_score > 10 AND http.request.uri.path contains "/app/student") → Challenge
(cf.threat_score > 10 AND http.request.uri.path contains "/contact.php") → Challenge
(cf.threat_score > 10 AND http.request.uri.path contains "/eligibility-checker.php") → Challenge
(http.request.uri.path contains "/app/login") → Allow (bypass)
```

#### 2. **Rate Limiting** (Cloudflare Dashboard > Security > Rate limiting)
Set rate limits per path:

| Path | Requests | Window | Action |
|------|----------|--------|--------|
| `/app/signup` | 5 | 1 hour | Challenge |
| `/contact.php` | 3 | 1 hour | Challenge |
| `/eligibility-checker.php` | 10 | 1 hour | Challenge |
| `/app/student/submit-student` | 2 | 1 hour | Block |
| `/api/*` | 100 | 1 minute | Challenge |

#### 3. **Caching Rules** (Cloudflare Dashboard > Caching > Rules)

| Path | Cache TTL | Bypass Cache On Cookie |
|------|-----------|------------------------|
| `/eligibility-checker.php` | 5 minutes | session_id |
| `/app/admin/agents` | 10 minutes | auth_token |
| `/app/agent/dashboard` | 5 minutes | user_id |

**Static Assets**: 24 hours (CSS, JS, images)

#### 4. **Security Settings** (Cloudflare Dashboard > Security)
- **Security Level**: High
- **Web Application Firewall (WAF)**: Enabled
- **DDoS Protection**: Enabled
- **Bot Management**: Consider enabled for high-traffic apps
- **HTTPS Redirect**: Enabled
- **Always Use HTTPS**: Enabled
- **Minimum TLS Version**: 1.2

#### 5. **Page Rules** (Cloudflare Dashboard > Rules > Page Rules)
```
Pattern: example.com/app/login
  - Disable Security Challenge: ON
  - Cache Level: Bypass
  
Pattern: example.com/app/student/*
  - Security Level: High
  - Cache Level: Cache Everything
```

## Code Implementation

### Location of Configuration
- **Main Config**: `app/includes/cloudflare.php`
- **API Helper**: `app/includes/cloudflare-api.php`
- **Integrated in**: `app/includes/functions.php`, `app/includes/auth-layout.php`

### How It Works

#### Automatic on Page Load
Every page load triggers:
```php
init_cloudflare_protection(); // Runs automatically
```

This handles:
1. ✅ Applies security headers
2. ✅ Sets cache headers based on page rules
3. ✅ Checks rate limits
4. ✅ Logs violations

#### Rate Limit Tracking
Local tracking (session-based for development):
```php
if (!check_rate_limit()) {
    http_response_code(429);
    die('Too many requests. Please try again later.');
}
```

Production: Cloudflare enforces rate limits globally.

#### Cache Headers
Automatically applied:
```php
// For cacheable pages (5 min)
Cache-Control: public, max-age=300, s-maxage=300
CF-Cache-Tag: medcon-eligibility-checker

// For non-cacheable pages
Cache-Control: no-store, no-cache, must-revalidate
```

## Testing

### Test Rate Limiting (Local)
```bash
# Simulate 6 requests to signup in quick succession
for i in {1..6}; do curl http://localhost/app/signup; done
# 6th request should return 429 Too Many Requests
```

### Test Cloudflare Headers
```bash
curl -I https://your-domain.com/app/signup
# Look for:
# - CF-Cache-Tag: medcon-app-signup
# - Cache-Control: public, max-age=...
# - Strict-Transport-Security
```

### Test Login Bypass (No Challenge)
```bash
curl -I https://your-domain.com/app/login
# Should NOT have CF-RAY headers on login page
# Cloudflare challenge should be bypassed
```

## Monitoring & Maintenance

### Check Violation Logs
```bash
tail -f data/rate-limit-violations.log
```

### Purge Cache (PHP)
```php
$cf = get_cloudflare_api();
if ($cf) {
    $cf->purge_cache(['/eligibility-checker.php', '/app/admin/agents']);
}
```

### View Cloudflare Analytics
Dashboard > Analytics & Logs:
- Threats blocked
- Cache hit ratio
- Performance metrics
- Rate limit events

## Troubleshooting

### Challenge Page Showing Unexpectedly
1. Check Firewall Rules for incorrect patterns
2. Verify IP is not in allowlist
3. Check browser cookies (sometimes needed for challenge)

### Cache Not Working
1. **Check page headers**: Should include `Cache-Control` and `CF-Cache-Tag`
2. **Verify cache rule exists** in Cloudflare dashboard
3. **Check Bypass Cache On Cookie** settings (auth/session cookies)
4. **Purge cache** if recently deployed

### Rate Limiting Not Working
1. **Local (dev)**: Requires session; check `session.save_path` permissions
2. **Production**: Ensure Cloudflare Rate Limiting rule is enabled
3. **Check logs**: `data/rate-limit-violations.log`
4. **Verify client IP**: Check `get_client_ip()` returns correct IP

### Login Page Issues
1. Verify `/app/login` is in `excluded_pages` config
2. Check Cloudflare Firewall Rules don't challenge this path
3. Test: `curl https://domain.com/app/login` should not show Cloudflare page

## Performance Impact

### Expected Improvements
- **Static Content**: +50-70% faster (cached globally)
- **First Byte Time**: +10-20% faster (geographic optimization)
- **Bandwidth**: -30-50% reduction (compression, caching)

### Monitoring Performance
Cloudflare Dashboard > Analytics:
- Cache Ratio
- Response Time
- Requests Saved
- Bandwidth Saved

## Support Resources

- **Cloudflare Docs**: https://developers.cloudflare.com
- **Rate Limiting**: https://developers.cloudflare.com/rate-limiting/
- **Firewall Rules**: https://developers.cloudflare.com/firewall/
- **Caching**: https://developers.cloudflare.com/cache/

## Next Steps

1. ✅ Copy `.env.cloudflare.example` → `.env.cloudflare`
2. ✅ Add Cloudflare credentials
3. ✅ Update your domain's nameservers to Cloudflare
4. ✅ Configure rules in Cloudflare Dashboard (see sections above)
5. ✅ Test each protected page
6. ✅ Monitor logs and analytics
7. ✅ Adjust rate limits based on legitimate traffic patterns
