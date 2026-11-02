<?php
declare(strict_types=1);

namespace Database\Connection;

use PDO;

final class ConnectData
{
    public static function getConnection(): PDO
    {
        $config = require __DIR__ . '/../../config/database.php';

        $dbInfo = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['database'],
            $config['charset']
        );

        return new PDO(
            $dbInfo,
            $config['username'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
}
