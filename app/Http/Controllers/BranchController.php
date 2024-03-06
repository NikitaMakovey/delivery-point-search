<?php

namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BranchController
{
    public function show(Request $request, Response $response, $uik): Response
    {
        $response->getBody()->write('branches.show!');
        return $response;
    }
}