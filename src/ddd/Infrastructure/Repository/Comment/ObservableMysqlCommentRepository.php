<?php

namespace pascualmg\cohete\ddd\Infrastructure\Repository\Comment;

use pascualmg\cohete\ddd\Domain\Entity\Comment\Comment;
use pascualmg\cohete\ddd\Domain\Entity\CommentRepository;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use React\Mysql\MysqlClient;
use React\Mysql\MysqlResult;
use React\Promise\PromiseInterface;
use Rx\Observable;

class ObservableMysqlCommentRepository implements CommentRepository
{
    public function __construct(
        private readonly MysqlClient $mysqlClient,
    ) {
    }

    public function findByPostId(PostId $postId): PromiseInterface
    {
        return Observable::fromPromise(
            $this->mysqlClient->query(
                'SELECT * FROM comment WHERE post_id = ? ORDER BY created_at ASC',
                [$postId->value]
            )
        )->map(
            fn (MysqlResult $result) => array_map([self::class, 'hydrate'], $result->resultRows)
        )->toPromise();
    }

    public function save(Comment $comment): PromiseInterface
    {
        return $this->mysqlClient->query(
            'INSERT INTO comment (id, post_id, author_name, body, created_at) VALUES (?, ?, ?, ?, ?)',
            [
                (string)$comment->id,
                (string)$comment->postId,
                (string)$comment->authorName,
                (string)$comment->body,
                $comment->createdAt->format('Y-m-d H:i:s'),
            ]
        )->then(
            fn (MysqlResult $result): bool => $result->affectedRows > 0,
            function (\Exception $e) { throw $e; }
        );
    }

    public function countGroupedByPost(): PromiseInterface
    {
        return Observable::fromPromise(
            $this->mysqlClient->query(
                'SELECT post_id, COUNT(id) as cnt FROM comment GROUP BY post_id'
            )
        )->map(
            fn (MysqlResult $result) => array_column($result->resultRows, 'cnt', 'post_id')
        )->toPromise();
    }

    private static function hydrate(array $row): Comment
    {
        return Comment::fromPrimitives(
            $row['id'],
            $row['post_id'],
            $row['author_name'],
            $row['body'],
            $row['created_at'],
        );
    }
}
