<?php
declare(strict_types=1);

use App\Middleware\JsonResponseMiddleware;
use Slim\App;

return function (App $app) {
    $app->add(JsonResponseMiddleware::class);
};
