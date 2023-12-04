<?php
require "../vendor/autoload.php";

use React\EventLoop\Loop;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use React\Stream\ReadableResourceStream;
use React\Stream\ThroughStream;
use React\Stream\WritableResourceStream;
use Rx\Observable;

$loop = Loop::get();

$istream = new ReadableResourceStream(STDIN, $loop);
$ostream = new WritableResourceStream(STDOUT, $loop);
$addEOL = new ThroughStream(
    function (string $data) {
        return $data . PHP_EOL;
    }
);

Observable::fromPromise((new Connector($loop))->connect('0.0.0.0:11334'))
    ->subscribe(
        function (ConnectionInterface $connection) use ($istream, $ostream, $addEOL){
            echo "conectao a " . $connection->getRemoteAddress() . PHP_EOL;
            $istream->pipe($connection)->pipe($addEOL)->pipe($ostream);
        },
        function (\Throwable $throwable) {
            var_dump($throwable->getMessage());
        }
    );

