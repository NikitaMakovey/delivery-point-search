<?php

use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/vendor/autoload.php';

/**
 * Instantiate App
 *
 * In order for the factory to work you need to ensure you have installed
 * a supported PSR-7 implementation of your choice e.g.: Slim PSR-7 and a supported
 * ServerRequest creator (included with Slim PSR-7)
 */
$app = AppFactory::create();

/**
 * The routing middleware should be added earlier than the ErrorMiddleware
 * Otherwise exceptions thrown from it will not be handled by the middleware
 */
$app->addRoutingMiddleware();

/**
 * Add Error Middleware
 *
 * @param bool                  $displayErrorDetails -> Should be set to false in production
 * @param bool                  $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool                  $logErrorDetails -> Display error details in error log
 * @param LoggerInterface|null  $logger -> Optional PSR-3 Logger
 *
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->get('/suggestions', function (Request $request, Response $response) {
    $queryParams = $request->getQueryParams();
    $searchTerm = $queryParams['query'] ?? '';

    $suggestions = getAddressSuggestions($searchTerm);

    $response->getBody()->write(json_encode($suggestions));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/delivery-point', function (Request $request, Response $response) {
    $queryParams = $request->getQueryParams();
    $address = $queryParams['address'] ?? '';

    $deliveryPointDetails = getDeliveryPointDetails($address);

    $response->getBody()->write(json_encode($deliveryPointDetails));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();


function getAddressSuggestions(string $searchTerm = ''): array
{
    // Implement your logic here to fetch address suggestions
    // This is just a placeholder response
    return [
        '123 Fake St, Faketown',
        '456 Phony Ave, Phonycity'
    ];
}

function getDeliveryPointDetails(string $address = ''): array
{
    // Implement your logic here to fetch delivery point details
    // This is just a placeholder response
    return [
        'address' => $address,
        'delivery_point' => 'Delivery Point Details for ' . $address
    ];
}

