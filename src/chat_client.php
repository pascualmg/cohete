<?php
require "../vendor/autoload.php";

use React\EventLoop\Loop;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;
use Rx\Observable;

$loop = Loop::get();

$istream = new ReadableResourceStream(STDIN, $loop);
$ostream = new WritableResourceStream(STDOUT, $loop);

Observable::fromPromise((new Connector($loop))->connect('0.0.0.0:11334'))
    ->subscribe(
        function (ConnectionInterface $connection) use ($istream, $ostream){
            echo "conectao a " . $connection->getRemoteAddress();
            $istream->pipe($connection);
            $connection->pipe($ostream);
        },
        function (\Throwable $throwable) {
            var_dump($throwable->getMessage());
        }
    );

