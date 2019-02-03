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

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__ . '/logs/app.log',
));

/*$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'secure' => array(
            'anonymous' => true,
            'pattern' => '^.*$',
            'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
            'logout' => array('logout_path' => '/logout'),
            'users' => function () use ($app) {
                return new MyApp\User\UserProvider($app['db']);
            },
        )
    )
));*/
//Определение правил доступа
//$app['security.access_rules'] = array(
//    array('^/admin', 'ROLE_ADMIN'),
//    array('^.*$', 'ROLE_USER'),
//);
$app->mount("/users", new MyApp\Controller\Providers\Users());
$app->mount("/books", new MyApp\Controller\Providers\Books());
$app->mount("/orders", new MyApp\Controller\Providers\Orders());
$app->mount("/authors", new MyApp\Controller\Providers\Authors());


$app['route_class'] = 'MyRoute';

$app->run();