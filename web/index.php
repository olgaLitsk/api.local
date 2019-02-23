<?php
//$app = require_once __DIR__.'/../app/app.php';
//require __DIR__ . '/../app/config/prod.php';
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

$app->register(new MyApp\Providers\DoctrineOrmServiceProvider(), array(
    'db.options' => $app['db.options']
));

$app->register(new Silex\Provider\SecurityServiceProvider());

$app['security.firewalls'] = array(
    'user' => array(
        'anonymous' => true,
        'pattern' => '^.*$',
        'http' => true,
        'users' => array(
            'user' => array('ROLE_USER', '$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a'),
            'admin'=> array('ROLE_ADMIN', '$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a'),
        )
    ),
);

$app['security.access_rules'] = array(
    array('^/users', 'ROLE_ADMIN'),
    array('^/authors', 'ROLE_ADMIN'),
);

$app->mount("/authors", new \MyApp\Controllers\AuthorsController());
$app->mount("/books", new \MyApp\Controllers\BooksController());
$app->mount("/users", new \MyApp\Controllers\UsersController());
$app->mount("/orders", new \MyApp\Controllers\OrdersController());



//if (isset($app_env) && in_array($app_env, ['prod', 'dev', 'test', 'qa']))
//    $app['env'] = $app_env;
//else
//    $app['env'] = 'prod';
//
//if ('test' === $app['env']) {
//    return $app;
//} else {
//    $app->run();
//}

$app->run();
return $app;