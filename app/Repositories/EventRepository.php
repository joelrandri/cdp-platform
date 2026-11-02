<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use Throwable;
use RuntimeException;

class EventRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function createEvent(
        string $email,
        string $name,
        string $eventType,
        array $properties
    ): array {
        $this->pdo->beginTransaction();

        try {
            // 1. Create or update customer
            $sql = "
                INSERT INTO customers (name, email)
                VALUES (:name, :email)
                ON DUPLICATE KEY UPDATE name = VALUES(name)
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'name' => $name,
                'email' => $email
            ]);

            // 2. Retrieve customer id
            $stmt = $this->pdo->prepare(
                "SELECT id FROM customers WHERE email = :email"
            );

            $stmt->execute(['email' => $email]);

            $customer = $stmt->fetch();

            if (!$customer) {
                throw new RuntimeException('Customer not found');
            }

            $customerId = (int) $customer['id'];

            // 3. Save event
            $stmt = $this->pdo->prepare("
                INSERT INTO events (customer_id, event_type)
                VALUES (:customer_id, :event_type)
            ");

            $stmt->execute([
                'customer_id' => $customerId,
                'event_type' => $eventType
            ]);

            $eventId = (int) $this->pdo->lastInsertId();

            // 4. Save the event propertises
            if (!empty($properties)) {
                $propertySql = "
                    INSERT INTO event_properties
                    (event_id, property_name, property_value)
                    VALUES (:event_id, :property_name, :property_value)
                ";

                $propertyStmt = $this->pdo->prepare($propertySql);

                foreach ($properties as $key => $value) {
                    if (is_array($value) || is_object($value)) {
                        $value = json_encode(
                            $value,
                            JSON_THROW_ON_ERROR
                        );
                    } elseif (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    } elseif ($value === null) {
                        $value = null;
                    } else {
                        $value = (string) $value;
                    }

                    $propertyStmt->execute([
                        'event_id' => $eventId,
                        'property_name' => (string) $key,
                        'property_value' => $value
                    ]);
                }
            }

            // 5. Validate transaction
            $this->pdo->commit();

            return [
                'customer_id' => $customerId,
                'event_id' => $eventId
            ];

        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}
