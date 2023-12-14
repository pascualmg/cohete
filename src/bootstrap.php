<?php

require __DIR__ . '/../vendor/autoload.php';
use Pascualmg\Rx\ddd\Infrastructure\HttpServer\ReactHttpServer;
use React\EventLoop\Loop;

$loop = Loop::get();
ReactHttpServer::init($loop);
$loop->run();
