<?php
// Shared by every rendered website and portal page. IDs are public identifiers.
$medconPixelId = trim((string) (getenv('MEDCON_META_PIXEL_ID') ?: '1789076078941935'));
?>
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-FZ8QPG3B9T"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-FZ8QPG3B9T');
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
