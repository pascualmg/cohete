<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Pascualmg\Rx\ddd\Infrastructure\HttpServer\ReactHttpServer;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

ReactHttpServer::init(
    $_ENV['ROUTES_CONFIG_PATH']
);
