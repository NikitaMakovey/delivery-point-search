<?php

namespace App\Core;

use PDO;
use PDOException;

class DatabaseConnection
{
    private static ?DatabaseConnection $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $dsn = 'pgsql:host=' . $_ENV['DB_HOST'] . ';port=' . $_ENV['DB_PORT'] . ';dbname=' . $_ENV['DB_NAME'];
        try {
            $this->connection = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Handle error
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public static function getInstance(): ?DatabaseConnection
    {
        if (!self::$instance) {
            self::$instance = new DatabaseConnection();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    // Prevent cloning and unserialization, which are loopholes in singleton
    private function __clone() { }
    private function __wakeup() { }
}
