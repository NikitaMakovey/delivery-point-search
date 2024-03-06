<?php

namespace App\Http\Controllers;

use App\Core\DatabaseConnection;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SearchController
{
    public function search(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $searchQuery = $queryParams['query'] ?? '';

        $dbConnection = DatabaseConnection::getInstance()->getConnection();

        $stmt = $dbConnection->prepare("SELECT * FROM addresses WHERE location ILIKE :query OR street ILIKE :query LIMIT 5");
        $stmt->execute(['query' => "%$searchQuery%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convert results to JSON
        $response->getBody()->write(json_encode(array_map(fn ($item) => [
            'branch_id' => $item['branch_id'],
            'address' => "{$item['location']}, {$item['street']}" . ($item['house'] == '*' ? '' : " {$item['house']}"),
        ], $results)));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
