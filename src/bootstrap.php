<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use Pascualmg\Rx\ddd\Infrastructure\HttpServer\ContainerFactory;
use Pascualmg\Rx\ddd\Infrastructure\HttpServer\ReactHttpServer;
use React\EventLoop\Loop;

$loop = Loop::get();
ReactHttpServer::init(
    $loop,
   ContainerFactory::create()
);


$loop->run();
