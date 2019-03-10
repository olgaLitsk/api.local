<?php
require_once __DIR__ . '/../app/bootstrap.php';
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

$app = new Silex\Application();
// Base path
if (!defined('BASEPATH')) {
    define('BASEPATH', realpath(__DIR__ . '/../'));
}

$app['config'] = function () use ($app) {
    $params = array();
    $params['base_path'] = BASEPATH;
    $fileConfig = is_file(BASEPATH . '/env.yml') ? BASEPATH . '/env.yml' : BASEPATH . '/app/config/parameters.yml';
    $data = Yaml::parse(file_get_contents($fileConfig));
    foreach ($data['parameters'] as $key => $value) {
        $params['parameters'][$key] = $value;
    }
    return $params;
};
$app->get('/', function () {
    return new Response('Welcome to my new Silex app');
});
$app->after(function (Request $request, Response $response) {
    $contentType = $request->getContentType();
    $response->headers->set('Content-Type', $contentType);
});

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app['phone.service'] = function () {
    return new MyApp\Services\CheckPhoneService();
};

$app->register(new MyApp\Providers\DoctrineOrmServiceProvider(), array(
    'db.options' => array(
        "driver" => $app['config']['parameters']['driver'],
        "host" => $app['config']['parameters']['host'],
        "dbname" => $app['config']['parameters']['dbname'],
        "user" => $app['config']['parameters']['user'],
        "port" => $app['config']['parameters']['port'],
        "password" => $app['config']['parameters']['password'],
    ),
));

$app->register(new Silex\Provider\SwiftmailerServiceProvider(), array(
    'swiftmailer.options' => array(
        'host' => $app['config']['parameters']['mail.host'],
        'port' => $app['config']['parameters']['mail.port'],
        'username' => $app['config']['parameters']['mail.username'],
        'password' => $app['config']['parameters']['mail.password'],
        'encryption' => $app['config']['parameters']['mail.encryption'],
        'auth_mode' => $app['config']['parameters']['mail.auth_mode'],
        'use_spool' => $app['config']['parameters']['mail.use_spool'],
    )));

$app->register(new Silex\Provider\SecurityServiceProvider());

$app['security.firewalls'] = array(
    'user' => array(
        'anonymous' => true,
        'pattern' => '^.*$',
        'http' => true,
        'users' => array(
            'user' => array('ROLE_USER', '$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a'),
            'admin' => array('ROLE_ADMIN', '$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a'),
        )
    ),
);

$app->mount("/authors", new \MyApp\Controllers\AuthorsController());
$app->mount("/books", new \MyApp\Controllers\BooksController());
$app->mount("/users", new \MyApp\Controllers\UsersController());
$app->mount("/orders", new \MyApp\Controllers\OrdersController());

if (isset($app_env) && in_array($app_env, ['prod', 'test']))
    $app['env'] = $app_env;
else
    $app['env'] = 'prod';

if ('test' === $app['env']) {
    return $app;
} else {
    $app->run();
}

