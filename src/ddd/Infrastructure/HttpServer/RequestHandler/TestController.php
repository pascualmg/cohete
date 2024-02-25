<?php

namespace pascualmg\reactor\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\reactor\ddd\Domain\Bus\Message;
use pascualmg\reactor\ddd\Domain\Bus\MessageBus;
use pascualmg\reactor\ddd\Domain\Entity\PostRepository;
use pascualmg\reactor\ddd\Infrastructure\Repository\Post\AsyncMysqlPostRepository;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

class TestController implements HttpRequestHandler
{
    private MessageBus $bus;

    public function __construct(MessageBus $bus, PostRepository $postRepository)
    {
        $this->bus = $bus;
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): PromiseInterface
    {
        $deferred = new Deferred();


        $deferred->resolve(
            (new AsyncMysqlPostRepository())->findAll()
                ->then(function (array $posts) use ($request) {
                    return new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        json_encode([
                            'payload' => $posts,
                            'your_ip' => $request->getAttribute('client_ip')
                        ], JSON_THROW_ON_ERROR)
                    );
                })
                ->catch(function (\Throwable $error) {
                    return new Response(
                        500,
                        ['Content-Type' => 'application/json'],
                        json_encode(['error' => $error->getMessage()], JSON_THROW_ON_ERROR)
                    );
                })
        );


        $this->bus->publish(
            new Message(
                'foo',
                "wee! desde testcontroller"
            )
        );
        return $deferred->promise();
    }
}
