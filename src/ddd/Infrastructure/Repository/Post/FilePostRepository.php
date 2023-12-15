<?php

namespace Pascualmg\Rx\ddd\Infrastructure\Repository\Post;

use DateTimeImmutable;
use Exception;
use JsonException;
use Pascualmg\Rx\ddd\Domain\Entity\Post;
use Pascualmg\Rx\ddd\Domain\Entity\PostRepository;
use React\Promise\Deferred;
use React\Promise\Promise;
use React\Promise\PromiseInterface;

class FilePostRepository implements PostRepository
{
    /**
     * @throws JsonException when gets the file and decode
     * @throws Exception in datetime creation
     */
    public function findAll(): PromiseInterface
    {
        $deferred = new Deferred();

        $rawPosts = json_decode(
            file_get_contents('/home/passh/src/php/rxphp/src/ddd/Infrastructure/Repository/Post/posts.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        $deferred->resolve(
            array_map(
                static fn(array $rawPost) => new Post(
                    $rawPost['id'],
                    $rawPost['body'],
                    new DateTimeImmutable($rawPost['creation_date']),
                ),
                $rawPosts
            )
        );
        return $deferred->promise();
    }
}
