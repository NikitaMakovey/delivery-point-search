<?php

namespace App\Core;

use PDO;
use PDOException;

class DatabaseConnection
{
    private static ?DatabaseConnection $instance = null;
    private ?PDO $connection = null; // Allow null to handle initialization failure

    private function __construct()
    {
        $dsn = 'pgsql:host=' . $_ENV['DB_HOST'] . ';port=' . $_ENV['DB_PORT'] . ';dbname=' . $_ENV['DB_NAME'];
        try {
            $this->connection = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Handle error more gracefully
            error_log("Connection failed: " . $e->getMessage());
            $this->connection = null; // Ensure connection is null on failure
        }
    }

    public static function getInstance(): ?DatabaseConnection
    {
        if (!self::$instance) {
            self::$instance = new DatabaseConnection();
            if (self::$instance->getConnection() === null) { // Check if connection was successfully established
                self::$instance = null; // Reset instance if connection failed
            }
        }

        return self::$instance;
    }

    public function getConnection(): ?PDO // Allow null to indicate connection failure
    {
        return $this->connection;
    }

    // Prevent cloning and unserialization, which are loopholes in singleton
    private function __clone() { }
    public function __wakeup() { } // Make public to comply with visibility rules
}
