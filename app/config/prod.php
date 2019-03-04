<?php
$bbb['db.options'] = array(
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
// set API Access Key -9903d695c5953b3b26aa028e9f853912
$app['access_key '] = '9903d695c5953b3b26aa028e9f853912';
