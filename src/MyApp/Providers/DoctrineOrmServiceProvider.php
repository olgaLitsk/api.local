<?php
namespace MyApp\Providers;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DoctrineOrmServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['em'] = function () {
            $app['db.options'] = array(
                "driver" => "pdo_pgsql",
                "host" => "localhost",
                "dbname" => "postgres",
                "user" => "postgres",
                "port" => "5432",
                "password" => "",
            );
            $isDevMode = true;
            $isSimpleMode = FALSE;
            $proxyDir = null;
            $cache = null;
            $config = Setup::createAnnotationMetadataConfiguration(
                array("/src/MyApp/Models/ORM"), $isDevMode, $proxyDir, $cache, $isSimpleMode
            );
            return EntityManager::create($app['db.options'], $config);
        };
    }
}


