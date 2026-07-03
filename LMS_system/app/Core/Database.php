<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use Throwable;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $config = Config::get('database');
        $charset = $config['charset'] ?? 'utf8mb4';
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $charset
        );

        self::$connection = new PDO($dsn, $config['username'], $config['password'], $config['options']);

        return self::$connection;
    }

    public static function transaction(callable $callback): mixed
    {
        $connection = self::connection();
        $connection->beginTransaction();

        try {
            $result = $callback($connection);
            $connection->commit();

            return $result;
        } catch (Throwable $throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $throwable;
        }
    }
}

