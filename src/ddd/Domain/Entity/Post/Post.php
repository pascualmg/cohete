<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Post;

use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\ArticleBody;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\Author;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\DatePublished;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\HeadLine;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\Slug;

class Post implements \JsonSerializable
{
    //properties from schema.org
    public Slug $slug;
    public ?string $authorType = null;

    public function __construct(
        public PostId $id,
        public readonly HeadLine $headline,
        public readonly ArticleBody $articleBody,
        public readonly Author $author,
        public readonly DatePublished $datePublished,
        public readonly ?string $orgSource = null,
    ) {
        $this->slug = Slug::from((string)$this->headline);
    }

    public static function fromPrimitives(
        string $id,
        string $headline,
        string $articleBody,
        string $author,
        string $datePublished,
        ?string $orgSource = null,
        ?string $authorType = null,
    ): Post {
        $post = new Post(
            PostId::from($id),
            HeadLine::from($headline),
            ArticleBody::from($articleBody),
            Author::from($author),
            DatePublished::from($datePublished),
            $orgSource,
        );
        $post->authorType = $authorType;
        return $post;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => (string)$this->id,
            'headline' => (string)$this->headline,
            'slug' => (string)$this->slug,
            'articleBody' => (string)$this->articleBody,
            'author' => (string)$this->author,
            'datePublished' => (string)$this->datePublished,
            'orgSource' => $this->orgSource,
        ];
    }
}
