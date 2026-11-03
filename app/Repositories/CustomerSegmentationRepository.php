<?php
declare(strict_types=1);

namespace App\Repositories;
use InvalidArgumentException;
use PDO;

class CustomerSegmentationRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function findCustomersBySegment(
        int $minPurchases,
        float $minSpend
    ): array {
        $sql = "
            SELECT
                c.id,
                c.name,
                c.email,
                COUNT(e.id) AS total_purchases,

                COALESCE(
                    SUM(
                        COALESCE(price.price, 0) *
                        COALESCE(quantity.quantity, 1)
                    ),
                    0
                ) AS total_spend

            FROM customers c

            LEFT JOIN events e
                ON e.customer_id = c.id
                AND e.event_type = 'purchase'

            LEFT JOIN (
                SELECT
                    event_id,
                    CAST(MAX(property_value) AS DECIMAL(10,2)) AS price
                FROM event_properties
                WHERE property_name = 'price'
                GROUP BY event_id
            ) price ON price.event_id = e.id

            LEFT JOIN (
                SELECT
                    event_id,
                    CAST(MAX(property_value) AS DECIMAL(10,2)) AS quantity
                FROM event_properties
                WHERE property_name = 'quantity'
                GROUP BY event_id
            ) quantity ON quantity.event_id = e.id

            GROUP BY c.id, c.name, c.email

            HAVING
                total_purchases >= :min_purchases
                AND total_spend >= :min_spend

            ORDER BY total_spend DESC
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'min_purchases' => $minPurchases,
            'min_spend' => $minSpend
        ]);

        $customers = $stmt->fetchAll();

        foreach ($customers as &$customer) {
            $customer['total_purchases'] =
                (int) $customer['total_purchases'];

            $customer['total_spend'] =
                (float) $customer['total_spend'];
        }

        return $customers;
    }

    public function findCustomersByConditions(array $conditions): array
    {
        $sql = "
            SELECT c.id, c.name, c.email
            FROM customers c
            WHERE 1 = 1
        ";

        $params = [];

        $allowedOperators = ['=', '!=', '>', '<', '>=', '<='];

        foreach ($conditions as $index => $condition) {
            $operator = $condition['operator'];

            if (!in_array($operator, $allowedOperators, true)) {
                throw new InvalidArgumentException('Invalid operator');
            }

            $eventParam = ":event_$index";
            $propertyParam = ":property_$index";
            $valueParam = ":value_$index";

            $sql .= "
                AND EXISTS (
                    SELECT 1
                    FROM events e
                    INNER JOIN event_properties ep
                        ON ep.event_id = e.id
                    WHERE e.customer_id = c.id
                    AND e.event_type = $eventParam
                    AND ep.property_name = $propertyParam
                    AND CAST(ep.property_value AS DECIMAL(12,2))
                        $operator $valueParam
                )
            ";

            $params[$eventParam] = $condition['event'];
            $params[$propertyParam] = $condition['property'];
            $params[$valueParam] = $condition['value'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}