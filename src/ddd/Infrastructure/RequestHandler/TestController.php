<?php

namespace Pascualmg\Rx\ddd\Infrastructure\RequestHandler;

use Pascualmg\Rx\ddd\Infrastructure\Repository\Post\MysqlPostRepository;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

class TestController
{
    public function handle(ServerRequestInterface $request): PromiseInterface
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

        return $deferred->promise();
    }
}
