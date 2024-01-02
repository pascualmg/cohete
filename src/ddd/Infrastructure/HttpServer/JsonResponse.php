<?php

namespace pascualmg\reactor\ddd\Infrastructure\HttpServer;

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
        $toArray = static fn(\Throwable $exception): array => [
            'name' => $exception::class,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => array_map('json_encode', $exception->getTrace())
        ];

        return self::create($e->getCode(), $toArray($e));
    }


    public static function notFound(?string $resource = null): ResponseInterface
    {
        return self::create(
            self::STATUS_NOT_FOUND,
            is_null($resource) ? "" : json_encode($resource, JSON_THROW_ON_ERROR)
        );
    }


}
