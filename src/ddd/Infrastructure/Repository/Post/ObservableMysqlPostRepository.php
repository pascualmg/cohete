<?php

namespace pascualmg\reactor\ddd\Infrastructure\Repository\Post;

use pascualmg\reactor\ddd\Domain\Entity\Post;
use pascualmg\reactor\ddd\Domain\Entity\PostRepository;
use React\Mysql\MysqlClient;
use React\Mysql\MysqlResult;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Rx\Observable;

class ObservableMysqlPostRepository implements PostRepository
{
    private MysqlClient $mysqlClient;

    public function __construct()
    {
        $this->mysqlClient = new MysqlClient('root:rootpassword@localhost:3306/test');
    }

    public function findAll(): PromiseInterface
    {
        $promiseOfQuery = $this->mysqlClient->query('SELECT * FROM post');

        return Observable::fromPromise($promiseOfQuery)
            ->flatMap(static fn(MysqlResult $mysqlResult): Observable => Observable::fromArray($mysqlResult->resultRows))
            ->map( static fn (array $postFromMysql): Post => self::hydrate($postFromMysql))
            ->toArray()
            ->toPromise();
    }

    public function findById(int $postId): PromiseInterface //of Post or Null
    {

        $promiseOfQuery =  $this->mysqlClient->query(
            "SELECT * FROM post where post.id = ?",
            [$postId]
        );

        return Observable::fromPromise($promiseOfQuery)
            ->map(fn (MysqlResult $mysqlResult) => self::hydrateOrNull($mysqlResult->resultRows[0] ??  null))
            ->toPromise();
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
            new \DateTimeImmutable($rawPost['datePublished'])
        );
    }
}
