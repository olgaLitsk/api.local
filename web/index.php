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


//$app['security.firewalls'] = array(
//    'secure' => array(
//        'anonymous' => true,
//        'pattern' => '^/.*$',
//        'form' => array('login_path' => '/user/login', 'check_path' => '/user/login_check'),
//        'logout' => array('logout_path' => '/user/logout'),
//        'users' => $app->share(function () { return new UserAuthUserProvider(); }),
//    ),
//);

//$app->register(new Silex\Provider\SecurityServiceProvider(), array(
//    'security.firewalls' => array(
////        'books' => array('pattern' => '^/books'), // Example of an url available as anonymous user
//        'default' => array(
//            'pattern' => '^.*$',
//            'anonymous' => true, // Needed as the login path is under the secured area
////            'form' => array('login_path' => '/', 'check_path' => 'login_check'),
////            'logout' => array('logout_path' => '/logout'), // url to call for logging out
//            'users' => function () use ($app) {
//                // Specific class App\User\UserProvider is described below
//                return new MyApp\User\UserProvider($app['db']);
//            }),
//        ),
//    'security.access_rules'=> array(
//        // You can rename ROLE_USER as you wish
////        array('^/.+$', 'ROLE_USER'),
////        array('^/admin', 'ROLE_ADMIN'),
//        array('^/books', 'ROLE_USER'), // This url is available as anonymous user
//    )
//));

$app->register(new Silex\Provider\SecurityServiceProvider());
$app['security.firewalls'] = array(
    'QQQ' => array(
        'pattern' => '^/books',
        'http' => true,
        'anonymous' => true,
        'users' => array(
            // raw password is foo
            'www' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
        ),
//        'users' => function () use ($app) {
//            return new MyApp\User\UserProvider($app['db']);
//        },
    ),
);

$app['phone.service'] = function() {
    return new MyApp\Services\CheckPhoneService();
};

$app->mount("/users", new MyApp\Controller\Providers\Users());
$app->mount("/books", new MyApp\Controller\Providers\Books());
$app->mount("/orders", new MyApp\Controller\Providers\Orders());
$app->mount("/authors", new MyApp\Controller\Providers\Authors());



$app->run();