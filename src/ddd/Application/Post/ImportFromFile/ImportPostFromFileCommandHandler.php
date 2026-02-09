<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Application\Post\ImportFromFile;

use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use pascualmg\cohete\ddd\Domain\Service\PostCreator;
use pascualmg\cohete\ddd\Infrastructure\Parser\FileParser;

readonly class ImportPostFromFileCommandHandler
{
    private const string DEFAULT_AUTHOR = 'Pascual Muñoz Galián';

    public function __construct(
        private FileParser $parser,
        private PostCreator $postCreator,
    ) {
    }

    public function __invoke(ImportPostFromFileCommand $command): void
    {
        $parsed = $this->parser->parse($command->filePath);

        $postId = $command->postId ?? (string) PostId::v4();

        $datePublished = $parsed->datePublished?->format(\DateTimeInterface::ATOM)
            ?? (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);

        ($this->postCreator)(
            $postId,
            $parsed->headline,
            $parsed->articleBody,
            $parsed->author ?? self::DEFAULT_AUTHOR,
            $datePublished
        );
    }
}
