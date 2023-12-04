<?php
require "../vendor/autoload.php";

use Psr\Http\Message\ResponseInterface;
use Rx\Observable;
use React\EventLoop\Loop;
use Rx\Scheduler;
use React\Http\Browser;

$loop = Loop::get();
$client = new Browser();

$googleObservable = Observable::fromPromise($client->get(
    'http://www.google.com'
));

Scheduler::setDefaultFactory(
    function () use ($loop) {
        return new Scheduler\EventLoopScheduler($loop);
    }
);
$source = Observable::fromArray([2,3,4]);
$onNext = function ($next) {
    echo $next;
};

$onError = function ($error) {
    echo $error;
};

$onCompleted = function () {
    echo "completed";
};

$source
    ->take(1)
    ->filter(function($a) {return$a > 0; })
    ->subscribe(
    $onNext ,
    $onError ,
    $onCompleted
);

$googleObservable
    ->map( fn(ResponseInterface $response) => $response->getBody()->getContents() )
    ->subscribe(function( string $var) {echo $var;} );
