<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$app->get('/api/collections', 'Collectors\API\Collection:index');
$app->get('/api/collections/{id}', 'Collectors\API\Collection:get');
$app->post('/api/collections', 'Collectors\API\Collection:create');
$app->put('/api/collections', 'Collectors\API\Collection:update');
$app->delete('/api/collections', 'Collectors\API\Collection:delete');

$app->run();
