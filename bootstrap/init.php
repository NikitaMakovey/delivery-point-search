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

$createDatabaseSQL = "CREATE DATABASE \"$databaseName\"";

$createBranchesTableSQL = "
CREATE TABLE IF NOT EXISTS branches (
    id SERIAL PRIMARY KEY,
    uik INT NOT NULL,
    uik_address TEXT NOT NULL
);";

$createAddressesTableSQL = "
CREATE TABLE IF NOT EXISTS addresses (
    id SERIAL PRIMARY KEY,
    branch_id INT NOT NULL,
    location VARCHAR(255),
    street VARCHAR(255),
    house VARCHAR(50),
    FOREIGN KEY (branch_id) REFERENCES branches(id)
);";

// Connect to PostgreSQL server
$pdoServer = new PDO("pgsql:host=$host;port=$port", $username, $password);
$pdoServer->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check for database existence and create if not exists
$dbExists = $pdoServer->query("SELECT 1 FROM pg_database WHERE datname = '$databaseName'")->rowCount() > 0;

if (!$dbExists) {
    $pdoServer->exec($createDatabaseSQL);
    echo "Database '$databaseName' created successfully.\n";
} else {
    echo "Database '$databaseName' already exists.\n";
}

// Connect to the database
$pdoDB = new PDO("pgsql:host=$host;port=$port;dbname=$databaseName", $username, $password);
$pdoDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create tables
$pdoDB->exec($createBranchesTableSQL);
echo "Table 'branches' is ready.\n";

$pdoDB->exec($createAddressesTableSQL);
echo "Table 'addresses' is ready.\n";
