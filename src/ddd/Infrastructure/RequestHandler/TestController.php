<?php

namespace Pascualmg\Rx\ddd\Infrastructure\RequestHandler;

use Pascualmg\Rx\ddd\Infrastructure\Repository\Post\FilePostRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use React\Http\Message\Response;

class TestController implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
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
