<?php

namespace pascualmg\cohete\ddd\Infrastructure\PSR11;

use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use pascualmg\cohete\ddd\Domain\Bus\MessageBus;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use pascualmg\cohete\ddd\Domain\Entity\CommentRepository;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use pascualmg\cohete\ddd\Infrastructure\Bus\ReactMessageBus;
use pascualmg\cohete\ddd\Infrastructure\Parser\FileParser;
use pascualmg\cohete\ddd\Infrastructure\Parser\OrgFileParser;
use pascualmg\cohete\ddd\Infrastructure\ReadModel\CommentCountProjection;
use pascualmg\cohete\ddd\Infrastructure\MCP\CoheteTransport;
use pascualmg\cohete\ddd\Infrastructure\MCP\McpServerFactory;
use pascualmg\cohete\ddd\Infrastructure\Repository\Author\ObservableMysqlAuthorRepository;
use pascualmg\cohete\ddd\Infrastructure\Repository\Comment\ObservableMysqlCommentRepository;
use pascualmg\cohete\ddd\Infrastructure\Repository\Post\ObservableMysqlPostRepository;
use pascualmg\cohete\ddd\Infrastructure\Service\InMemoryRateLimiter;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Mysql\MysqlClient;

class ContainerFactory
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws \Exception
     */
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

        //todo: extract definitions outside this file ?
        $definitions = [
            LoopInterface::class => static fn () => Loop::get(),
            LoggerInterface::class => function (ContainerInterface $_) {
                //todo: extract factory ?
                $logger = new Logger('cohete');
                $logger->pushHandler(
                    new StreamHandler(
                        dirname(__DIR__, 2) . '/Infrastructure/var/log/cohete.log'
                    )
                );
                return $logger;
            },
            PostRepository::class => static fn (ContainerInterface $c) => $c->get(ObservableMysqlPostRepository::class),
            AuthorRepository::class => static fn (ContainerInterface $c) => $c->get(ObservableMysqlAuthorRepository::class),
            CommentRepository::class => static fn (ContainerInterface $c) => $c->get(ObservableMysqlCommentRepository::class),
            CommentCountProjection::class => static fn (ContainerInterface $c) => new CommentCountProjection($c->get(CommentRepository::class)),
            FileParser::class => static fn (ContainerInterface $c) => $c->get(OrgFileParser::class),
            MessageBus::class => static fn (ContainerInterface $c) => $c->get(ReactMessageBus::class),
            ReactMessageBus::class => static fn (ContainerInterface $c) => new ReactMessageBus(
                $c->get(LoopInterface::class)
            ),
            InMemoryRateLimiter::class => static fn () => new InMemoryRateLimiter(5, 600),
            'EventBus' => static fn (ContainerInterface $c) => new ReactMessageBus($c->get(LoopInterface::class)),
            'CommandBus' => static fn (ContainerInterface $c) => new ReactMessageBus($c->get(LoopInterface::class)),
            'QueryBus' => static fn (ContainerInterface $c) => new ReactMessageBus($c->get(LoopInterface::class)),
            'routes.path' => static fn (ContainerInterface $_) => $_ENV['ROUTES_PATH'],
            MysqlClient::class => static fn (ContainerInterface $c) => new MysqlClient(
                "{$_ENV['MYSQL_USER']}:{$_ENV['MYSQL_PASSWORD']}@{$_ENV['MYSQL_HOST']}:{$_ENV['MYSQL_PORT']}/{$_ENV['MYSQL_DATABASE']}",
            ),
            CoheteTransport::class => static function (ContainerInterface $c) {
                $transport = new CoheteTransport();
                McpServerFactory::create(
                    $c,
                    $c->get(LoggerInterface::class),
                    $transport,
                );
                return $transport;
            },
        ];

        if (!empty($definitions)) {
            $builder->addDefinitions($definitions);
        }

        $container = $builder->build();

        $container->get(MessageBus::class)->subscribe(
            'domain_event.post_created',
            function ($data) use ($container) {
                var_dump($data);
                $container->get(LoggerInterface::class)->info(
                    "Escuchando al evento PostCreated !! enviando un email o loque sea "
                );
            }
        );

        $projection = $container->get(CommentCountProjection::class);
        $projection->boot();

        $container->get(MessageBus::class)->subscribe(
            'domain_event.comment_published',
            function ($data) use ($container) {
                $container->get(CommentCountProjection::class)->onCommentPublished($data);
                $container->get(LoggerInterface::class)->info(
                    "CommentWasPublished: counter updated",
                    is_array($data) ? $data : [$data]
                );
            }
        );

        return $container;
    }
}
