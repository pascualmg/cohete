<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Application\Media;

readonly class UploadMediaCommand
{
    public function __construct(
        public string $contentType,
        public string $body,         // raw bytes
        public string $authorName,   // ya verificado por el controller
    ) {
    }
}
