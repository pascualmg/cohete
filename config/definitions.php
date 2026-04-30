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
use pascualmg\cohete\ddd\Application\Media\UploadMediaCommandHandler;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaRepository;
use pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject\Bucket;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use pascualmg\cohete\ddd\Infrastructure\MCP\CoheteTransport;
use pascualmg\cohete\ddd\Infrastructure\MCP\McpServerFactory;
use pascualmg\cohete\ddd\Infrastructure\Media\Aws4Signer;
use pascualmg\cohete\ddd\Infrastructure\Media\ObservableS3MediaRepository;
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
use React\Http\Browser;
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

    // Object storage (S3-compatible: MinIO, Garage, AWS S3, Cloudflare R2...).
    // Vars de entorno: S3_ENDPOINT, S3_REGION, S3_ACCESS_KEY, S3_SECRET_KEY, S3_BUCKET.
    Aws4Signer::class => static fn () => new Aws4Signer(
        accessKey: $_ENV['S3_ACCESS_KEY'] ?? '',
        secretKey: $_ENV['S3_SECRET_KEY'] ?? '',
        region:    $_ENV['S3_REGION']    ?? 'us-east-1',
    ),
    MediaRepository::class => static fn (ContainerInterface $c) => new ObservableS3MediaRepository(
        http:          new Browser(),
        signer:        $c->get(Aws4Signer::class),
        endpoint:      $_ENV['S3_ENDPOINT'] ?? 'http://localhost:9000',
        defaultBucket: Bucket::from($_ENV['S3_BUCKET'] ?? 'cohete-media'),
    ),

    UploadMediaCommandHandler::class => static fn (ContainerInterface $c) => new UploadMediaCommandHandler(
        mediaRepository: $c->get(MediaRepository::class),
        defaultBucket:   $_ENV['S3_BUCKET'] ?? 'cohete-media',
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
