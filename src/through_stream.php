<?php

require "../vendor/autoload.php";

use React\Stream\ReadableResourceStream;
use React\Stream\ThroughStream;
use React\Stream\WritableResourceStream;

$istream = new ReadableResourceStream(STDIN);
$ostream = new WritableResourceStream(STDOUT);

$toUpper = new ThroughStream('strtoupper');


$istream->pipe($toUpper)->pipe($ostream);
