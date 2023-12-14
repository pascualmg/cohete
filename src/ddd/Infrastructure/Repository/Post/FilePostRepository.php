<?php

namespace Pascualmg\Rx\ddd\Infrastructure\Repository\Post;

use DateTimeImmutable;
use Exception;
use JsonException;
use Pascualmg\Rx\ddd\Domain\Entity\Post;
use Pascualmg\Rx\ddd\Domain\Entity\PostRepository;

class FilePostRepository implements PostRepository
{
    /**
     * @throws JsonException when gets the file and decode
     * @throws Exception in datetime creation
     */
    public function findAll(): array
    {
        $rawPosts = json_decode(
            file_get_contents(__DIR__ . 'ddd/Infrastructure/Repository/Post/posts.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        return array_map(
            static fn (array $rawPost) => new Post(
                $rawPost['id'],
                $rawPost['body'],
                new DateTimeImmutable($rawPost['creation_date']),
            ),
            $rawPosts
        );
    }
}
