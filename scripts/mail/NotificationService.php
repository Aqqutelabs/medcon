<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/TemplateRenderer.php';
require_once __DIR__ . '/MailTransport.php';

final class MedconNotificationService
{
    private array $config;
    public function __construct(private PDO $pdo)
    {
        $this->config = medcon_mail_config();
    }

    public function sendToUser(string $event, int $userId, string $idempotencyKey, array $data = [], ?string $entityType = null, ?int $entityId = null): bool
    {
        $query = $this->pdo->prepare('SELECT id,email,first_name,last_name,status FROM users WHERE id=? LIMIT 1');
        $query->execute([$userId]); $recipient = $query->fetch(PDO::FETCH_ASSOC);
        if (!$recipient || $recipient['status'] === 'suspended') return false;
        $data += ['name' => $recipient['first_name'], 'logo_url' => $this->config['base_url'] . '/img/logo.svg'];
        return $this->send($event, $userId, $recipient['email'], $idempotencyKey, $data, $entityType, $entityId);
    }

    public function sendToEmail(string $event, string $email, string $idempotencyKey, array $data = [], ?string $entityType = null, ?int $entityId = null): bool
    {
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)) return false;
        $data += ['logo_url'=>$this->config['base_url'].'/img/logo.svg'];
        return $this->send($event,null,strtolower($email),$idempotencyKey,$data,$entityType,$entityId);
    }

    private function send(string $event, ?int $userId, string $email, string $key, array $data, ?string $entityType, ?int $entityId): bool
    {
        try {
            $insert = $this->pdo->prepare("INSERT INTO email_logs (event_key,recipient_user_id,recipient_email,entity_type,entity_id,idempotency_key,status) VALUES (?,?,?,?,?,?,'pending')");
            $insert->execute([$event,$userId,$email,$entityType,$entityId,$key]);
        } catch (PDOException $exception) {
            if ((string) $exception->getCode() === '23000') return true;
            return false;
        }
        $logId = (int) $this->pdo->lastInsertId();
        try {
            $mail = (new MedconTemplateRenderer())->render($event, $data);
            $result = (new MedconMailTransport($this->config))->send($email, $mail['subject'], $mail['html'], $key);
            $this->pdo->prepare("UPDATE email_logs SET status='sent',provider_message_id=?,sent_at=NOW() WHERE id=?")->execute([$result['id'] ?: null,$logId]);
            return true;
        } catch (Throwable $exception) {
            $safe = preg_replace('/sk_(?:test|live)_[A-Za-z0-9_-]+/', '[redacted]', $exception->getMessage());
            $this->pdo->prepare("UPDATE email_logs SET status='failed',error_message=? WHERE id=?")->execute([substr((string)$safe,0,500),$logId]);
            error_log('Medcon notification failed: event=' . $event . ' log_id=' . $logId);
            return false;
        }
    }
}

function medcon_notify(PDO $pdo, string $event, int $userId, string $idempotencyKey, array $data = [], ?string $entityType = null, ?int $entityId = null): bool
{
    try { return (new MedconNotificationService($pdo))->sendToUser($event,$userId,$idempotencyKey,$data,$entityType,$entityId); }
    catch (Throwable $exception) { error_log('Medcon notification initialization failed.'); return false; }
}

function medcon_notify_email(PDO $pdo,string $event,string $email,string $idempotencyKey,array $data=[],?string $entityType=null,?int $entityId=null):bool
{
    try{return(new MedconNotificationService($pdo))->sendToEmail($event,$email,$idempotencyKey,$data,$entityType,$entityId);}catch(Throwable $exception){error_log('Medcon public notification initialization failed.');return false;}
}
