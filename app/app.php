<?php
require_once __DIR__ . '/bootstrap.php';

use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
require __DIR__ . '/../app/config/prod.php';

$app->get('/', function() {
    return new Response('Welcome to my new Silex app');
});

// Detect environment (default: prod) by checking for the existence of $app_env
if (isset($app_env) && in_array($app_env, ['prod', 'dev', 'test', 'qa']))
    $app['env'] = $app_env;
else
    $app['env'] = 'prod';

return $app;