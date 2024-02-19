<?php

namespace pascualmg\reactor\ddd\Infrastructure\HttpServer\RequestHandler;

use Fig\Http\Message\StatusCodeInterface;
use pascualmg\reactor\ddd\Domain\Entity\Post;
use pascualmg\reactor\ddd\Domain\Entity\PostRepository;
use pascualmg\reactor\ddd\Domain\ValueObject\Uuid;
use pascualmg\reactor\ddd\Infrastructure\HelperFunctions\ExceptionTo;
use pascualmg\reactor\ddd\Infrastructure\HttpServer\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

class CreatePostController implements HttpRequestHandler
{
    public function __construct(
        private readonly PostRepository $postRepository
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $body = $request->getBody()->getContents();
        try {
            $payload = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return JsonResponse::withError($e);
        }
        $postToCreate = new Post(
            id: Uuid::from($payload['id']),
            headline: $payload['headline'],
            articleBody: $payload['articleBody'],
            image: $payload['image'],
            author: $payload['author'],
            datePublished: new \DateTimeImmutable($payload['datePublished']),
        );

        return $this->postRepository->save($postToCreate)->then(
            static fn (Bool $affectedRows) => JsonResponse::create(StatusCodeInterface::STATUS_CREATED, [$affectedRows]),
            static fn (\Exception $error) => JsonResponse::withError($error)
        );
    }

}
