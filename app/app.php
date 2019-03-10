<?php
require_once __DIR__ . '/bootstrap.php';

use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->get('/', function () {
    return new Response('Welcome to my new Silex app');
});

if (isset($app_env) && in_array($app_env, ['prod', 'test']))
    $app['env'] = $app_env;
else
    $app['env'] = 'prod';

return $app;