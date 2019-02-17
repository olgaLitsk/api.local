<?php
// /web/index.php
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->get('/', function() {
    return new Response('Welcome to my new Silex app');
});

//Services
$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../resources/views'
));
$app['db.options'] = array(
    "driver"     => "pdo_pgsql",
    "host"       => "localhost",
    "dbname"     =>"postgres",
    "user"       => "postgres",
    "port"       =>"5432",
    "password"   => "",
);
$app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $app['db.options']
));

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app['phone.service'] = function () {
    return new MyApp\Services\CheckPhoneService();
};

$app->register(new \MyApp\Providers\AuthorServiceProvider());

$app->register(new MyApp\Providers\DoctrineOrmServiceProvider(), array(
    'db.options' => $app['db.options']
));

$app->mount("/authors", new \MyApp\Controllers\AuthorsController());
$app->mount("/books", new \MyApp\Controllers\BooksController());
$app->mount("/users", new \MyApp\Controllers\UsersController());
$app->mount("/orders", new \MyApp\Controllers\OrdersController());

//$app->register(new Silex\Provider\SecurityServiceProvider());
//$app['security.firewalls'] = array(
//    'secure' => array(
//        'anonymous' => true,
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
//
//
//$app['security.access_rules'] = array(
//    array('^/orders$', 'ROLE_ADMIN'),
//);
$app->run();
//return $app;