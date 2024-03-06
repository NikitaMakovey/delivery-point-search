<?php

namespace App\Http\Controllers;

use App\Core\DatabaseConnection;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BranchController
{
    public function show(Request $request, Response $response, $id): Response
    {
        // Obtain database connection
        $dbConnection = DatabaseConnection::getInstance()->getConnection();
        $stmt = $dbConnection->prepare("SELECT * FROM branches WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $branch = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($branch) {
            $response->getBody()->write(json_encode([
                'id' => $branch['id'],
                'address' => $branch['uik_address'],
            ]));
        } else {
            $response->getBody()->write(json_encode(['error' => 'Branch not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
