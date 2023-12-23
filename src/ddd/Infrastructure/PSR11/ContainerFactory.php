<?php

namespace Pascualmg\Rx\ddd\Infrastructure\PSR11;

use DI\ContainerBuilder;
use Pascualmg\Rx\ddd\Domain\Bus\Bus;
use Pascualmg\Rx\ddd\Domain\Entity\PostRepository;
use Pascualmg\Rx\ddd\Infrastructure\Bus\ReactEventBus;
use Pascualmg\Rx\ddd\Infrastructure\Repository\Post\MysqlPostRepository;
use Psr\Container\ContainerInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;

use function DI\autowire;
use function DI\factory;

class ContainerFactory
{
    public static function create(
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

        $definitions = [
            LoopInterface::class => autowire(Loop::class),
            Bus::class => factory(
                function (ContainerInterface $c) {
                    return new ReactEventBus(
                        $c->get(LoopInterface::class)
                    );
                }
            ),
            PostRepository::class => autowire(MysqlPostRepository::class),
        ];

        if (!empty($definitions)) {
            $builder->addDefinitions($definitions);
        }

        return $builder->build();
    }
}
