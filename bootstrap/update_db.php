<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Database and tables setup
$host = $_ENV['DB_HOST'];
$port = $_ENV['DB_PORT'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];
$databaseName = $_ENV['DB_NAME'];

// Connect to the database
$pdoDB = new PDO("pgsql:host=$host;port=$port;dbname=$databaseName", $username, $password);
$pdoDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create tables
$pdoDB->exec("ALTER TABLE addresses ADD COLUMN context TEXT;");
$pdoDB->exec("ALTER TABLE addresses ADD COLUMN display_address TEXT;");
echo "Columns 'context' and 'display_address' are ready.\n";