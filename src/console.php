#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use pascualmg\cohete\ddd\Infrastructure\Console\ConsoleDispatcher;
use pascualmg\cohete\ddd\Infrastructure\Console\Command\PostListCommand;
use pascualmg\cohete\ddd\Infrastructure\Console\Command\PostRegenerateHtmlCommand;
use pascualmg\cohete\ddd\Infrastructure\PSR11\ContainerFactory;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$container = ContainerFactory::create();

$dispatcher = new ConsoleDispatcher($container, [
    PostListCommand::class,
    PostRegenerateHtmlCommand::class,
]);

exit($dispatcher->run($argv));
