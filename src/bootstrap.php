<?php

ini_set('memory_limit', '256M');
ini_set('xdebug.mode', 'debug');
ini_set('xdebug.start_with_request', 'yes');

require __DIR__ . '/../vendor/autoload.php';

use Cohete\Bus\MessageBus;
use Cohete\Container\ContainerFactory;
use Cohete\HttpServer\Kernel;
use Cohete\HttpServer\ReactHttpServer;
use Dotenv\Dotenv;
use pascualmg\cohete\ddd\Infrastructure\WebSocket\WebSocketServer;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use Rx\Scheduler;
use Rx\Scheduler\EventLoopScheduler;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$isDevelopment = ($_ENV['APP_ENV'] ?? 'prod') === 'dev';

if($isDevelopment) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$loop = Loop::get();

$scheduler = new EventLoopScheduler($loop);
Scheduler::setDefaultFactory(static fn () => $scheduler);

// Blog-specific container definitions
$definitions = require __DIR__ . '/../config/definitions.php';
$container = ContainerFactory::create($definitions);

// Wire domain event subscribers
/** @var MessageBus $bus */
$bus = $container->get(MessageBus::class);
/** @var LoggerInterface $logger */
$logger = $container->get(LoggerInterface::class);

$bus->subscribe(
    'domain_event.post_created',
    function ($data) use ($logger) {
        $logger->info("PostCreated event received", is_array($data) ? $data : [$data]);
    }
);

$bus->subscribe(
    'domain_event.comment_published',
    function ($data) use ($logger) {
        $logger->info("CommentWasPublished: nuevo comentario", is_array($data) ? $data : [$data]);
    }
);

// HTTP server
$routesPath = $_ENV['ROUTES_PATH'];
$kernel = new Kernel($container, $routesPath);
$staticRoot = __DIR__ . '/ddd/Infrastructure/webserver/html';

ReactHttpServer::init(
    host: $_ENV['HTTP_SERVER_HOST'],
    port: $_ENV['HTTP_SERVER_PORT'],
    kernel: $kernel,
    loop: $loop,
    staticRoot: $staticRoot,
    isDevelopment: $isDevelopment,
);

// WebSocket server (same loop, port 8001)
WebSocketServer::init(
    $_ENV['HTTP_SERVER_HOST'],
    8001,
    $loop,
);

$loop->run();
