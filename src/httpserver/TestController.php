<?php

namespace Passh\Rx\httpserver;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;

class TestController
{
    public function __invoke(RequestInterface $request) : ResponseInterface
    {
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(['paload' => 'guay'])
        );
    }

}