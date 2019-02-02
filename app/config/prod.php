<?php
$app['db.options'] = array(
    "driver"     => "pdo_pgsql",
    "host"       => "localhost",
    "dbname"     =>"postgres",
    "user"       => "postgres",
    "port"       =>"5432",
    "password"   => "",
);

// debug
$app['debug'] = true;
