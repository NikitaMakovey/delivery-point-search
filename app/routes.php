<?php

use Slim\App;
use App\Http\Controllers\{BranchController, SearchController};
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

return function (App $app) {
    $corsMiddleware = function (Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);
        return $response
            ->withHeader('Access-Control-Allow-Origin', 'https://www-xn--27-dlcmaa0acz0cc8k-xn--p1ai.filesusr.com')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, Ngrok-Skip-Browser-Warning')
            ->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
    };
    $app->add($corsMiddleware);

    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    $app->get('/search', [SearchController::class, 'search']);

    $app->get('/branches/{id}', [BranchController::class, 'show']);
};
