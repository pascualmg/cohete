<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResponseDumper implements MiddlewareInterface
{


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        echo "\n\e[0;33mResponse\e[0m \n";
        echo "\e[0;35mStatus:\e[0m \e[0;32m{$response->getStatusCode()} ";
        echo "\e[0;35mReason:\e[0m \e[0;32m{$response->getReasonPhrase()} \n";
        echo "\e[0;35mHeaders:\e[0m \n";
        foreach ($response->getHeaders() as $name => $values) {
            echo "\e[0;35m$name:\e[0m \e[0;36m" . implode(", ", $values) . " ";
        }
        $body = $response->getBody();
        if ($body->getSize() > 0) {
            echo "\n";
            echo "\e[0;35mBody:\e[0m ";
            echo "\e[0;36m{$body}\e[0m ";
        }
        //final line to delimite the request and response dumps
        echo "\n\e[0;33m================================\e[0m \n";
        return $response;
    }
}