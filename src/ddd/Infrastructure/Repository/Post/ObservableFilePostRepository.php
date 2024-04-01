<?php

namespace pascualmg\cohete\ddd\Infrastructure\Repository\Post;

use DateTimeImmutable;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use React\Promise\PromiseInterface;
use React\Promise\Stream;
use React\Stream\ReadableResourceStream;
use Rx\Observable;

class ObservableFilePostRepository implements PostRepository
{
    public function findAll(): PromiseInterface //of Post[]
    {
        return self::observableFromFile()
            ->map(fn ($file) => json_decode($file, true, 512, JSON_THROW_ON_ERROR))
            ->flatMap(fn ($posts) => Observable::fromArray($posts))
            ->map(fn ($post) => self::hydrate($post))
            ->toArray()
            ->toPromise();
    }

    public function findById(int $postId): PromiseInterface //of ?Post
    {
        return self::observableFromFile()
            ->map(fn ($file) => json_decode($file, true, 512, JSON_THROW_ON_ERROR))
            ->flatMap(fn ($posts) => Observable::fromArray($posts))
            ->filter(fn ($post) => $post['id'] === $postId)
            ->map(fn ($post) => self::hydrate($post))
            ->toPromise();
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


    public static function observableFromFile(): Observable
    {
        $postFilePath = dirname(__DIR__) . '/Post/posts.json';

        $postFileStream = new ReadableResourceStream(
            fopen($postFilePath, 'rb')
        );
        $buffer = Stream\buffer($postFileStream);

        return Observable::fromPromise($buffer);
    }
}
