<?php
$app['db.options'] = array(
    "driver" => "pdo_pgsql",
    "host" => "localhost",
    "dbname" => "postgres",
    "user" => "postgres",
    "port" => "5432",
    "password" => "",
);

$app['swiftmailer.options'] = array(
    'host' => 'smtp.mail.ru',
    'port' => '587',
    'username' => 'litskevich_olga@mail.ru',
    'password' => 'k50ijseries',
    'encryption' => 'TLS',
    'auth_mode' => null
);

// debug
$app['debug'] = true;
