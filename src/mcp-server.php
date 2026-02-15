#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use pascualmg\cohete\ddd\Infrastructure\MCP\BlogToolHandlers;
use pascualmg\cohete\ddd\Infrastructure\PSR11\ContainerFactory;
use PhpMcp\Server\Server;
use PhpMcp\Server\Transports\StdioServerTransport;
use Psr\Log\AbstractLogger;

// Stderr logger (stdout is reserved for MCP protocol)
$logger = new class extends AbstractLogger {
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        fwrite(STDERR, sprintf("[%s][%s] %s\n", date('H:i:s'), strtoupper($level), $message));
    }
};

try {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();

    $container = ContainerFactory::create();

    $server = Server::make()
        ->withServerInfo('cohete-blog', '1.0.0')
        ->withLogger($logger)
        ->withContainer($container)
        ->withTool([BlogToolHandlers::class, 'listPosts'], 'list_posts', 'List all blog posts')
        ->withTool([BlogToolHandlers::class, 'getPost'], 'get_post', 'Get a single blog post by UUID')
        ->withTool([BlogToolHandlers::class, 'createPost'], 'create_post', 'Create a new blog post')
        ->withTool([BlogToolHandlers::class, 'publishOrg'], 'publish_org', 'Publish a blog post from org-mode content')
        ->withTool([BlogToolHandlers::class, 'updatePost'], 'update_post', 'Update an existing blog post')
        ->build();

    $transport = new StdioServerTransport();
    $server->listen($transport);
} catch (\Throwable $e) {
    fwrite(STDERR, "[CRITICAL] " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
    exit(1);
}
