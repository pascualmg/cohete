<?php

namespace Pascualmg\Rx\ddd\Infrastructure\HttpServer;

use Pascualmg\Rx\ddd\Infrastructure\HttpServer\PostRepository\FilePostRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use React\Http\Message\Response;

class TestController implements RequestHandlerInterface
{
    public function handle(RequestInterface $request): ResponseInterface
    {
        if($request->getMethod() === 'GET') {
            return new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode(['client_ip' => $request->getAttribute('client-ip')], JSON_THROW_ON_ERROR)
            );
        }
        [$id, $fecha] = array_values(
            json_decode($request->getBody()->getContents(), true, 2, JSON_THROW_ON_ERROR)
        );

        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(['paload' => (new FilePostRepository())->findAll()], JSON_THROW_ON_ERROR)
        );
    }
}
