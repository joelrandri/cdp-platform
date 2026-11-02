<?php

declare(strict_types=1);

namespace Database\Seeders;

use PDO;

class CustomerSeeder
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function run(): void
    {
        // customers data to insert
        $customers = [
            [
                'name' => 'julien',
                'email' => 'julien@test.com'
            ],
            [
                'name' => 'Miora',
                'email' => 'miora@test.com'
            ],
            [
                'name' => 'Marcus Milaire',
                'email' => 'marcus@test.com'
            ],
            [
                'name' => 'Felana Rakoto',
                'email' => 'felana@test.com'
            ],
            [
                'name' => 'Tojo soa',
                'email' => 'tojo@test.com'
            ],
        ];

        $sql = "INSERT INTO customers (name, email)
                VALUES (:name, :email)
                ON DUPLICATE KEY UPDATE name = VALUES(name)";

        $stmt = $this->pdo->prepare($sql);

        foreach ($customers as $customer) {
            $stmt->execute([
                'name' => $customer["name"],
                'email' => $customer["email"],
            ]);
        }
    }
}