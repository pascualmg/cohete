<?php

namespace Passh\Rx\httpserver;

use DateTimeImmutable;

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
