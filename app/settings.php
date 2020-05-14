<?php
declare(strict_types=1);

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'display_error_details' => true,
            'pdo' => [
                'dsn' => sprintf("mysql:dbname=%s;host=%s;charset=utf8", getenv('MYSQL_DB'), getenv('MYSQL_HOST')),
                'username' => getenv('MYSQL_USER'),
                'password' => getenv('MYSQL_PASSWORD'),
            ],
            'slack' => [
                'signing_secret' => getenv('SLACK_SIGNING_SECRET'),
            ],
        ],
    ]);
};
