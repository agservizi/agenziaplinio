<?php
class SimpleSmtpMailer
{
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private bool $secure;
    private bool $auth;

    public function __construct(array $config = [])
    {
        $this->host = $config['host'] ?? 'localhost';
        $this->port = (int)($config['port'] ?? 25);
        $this->username = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->secure = (bool)($config['secure'] ?? false);
        $this->auth = !empty($this->username) && !empty($this->password);
    }

    public function send(array $payload): array
    {
        $from = $payload['from'] ?? '';
        $to = $payload['to'] ?? '';
        $subject = $payload['subject'] ?? '';
        $body = $payload['body'] ?? '';

        if (!$from || !$to) {
            return ['success' => false, 'message' => 'Mittente o destinatario mancanti'];
        }

        $headers = [
            'From' => $from,
            'Reply-To' => $payload['reply_to'] ?? $from,
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/html; charset=UTF-8'
        ];

        $message = $this->buildMessage($headers, $body, $subject);

        $socket = @fsockopen($this->host, $this->port, $errno, $errstr, 10);
        if (!$socket) {
            // fallback su mail()
            if (@mail($to, $subject, $body, $this->buildHeaderString($headers))) {
                return ['success' => true];
            }
            return ['success' => false, 'message' => "SMTP non disponibile: {$errstr}"];
        }

        $this->read($socket);
        $this->write($socket, 'EHLO agenziaplinio.it');

        if ($this->secure) {
            $this->write($socket, 'STARTTLS');
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $this->write($socket, 'EHLO agenziaplinio.it');
        }

        if ($this->auth) {
            $this->write($socket, 'AUTH LOGIN');
            $this->write($socket, base64_encode($this->username));
            $this->write($socket, base64_encode($this->password));
        }

        $this->write($socket, 'MAIL FROM: <' . $from . '>');
        $this->write($socket, 'RCPT TO: <' . $to . '>');
        $this->write($socket, 'DATA');
        $this->write($socket, $message . "\r\n.");
        $this->write($socket, 'QUIT');
        fclose($socket);

        return ['success' => true];
    }

    private function buildMessage(array $headers, string $body, string $subject): string
    {
        $headers['Subject'] = $subject;
        $headers['Date'] = date(DATE_RFC2822);
        return $this->buildHeaderString($headers) . "\r\n" . $body;
    }

    private function buildHeaderString(array $headers): string
    {
        $compiled = '';
        foreach ($headers as $key => $value) {
            $compiled .= $key . ': ' . $value . "\r\n";
        }
        return $compiled;
    }

    private function read($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $response;
    }

    private function write($socket, string $data): void
    {
        fwrite($socket, $data . "\r\n");
        $this->read($socket);
    }
}

class ResendMailer
{
    private string $apiKey;
    private string $endpoint;

    public function __construct(?string $apiKey, string $endpoint = 'https://api.resend.com/emails')
    {
        $this->apiKey = trim((string) $apiKey);
        $this->endpoint = $endpoint;
    }

    public function send(array $payload): array
    {
        if ($this->apiKey === '') {
            return ['success' => false, 'message' => 'Resend API key non configurata'];
        }
        if (!function_exists('curl_init')) {
            return ['success' => false, 'message' => 'Estensione cURL non disponibile'];
        }

        $from = $payload['from'] ?? '';
        $to = $this->normalizeRecipients($payload['to'] ?? []);
        $subject = trim((string) ($payload['subject'] ?? ''));
        $html = $payload['html'] ?? ($payload['body'] ?? '');

        if (!$from || empty($to) || !$subject || !$html) {
            return ['success' => false, 'message' => 'Parametri mancanti per invio via Resend'];
        }

        $body = [
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'html' => $html,
        ];

        if (!empty($payload['text'])) {
            $body['text'] = $payload['text'];
        }
        if (!empty($payload['reply_to'])) {
            $body['reply_to'] = $payload['reply_to'];
        }
        $bcc = $this->normalizeRecipients($payload['bcc'] ?? []);
        if (!empty($bcc)) {
            $body['bcc'] = $bcc;
        }

        $ch = curl_init($this->endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        $responseBody = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($responseBody === false || $status >= 400) {
            $message = 'Errore invio via Resend';
            if ($responseBody) {
                $decoded = json_decode($responseBody, true);
                if (isset($decoded['message'])) {
                    $message .= ': ' . $decoded['message'];
                }
            } elseif ($error) {
                $message .= ': ' . $error;
            }
            return ['success' => false, 'message' => $message];
        }

        return ['success' => true];
    }

    /**
     * @param string|array $value
     */
    private function normalizeRecipients($value): array
    {
        if (is_string($value)) {
            $value = array_filter(array_map('trim', preg_split('/[,;]+/', $value))); // allow comma/semicolon separated strings
        }
        if (!is_array($value)) {
            return [];
        }
        return array_values(array_filter(array_map('trim', $value)));
    }
}
