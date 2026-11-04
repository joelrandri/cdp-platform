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
            // 1. Créer ou mettre à jour le client
            $sql = "
                INSERT INTO customers (name, email)
                VALUES (:name, :email)

                ON DUPLICATE KEY UPDATE
                    name = VALUES(name)
            ";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'name' => $name,
                'email' => $email
            ]);

            // 2. Récupérer l'identifiant du client
            $stmt = $this->pdo->prepare("
                SELECT id
                FROM customers
                WHERE email = :email
            ");

            $stmt->execute([
                'email' => $email
            ]);

            $customer = $stmt->fetch();

            if (!$customer) {
                throw new RuntimeException(
                    'Customer not found'
                );
            }

            $customerId = (int) $customer['id'];

            // 3. Enregistrer l'événement
            $stmt = $this->pdo->prepare("
                INSERT INTO events (
                    customer_id,
                    event_type
                )
                VALUES (
                    :customer_id,
                    :event_type
                )
            ");

            $stmt->execute([
                'customer_id' => $customerId,
                'event_type' => $eventType
            ]);

            $eventId = (int) $this->pdo->lastInsertId();

            // 4. Enregistrer les propriétés de l'événement
            if (!empty($properties)) {

                $propertySql = "
                    INSERT INTO event_properties (
                        event_id,
                        property_name,
                        property_value,
                        value_number
                    )
                    VALUES (
                        :event_id,
                        :property_name,
                        :property_value,
                        :value_number
                    )
                ";

                $propertyStmt = $this->pdo->prepare($propertySql);

                foreach ($properties as $key => $value) {

                    $propertyValue = null;
                    $valueNumber = null;

                    // Valeurs numériques
                    if (
                        is_int($value) ||
                        is_float($value) ||
                        (is_string($value) && is_numeric($value))
                    ) {
                        $propertyValue = (string) $value;
                        $valueNumber = $value;

                    // Valeurs booléennes
                    } elseif (is_bool($value)) {
                        $propertyValue = $value ? 'true' : 'false';

                    // Valeurs nulles
                    } elseif ($value === null) {
                        $propertyValue = null;

                    // Tableaux et objets JSON
                    } elseif (is_array($value) || is_object($value)) {
                        $propertyValue = json_encode(
                            $value,
                            JSON_THROW_ON_ERROR
                        );

                    // Valeurs textuelles
                    } else {
                        $propertyValue = (string) $value;
                    }

                    $propertyStmt->execute([
                        'event_id' => $eventId,
                        'property_name' => (string) $key,
                        'property_value' => $propertyValue,
                        'value_number' => $valueNumber
                    ]);
                }
            }

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