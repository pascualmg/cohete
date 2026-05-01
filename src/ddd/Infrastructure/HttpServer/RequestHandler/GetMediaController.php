<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use Cohete\HttpServer\HttpRequestHandler;
use Cohete\HttpServer\JsonResponse;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaId;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use Rx\Observable;

/**
 * GET /media/{id}
 *
 * Sirve un media subido a Garage. Comprueba que existe (HEAD) y redirige
 * con 302 a una URL firmada con TTL largo. El navegador la sigue
 * automaticamente cuando esta en <audio src> o <img src>.
 */
class GetMediaController implements HttpRequestHandler
{
    private const PRESIGN_TTL = 86400;

    public function __construct(
        private readonly MediaRepository $mediaRepository,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $id = MediaId::from($routeParams['id'] ?? '');

        return Observable::fromPromise($this->mediaRepository->find($id))
            ->flatMap(function ($media) use ($id) {
                if ($media === null) {
                    return Observable::of(JsonResponse::notFound('Media'));
                }
                return Observable::fromPromise(
                    $this->mediaRepository->presignedUrl($id, self::PRESIGN_TTL)
                )->map(fn (string $url) => new Response(
                    302,
                    [
                        'Location' => $url,
                        'Cache-Control' => 'public, max-age=3600',
                    ],
                    ''
                ));
            })
            ->toPromise();
    }
}
