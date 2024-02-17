<?php

namespace pascualmg\reactor\ddd\Infrastructure\HttpServer;

use Fig\Http\Message\StatusCodeInterface;
use pascualmg\reactor\ddd\Infrastructure\HelperFunctions\ExceptionTo;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;
use Throwable;

/**
 * Cuidado con esta clase , que esta muy bien cuando es
 * para cosas sencillas , pero no hace uso de poder
 * pasarle en el body en vez de una String , un StreamReadableResourceInterface
 * que es donde se puede sacar partido a la capacidad de reactphp
 *
 * no se va a modificar para incluir funcionalidad al respecto , si
 * se necesita lo ideal  es usar la response de React
 */
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

    public static function withError(Throwable $e): ResponseInterface
    {
        return self::create($e->getCode(), ExceptionTo::array($e));
    }


    public static function notFound(?string $resource = null): ResponseInterface
    {
        return self::create(
            self::STATUS_NOT_FOUND,
            is_null($resource) ? "" : json_encode($resource, JSON_THROW_ON_ERROR)
        );
    }


}
