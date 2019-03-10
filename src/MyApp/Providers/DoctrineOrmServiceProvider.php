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
        $app['em'] = function () use ($app) {
            $isDevMode = true;
            $isSimpleMode = FALSE;
            $proxyDir = null;
            $cache = null;
            $config = Setup::createAnnotationMetadataConfiguration(
                array($app['config']['parameters']['orm.metadata']), $isDevMode, $proxyDir, $cache, $isSimpleMode
            );
            return EntityManager::create($app['db.options'], $config);
        };
    }
}
