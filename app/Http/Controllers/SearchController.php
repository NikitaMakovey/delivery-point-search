<?php

namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SearchController
{
    public function search(Request $request, Response $response): Response
    {
        $response->getBody()->write('search.show!');
        return $response;
    }
}