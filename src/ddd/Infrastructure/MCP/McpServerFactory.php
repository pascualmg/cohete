<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\MCP;

use PhpMcp\Server\Server;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Builds the MCP Server and binds Protocol <-> CoheteTransport.
 *
 * Se crea una sola vez en el ContainerFactory. El Protocol
 * escucha eventos del transport y envia respuestas via SSE.
 */
class McpServerFactory
{
    public static function create(
        ContainerInterface $container,
        LoggerInterface $logger,
        CoheteTransport $transport,
    ): CoheteTransport {
        $server = Server::make()
            ->withServerInfo('cohete-blog', '2.0.0')
            ->withLogger($logger)
            ->withContainer($container)
            ->withTool([BlogToolHandlers::class, 'listPosts'], 'list_posts', 'List all blog posts with id, headline, author and date')
            ->withTool([BlogToolHandlers::class, 'getPost'], 'get_post', 'Get a single blog post by UUID, including full HTML body and org source')
            ->withTool([BlogToolHandlers::class, 'publishOrg'], 'publish_org', 'Publish a blog post from org-mode content (converted to HTML via Pandoc). Use #+TITLE: #+AUTHOR: #+DATE: headers. Requires author_key if publishing as an existing author.')
            ->withTool([BlogToolHandlers::class, 'updatePost'], 'update_post', 'Update a blog post. articleBody MUST be HTML. Requires author_key matching the post author.')
            ->withTool([BlogToolHandlers::class, 'deletePost'], 'delete_post', 'Delete a blog post. Requires author_key matching the post author.')
            ->withTool([BlogToolHandlers::class, 'listComments'], 'list_comments', 'List comments for a blog post')
            ->withTool([BlogToolHandlers::class, 'createComment'], 'create_comment', 'Create a comment on a blog post. Open to anyone.')
            ->build();

        // Framework integration: bind protocol to transport without running the loop
        $protocol = $server->getProtocol();
        $protocol->bindTransport($transport);
        $transport->listen(); // emits 'ready', no-op otherwise

        return $transport;
    }
}
