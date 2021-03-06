<?php

require __DIR__.'/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$twig = Twig::create(__DIR__.'/../templates', ['debug' => true]);
$app->add(TwigMiddleware::create($app, $twig));

////////////////////////////////////////////////////////////////////////////////

$app->get('/api/collections', 'Collectors\API\Collection:index');
$app->get('/api/collections/{id}', 'Collectors\API\Collection:get');
$app->post('/api/collections', 'Collectors\API\Collection:create');
$app->put('/api/collections', 'Collectors\API\Collection:update');
$app->delete('/api/collections', 'Collectors\API\Collection:delete');

$app->get('/api/fields', 'Collectors\API\Field:index');
$app->get('/api/fields/{id}', 'Collectors\API\Field:get');
$app->post('/api/fields', 'Collectors\API\Field:create');
$app->put('/api/fields', 'Collectors\API\Field:update');
$app->put('/api/fields/order', 'Collectors\API\Field:updateOrder');
$app->delete('/api/fields', 'Collectors\API\Field:delete');

$app->get('/api/items', 'Collectors\API\Item:index');
$app->get('/api/items/{id}', 'Collectors\API\Item:get');
$app->post('/api/items', 'Collectors\API\Item:create');
$app->put('/api/items', 'Collectors\API\Item:update');
$app->delete('/api/items', 'Collectors\API\Item:delete');

////////////////////////////////////////////////////////////////////////////////

$app->get('/{route}[/{method}[/{id}]]', function ($request, $response, $args) {
	$args['method'] = $args['method'] ?? 'index';
	$args['id'] = $args['id'] ?? '';
	return Twig::fromRequest($request)->render($response, $args['route'].'/'.$args['method'].'.html', [
		'id' => $args['id'],
		'query' => $request->getQueryParams()
	]);
});

////////////////////////////////////////////////////////////////////////////////

$app->run();
