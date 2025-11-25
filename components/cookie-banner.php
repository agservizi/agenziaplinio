<?php
// Cookie banner component
$cookieConsent = isset($_COOKIE['ap_cookie_consent']) ? $_COOKIE['ap_cookie_consent'] : null;
if (!$cookieConsent):
?>
<div class="cookie-banner">
    <div class="cookie-banner__content">
        <div class="cookie-banner__text">
            <h4>🍪 Rispettiamo la tua privacy</h4>
            <p>
                Utilizziamo cookie tecnici essenziali per il funzionamento del sito e cookie analitici per migliorare la tua esperienza.
                I tuoi dati sono protetti secondo il GDPR e non vengono venduti a terzi.
                <a href="?page=privacy" target="_blank">Scopri di più sulla nostra privacy policy</a>.
            </p>
        </div>
        <div class="cookie-banner__buttons">
            <button type="button" class="ap-btn ap-btn--ghost" onclick="CookieBanner.reject()">
                Rifiuta
            </button>
            <button type="button" class="ap-btn ap-btn--primary" onclick="CookieBanner.accept()">
                Accetta
            </button>
        </div>
    </div>
</div>
<?php endif; ?>