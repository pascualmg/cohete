<?php

require "../../vendor/autoload.php";

use Colors\Color;
use React\EventLoop\Loop;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use React\Stream\ReadableResourceStream;
use React\Stream\ThroughStream;
use React\Stream\WritableResourceStream;
use Rx\Observable;

$color = new Color();

$loop = Loop::get();

$istream = new ReadableResourceStream(STDIN, $loop);
$ostream = new WritableResourceStream(STDOUT, $loop);
$addEOL = new ThroughStream(
    function (string $data) {
        return $data . PHP_EOL;
    }
);
$addColor = new ThroughStream(
    function (string $data) use ($color) {
        return $color($data)->green()->bold()->bg_black;
    }
);


Observable::fromPromise((new Connector($loop))->connect('0.0.0.0:11334'))
    ->subscribe(
        function (ConnectionInterface $connection) use ($istream, $ostream, $addEOL, $addColor) {
            echo "conectao a " . $connection->getRemoteAddress() . PHP_EOL;
            $istream
                ->pipe($connection)
                ->pipe($addEOL)
                ->pipe($addColor)
                ->pipe($ostream);
        },
        function (Throwable $throwable) {
            var_dump($throwable->getMessage());
        }
    );
