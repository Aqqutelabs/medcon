<?php
declare(strict_types=1);

final class MedconMailTransport
{
    public function __construct(private array $config) {}

    public function send(string $to, string $subject, string $html, string $idempotencyKey): array
    {
        if (!in_array(($this->config['mode'] ?? ''), ['test','live'], true)) throw new RuntimeException('SendByte configuration is unavailable.');
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Recipient email is invalid.');
        if (!function_exists('curl_init')) throw new RuntimeException('PHP cURL is required for SendByte.');
        $from = ($this->config['from_name'] ? $this->config['from_name'] . ' <' . $this->config['from_email'] . '>' : $this->config['from_email']);
        $payload = ['from' => $from, 'to' => $to, 'subject' => $subject, 'html' => $html, 'idempotency_key' => $idempotencyKey];
        if (!empty($this->config['reply_to'])) $payload['reply_to'] = $this->config['reply_to'];
        $handle = curl_init((string) $this->config['endpoint']);
        curl_setopt_array($handle, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15, CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $this->config['api_key'], 'Content-Type: application/json'], CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR)]);
        $body = curl_exec($handle); $error = curl_error($handle); $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE); curl_close($handle);
        if ($body === false || $error !== '') throw new RuntimeException('SendByte connection failed.');
        $decoded = json_decode((string) $body, true);
        if ($status < 200 || $status >= 300) throw new RuntimeException('SendByte rejected the request (HTTP ' . $status . ').');
        return ['id' => (string) ($decoded['id'] ?? $decoded['data']['id'] ?? '')];
    }
}
