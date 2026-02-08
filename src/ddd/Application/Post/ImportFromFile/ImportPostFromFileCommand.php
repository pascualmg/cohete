<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Application\Post\ImportFromFile;

readonly class ImportPostFromFileCommand
{
    public function __construct(
        public string $filePath,
        public ?string $postId = null,
    ) {
    }
}
