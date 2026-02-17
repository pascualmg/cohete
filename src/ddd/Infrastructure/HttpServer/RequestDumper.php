<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestDumper implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //include timestamp
        echo "\n\e[0;33mRequest\e[0m \n";
        echo "\e[0;33mAt:\e[0m \e[0;32m" . date('Y-m-d H:i:s') . "\n";
        echo "\e[0;35mMethod:\e[0m \e[0;32m{$request->getMethod()} ";
        echo "\e[0;35mUri:\e[0m \e[0;32m{$request->getUri()} \n";
        echo "\e[0;35mHeaders:\e[0m \n";
        foreach ($request->getHeaders() as $name => $values) {
            echo "\e[0;35m$name:\e[0m \e[0;36m" . implode(", ", $values) . " ";
        }
        $bodySize = $request->getBody()->getSize();
        if($bodySize !== null && $bodySize > 0){
            echo "\n";
            echo "\e[0;35mBody:\e[0m ";
            echo "\e[0;36m({$bodySize} bytes)\e[0m ";
        }
        return $handler->handle($request);
    }
}