<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use Pascualmg\Rx\ddd\Domain\Bus\Event;
use Pascualmg\Rx\ddd\Infrastructure\Bus\ReactEventBus;
use Pascualmg\Rx\ddd\Infrastructure\HttpServer\ReactHttpServer;
use React\EventLoop\Loop;

$loop = Loop::get();
ReactHttpServer::init(
    $loop,
    __DIR__ . '/ddd/Infrastructure/HttpServer/routes.json'
);

$eventBus = new ReactEventBus(
    Loop::get(),
);

$eventBus->subscribe(
    'foo',
    static function ($quechoy, $ber, $bir) {

        var_dump($quechoy);
    }
);

$eventBus->dispatch(
    new Event('foo' , ["bar", "ber", "bir"])
);


$loop->run();
