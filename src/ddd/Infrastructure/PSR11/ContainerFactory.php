<?php

namespace Pascualmg\Rx\ddd\Infrastructure\PSR11;

use DI\ContainerBuilder;
use Pascualmg\Rx\ddd\Domain\Bus\Bus;
use Pascualmg\Rx\ddd\Domain\Entity\PostRepository;
use Pascualmg\Rx\ddd\Infrastructure\Bus\ReactBus;
use Pascualmg\Rx\ddd\Infrastructure\Repository\Post\MysqlPostRepository;
use Psr\Container\ContainerInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;

class ContainerFactory
{
    public static function create(
        bool $isProd = false,
        bool $useAutowiring = true,
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
            LoopInterface::class => static fn() => Loop::get(),
            ReactBus::class => static fn(ContainerInterface $c) => new ReactBus(
                $c->get(LoopInterface::class)
            ),
            Bus::class => static fn(ContainerInterface $c) => $c->get(ReactBus::class),
            PostRepository::class => static fn(ContainerInterface $c) => $c->get(MysqlPostRepository::class),
            'EventBus' => static fn(ContainerInterface $c) => new ReactBus($c->get(LoopInterface::class)),
            'CommandBus' => static fn(ContainerInterface $c) => new ReactBus($c->get(LoopInterface::class)),
            'QueryBus' => static fn(ContainerInterface $c) => new ReactBus($c->get(LoopInterface::class)),
        ];

        if (!empty($definitions)) {
            $builder->addDefinitions($definitions);
        }

        $container = $builder->build();
        $container->get(Bus::class)->subscribe(
            'foo',
            function ($data) {
                var_dump($data);
            }
        );
        return $container;
    }
}
