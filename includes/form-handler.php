<?php
require_once __DIR__ . '/env.php';
ap_load_env(__DIR__ . '/../.env');
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/email-template.php';

function handleContactForm(): ?array
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['form_scope'] ?? '') !== 'contact') {
        regenerate_form_token();
        return null;
    }

    $response = ['success' => false, 'message' => ''];

    if (!empty($_POST['company_website'])) {
        $response['message'] = 'Richiesta non valida.';
        return finalize_response($response);
    }

    if (!hash_equals($_SESSION['ap_form_token'] ?? '', $_POST['ap_token'] ?? '')) {
        $response['message'] = 'Token non valido, aggiorna la pagina.';
        return finalize_response($response);
    }

    $name = trim($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $service = trim($_POST['service'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $priority = trim($_POST['priority'] ?? 'standard');
    $appointmentRequest = trim($_POST['appointment_request'] ?? 'contact');
    $preferredDate = trim($_POST['preferred_date'] ?? '');
    $preferredTime = trim($_POST['preferred_time'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || strlen($message) < 5) {
        $response['message'] = 'Compila tutti i campi obbligatori.';
        return finalize_response($response);
    }

    // Validate appointment fields if appointment is requested
    if ($appointmentRequest === 'appointment') {
        if (empty($preferredDate) || empty($preferredTime)) {
            $response['message'] = 'Per gli appuntamenti, seleziona data e orario preferiti.';
            return finalize_response($response);
        }
        
        $appointmentDateTime = strtotime($preferredDate . ' ' . $preferredTime);
        if ($appointmentDateTime < time() + 24 * 60 * 60) { // At least 24 hours in advance
            $response['message'] = 'Gli appuntamenti devono essere prenotati almeno 24 ore in anticipo.';
            return finalize_response($response);
        }
        
        $dayOfWeek = date('N', $appointmentDateTime); // 1 = Monday, 7 = Sunday
        if ($dayOfWeek > 5) { // Weekend
            $response['message'] = 'Gli appuntamenti sono disponibili solo dal lunedì al venerdì.';
            return finalize_response($response);
        }
    }

    $ticketCode = ap_contact_generate_ticket_code();
    $config = require __DIR__ . '/config.php';
    $smtp = $config['smtp'];

    $resendSettings = [
        'api_key' => ap_env('RESEND_API_KEY'),
        'from' => ap_env('RESEND_FROM_ADDRESS', $smtp['from']),
        'to' => ap_env('RESEND_CONTACT_TO', $smtp['to']),
        'bcc' => ap_env('RESEND_CONTACT_BCC'),
    ];

    $body = ap_contact_email_markup([
        'ticket' => $ticketCode,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'company' => $company,
        'service' => $service,
        'priority' => $priority,
        'appointment_request' => $appointmentRequest,
        'preferred_date' => $preferredDate,
        'preferred_time' => $preferredTime,
        'message' => $message,
    ]);

    $sendResult = ap_dispatch_contact_email([
        'resend' => $resendSettings,
        'smtp' => $smtp,
        'reply_to' => $email,
        'body' => $body,
        'ticket' => $ticketCode,
    ]);

    if ($sendResult['success']) {
        $response['success'] = true;
        $response['message'] = 'Richiesta inviata correttamente · Ticket ' . $ticketCode;
        $response['ticket_code'] = $ticketCode;
    } else {
        $response['message'] = $sendResult['message'] ?? 'Errore durante l\'invio.';
    }

    regenerate_form_token();
    $response['token'] = $_SESSION['ap_form_token'] ?? '';
    return finalize_response($response);
}

function ap_dispatch_contact_email(array $context): array
{
    $ticket = $context['ticket'] ?? '';
    $body = $context['body'] ?? '';
    $replyTo = $context['reply_to'] ?? null;
    $smtp = $context['smtp'] ?? [];
    $resend = $context['resend'] ?? [];
    $subject = sprintf('[Ticket %s] Nuova richiesta contatto', $ticket ?: date('ymdHis'));

    if (!empty($resend['api_key'])) {
        $resendMailer = new ResendMailer($resend['api_key']);
        $resendResult = $resendMailer->send([
            'from' => $resend['from'] ?? ($smtp['from'] ?? 'no-reply@agenziaplinio.it'),
            'to' => $resend['to'] ?? ($smtp['to'] ?? ''),
            'bcc' => $resend['bcc'] ?? null,
            'subject' => $subject,
            'html' => $body,
            'reply_to' => $replyTo,
        ]);
        if ($resendResult['success'] ?? false) {
            return $resendResult;
        }
    }

    $mailer = new SimpleSmtpMailer($smtp);
    return $mailer->send([
        'from' => $smtp['from'] ?? 'no-reply@agenziaplinio.it',
        'to' => $smtp['to'] ?? 'info@agenziaplinio.it',
        'reply_to' => $replyTo,
        'subject' => $subject,
        'body' => $body,
    ]);
}

function ap_contact_email_markup(array $data): string
{
    $priorityLabel = ap_contact_priority_label($data['priority'] ?? 'standard');
    $appointmentLabel = ap_contact_appointment_label($data['appointment_request'] ?? 'contact');
    $submittedAt = (new DateTime('now', new DateTimeZone('Europe/Rome')))->format('d/m/Y H:i');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'n/d';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'n/d';

    $rows = [
        'Ticket' => $data['ticket'] ?? '',
        'Nome e cognome' => $data['name'] ?? '',
        'Azienda / Ente' => $data['company'] ?? '',
        'Email' => $data['email'] ?? '',
        'Telefono' => $data['phone'] ?? '',
        'Servizio richiesto' => $data['service'] ?? '',
        'Tipo richiesta' => $appointmentLabel,
        'Priorità' => $priorityLabel,
    ];

    // Add appointment details if appointment was requested
    if (($data['appointment_request'] ?? 'contact') === 'appointment') {
        $preferredDate = $data['preferred_date'] ?? '';
        $preferredTime = $data['preferred_time'] ?? '';
        if ($preferredDate && $preferredTime) {
            $appointmentDateTime = date('d/m/Y H:i', strtotime($preferredDate . ' ' . $preferredTime));
            $rows['Appuntamento richiesto'] = $appointmentDateTime;
        }
    }

    $tableRows = '';
    foreach ($rows as $label => $value) {
        if ($value === '' || $value === null) {
            continue;
        }
        $tableRows .= sprintf(
            '<tr><th style="text-align:left;padding:8px 12px;border:1px solid #e1e6ff;background:#f7f9ff;width:40%%;">%s</th><td style="padding:8px 12px;border:1px solid #e1e6ff;">%s</td></tr>',
            htmlspecialchars($label, ENT_QUOTES),
            nl2br(htmlspecialchars((string) $value, ENT_QUOTES))
        );
    }

    $messageBlock = nl2br(htmlspecialchars($data['message'] ?? '', ENT_QUOTES));
    $bodyHtml = '<p style="margin:0 0 8px 0;color:#4d5671;">Inoltrata il ' . $submittedAt . '</p>'
        . '<table style="border-collapse:collapse;margin:20px 0;width:100%;">' . $tableRows . '</table>'
        . '<div style="margin-top:16px;padding:16px;border-radius:12px;background:#f1f5ff;border:1px solid #dce4ff;">'
        . '<p style="margin-top:0;margin-bottom:8px;font-weight:600;">Messaggio</p>'
        . '<div style="white-space:pre-wrap;line-height:1.5;">' . $messageBlock . '</div>'
        . '</div>'
        . '<p style="margin-top:20px;font-size:0.9rem;color:#6b7285;">Meta: IP ' . htmlspecialchars($ip, ENT_QUOTES)
        . ' · UA ' . htmlspecialchars($userAgent, ENT_QUOTES) . '</p>';

    $ticket = $data['ticket'] ?? '';
    $heroSubtitle = $ticket !== '' ? 'Ticket ' . htmlspecialchars($ticket, ENT_QUOTES) : 'Richiesta interna';
    $ctaUrl = rtrim(ap_env('APP_URL', 'http://127.0.0.1:8000') ?? 'http://127.0.0.1:8000', '/') . '/?page=admin';

    return ap_email_layout([
        'hero_title' => 'Nuova richiesta dal sito',
        'hero_subtitle' => $heroSubtitle,
        'body_html' => $bodyHtml,
        'cta' => [
            'label' => 'Apri area admin',
            'url' => $ctaUrl,
        ],
        'footer_note' => 'Ticket generato automaticamente dalla sezione contatti.',
    ]);
}

function ap_contact_generate_ticket_code(): string
{
    return 'AP-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
}

function ap_contact_priority_label(string $priority): string
{
    $map = [
        'standard' => 'Standard (entro 24h)',
        'fast-track' => 'Fast track (entro 8h)',
        'emergenza' => 'Emergenza operativa',
    ];
    $key = strtolower(trim($priority));
    return $map[$key] ?? ucfirst(str_replace('-', ' ', $key));
}

function ap_contact_appointment_label(string $appointmentRequest): string
{
    $map = [
        'contact' => 'Richiesta informazioni',
        'appointment' => 'Richiesta appuntamento',
    ];
    $key = strtolower(trim($appointmentRequest));
    return $map[$key] ?? ucfirst(str_replace('-', ' ', $key));
}

function regenerate_form_token(): void
{
    $_SESSION['ap_form_token'] = bin2hex(random_bytes(16));
}

function finalize_response(array $response): array
{
    $response['token'] = $_SESSION['ap_form_token'] ?? '';
    if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    $_SESSION['ap_form_feedback'] = $response;
    return $response;
}
