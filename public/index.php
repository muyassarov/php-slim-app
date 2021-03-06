<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use DI\Container;

session_start();

$users = ['mike', 'mishel', 'adel', 'keks', 'kamila'];
$container = new Container();
$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});

$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response) {
    return $response->write('Welcome to Slim!');
});

$app->get('/users', function (Request $req, Response $res) use ($users) {
    $term = $req->getQueryParam('term');
    $params = [];
    if ($term) {
        $filteredUsers = array_filter($users, function ($user) use ($term) {
            return strpos($user, $term) !== false;
        });
        $params['users'] = $filteredUsers;
    } else {
        $params['users'] = $users;
    }
    $params['term'] = $term;
    return $this->get('renderer')->render($res, 'users/index.phtml', $params);
});

$app->get('/users/{id}', function (Request $req, Response $res, $args) {
    $params = ['id' => $args['id'], 'nickname' => 'user-' . $args['id']];
    return $this->get('renderer')->render($res, 'users/show.phtml', $params);
});

$app->get('/courses/{id}', function (Request $req, Response $res, $args) {
    ['id' => $id] = $args;
    return $res->write("Course id: {$id}");
});

$app->get('/foo', function (Request $req, Response $res) {
    $this->get('flash')->addMessage('success', 'This is a message');
    
    return $res->withRedirect('/bar');
});

$app->get('/bar', function (Request $req, Response $res) {
    $messages = $this->get('flash')->getMessages();
    $body = '<pre>' . print_r($messages, true) . '</pre>';
    return $res->write($body);
});

$app->run();
