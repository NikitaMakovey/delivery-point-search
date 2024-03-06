<?php

use App\Core\DatabaseConnection;
use Psr\Container\ContainerInterface;

return function (ContainerInterface $container) {
    $container->set('database', function () {
        return DatabaseConnection::getInstance()?->getConnection();
    });
};
