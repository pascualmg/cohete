<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use pascualmg\reactor\ddd\Infrastructure\HttpServer\ReactHttpServer;
use React\EventLoop\Loop;
use Rx\Scheduler;
use Rx\Scheduler\EventLoopScheduler;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$loop = Loop::get();
$scheduler = new EventLoopScheduler($loop);
try {
    Scheduler::setDefaultFactory(static fn () => $scheduler);
} catch (Exception $e) {
    echo "Error inicializando el sheduler de rx ";
    var_dump($e);
}

ReactHttpServer::init(
    $_ENV['ROUTES_CONFIG_PATH']
);

$loop->run();
