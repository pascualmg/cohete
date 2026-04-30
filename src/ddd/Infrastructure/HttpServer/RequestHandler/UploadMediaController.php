<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use Cohete\HttpServer\HttpRequestHandler;
use Cohete\HttpServer\JsonResponse;
use pascualmg\cohete\ddd\Application\Media\UploadMediaCommand;
use pascualmg\cohete\ddd\Application\Media\UploadMediaCommandHandler;
use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Service\AuthorAuthenticator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

/**
 * POST /media
 *
 * Sube un fichero al object storage S3 (MinIO/Garage/AWS).
 *
 * Headers:
 *   Authorization: Bearer <author_token>
 *   Content-Type:  el del fichero (image/png, image/jpeg, application/pdf, etc.)
 *
 * Body: raw bytes del fichero.
 *
 * Response 201:
 *   { "id": "uuid", "key": "media/uuid", "byteSize": 12345, "contentType": "image/png" }
 */
class UploadMediaController implements HttpRequestHandler
{
    private const MAX_BYTES = 50 * 1024 * 1024;  // 50 MB

    public function __construct(
        private readonly UploadMediaCommandHandler $uploadMedia,
        private readonly AuthorAuthenticator $authorAuthenticator,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            return JsonResponse::create(401, ['error' => 'Missing Authorization: Bearer <author_token>']);
        }
        $token = substr($authHeader, 7);

        $contentType = $request->getHeaderLine('Content-Type') ?: 'application/octet-stream';
        // Content-Type may carry params like "image/png; charset=utf-8" — strip them.
        if (str_contains($contentType, ';')) {
            $contentType = trim(strstr($contentType, ';', true));
        }

        $body = (string) $request->getBody();
        $byteSize = strlen($body);

        if ($byteSize === 0) {
            return JsonResponse::create(400, ['error' => 'Empty body']);
        }
        if ($byteSize > self::MAX_BYTES) {
            return JsonResponse::create(413, ['error' => 'Payload too large (max 50MB)']);
        }

        return $this->authorAuthenticator->authenticate($token)
            ->then(function (?Author $author) use ($contentType, $body): ResponseInterface|PromiseInterface {
                if ($author === null) {
                    return JsonResponse::create(403, ['error' => 'Invalid bearer token']);
                }

                return ($this->uploadMedia)(new UploadMediaCommand(
                    contentType: $contentType,
                    body:        $body,
                    authorName:  (string)$author->name,
                ))->then(
                    fn (array $result): ResponseInterface => JsonResponse::create(201, $result),
                    function (\Throwable $e): ResponseInterface {
                        return JsonResponse::create(500, [
                            'error' => 'Upload failed',
                            'detail' => $e->getMessage(),
                        ]);
                    }
                );
            });
    }
}
