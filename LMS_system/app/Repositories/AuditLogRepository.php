<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class AuditLogRepository
{
    public function record(?int $actorId, string $action, string $entityType, ?int $entityId = null, array $metadata = []): void
    {
        $statement = Database::connection()->prepare(
            'INSERT INTO audit_logs (actor_id, action, entity_type, entity_id, metadata, ip_address, user_agent, created_at)
             VALUES (:actor_id, :action, :entity_type, :entity_id, :metadata, :ip_address, :user_agent, NOW())'
        );

        $statement->execute([
            'actor_id' => $actorId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'metadata' => $metadata === [] ? null : json_encode($metadata, JSON_THROW_ON_ERROR),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
        ]);
    }

    public function recent(int $limit = 10): array
    {
        $limit = max(1, min($limit, 50));

        return Database::connection()
            ->query(
                "SELECT audit_logs.*, users.name AS actor_name
                 FROM audit_logs
                 LEFT JOIN users ON users.id = audit_logs.actor_id
                 ORDER BY audit_logs.created_at DESC
                 LIMIT {$limit}"
            )
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countToday(): int
    {
        $statement = Database::connection()->query(
            'SELECT COUNT(*) FROM audit_logs WHERE DATE(created_at) = CURRENT_DATE()'
        );

        return (int) $statement->fetchColumn();
    }
}

