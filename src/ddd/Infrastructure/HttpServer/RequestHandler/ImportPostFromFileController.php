<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Application\Post\ImportFromFile\ImportPostFromFileCommand;
use pascualmg\cohete\ddd\Application\Post\ImportFromFile\ImportPostFromFileCommandHandler;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class ImportPostFromFileController
{
    public function __construct(
        private ImportPostFromFileCommandHandler $handler
    ) {
    }

    public function __invoke(ServerRequestInterface $request, array $routeParams): ResponseInterface
    {
        try {
            // Use (string) cast instead of getContents() because the body may have been 
            // already read by middlewares (like RequestDumper) and streams can only be read once.
            // The RequestBodyBufferMiddleware ensures the body is buffered so casting works.
            $bodyContent = (string) $request->getBody();
            
            $body = json_decode(
                $bodyContent,
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            if (!isset($body['filePath'])) {
                return JsonResponse::withError(
                    new \InvalidArgumentException('Missing required field: filePath'),
                    400
                );
            }

            $command = new ImportPostFromFileCommand(
                filePath: $body['filePath'],
                postId: $body['id'] ?? null,
            );

            ($this->handler)($command);

            return JsonResponse::accepted([
                'message' => 'Post import initiated',
                'filePath' => $body['filePath'],
            ]);
        } catch (\JsonException $e) {
            return JsonResponse::withError(
                new \InvalidArgumentException('Invalid JSON payload'),
                400
            );
        } catch (\InvalidArgumentException $e) {
            return JsonResponse::withError($e, 400);
        } catch (\Throwable $e) {
            return JsonResponse::withError($e, 500);
        }
    }
}
