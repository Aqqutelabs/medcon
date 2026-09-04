<?php
declare(strict_types=1);

final class MedconTokenService
{
    public function __construct(private PDO $pdo) {}
    private function raw(): string { return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='); }
    private function hash(string $raw): string { return hash('sha256', $raw); }

    public function createVerification(int $userId, int $minutes = 1440): array
    {
        $raw=$this->raw(); $this->pdo->prepare('UPDATE email_verification_tokens SET used_at=NOW() WHERE user_id=? AND used_at IS NULL')->execute([$userId]);
        $this->pdo->prepare('INSERT INTO email_verification_tokens (user_id,token_hash,expires_at) VALUES (?,?,DATE_ADD(NOW(),INTERVAL ? MINUTE))')->execute([$userId,$this->hash($raw),$minutes]);
        return ['id'=>(int)$this->pdo->lastInsertId(),'token'=>$raw];
    }
    public function consumeVerification(string $raw): ?int
    {
        $this->pdo->beginTransaction();
        try { $q=$this->pdo->prepare('SELECT id,user_id FROM email_verification_tokens WHERE token_hash=? AND used_at IS NULL AND expires_at>NOW() LIMIT 1 FOR UPDATE');$q->execute([$this->hash($raw)]);$row=$q->fetch();if(!$row){$this->pdo->rollBack();return null;}$this->pdo->prepare('UPDATE email_verification_tokens SET used_at=NOW() WHERE id=?')->execute([$row['id']]);$this->pdo->prepare('UPDATE users SET email_verified=1,updated_at=NOW() WHERE id=?')->execute([$row['user_id']]);$this->pdo->commit();return (int)$row['user_id']; } catch(Throwable $e){if($this->pdo->inTransaction())$this->pdo->rollBack();throw $e;}
    }
    public function createReset(int $userId, int $minutes = 60): array
    {
        $raw=$this->raw();$this->pdo->prepare('UPDATE password_resets SET used_at=NOW() WHERE user_id=? AND used_at IS NULL')->execute([$userId]);$this->pdo->prepare('INSERT INTO password_resets (user_id,token,expires_at) VALUES (?,?,DATE_ADD(NOW(),INTERVAL ? MINUTE))')->execute([$userId,$this->hash($raw),$minutes]);return ['id'=>(int)$this->pdo->lastInsertId(),'token'=>$raw];
    }
    public function consumeReset(string $raw, string $passwordHash): ?array
    {
        $this->pdo->beginTransaction();try{$q=$this->pdo->prepare('SELECT id,user_id FROM password_resets WHERE token=? AND used_at IS NULL AND expires_at>NOW() LIMIT 1 FOR UPDATE');$q->execute([$this->hash($raw)]);$row=$q->fetch();if(!$row){$this->pdo->rollBack();return null;}$this->pdo->prepare('UPDATE users SET password_hash=?,updated_at=NOW() WHERE id=?')->execute([$passwordHash,$row['user_id']]);$this->pdo->prepare('UPDATE password_resets SET used_at=NOW() WHERE id=?')->execute([$row['id']]);$this->pdo->commit();return ['id'=>(int)$row['id'],'user_id'=>(int)$row['user_id']];}catch(Throwable $e){if($this->pdo->inTransaction())$this->pdo->rollBack();throw $e;}
    }
}
