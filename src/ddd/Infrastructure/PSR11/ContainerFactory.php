<?php

namespace pascualmg\reactor\ddd\Infrastructure\PSR11;

use DI\ContainerBuilder;
use pascualmg\reactor\ddd\Domain\Bus\MessageBus;
use pascualmg\reactor\ddd\Domain\Entity\PostRepository;
use pascualmg\reactor\ddd\Infrastructure\Bus\ReactMessageBus;
use pascualmg\reactor\ddd\Infrastructure\Repository\Post\FilePostRepository;
use pascualmg\reactor\ddd\Infrastructure\Repository\Post\AsyncMysqlPostRepository;
use pascualmg\reactor\ddd\Infrastructure\Repository\Post\ObservableFilePostRepository;
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
            LoopInterface::class => static fn () => Loop::get(),
            ReactMessageBus::class => static fn (ContainerInterface $c) => new ReactMessageBus(
                $c->get(LoopInterface::class)
            ),
            MessageBus::class => static fn (ContainerInterface $c) => $c->get(ReactMessageBus::class),
            PostRepository::class => static fn (ContainerInterface $c) => $c->get(AsyncMysqlPostRepository::class),
            'EventBus' => static fn (ContainerInterface $c) => new ReactMessageBus($c->get(LoopInterface::class)),
            'CommandBus' => static fn (ContainerInterface $c) => new ReactMessageBus($c->get(LoopInterface::class)),
            'QueryBus' => static fn (ContainerInterface $c) => new ReactMessageBus($c->get(LoopInterface::class)),
            'routes.path' => static fn (ContainerInterface $_) => $_ENV['ROUTES_PATH'],
        ];

        if (!empty($definitions)) {
            $builder->addDefinitions($definitions);
        }

        try {
            $container = $builder->build();
        } catch (\Exception $e) {
        }
        $container->get(MessageBus::class)->subscribe(
            'foo',
            function ($data) {
                var_dump($data);
            }
        );
        return $container;
    }
}
