<?php
declare(strict_types=1);

function medcon_ga4_markup(): string
{
    return '<!-- Google tag (gtag.js) --><script async src="https://www.googletagmanager.com/gtag/js?id=G-FZ8QPG3B9T"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag("js",new Date());gtag("config","G-FZ8QPG3B9T");</script><!-- Google tag (gtag.js) event - delayed navigation helper --><script>function gtagSendEvent(url){var callback=function(){if(typeof url==="string"){window.location=url;}};gtag("event","ads_conversion_Submit_lead_form_1",{"event_callback":callback,"event_timeout":2000});return false;}</script>';
}

function medcon_meta_pixel_markup(): string
{
    return '<!-- Meta Pixel Code --><script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version="2.0";n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,"script","https://connect.facebook.net/en_US/fbevents.js");fbq("init","1789076078941935");fbq("track","PageView");</script><!-- End Meta Pixel Code -->';
}

function medcon_meta_pixel_noscript(): string
{
    return '<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1789076078941935&amp;ev=PageView&amp;noscript=1" alt=""></noscript>';
}

function medcon_enable_ga4(): void
{
    static $enabled=false;
    if($enabled)return;
    $enabled=true;
    ob_start(static function (string $html): string {
        $headEnd = stripos($html, '</head>');
        if ($headEnd === false) return $html;
        $headMarkup='';
        require_once dirname(__DIR__, 2) . '/includes/site-activity.php';
        if(strpos($html,'internal-analytics.js')===false)$headMarkup.=site_activity_markup();
        if(strpos($html,'G-FZ8QPG3B9T')===false)$headMarkup.=medcon_ga4_markup();
        if(strpos($html,'1789076078941935')===false)$headMarkup.=medcon_meta_pixel_markup();
        if($headMarkup!=='')$html=substr_replace($html,$headMarkup,$headEnd,0);
        if(strpos($html,'facebook.com/tr?id=1789076078941935')===false){$bodyStart=stripos($html,'<body');if($bodyStart!==false){$bodyOpenEnd=strpos($html,'>',$bodyStart);if($bodyOpenEnd!==false)$html=substr_replace($html,medcon_meta_pixel_noscript(),$bodyOpenEnd+1,0);}}
        return $html;
    });
}
