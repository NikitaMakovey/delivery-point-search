<?php

use Slim\App;
use App\Http\Controllers\{BranchController, SearchController};

return function (App $app) {
    $app->get('/search', [SearchController::class, 'search']);

    $app->get('/branches/{id}', [BranchController::class, 'show']);
};
