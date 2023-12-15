<?php

require __DIR__ . '/../vendor/autoload.php';
use Pascualmg\Rx\ddd\Infrastructure\HttpServer\ReactHttpServer;
use React\EventLoop\Loop;

$loop = Loop::get();
ReactHttpServer::init(
    $loop,
    __DIR__ . '/ddd/Infrastructure/HttpServer/routes.json'
);

$repo = new \Pascualmg\Rx\ddd\Infrastructure\Repository\Post\MysqlPostRepository();
$repo->findAll();
$loop->run();
