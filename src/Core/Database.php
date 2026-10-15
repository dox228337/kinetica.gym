<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    /**
     * Prevent external instantiation (Singleton pattern)
     */
    private function __construct() {}
    private function __clone() {}

    /**
     * Returns the singleton active PDO connection instance
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';

            $dsn = sprintf(
                "mysql:host=%s;port=%d;dbname=%s;charset=%s",
                $config['host'],
                $config['port'],
                $config['dbname'],
                $config['charset']
            );

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            } catch (PDOException $e) {
                // Log error for debugging, show generic message to user
                error_log('Kinetica DB connection error: ' . $e->getMessage());
                die('Database connection error. Please try again later or contact support.');
            }
        }

        return self::$instance;
    }
}
