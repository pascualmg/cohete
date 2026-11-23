<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use Cohete\HttpServer\HttpRequestHandler;
use Cohete\HttpServer\JsonResponse;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

/**
 * GET /selena — portfolio de Selena Pascual (diseñadora gráfica).
 *
 * Pagina aparte del blog: index.html carga los web components de
 * /html/selena/js/ y estos leen el contenido de /html/selena/portfolio.json.
 */
class SelenaController implements HttpRequestHandler, StatusCodeInterface
{
    public function __invoke(
        ServerRequestInterface $request,
        ?array $routeParams
    ): ResponseInterface|PromiseInterface {
        $file = dirname(__DIR__, 2) . '/webserver/html/selena/index.html';

        if (!file_exists($file)) {
            return JsonResponse::notFound($file);
        }

        return new Response(
            self::STATUS_OK,
            ['Content-Type' => 'text/html; charset=utf-8'],
            (string) file_get_contents($file)
        );
    }
}
