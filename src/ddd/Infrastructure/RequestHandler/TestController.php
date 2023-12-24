<?php

namespace Pascualmg\Rx\ddd\Infrastructure\RequestHandler;

use Pascualmg\Rx\ddd\Domain\Bus\Bus;
use Pascualmg\Rx\ddd\Domain\Bus\Event;
use Pascualmg\Rx\ddd\Domain\Entity\PostRepository;
use Pascualmg\Rx\ddd\Infrastructure\Repository\Post\MysqlPostRepository;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

class TestController implements HttpRequestHandler
{
    private Bus $bus;

    public function __construct(Bus $bus, PostRepository $postRepository)
    {
        $this->bus = $bus;
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): PromiseInterface
    {
        $deferred = new Deferred();


        $deferred->resolve(
            (new MysqlPostRepository())->findAll()
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


        $this->bus->dispatch(
            new Event(
                'foo',
                "wee! desde testcontroller"
            )
        );
        return $deferred->promise();
    }
}
