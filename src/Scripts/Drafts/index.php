<?php

require "../vendor/autoload.php";

use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Loop;
use React\Http\Browser;
use Rx\Observable;
use Rx\Scheduler;

$loop = Loop::get();
$client = new Browser();

$googleObservable = Observable::fromPromise(
    $client->get(
        'http://www.google.com'
    )
);

Scheduler::setDefaultFactory(
    function () use ($loop) {
        return new Scheduler\EventLoopScheduler($loop);
    }
);
$source = Observable::fromArray([2, 3, 4]);
$onNext = function ($next): void {
    echo $next;
};

$onError = function ($error): void {
    echo $error;
};

$onCompleted = function (): void {
    echo "completed";
};

$source
    ->take(1)
    ->filter(function ($a) {
        return $a > 0;
    })
    ->subscribe(
        $onNext,
        $onError,
        $onCompleted
    );

$googleObservable
    ->map(fn (ResponseInterface $response) => $response->getBody()->getContents())
    ->subscribe(function (string $var) {
        echo $var;
    });
