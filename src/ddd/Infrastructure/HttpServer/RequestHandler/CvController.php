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
 * GET /cv — sirve la pagina del curriculum imprimible (HTML plano, A4).
 *
 * Pagina aparte del portfolio: sin Web Components ni Shadow DOM (las reglas
 * @media print no atraviesan bien el shadow DOM). Lee sus datos de
 * /html/cv-data/*.json en el cliente y el PDF lo genera el navegador con
 * window.print(). No hay generacion de PDF en el servidor.
 */
class CvController implements HttpRequestHandler, StatusCodeInterface
{
    public function __invoke(
        ServerRequestInterface $request,
        ?array $routeParams
    ): ResponseInterface|PromiseInterface {
        $file = dirname(__DIR__, 2) . '/webserver/html/cv.html';

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
