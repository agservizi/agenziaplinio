<?php
declare(strict_types=1);

require_once __DIR__ . '/../mailer.php';
require_once __DIR__ . '/../ecommerce/helpers.php';
require_once __DIR__ . '/../email-template.php';

function ap_mail_settings(): array
{
    static $settings = null;
    if ($settings === null) {
        $config = require __DIR__ . '/../config.php';
        $settings = $config['smtp'] ?? [];
    }
    return $settings;
}

function ap_get_order_payment_provider(int $orderId): string
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT provider FROM payments WHERE order_id = ? LIMIT 1');
    $stmt->execute([$orderId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['provider'] ?? 'manual';
}

function ap_notify_order_received(int $orderId): void
{
    $bundle = ap_fetch_order_bundle($orderId);
    if (!$bundle) {
        return;
    }
    $order = $bundle['order'];
    $items = $bundle['items'];
    $customerEmail = $order['customer_email'];
    if (!$customerEmail) {
        return;
    }
    $provider = ap_get_order_payment_provider($orderId);
    $subject = sprintf('Conferma ordine #%d', $orderId);
    $intro = 'Grazie per il tuo ordine!';
    if ($provider === 'wire') {
        $intro .= ' Per completare il pagamento, effettua un bonifico bancario alle seguenti coordinate:<br><br>' .
            '<strong>Banca:</strong> [Nome Banca]<br>' .
            '<strong>IBAN:</strong> [IBAN]<br>' .
            '<strong>Intestatario:</strong> Agenzia Plinio<br>' .
            '<strong>Causale:</strong> Ordine #' . $orderId . '<br><br>' .
            'Una volta effettuato il bonifico, invia la ricevuta a <a href="mailto:pagamenti@agenziaplinio.it">pagamenti@agenziaplinio.it</a> per confermare il pagamento.';
    }
    $body = ap_render_order_email($order, $items, $intro);
    ap_mailer()->send([
        'from' => ap_mail_settings()['from'] ?? 'no-reply@agenziaplinio.it',
        'to' => $customerEmail,
        'subject' => $subject,
        'body' => $body,
    ]);

    $adminSubject = sprintf('Nuovo ordine #%d ricevuto', $orderId);
    $adminBody = ap_render_order_email($order, $items, 'Nuovo ordine online pronto da lavorare.');
    ap_mailer()->send([
        'from' => ap_mail_settings()['from'] ?? 'no-reply@agenziaplinio.it',
        'to' => ap_mail_settings()['to'] ?? $customerEmail,
        'subject' => $adminSubject,
        'body' => $adminBody,
    ]);
}

function ap_notify_order_status(int $orderId, string $status): void
{
    $bundle = ap_fetch_order_bundle($orderId);
    if (!$bundle) {
        return;
    }
    $order = $bundle['order'];
    $customerEmail = $order['customer_email'];
    if (!$customerEmail) {
        return;
    }
    $subject = sprintf('Aggiornamento ordine #%d: %s', $orderId, ucfirst($status));
    $body = '<p>Ciao ' . htmlspecialchars($order['shipping_name'], ENT_QUOTES) . ',</p>' .
        '<p>lo stato del tuo ordine è ora <strong>' . htmlspecialchars(ucfirst($status), ENT_QUOTES) . "</strong>.</p>" .
        ($order['tracking_code'] ? '<p>Tracking: <strong>' . htmlspecialchars($order['tracking_code'], ENT_QUOTES) . '</strong></p>' : '') .
        '<p>Per qualsiasi dubbio rispondi a questa email.</p>';
    ap_mailer()->send([
        'from' => ap_mail_settings()['from'] ?? 'no-reply@agenziaplinio.it',
        'to' => $customerEmail,
        'subject' => $subject,
        'body' => $body,
    ]);
}

function ap_render_order_email(array $order, array $items, string $intro): string
{
    $rows = '';
    foreach ($items as $item) {
        $rows .= sprintf(
            '<tr><td style="padding:6px 12px;border:1px solid #dde2ec;">%s × %d</td><td style="padding:6px 12px;border:1px solid #dde2ec;">%s</td></tr>',
            htmlspecialchars($item['name'], ENT_QUOTES),
            (int) $item['quantity'],
            ap_price_format((int) $item['price_cents'])
        );
    }
    $shippingLine = sprintf('<p><strong>Spedizione:</strong> %s (%s)</p>',
        htmlspecialchars(ucfirst($order['shipping_method'] ?? 'standard'), ENT_QUOTES),
        ap_price_format((int) ($order['shipping_cost_cents'] ?? 0))
    );
    $addons = ap_order_meta_array($order['addons'] ?? null);
    $opsChannels = ap_order_meta_array($order['ops_channels'] ?? null);
    $attachments = ap_order_procurement_files($order['procurement_files'] ?? null);
    $windowLabels = [
        'standard' => '08:00-18:00',
        'early' => '06:00-09:00',
        'late' => '18:00-22:00',
    ];
    $slaBadges = [
        'core' => 'Assistenza standard 24h',
        'priority' => 'Gestione prioritaria 12h',
        'mission' => 'Supporto critico 4h',
    ];
    $enterpriseItems = [];
    if (!empty($order['po_number'])) {
        $enterpriseItems[] = 'PO: <strong>' . htmlspecialchars($order['po_number'], ENT_QUOTES) . '</strong>';
    }
    if (!empty($order['cost_center'])) {
        $enterpriseItems[] = 'Centro di costo: ' . htmlspecialchars($order['cost_center'], ENT_QUOTES);
    }
    if (!empty($order['sla_plan'])) {
        $enterpriseItems[] = 'Livello di servizio: ' . htmlspecialchars($slaBadges[$order['sla_plan']] ?? strtoupper($order['sla_plan']), ENT_QUOTES);
    }
    if (!empty($order['delivery_window'])) {
        $enterpriseItems[] = 'Finestra: ' . htmlspecialchars($windowLabels[$order['delivery_window']] ?? $order['delivery_window'], ENT_QUOTES);
    }
    if (!empty($addons)) {
        $enterpriseItems[] = 'Add-on: ' . htmlspecialchars(implode(', ', array_map(static function ($value) {
            return ucwords(str_replace('-', ' ', (string) $value));
        }, $addons)), ENT_QUOTES);
    }
    if (!empty($opsChannels)) {
        $enterpriseItems[] = 'Canali operativi: ' . htmlspecialchars(implode(', ', array_map(static function ($value) {
            return ucfirst((string) $value);
        }, $opsChannels)), ENT_QUOTES);
    }
    if (!empty($order['contract_ref'])) {
        $enterpriseItems[] = 'Riferimento contratto: ' . htmlspecialchars($order['contract_ref'], ENT_QUOTES);
    }
    if (!empty($attachments)) {
        $enterpriseItems[] = 'Documenti condivisi: ' . count($attachments);
    }
    $enterpriseBlock = '';
    if (!empty($enterpriseItems)) {
        $enterpriseBlock = '<p><strong>Dettagli enterprise</strong></p><ul style="padding-left:18px;">';
        foreach ($enterpriseItems as $item) {
            $enterpriseBlock .= '<li style="margin-bottom:4px;">' . $item . '</li>';
        }
        $enterpriseBlock .= '</ul>';
    }
    return '<div style="font-family:Arial,sans-serif;color:#1c2440;">'
        . '<h2 style="color:#2d6bff;">Agenzia Plinio</h2>'
        . '<p>' . $intro . '</p>'
        . '<table style="border-collapse:collapse;margin:20px 0;width:100%;">'
        . '<thead><tr><th style="text-align:left;padding:6px 12px;border:1px solid #dde2ec;">Prodotto</th>'
        . '<th style="text-align:left;padding:6px 12px;border:1px solid #dde2ec;">Prezzo unitario</th></tr></thead>'
        . '<tbody>' . $rows . '</tbody>'
        . '</table>'
        . '<p><strong>Totale:</strong> ' . ap_price_format((int) $order['total_cents']) . '</p>'
        . $shippingLine
        . '<p><strong>Indirizzo spedizione:</strong><br>' . nl2br(htmlspecialchars($order['shipping_address'], ENT_QUOTES)) . '</p>'
        . $enterpriseBlock
        . '<p style="margin-top:24px;font-size:0.9rem;color:#687294;">Ordine #' . (int) $order['id'] . '</p>'
        . '</div>';
}

function ap_notify_abandoned_cart(array $cart): void
{
    $email = $cart['email'] ?? null;
    if (!$email) {
        return;
    }
    $items = json_decode((string) ($cart['cart_snapshot'] ?? '[]'), true);
    if (!is_array($items) || empty($items)) {
        return;
    }
    $subject = 'Hai dimenticato il carrello su Agenzia Plinio';
    $rows = '';
    $maxRows = 5;
    $count = 0;
    foreach ($items as $item) {
        $count++;
        if ($count > $maxRows) {
            break;
        }
        $rows .= '<tr>'
            . '<td style="padding:6px 12px;border:1px solid #dde2ec;">'
            . htmlspecialchars($item['name'] ?? 'Prodotto', ENT_QUOTES)
            . ' × ' . (int) ($item['quantity'] ?? 1)
            . '</td>'
            . '<td style="padding:6px 12px;border:1px solid #dde2ec;">'
            . ap_price_format((int) ($item['price_cents'] ?? 0)) . '</td>'
            . '</tr>';
    }
    if ($count > $maxRows) {
        $rows .= '<tr><td colspan="2" style="padding:6px 12px;border:1px solid #dde2ec;color:#687294;font-size:0.9rem;">... e altri articoli salvati.</td></tr>';
    }
    $total = (int) ($cart['total_cents'] ?? 0);
    $discount = (int) ($cart['discount_cents'] ?? 0);
    $net = max(0, $total - $discount);
    $totalsBlock = '<p><strong>Totale prodotti:</strong> ' . ap_price_format($total) . '</p>';
    if ($discount > 0) {
        $totalsBlock .= '<p style="color:#198754;"><strong>Coupon ' . htmlspecialchars($cart['coupon_code'] ?? '', ENT_QUOTES)
            . ':</strong> -' . ap_price_format($discount) . '</p>';
    }
    $totalsBlock .= '<p><strong>Totale stimato:</strong> ' . ap_price_format($net) . '</p>';
    $appUrl = rtrim(ap_env('APP_URL', 'http://127.0.0.1:8000') ?? 'http://127.0.0.1:8000', '/');
    $ctaUrl = $appUrl . '/?page=cart';
    $itemsCount = count($items);
    $heroSubtitle = $itemsCount > 1 ? $itemsCount . ' articoli in attesa' : 'Hai un articolo in sospeso';
    $bodyHtml = '<p style="margin-top:0;color:#4d5671;">Abbiamo riservato i tuoi articoli per qualche ora, così puoi riprendere l\'ordine quando desideri.</p>'
        . '<table style="border-collapse:collapse;margin:20px 0;width:100%;">'
        . '<thead><tr><th style="text-align:left;padding:6px 12px;border:1px solid #dde2ec;">Prodotto</th>'
        . '<th style="text-align:left;padding:6px 12px;border:1px solid #dde2ec;">Prezzo</th></tr></thead>'
        . '<tbody>' . $rows . '</tbody>'
        . '</table>'
        . $totalsBlock
        . '<p style="font-size:0.9rem;color:#687294;">Se hai già completato l\'ordine puoi ignorare questo promemoria.</p>';

    $body = ap_email_layout([
        'hero_title' => 'Il tuo carrello ti aspetta',
        'hero_subtitle' => $heroSubtitle,
        'body_html' => $bodyHtml,
        'cta' => [
            'label' => 'Torna al carrello',
            'url' => $ctaUrl,
        ],
        'footer_note' => 'Promemoria automatico per carrelli salvati.',
    ]);
    ap_mailer()->send([
        'from' => ap_mail_settings()['from'] ?? 'no-reply@agenziaplinio.it',
        'to' => $email,
        'subject' => $subject,
        'body' => $body,
    ]);
}

function ap_notify_user_registered(string $name, string $email): void
{
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return;
        }
        $subject = 'Registrazione confermata · Agenzia Plinio';
        $html = ap_registration_email_markup([
                'name' => $name,
                'email' => $email,
        ]);
        $from = ap_env('RESEND_FROM_ADDRESS', ap_mail_settings()['from'] ?? 'Agenzia Plinio <no-reply@agenziaplinio.it>');
        $resendKey = ap_env('RESEND_API_KEY');
        if ($resendKey) {
                $resendMailer = new ResendMailer($resendKey);
                $resendResult = $resendMailer->send([
                        'from' => $from,
                        'to' => $email,
                        'subject' => $subject,
                        'html' => $html,
                ]);
                if ($resendResult['success'] ?? false) {
                        return;
                }
        }
        ap_mailer()->send([
                'from' => ap_mail_settings()['from'] ?? 'no-reply@agenziaplinio.it',
                'to' => $email,
                'subject' => $subject,
                'body' => $html,
        ]);
}

