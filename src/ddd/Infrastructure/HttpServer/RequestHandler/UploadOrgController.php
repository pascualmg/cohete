<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Application\Post\CreatePostCommand;
use pascualmg\cohete\ddd\Application\Post\CreatePostCommandHandler;
use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Service\AuthorAuthenticator;
use pascualmg\cohete\ddd\Domain\ValueObject\UuidValueObject;
use pascualmg\cohete\ddd\Infrastructure\HttpServer\JsonResponse;
use pascualmg\cohete\ddd\Infrastructure\Service\OrgToHtmlConverter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class UploadOrgController implements HttpRequestHandler
{
    public function __construct(
        private readonly CreatePostCommandHandler $createPostCommandHandler,
        private readonly OrgToHtmlConverter $orgToHtmlConverter,
        private readonly AuthorAuthenticator $authorAuthenticator,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            return JsonResponse::create(401, ['error' => 'Missing Authorization: Bearer <key>']);
        }

        $token = substr($authHeader, 7);

        return $this->authorAuthenticator->authenticate($token)->then(
            function (?Author $author) use ($request): ResponseInterface {
                if ($author === null) {
                    return JsonResponse::create(403, ['error' => 'Invalid bearer key']);
                }

                $orgContent = (string) $request->getBody();

                if (empty(trim($orgContent))) {
                    return JsonResponse::create(400, ['error' => 'Empty org content']);
                }

                try {
                    $metadata = $this->orgToHtmlConverter->extractMetadata($orgContent);
                    $html = $this->orgToHtmlConverter->convert($orgContent);
                } catch (\Throwable $e) {
                    return JsonResponse::withError($e);
                }

                $datePublished = $this->normalizeDate($metadata['date']);
                $postId = UuidValueObject::v4();

                // Authenticated author overrides #+AUTHOR: from org
                $authorName = (string)$author->name;

                ($this->createPostCommandHandler)(
                    new CreatePostCommand(
                        (string)$postId,
                        $metadata['title'],
                        $html,
                        $authorName,
                        $datePublished,
                        $orgContent,
                    )
                );

                return JsonResponse::create(202, [
                    'id' => (string)$postId,
                    'headline' => $metadata['title'],
                    'author' => $authorName,
                    'datePublished' => $datePublished,
                ]);
            }
        );
    }

    private function normalizeDate(string $date): string
    {
        try {
            $dt = new \DateTimeImmutable($date);
            return $dt->format(\DateTimeInterface::ATOM);
        } catch (\Exception) {
            return (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);
        }
    }
}
