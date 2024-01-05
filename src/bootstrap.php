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

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$loop = Loop::get();

//activamos calendarizador de rx
$scheduler = new EventLoopScheduler($loop);
Scheduler::setDefaultFactory(static fn () => $scheduler);

//iniciamos el servidor web
ReactHttpServer::init(
    '0.0.0.0',
    '8000',
    $loop
);


$loop->run();
