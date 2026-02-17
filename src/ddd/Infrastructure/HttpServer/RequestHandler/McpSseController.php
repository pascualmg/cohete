<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Infrastructure\MCP\CoheteTransport;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use React\Stream\ThroughStream;

class McpSseController implements HttpRequestHandler
{
    public function __construct(
        private readonly CoheteTransport $transport,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $clientId = 'sse_' . bin2hex(random_bytes(16));
        $stream = new ThroughStream();

        $this->transport->registerClient($clientId, $stream);

        // Defer the initial endpoint event to next tick so the Response
        // with the ThroughStream body is sent first by ReactPHP
        Loop::futureTick(function () use ($clientId, $request, $stream) {
            if (!$stream->isWritable()) {
                return;
            }

            $baseUri = $request->getUri()->withPath('/mcp/message')->withQuery('')->withFragment('');

            // Cloudflare/reverse proxies terminate TLS, so ReactPHP sees http://.
            // Use X-Forwarded-Proto to match the client's original scheme.
            $proto = $request->getHeaderLine('X-Forwarded-Proto');
            if ($proto === 'https') {
                $baseUri = $baseUri->withScheme('https');
            }

            $postEndpoint = (string)$baseUri->withQuery("clientId={$clientId}");

            $frame = "event: endpoint\n";
            $frame .= "id: init-{$clientId}\n";
            $frame .= "data: {$postEndpoint}\n";
            $frame .= "\n";
            $stream->write($frame);
        });

        return new Response(
            200,
            [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
                'Access-Control-Allow-Origin' => '*',
            ],
            $stream
        );
    }
}
