<?php

namespace pascualmg\reactor\ddd\Infrastructure\HelperFunctions;

use Throwable;

class ExceptionTo
{
    public static function array(Throwable $throwable): array
    {
        return [
            'name' => $throwable::class,
            'code' => $throwable->getCode(),
            'message' => $throwable->getMessage(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'trace' => array_map(
                'json_decode',
                array_map('json_encode', $throwable->getTrace())
            )
        ];
    }

    public static function arrayWithShortTrace(Throwable $throwable): array
    {
        return [
            'name' => $throwable::class,
            'code' => $throwable->getCode(),
            'message' => $throwable->getMessage(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'shortTrace' => $throwable->getTrace()[0]
        ];

    }

}
