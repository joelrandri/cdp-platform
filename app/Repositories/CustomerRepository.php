<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class CustomerRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function findById(int $id): array|false
    {
        $sql = "
            SELECT id, name, email, created_at
            FROM customers
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        return $stmt->fetch();
    }

    public function getRecentEvents(int $customerId): array
    {
        $sql = "
            SELECT id, event_type, created_at
            FROM events
            WHERE customer_id = :customer_id
            ORDER BY created_at DESC, id DESC
            LIMIT 10
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'customer_id' => $customerId
        ]);

        return $stmt->fetchAll();
    }

    public function countPurchases(int $customerId): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM events
            WHERE customer_id = :customer_id
            AND event_type = 'purchase'
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'customer_id' => $customerId
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function getTotalSpend(int $customerId): float
    {
        $sql = "
            SELECT COALESCE(
                SUM(CAST(ep.property_value AS DECIMAL(10,2))),
                0
            )
            FROM events e
            INNER JOIN event_properties ep
                ON ep.event_id = e.id
            WHERE e.customer_id = :customer_id
            AND e.event_type = 'purchase'
            AND ep.property_name = 'price'
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'customer_id' => $customerId
        ]);

        return (float) $stmt->fetchColumn();
    }
}