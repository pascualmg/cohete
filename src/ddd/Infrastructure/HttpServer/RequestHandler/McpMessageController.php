<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Infrastructure\MCP\CoheteTransport;
use PhpMcp\Schema\JsonRpc\Error;
use PhpMcp\Schema\JsonRpc\Parser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

class McpMessageController implements HttpRequestHandler
{
    public function __construct(
        private readonly CoheteTransport $transport,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $query = $request->getQueryParams();
        $clientId = $query['clientId'] ?? '';

        if (empty($clientId)) {
            return new Response(400, ['Content-Type' => 'text/plain'], 'Missing clientId query parameter');
        }

        if (!$this->transport->isClientConnected($clientId)) {
            return new Response(404, ['Content-Type' => 'text/plain'], 'Client not connected');
        }

        $body = (string)$request->getBody();

        if (empty($body)) {
            return new Response(400, ['Content-Type' => 'text/plain'], 'Empty request body');
        }

        try {
            $message = Parser::parse($body);
        } catch (\Throwable $e) {
            $error = Error::forParseError('Invalid JSON-RPC: ' . $e->getMessage());
            return new Response(400, ['Content-Type' => 'application/json'], json_encode($error, JSON_UNESCAPED_SLASHES));
        }

        $this->transport->handleIncomingMessage($message, $clientId);

        return new Response(202, ['Content-Type' => 'text/plain'], 'Accepted');
    }
}
