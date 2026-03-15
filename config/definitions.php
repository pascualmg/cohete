<?php

/**
 * Blog-specific container definitions.
 * These override framework defaults in Cohete\Container\ContainerFactory.
 *
 * Shared by bootstrap.php, console.php and mcp-server.php.
 */

use Cohete\Bus\MessageBus;
use Cohete\Bus\ReactMessageBus;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use pascualmg\cohete\ddd\Domain\Entity\CommentRepository;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use pascualmg\cohete\ddd\Infrastructure\MCP\CoheteTransport;
use pascualmg\cohete\ddd\Infrastructure\MCP\McpServerFactory;
use pascualmg\cohete\ddd\Infrastructure\Parser\FileParser;
use pascualmg\cohete\ddd\Infrastructure\Parser\OrgFileParser;
use pascualmg\cohete\ddd\Infrastructure\Repository\Author\ObservableMysqlAuthorRepository;
use pascualmg\cohete\ddd\Infrastructure\Repository\Comment\ObservableMysqlCommentRepository;
use pascualmg\cohete\ddd\Infrastructure\Repository\Post\ObservableMysqlPostRepository;
use pascualmg\cohete\ddd\Infrastructure\Service\InMemoryRateLimiter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\Mysql\MysqlClient;

return [
    'routes.path' => static fn () => $_ENV['ROUTES_PATH'],

    LoggerInterface::class => static function () {
        $logger = new Logger('cohete');
        $logger->pushHandler(
            new StreamHandler(
                dirname(__DIR__) . '/src/ddd/Infrastructure/var/log/cohete.log'
            )
        );
        return $logger;
    },

    MysqlClient::class => static fn () => new MysqlClient(
        "{$_ENV['MYSQL_USER']}:{$_ENV['MYSQL_PASSWORD']}@{$_ENV['MYSQL_HOST']}:{$_ENV['MYSQL_PORT']}/{$_ENV['MYSQL_DATABASE']}",
    ),

    PostRepository::class => static fn (ContainerInterface $c) => $c->get(ObservableMysqlPostRepository::class),
    AuthorRepository::class => static fn (ContainerInterface $c) => $c->get(ObservableMysqlAuthorRepository::class),
    CommentRepository::class => static fn (ContainerInterface $c) => $c->get(ObservableMysqlCommentRepository::class),
    FileParser::class => static fn (ContainerInterface $c) => $c->get(OrgFileParser::class),
    InMemoryRateLimiter::class => static fn () => new InMemoryRateLimiter(5, 600),

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
