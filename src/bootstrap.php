<?php


ini_set('memory_limit', '256M');

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\ReactHttpServer;
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

//activamos calendarizador de rx
$scheduler = new EventLoopScheduler($loop);
Scheduler::setDefaultFactory(static fn () => $scheduler);

//iniciamos el servidor web
ReactHttpServer::init(
    $_ENV['HTTP_SERVER_HOST'],
    $_ENV['HTTP_SERVER_PORT'],
    $loop,
    $isDevelopment,
);

$loop->run();
