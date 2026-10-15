<?php

/**
 * Kinetica Gym - Database Configuration
 * Supports environment variables with fallback to local OSPanel defaults.
 */
return [
    'host'     => getenv('DB_HOST') ?: 'MySQL-8.0',
    'port'     => (int)(getenv('DB_PORT') ?: 3306),
    'dbname'   => getenv('DB_NAME') ?: 'kinetica_db',
    'charset'  => 'utf8mb4',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : (getenv('DB_PASS') !== false ? getenv('DB_PASS') : ''),
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];
