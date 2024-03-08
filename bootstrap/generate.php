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

// rest of the code
const ABBREVIATIONS = [
    'г.', 'город', 'г',
    'ул.', 'улица', 'ул',
    'пер.', 'переулок', 'пер',
    'пр-т', 'проспект', 'пр-т', 'просп',
    'б-р', 'бульвар', 'б-р', 'бул',
    'д.', 'деревня', 'дер',
    'х.', 'хутор', 'хут',
    'ст.', 'станица', 'стн',
    'а.', 'аул',
    'пр-д', 'проезд', 'пр',
    'ал.', 'аллея', 'алл',
    'наб.', 'набережная', 'наб',
    'пл.', 'площадь',
    'туп.', 'тупик', 'туп',
    'прлк.', 'проулок',
    'кв.', 'квартал', 'кварт',
    'мкр.', 'микрорайон', 'мкрн',
    'жк', 'жилой комплекс',
    'тер.', 'территория', 'терр',
    'п.', 'поселок', 'пос',
    'с.', 'село', 'сел',
    'р. п.', 'рабочий поселок', 'рп',
    'н. п.', 'населенный пункт', 'нп',
    'ш.', 'шоссе', 'шос',
];

function generateAddressCombinations($suggestion): string
{
    $combinations = [];
    $suggestion = mb_strtolower($suggestion);

    // Base combination
    $combinations[] = $suggestion;

    $suggestionParts = [];
    $parts = explode(',', $suggestion);
    foreach ($parts as $part) {
        $smallParts = explode(' ', $part);
        foreach ($smallParts as $smallPart) {
            if (!in_array($smallPart, ABBREVIATIONS) && $smallPart != '') {
                $suggestionParts[] = $smallPart;
            }
        }
    }

    // @todo: shuffle all the suggestion part into different string with spaces
    foreach ($suggestionParts as $index => $layer1Part) {
        $combinationData = [];
        $i = $index;
        $itemsNumber = count($suggestionParts);
        while ($itemsNumber != 0) {
            $combinationData[] = $suggestionParts[$i];
            $i++;
            if ($i == count($suggestionParts)) {
                $i = 0;
            }
            $itemsNumber--;
        }
        $combinations[] = implode(' ', $combinationData);
    }

    return implode(' ', $combinations);
}

// Fetch all addresses
$query = "SELECT id, location, street, house FROM addresses";
$stmt = $pdoDB->prepare($query);
$stmt->execute();

// Loop through all addresses
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $addressString = "{$row['location']}" .
        (in_array($row['street'], ['*', '']) ? '' : ", {$row['street']}") .
        (in_array($row['house'], ['*', '']) ? '' : ", {$row['house']}");

    // Generate context for the address
    $context = generateAddressCombinations($addressString);

    // Update the addresses table with the new context
    $updateQuery = "UPDATE addresses SET context = :context, display_address = :display_address WHERE id = :id";
    $updateStmt = $pdoDB->prepare($updateQuery);
    $updateStmt->execute([
        'display_address' => $addressString,
        'context' => $context,
        'id' => $id,
    ]);
}

echo "All addresses have been updated with context.\n";




