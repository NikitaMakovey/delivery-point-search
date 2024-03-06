<?php

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

$database = require __DIR__ . '/../app/database.php';
$database($container);

$app = SlimAppFactory::create($container);

$app->getContainer()->get('database');

$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

$app->run();

