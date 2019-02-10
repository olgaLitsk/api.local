<?php
// /web/index.php
$app = require_once __DIR__ . '/../app/app.php';
require __DIR__ . '/../app/config/prod.php';

//Services
$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../resources/views'
));

$app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $app['db.options']
));

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app['phone.service'] = function () {
    return new MyApp\Services\CheckPhoneService();
};

$app->register(new MyApp\Providers\DoctrineOrmServiceProvider(), array(
    'db.options' => $app['db.options']
));

//$app->mount("/users", new MyApp\Controller\Providers\Users());
//$app->mount("/books", new MyApp\Controller\Providers\Books());
//$app->mount("/orders", new MyApp\Controller\Providers\Orders());
//$app->mount("/authors", new MyApp\Controller\Providers\Authors());


$app->mount("/authors", new \MyApp\Controllers\AuthorsController());
$app->mount("/books", new \MyApp\Controllers\BooksController());
$app->mount("/users", new \MyApp\Controllers\UsersController());
$app->mount("/orders", new \MyApp\Controllers\OrdersController());
//$app->register(new Silex\Provider\SecurityServiceProvider());
//$app['security.firewalls'] = array(
//    'anonumous' => array(
//        'pattern' => '^/books',
//        'http' => true,
//        'users' => array(
//            // raw password is foo
//            'user' => array('ROLE_USER', '$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a'),
//        ),
//        //        'users' => function () use ($app) {
////            return new MyApp\User\UserProvider($app['db']);
////        },
//    ),
//);
$app->run();