<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use Cohete\HttpServer\HttpRequestHandler;
use Cohete\HttpServer\JsonResponse;
use pascualmg\cohete\ddd\Domain\Entity\Media\Media;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaId;
use pascualmg\cohete\ddd\Domain\Entity\Media\MediaRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Browser;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use Rx\Observable;

/**
 * GET /media/{id}
 *
 * Proxy stream del backend Garage. cohete-blog hace fetch interno via mesh
 * tailscale al presigned URL y devuelve el body al cliente. Asi el navegador
 * solo ve la URL publica /media/{id}, sin redirects a IPs internas.
 */
class GetMediaController implements HttpRequestHandler
{
    private const PRESIGN_TTL = 600;

    public function __construct(
        private readonly MediaRepository $mediaRepository,
        private readonly Browser $http,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $id = MediaId::from($routeParams['id'] ?? '');

        return Observable::fromPromise($this->mediaRepository->find($id))
            ->flatMap(function (?Media $media) use ($id) {
                if ($media === null) {
                    return Observable::of(JsonResponse::notFound('Media'));
                }
                return Observable::fromPromise(
                    $this->mediaRepository->presignedUrl($id, self::PRESIGN_TTL)
                )->flatMap(fn (string $url) => Observable::fromPromise(
                    $this->http->get($url)
                )->map(fn (ResponseInterface $upstream) => new Response(
                    200,
                    [
                        'Content-Type' => (string)$media->contentType,
                        'Content-Length' => (string)$media->byteSize,
                        'Cache-Control' => 'public, max-age=86400',
                    ],
                    (string)$upstream->getBody(),
                )));
            })
            ->toPromise();
    }
}
