<?php

require "../vendor/autoload.php";


use React\EventLoop\Loop;
use React\Stream;


$istream = new Stream\ReadableResourceStream(STDIN, null, 1);

$istream->on('data', function ($_) use ($istream){
    $istream->pause();

   Loop::get()->addTimer(1, function () use ($istream){
     $istream->resume();
   });
});

$istream->pipe(
    new Stream\WritableResourceStream(STDOUT)
);
