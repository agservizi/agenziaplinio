<?php
declare(strict_types=1);

function ap_site_profile(): array
{
    static $site = null;
    if ($site === null) {
        $config = require __DIR__ . '/config.php';
        $site = $config['site'] ?? [];
    }
    return $site;
}

function ap_email_layout(array $data): string
{
    $site = ap_site_profile();
    $siteName = $site['name'] ?? 'Agenzia Plinio';
    $business = $site['business'] ?? [];
    $businessEmail = $business['email'] ?? 'info@agenziaplinio.it';
    $businessPhone = $business['telephone'] ?? '+39 081 058 45 42';
    $addressParts = [];
    if (!empty($business['address']['street'])) {
        $addressParts[] = $business['address']['street'];
    }
    $cityLine = trim(($business['address']['postal_code'] ?? '') . ' ' . ($business['address']['locality'] ?? ''));
    if ($cityLine !== '') {
        $addressParts[] = $cityLine;
    }
    $addressLine = implode(' · ', array_filter($addressParts));

    $appUrl = rtrim(ap_env('APP_URL', 'http://127.0.0.1:8000') ?? 'http://127.0.0.1:8000', '/');
    $logoUrl = $data['logo_url'] ?? ($appUrl . '/assets/img/logo.png');
    $heroTitle = htmlspecialchars($data['hero_title'] ?? $siteName, ENT_QUOTES);
    $heroSubtitle = htmlspecialchars($data['hero_subtitle'] ?? '', ENT_QUOTES);
    $bodyHtml = $data['body_html'] ?? '';
    $footerNote = $data['footer_note'] ?? 'Non rispondere a questa email, è generata automaticamente.';
    $footerNoteEsc = htmlspecialchars($footerNote, ENT_QUOTES);

    $ctaHtml = '';
    if (!empty($data['cta']['label']) && !empty($data['cta']['url'])) {
        $ctaLabel = htmlspecialchars($data['cta']['label'], ENT_QUOTES);
        $ctaUrl = htmlspecialchars($data['cta']['url'], ENT_QUOTES);
        $ctaHtml = '<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;">'
            . '<tr><td align="center">'
            . '<a href="' . $ctaUrl . '" class="button">' . $ctaLabel . '</a>'
            . '</td></tr></table>';
    }

    $siteNameEsc = htmlspecialchars($siteName, ENT_QUOTES);
    $businessEmailEsc = htmlspecialchars($businessEmail, ENT_QUOTES);
    $businessPhoneEsc = htmlspecialchars($businessPhone, ENT_QUOTES);
    $addressLineEsc = htmlspecialchars($addressLine, ENT_QUOTES);
    $logoUrlEsc = htmlspecialchars($logoUrl, ENT_QUOTES);

    return <<<HTML
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title>{$heroTitle}</title>
<style>
  body { margin:0; padding:0; background:#f2f4f7; }
  table { border-collapse:collapse; }
  img { border:0; display:block; }
  .button {
    background:#0a5bb5;
    color:#ffffff;
    padding:14px 26px;
    font-size:16px;
    font-weight:bold;
    text-decoration:none;
    border-radius:6px;
    display:inline-block;
  }
  @media only screen and (max-width:600px) {
    .container { width:100% !important; }
    .hero-text { font-size:22px !important; }
  }
</style>
</head>
<body>
<table width="100%" bgcolor="#f2f4f7" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" style="padding:20px 10px;">
      <table width="600" class="container" bgcolor="#ffffff" cellpadding="0" cellspacing="0" style="border-radius:8px; overflow:hidden;">
        <tr>
          <td>
            <table width="100%" bgcolor="#0a5bb5">
              <tr>
                <td align="center" style="padding:40px 20px;">
                  <img src="{$logoUrlEsc}" alt="{$siteNameEsc}" width="120" height="auto" style="margin-bottom:16px;">
                  <h1 class="hero-text" style="color:#ffffff; font-size:26px; font-weight:600; margin:0; font-family:Arial,Helvetica;">
                    {$heroTitle}
                  </h1>
                  <p style="color:#dbe9ff; font-size:15px; margin-top:10px; font-family:Arial,Helvetica;">
                    {$heroSubtitle}
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td style="padding:30px 25px; font-family:Arial,Helvetica; color:#333;">
            {$bodyHtml}
            {$ctaHtml}
          </td>
        </tr>
        <tr>
          <td bgcolor="#f7f7f7" style="padding:20px; text-align:center; font-size:12px; color:#777; font-family:Arial,Helvetica;">
            {$siteNameEsc} &middot; {$businessEmailEsc} &middot; {$businessPhoneEsc}<br>
            {$addressLineEsc}<br>
            {$footerNoteEsc}<br>
            &copy; {$siteNameEsc} &mdash; Tutti i diritti riservati
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
HTML;
}
