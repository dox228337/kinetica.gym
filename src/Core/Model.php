<?php

namespace App\Core;

use PDO;

abstract class Model
{
    protected static ?PDO $db = null;

    /**
     * Get active PDO database connection
     */
    protected static function db(): PDO
    {
        if (static::$db === null) {
            static::$db = Database::getConnection();
        }
        return static::$db;
    }
}
