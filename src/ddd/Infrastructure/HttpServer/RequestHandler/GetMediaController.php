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
        // Los navegadores piden los videos por trozos (Range). Se reenvia tal cual a
        // Garage (S3 entiende Range) y se devuelve 206 con su Content-Range.
        $range = $request->getHeaderLine('Range');
        $upstreamHeaders = $range !== '' ? ['Range' => $range] : [];

        return Observable::fromPromise($this->mediaRepository->find($id))
            ->flatMap(function (?Media $media) use ($id, $upstreamHeaders) {
                if ($media === null) {
                    return Observable::of(JsonResponse::notFound('Media'));
                }
                return Observable::fromPromise(
                    $this->mediaRepository->presignedUrl($id, self::PRESIGN_TTL)
                )->flatMap(fn (string $url) => Observable::fromPromise(
                    // Streaming: el body no se bufferiza (Browser corta a 16MB y los
                    // videos pesan mas). Se pasa el stream de Garage directo al cliente.
                    $this->http->requestStreaming('GET', $url, $upstreamHeaders)
                )->map(function (ResponseInterface $upstream) use ($media) {
                    $partial = $upstream->getStatusCode() === 206;
                    $headers = [
                        'Content-Type' => (string)$media->contentType,
                        'Content-Length' => $upstream->getHeaderLine('Content-Length') ?: (string)$media->byteSize,
                        'Accept-Ranges' => 'bytes',
                        'Cache-Control' => 'public, max-age=86400',
                    ];
                    if ($partial) {
                        $headers['Content-Range'] = $upstream->getHeaderLine('Content-Range');
                    }
                    return new Response($partial ? 206 : 200, $headers, $upstream->getBody());
                }));
            })
            ->toPromise();
    }
}
