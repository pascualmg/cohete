<?php

namespace Passh\Rx\httpserver;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;

class TestController
{
    public function __invoke(RequestInterface $request): ResponseInterface
    {
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
