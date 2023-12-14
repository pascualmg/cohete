<?php

namespace Pascualmg\Rx\ddd\Infrastructure\Repository\Post;

use DateTimeImmutable;
use Pascualmg\Rx\ddd\Domain\Entity\PostRepository;

class FilePostRepository implements PostRepository
{
    public function findAll(): array
    {
        $rawposts = json_decode(file_get_contents('posts.json'), true);
        return array_map(
            function (array $rawPost) {
                return new Post(
                    $rawPost['id'],
                    $rawPost['body'],
                    new DateTimeImmutable($rawPost['creation_date']),
                );
            },
            $rawposts
        );
    }
}
