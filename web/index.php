<?php
// /web/index.php
$app = require_once __DIR__.'/../app/app.php';
require __DIR__ . '/../app/config/prod.php';
//Services
$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../resources/views'
));

$app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $app['db.options']
));

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->register(new MonologServiceProvider, array(
    'monolog.logfile' => __DIR__ . '/logs/app.log',
));

$app->mount("/users", new MyApp\Controller\Providers\Users());
$app->mount("/books", new MyApp\Controller\Providers\Books());
$app->mount("/orders", new MyApp\Controller\Providers\Orders());
$app->mount("/authors", new MyApp\Controller\Providers\Authors());



$app->run();