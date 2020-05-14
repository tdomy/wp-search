<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Service\PostService;

return function (App $app) {
    // Check server status
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['status' => 'OK']));
        return $response;
    });

    $app->group('/slack', function (RouteCollectorProxy $group) {
        $group->post('/posts', function (Request $request, Response $response, $args) {
            $params = $request->getParsedBody();
            $target_date = DateTime::createFromFormat('Ymd', $params['text']);
            $posts = $this->get(PostService::class)->findByPostDate($target_date);
            $tsv = '';
            foreach ($posts as $post) {
                $tsv .= sprintf("%s\t%s\n", $post['post_date'], $post['post_title']);
            }

            $response->getBody()->write(json_encode([
                'response_type' => 'in_channel',
                'blocks' => [
                    [
                        'type' => 'section',
                        'text' => [
                            'type' => 'mrkdwn',
                            'text' => "```$tsv```",
                        ],
                    ],
                ],
            ]));

            return $response;
        });
    });
};