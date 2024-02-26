<?php

namespace pascualmg\reactor\ddd\Domain\Entity\Post;

use DateTimeInterface;

class Post implements \JsonSerializable
{
    //properties from schema.org
    public function __construct(
        public PostId $id,
        public readonly HeadLine $headline,
        public readonly ArticleBody $articleBody,
        public readonly Author $author,
        public readonly DatePublished $datePublished
    ) {
    }

    public static function fromPrimitives(
        string $id,
        string $headline,
        string $articleBody,
        string $author,
        string $datePublished,
    ): Post {
        return new Post(
            PostId::from($id),
            HeadLine::from($headline),
            ArticleBody::from($articleBody),
            Author::from($author),
           DatePublished::from($datePublished)
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => (string)$this->id,
            'headline' => (string)$this->headline,
            'articleBody' => (string)$this->articleBody,
            'author' => (string)$this->author,
            'datePublished' => (string)$this->datePublished
        ];
    }
}
