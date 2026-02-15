<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use pascualmg\cohete\ddd\Application\Post\CreatePostCommand;
use pascualmg\cohete\ddd\Application\Post\CreatePostCommandHandler;
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
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ?array $routeParams): ResponseInterface|PromiseInterface
    {
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

        ($this->createPostCommandHandler)(
            new CreatePostCommand(
                (string)$postId,
                $metadata['title'],
                $html,
                $metadata['author'],
                $datePublished,
                $orgContent,
            )
        );

        return JsonResponse::create(202, [
            'id' => (string)$postId,
            'headline' => $metadata['title'],
            'author' => $metadata['author'],
            'datePublished' => $datePublished,
        ]);
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
