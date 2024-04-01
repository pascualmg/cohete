<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use Fig\Http\Message\StatusCodeInterface;
use finfo;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\JsonResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use React\Stream\ReadableResourceStream;
use React\Stream\ThroughStream;

class HtmlController implements HttpRequestHandler, StatusCodeInterface
{
    public function __construct(ContainerInterface $container)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ?array $routeParams
    ): ResponseInterface|PromiseInterface {
        //omg :) nice
        $foo = new ThroughStream(static fn ($id) => $id);

        $filename = dirname(__DIR__, 2) . '/webserver/html' . $routeParams['params'] ?? "";

        if(
            !file_exists($filename) ||
            is_dir($filename)
        ) {
            return JsonResponse::notFound($filename);
        }
        $html = new ReadableResourceStream(
            fopen(
                $filename,
                'rb'
            )
        );


        return new Response(
            self::STATUS_OK,
            ['Content-Type' => $this->getMimeType($filename) ],
            $html->pipe($foo)
        );
    }

    /**
     * Retrieves the MIME type of a given file.
     *
     * @param string $filename The name of the file to get the MIME type for.
     * @return string The MIME type of the file.
     */
    public function getMimeType($filename): string
    {
        $mimeTypes = [
            'js' => 'application/javascript',
            'css' => 'text/css',
            'html' => 'text/html',
            'json' => 'application/json',
        ];

        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

        return $mimeTypes[$fileExtension] ?? (new finfo(FILEINFO_MIME_TYPE))->file($filename);
    }
}
