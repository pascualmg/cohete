<?php

namespace Pascualmg\Rx\ddd\Infrastructure\Repository\Post;

use Pascualmg\Rx\ddd\Domain\Entity\Post;
use Pascualmg\Rx\ddd\Domain\Entity\PostRepository;
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

        $this->mysqlClient->query('SELECT * FROM post')->then(
            function (MysqlResult $command) use ($deferred) {
                echo count($command->resultRows) . ' row(s) in set' . PHP_EOL;
                $deferred->resolve(
                    array_map(
                        static fn ($resultRow) => new Post(
                            $resultRow['id'],
                            $resultRow['title'] . $resultRow['content'],
                            new \DateTimeImmutable($resultRow['created_at'])
                        ),
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
            $rawPostData = $mysqlResult->resultRows[0] ?? null;

            $deferred->resolve(
                $rawPostData === null ? null : new Post(
                    $rawPostData['id'],
                    $rawPostData['title'] . $rawPostData['content'],
                    new \DateTimeImmutable($rawPostData['created_at'])
                )
            );
        });

        return $deferred->promise();
    }
}
