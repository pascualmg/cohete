<?php

namespace Pascualmg\Rx\ddd\Infrastructure\RequestHandler;

use Pascualmg\Rx\ddd\Infrastructure\Repository\Post\FilePostRepository;
use Pascualmg\Rx\ddd\Infrastructure\Repository\Post\MysqlPostRepository;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\Deferred;

class TestController
{
    public function handle(ServerRequestInterface $request): \React\Promise\PromiseInterface
    {
        $deferred = new Deferred();

        if ($request->getMethod() === 'GET') {
            $deferred->resolve(
                new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode(['client_ip' => $request->getAttribute('client-ip')], JSON_THROW_ON_ERROR)
                )
            );
        }


        if ($request->getMethod() === 'POST') {
            [$id, $fecha] = array_values(
                json_decode($request->getBody()->getContents(), true, 2, JSON_THROW_ON_ERROR)
            );

            $deferred->resolve(
                (new MysqlPostRepository())->findAll()
                    ->then(function (array $posts) {
                        return new Response(
                            200,
                            ['Content-Type' => 'application/json'],
                            json_encode(['payload' => $posts], JSON_THROW_ON_ERROR)
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
        }

        return $deferred->promise();
    }
}
