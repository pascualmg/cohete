<?php

namespace Pascualmg\Rx\ddd\Infrastructure\HttpServer;

use DI\Container;
use DI\ContainerBuilder;

class ContainerFactory
{
    //todo: maybe to use in a jsonloader
    public static function create(
        array $definitions = [],
        bool $autowire = true,
        bool $isProd = false,
        string $compilationPath = __DIR__ . '/var/cache',
        string $proxyDirectory = __DIR__ . '/var/tmp'
    ): Container {
        $builder = new ContainerBuilder();

        $builder->useAutowiring($autowire);

        // Habilita las optimizaciones en producción
        if ($isProd) {
            $builder->enableCompilation($compilationPath);
            $builder->writeProxiesToFile(true, $proxyDirectory);
        }

        if (!empty($definitions)) {
            $builder->addDefinitions($definitions);
        }

        return $builder->build();
    }
}
