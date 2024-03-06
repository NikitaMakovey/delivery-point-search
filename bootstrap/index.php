<?php

use App\Core\DatabaseConnection;
use DI\Container;
use DI\Bridge\Slim\Bridge as SlimAppFactory;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/helpers.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$container = new Container();

$settings = require __DIR__ . '/../app/settings.php';
$settings($container);

$app = SlimAppFactory::create($container);

$container = $app->getContainer();

$container['db'] = function ($c) {
    return DatabaseConnection::getInstance()->getConnection();
};

$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

$app->run();

