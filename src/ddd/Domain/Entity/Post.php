<?php

namespace Pascualmg\Rx\ddd\Domain\Entity;

use DateTimeInterface;

class Post
{
    public function __construct(
        public int $id,
        public string $body,
        public DateTimeInterface $creationDate
    ) {
    }

}
