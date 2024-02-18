<?php

namespace pascualmg\reactor\ddd\Domain\Entity;

use DateTimeInterface;
use pascualmg\reactor\ddd\Domain\ValueObject\Uuid;

class Post implements \JsonSerializable
{
    //properties from schema.org
    public function __construct(
        public Uuid $id,
        public readonly string $headline,
        public readonly string $articleBody,
        public readonly string $image,
        public readonly string $author,
        public readonly DateTimeInterface $datePublished
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => (string)$this->id,
            'headline' => $this->headline,
            'articleBody' => $this->articleBody,
            'image' => $this->image,
            'author' => $this->author,
            'datePublished' => $this->datePublished->format(DateTimeInterface::ATOM),
        ];
    }
}
