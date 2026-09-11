<?php
// Shared by every rendered website and portal page. IDs are public identifiers.
$medconPixelId = trim((string) (getenv('MEDCON_META_PIXEL_ID') ?: '1789076078941935'));
require_once __DIR__ . '/site-activity.php';
echo site_activity_markup();
?>
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-FZ8QPG3B9T"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-FZ8QPG3B9T');
</script>
<?php
$inquiryConversion = $_SESSION['inquiry_conversion'] ?? null;
if (is_array($inquiryConversion)
    && ($inquiryConversion['expires'] ?? 0) >= time()
    && ($_SERVER['REQUEST_METHOD'] ?? '') === 'GET'
    && ($_GET['inquiry'] ?? '') === 'received'
    && ($inquiryConversion['path'] ?? null) === parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH)):
    // Consume before rendering so refreshes do not send another conversion.
    unset($_SESSION['inquiry_conversion']);
?>
<!-- Google tag (gtag.js) event -->
<script>
gtag('event', 'conversion_event_default', {});
</script>
<?php endif; ?>
<!-- Google tag (gtag.js) event - delayed navigation helper -->
<script>
// Call in response to an action that should navigate after sending the event.
function gtagSendEvent(url) {
    var callback = function () {
        if (typeof url === 'string') {
            window.location = url;
        }
    };
    gtag('event', 'ads_conversion_Submit_lead_form_1', {
        'event_callback': callback,
        'event_timeout': 2000
    });
    return false;
}
</script>
<?php if (preg_match('/^[0-9]+$/D', $medconPixelId)): ?>
<!-- Meta Pixel -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;
s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}
(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', <?= json_encode($medconPixelId) ?>);
fbq('track', 'PageView');
</script>
<?php endif; ?>
