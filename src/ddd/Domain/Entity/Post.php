<?php

namespace pascualmg\reactor\ddd\Domain\Entity;

use DateTimeInterface;

class Post implements \JsonSerializable
{
    //properties from schema.org
    public function __construct(
        public int $id,
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
            'id' => $this->id,
            'headline' => $this->headline,
            'articleBody' => $this->articleBody,
            'image' => $this->image,
            'author' => $this->author,
            'datePublished' => $this->datePublished->format(DateTimeInterface::ATOM),
        ];
    }
}
