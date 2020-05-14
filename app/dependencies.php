<?php
declare(strict_types=1);

use App\Service\PostService;
use DI\ContainerBuilder;
use PDO;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        PDO::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $pdo = new PDO(
                $settings['pdo']['dsn'],
                $settings['pdo']['username'],
                $settings['pdo']['password']
            );
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $pdo;
        },
        PostService::class => function (ContainerInterface $c) {
            return new PostService($c->get(PDO::class));
        },
    ]);
};
