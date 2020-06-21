<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use DI\Container;

$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});

$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response) {
    return $response->getBody()->write('Welcome to Slim!');
});
$app->get('/users', function (Request $req, Response $res) {
    return $res->write('GET /users');
});
$app->post('/users', function (Request $req, Response $res) {
    return $res->withStatus(302);
});
$app->get('/users/{id}', function (Request $req, Response $res, $args) {
    $params = ['id' => $args['id'], 'nickname' => 'user-' . $args['id']];
    return $this->get('renderer')->render($res, 'users/show.phtml', $params);
});
$app->get('/courses/{id}', function (Request $req, Response $res, $args) {
    ['id' => $id] = $args;
    return $res->write("Course id: {$id}");
});
$app->run();
