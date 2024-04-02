<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Post;

use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\ArticleBody;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\Author;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\DatePublished;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\HeadLine;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;

class PostMother extends Post
{

    /**
     * @return Post
     */
    public static function randomValid(): Post
    {
        return new Post(
            PostId::v4(),
            HeadLine::from('headline_' . self::randomInt()),
            ArticleBody::from('article_body_' . self::randomInt()),
            Author::from('author_'. self::randomInt()),
            DatePublished::now()
        );
    }


    private static function randomInt(): int
    {
            return random_int(0, PHP_INT_MAX);
    }
}