<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\DatabaseConnection;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..'); // Adjust the path as necessary
$dotenv->load();

// Initialize database connection
$dbConnection = DatabaseConnection::getInstance()->getConnection();

$csvFilePath = __DIR__ . '/../resources/files/addresses.csv';

// Open the CSV file for reading
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    // Skip the header row
    fgetcsv($handle, 1000, ",");

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        [$uik, $uik_address, $location, $street, $house] = $data;
        $uik = (int) $uik;

        // Start a transaction
        $dbConnection->beginTransaction();

        try {
            // Check if a branch with the given uik already exists
            $stmt = $dbConnection->prepare("SELECT id FROM branches WHERE uik = ?");
            $stmt->execute([$uik]);
            $existingBranch = $stmt->fetch(PDO::FETCH_ASSOC);

            $branchId = null;
            if ($existingBranch) {
                // If the branch already exists, use the existing id
                $branchId = $existingBranch['id'];
            } else {
                // If the branch does not exist, insert a new one and get its id
                $insertStmt = $dbConnection->prepare("INSERT INTO branches (uik, uik_address) VALUES (?, ?) RETURNING id");
                $insertStmt->execute([$uik, $uik_address]);
                $branchId = $insertStmt->fetch(PDO::FETCH_ASSOC)['id'];
            }

            // Insert into 'addresses' table
            $stmt = $dbConnection->prepare("INSERT INTO addresses (branch_id, location, street, house) VALUES (?, ?, ?, ?)");
            $stmt->execute([$branchId, $location, $street, $house]);

            // Commit the transaction
            $dbConnection->commit();
        } catch (Exception $e) {
            // Roll back the transaction if something failed
            $dbConnection->rollBack();
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    fclose($handle);
}

echo "CSV data imported successfully.\n";

