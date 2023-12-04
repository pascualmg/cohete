<?php

require "../vendor/autoload.php";


use React\EventLoop\Loop;
use React\Http;


$http = new Http\HttpServer(Loop::get());
$http->listen(
);