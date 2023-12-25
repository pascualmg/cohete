<?php

namespace Pascualmg\Rx\ddd\Infrastructure\HttpServer;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;

class JsonResponse implements StatusCodeInterface
{
    private function __construct()
    {
        //factory
    }

    public static function create(int $code = self::STATUS_OK, $payload = null): ResponseInterface
    {
        return new Response(
            $code,
            ['Content-type' => 'application/json'],
            json_encode($payload, JSON_THROW_ON_ERROR)
        );
    }

    public static function withPayload($payload): ResponseInterface
    {
        return self::create(200, $payload);
    }

    public static function OK(): ResponseInterface
    {
        return self::create(200, 'OK');
    }

    public static function withError(\Throwable $e): ResponseInterface
    {
        return self::create($e->getCode(), $e->getMessage());
    }


}
