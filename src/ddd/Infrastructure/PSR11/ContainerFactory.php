<?php

namespace Pascualmg\Rx\ddd\Infrastructure\PSR11;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

class ContainerFactory
{
    public static function create(
        array $definitions = [],
        bool $useAutowiring = true,
        bool $isProd = false,
        string $compilationPath = __DIR__ . '/var/cache',
        string $proxyDirectory = __DIR__ . '/var/tmp'
    ): ContainerInterface {
        $builder = new ContainerBuilder();

        $builder->useAutowiring($useAutowiring);

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
