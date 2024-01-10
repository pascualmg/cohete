<?php

namespace pascualmg\reactor\ddd\Infrastructure\HelperFunctions;

use Throwable;

class ExceptionTo
{
    public static function array(Throwable $exception): array
    {
        return [
            'name' => $exception::class,
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => array_map('json_decode',
                array_map('json_encode', $exception->getTrace())
            )
        ];
    }

}