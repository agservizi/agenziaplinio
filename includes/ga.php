<!-- Google Analytics 4 -->
<?php
$gaId = ap_env('GA_MEASUREMENT_ID', '');
$cookieConsent = isset($_COOKIE['ap_cookie_consent']) ? $_COOKIE['ap_cookie_consent'] : null;

if (!empty($gaId) && $cookieConsent === 'accepted'):
?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($gaId, ENT_QUOTES); ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('consent', 'default', {
    'analytics_storage': 'denied'
  });
  gtag('config', '<?php echo htmlspecialchars($gaId, ENT_QUOTES); ?>');
</script>
<?php endif; ?>