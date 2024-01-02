<?php

namespace pascualmg\reactor\ddd\Infrastructure\Repository\Post;

use DateTimeImmutable;
use Exception;
use JsonException;
use pascualmg\reactor\ddd\Domain\Entity\Post;
use pascualmg\reactor\ddd\Domain\Entity\PostRepository;
use React\Promise\Deferred;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use React\Stream\ReadableResourceStream;

class FilePostRepository implements PostRepository
{

    public function findAll(): PromiseInterface
    {
        $deferred = new Deferred();

        try{
            $rawPosts = json_decode(
                file_get_contents( dirname(__DIR__, 1).'/Post/posts.json'),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (Exception $exception){
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

    public function findById(int $postId): PromiseInterface
    {
        $deferred = new Deferred();
        $contents = '';

        $file = new ReadableResourceStream(
            fopen(
                dirname(__DIR__) . '/Post/posts.json',
                'rb'
            )
        );


        $file->on('data', function ($data) use (&$contents) {
            $contents .= $data;
        });

        $file->on('end', function () use ($postId, &$contents, $deferred) {
            $posts = json_decode($contents, true);
            foreach ($posts as $post) {
                if ($post['id'] === $postId) {
                    $deferred->resolve(self::hydrate($post));
                }
            }
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
            $post['image'],
            $post['author'],
            new DateTimeImmutable($post['datePublished'])
        );
    }
}
