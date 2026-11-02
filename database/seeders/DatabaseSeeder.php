<?php

declare(strict_types=1);

namespace Database\Seeders;

use PDO;
use Throwable;

class DatabaseSeeder
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function run(): void
    {
        $this->pdo->beginTransaction();

        try {
            (new CustomerSeeder($this->pdo))->run();

            (new EventSeeder($this->pdo))->run();

            $this->pdo->commit();

        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}