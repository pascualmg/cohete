<?php

namespace pascualmg\cohete\ddd\Infrastructure\Repository\Post;

use Exception;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\DatePublished;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;

class FilePostRepository implements PostRepository
{
    public function findAll(): PromiseInterface
    {
        $deferred = new Deferred();

        try {
            $rawPosts = json_decode(
                file_get_contents(dirname(__DIR__, 1).'/Post/posts.json'),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (Exception $exception) {
            $deferred->reject($exception);
            return $deferred->promise();
        }

        $deferred->resolve(
            array_map(
                [self::class,'hydrate'],
                $rawPosts
            )
        );

        return $deferred->promise();
    }

    public function findById(PostId $postId): PromiseInterface
    {
        $deferred = new Deferred();
        $contents = '';

        $file = new ReadableResourceStream(
            fopen(
                $this->filename(),
                'rb'
            )
        );


        $file->on('data', function ($data) use (&$contents) {
            $contents .= $data;
        });

        $file->on('end', function () use ($postId, &$contents, $deferred) {
            $posts = json_decode($contents, true);
            foreach ($posts as $post) {
                if ($post['id'] === (string)$postId) {
                    $deferred->resolve(self::hydrate($post));
                    return;
                }
            }

            $deferred->resolve(null);
        });

        $file->on('error', function ($error) use ($deferred) {
            $deferred->reject(new Exception("Error reading file: {$error}"));
        });

        return $deferred->promise();
    }

    private static function hydrate(array $post): Post
    {
        return new Post(
            $post['id'],
            $post['headline'],
            $post['articleBody'],
            $post['author'],
            DatePublished::from($post['datePublished'])
        );
    }

    public function save(Post $postToCreate): PromiseInterface
    {
        $deferred = new Deferred();

        $ostream = new WritableResourceStream(
            fopen(
                $this->filename(),
                'ab+'
            )
        );

        //todo: implement
        $deferred->resolve(true);
        return $deferred->promise();
    }

    /**
     * @return string
     */
    public function filename(): string
    {
        return dirname(__DIR__) . '/Post/posts.json';
    }
}
