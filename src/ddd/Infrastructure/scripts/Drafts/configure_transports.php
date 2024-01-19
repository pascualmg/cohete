<?php

require dirname(__DIR__, 5) . '/vendor/autoload.php';

use Bunny\Async\Client;
use Bunny\Channel;
use Bunny\Protocol\MethodExchangeDeclareOkFrame;
use Bunny\Protocol\MethodQueueBindOkFrame;
use Bunny\Protocol\MethodQueueDeclareOkFrame;
use React\EventLoop\Loop;
use Rx\Observable;


$client = new Client(Loop::get(), [
    'host' => '127.0.0.1', // host to connect to
    'port' => 5672, // port to connect to
    'vhost' => '/', // the virtual host to use
    'user' => 'rabbitmq', // user
    'password' => 'rabbitmq' // password
]);


$connectionObservable = Observable::fromPromise($client->connect());

$connectionObservable
    ->flatMap(function ($client) {
        return Observable::fromPromise($client->channel());
    })
    ->flatMap(function (Channel $channel) {
        return Observable::fromPromise(\React\Promise\all([
            $channel->queueDeclare('queue_name'),
            $channel->exchangeDeclare('exchange_name', 'direct'),
        ]));
    })
    ->flatMap(function ($resultsArray) use ($client) {
        [$response1, $response2] = $resultsArray;

        if(
            $response1 instanceof MethodQueueDeclareOkFrame &&
            $response2 instanceof MethodExchangeDeclareOkFrame
        ) {
            return Observable::fromPromise($client->channel());
        }
        throw new \RuntimeException("algo se ha roto mientras se declaraba la cola o el exchange");
    })
    ->flatMap(
        function ($channel ) {
            return Observable::fromPromise($channel->queueBind('queue_name', 'exchange_name', 'routing_key'));
        }
    )
    ->subscribe(
        function ($item) {
            if ($item instanceof MethodQueueBindOkFrame) {
                echo "¡Cola y Exchanges creados y vinculados!\n";
            }
        },
        function ($e) {
            echo "Hubo un error al crear cola/exchange.\n";
        }
    );



