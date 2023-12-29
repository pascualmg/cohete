<?php

namespace pascualmg\reactor\ddd\Infrastructure\Repository\Post;

use pascualmg\reactor\ddd\Domain\Entity\Post;
use pascualmg\reactor\ddd\Domain\Entity\PostRepository;
use React\Mysql\MysqlClient;
use React\Mysql\MysqlResult;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

class MysqlPostRepository implements PostRepository
{
    private MysqlClient $mysqlClient;

    public function __construct()
    {
        $this->mysqlClient = new MysqlClient('root:rootpassword@localhost:3306/test');
    }

    public function findAll(): PromiseInterface
    {
        $deferred = new Deferred();

        $this->mysqlClient->query('SELECT * FROM post')
            ->then(
                function (MysqlResult $command) use ($deferred) {
                    $deferred->resolve(
                        array_map(
                            [self::class, 'hydrate'],
                            $command->resultRows
                        )
                    );
                },
                function (\Throwable $error) {
                    echo 'Error: ' . $error->getMessage() . PHP_EOL;
                }
            );
        return $deferred->promise();
    }

    public function findById(int $postId): PromiseInterface //of Post or Null
    {
        $deferred = new Deferred();

        $this->mysqlClient->query(
            "SELECT * FROM post where post.id = ?",
            [$postId]
        )->then(function (MysqlResult $mysqlResult) use ($deferred) {
            $deferred->resolve(
                self::hydrateOrNull(
                    $mysqlResult->resultRows[0] ?? null
                )
            );
        });

        return $deferred->promise();
    }

    private static function hydrateOrNull(?array $maybeResultRow): ?Post
    {
        if (null === $maybeResultRow) {
            return null;
        }
        return self::hydrate($maybeResultRow);
    }

    private static function hydrate(array $rawPost): Post
    {
        return new Post(
            $rawPost['id'],
            $rawPost['headline'] ?? "",
            $rawPost['articleBody'] ?? "",
            $rawPost['image'] ?? "",
            $rawPost['author'] ?? "",
            new \DateTimeImmutable($rawPost['created_at'])
        );
    }
}