function ap_registration_email_markup(array $context): string
{
        $now = new DateTime('now', new DateTimeZone('Europe/Rome'));
        $dateLabel = $now->format('d/m/Y H:i');
        $appUrl = rtrim(ap_env('APP_URL', 'http://127.0.0.1:8000') ?? 'http://127.0.0.1:8000', '/');
        $loginUrl = $appUrl . '/?page=account';
        $displayName = trim($context['name'] ?? '');
        $greeting = $displayName !== '' ? 'Ciao ' . htmlspecialchars($displayName, ENT_QUOTES) . '!' : 'Ciao!';
        $userEmail = htmlspecialchars($context['email'] ?? '', ENT_QUOTES);

        $bodyHtml = '<h2 style="margin:0 0 12px 0; color:#0a5bb5; font-size:20px;">' . $greeting . '</h2>'
            . '<p style="font-size:15px; line-height:1.6;">'
            . 'Siamo felici di averti con noi. Da adesso puoi accedere alla tua area personale e utilizzare tutti i servizi disponibili.'
            . '</p>'
            . '<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#eef3f8" style="border-radius:6px; margin-top:18px;">'
            . '<tr><td style="padding:15px; font-size:14px; color:#333;">'
            . '<strong style="color:#0a5bb5;">Dettagli account</strong><br><br>'
            . 'Stato registrazione: <b>Attiva</b><br>'
            . 'Data: <b>' . $dateLabel . '</b><br>'
            . 'Email: <b>' . $userEmail . '</b>'
            . '</td></tr></table>'
            . '<p style="font-size:14px; line-height:1.6; color:#555; margin-top:20px;">'
            . 'Se non hai effettuato tu questa registrazione, ignora questa email o contattaci immediatamente.'
            . '</p>';

        return ap_email_layout([
                'hero_title' => 'Benvenuto a bordo!',
                'hero_subtitle' => 'La tua registrazione è confermata',
                'body_html' => $bodyHtml,
                'cta' => [
                        'label' => 'Accedi al tuo account',
                        'url' => $loginUrl,
                ],
                'footer_note' => 'Email automatica generata durante la registrazione.',
        ]);
}
