#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Cohete\Container\ContainerFactory;
use Dotenv\Dotenv;
use pascualmg\cohete\ddd\Infrastructure\Console\ConsoleDispatcher;
use pascualmg\cohete\ddd\Infrastructure\Console\Command\PostListCommand;
use pascualmg\cohete\ddd\Infrastructure\Console\Command\PostRegenerateHtmlCommand;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$definitions = require __DIR__ . '/../config/definitions.php';
$container = ContainerFactory::create($definitions);

$dispatcher = new ConsoleDispatcher($container, [
    PostListCommand::class,
    PostRegenerateHtmlCommand::class,
]);

exit($dispatcher->run($argv));
