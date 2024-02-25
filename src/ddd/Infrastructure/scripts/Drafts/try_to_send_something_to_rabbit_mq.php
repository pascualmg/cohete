<?php

require dirname(__DIR__, 5) . '/vendor/autoload.php';

use Bunny\Async\Client;
use pascualmg\reactor\ddd\Domain\Bus\Message;
use pascualmg\reactor\ddd\Infrastructure\Bus\BunnieMessageBus;
use React\EventLoop\Loop;
use Rx\Scheduler;
use Rx\Scheduler\EventLoopScheduler;

$loop = Loop::get();
$client = new Client($loop, [
    'host' => '127.0.0.1', // host to connect to
    'port' => 5672, // port to connect to
    'vhost' => '/', // the virtual host to use
    'user' => 'rabbitmq', // user
    'password' => 'rabbitmq' // password
]);



$bunnieMb = new  BunnieMessageBus(
    $client
);

$scheduler = new EventLoopScheduler($loop);
Scheduler::setDefaultFactory(static fn () => $scheduler);

$bunnieMb->subscribe('foo', function ($id) {
    xdebug_var_dump($id);
    return $id;
});


$message = new Message('foo', ['bar']);
while(true) {
    $bunnieMb->publish(
        $message
    );
}




$loop->run();
