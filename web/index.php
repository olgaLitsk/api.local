<?php
// /web/index.php
$app = require_once __DIR__.'/../app/app.php';
//Services
$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../resources/views'
));

$app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $app['db.options']
));

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->mount("/users", new MyApp\Controller\Provider\Users());
$app->mount("/books", new MyApp\Controller\Provider\Books());
$app->mount("/orders", new MyApp\Controller\Provider\Orders());
$app->mount("/authors", new MyApp\Controller\Provider\Authors());

$app->run();