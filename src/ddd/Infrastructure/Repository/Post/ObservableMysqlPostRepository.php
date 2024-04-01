<?php

namespace pascualmg\cohete\ddd\Infrastructure\Repository\Post;

use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use React\Mysql\MysqlClient;
use React\Mysql\MysqlResult;
use React\Promise\PromiseInterface;
use Rx\Observable;

class ObservableMysqlPostRepository implements PostRepository
{
    private MysqlClient $mysqlClient;

    public function __construct(MysqlClient $mysqlClient)
    {
        $this->mysqlClient = $mysqlClient;
    }

    public function findAll(): PromiseInterface //of an array of Posts
    {
        $promiseOfQuery = $this->mysqlClient->query('SELECT * FROM post ORDER BY post.datePublished DESC');

        return Observable::fromPromise($promiseOfQuery)
            ->map(function (MysqlResult $mysqlResult) {
                return array_map([self::class, 'hydrate'], $mysqlResult->resultRows);
            })
            ->toPromise();
    }

    public function findById(int $postId): PromiseInterface //of Post or Null
    {
        $promiseOfQuery = $this->mysqlClient->query(
            "SELECT * FROM post where post.id = ?",
            [$postId]
        );

        return Observable::fromPromise($promiseOfQuery)
            ->map(fn (MysqlResult $mysqlResult) => self::hydrateOrNull($mysqlResult->resultRows[0] ?? null))
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
        return Post::fromPrimitives(
            $rawPost['id'],
            $rawPost['headline'],
            $rawPost['articleBody'],
            $rawPost['author'],
            (new \DateTimeImmutable($rawPost['datePublished']))->format(\DateTimeInterface::ATOM),
        );
    }

    public function save(Post $postToCreate): PromiseInterface
    {
        $insertPostQuery = "
INSERT INTO post 
(id, headline, articleBody, author, datePublished) VALUES 
(?,?,?,?,?)
";
        return $this->mysqlClient->query($insertPostQuery, [
            (string)$postToCreate->id,
            (string)$postToCreate->headline,
            (string)$postToCreate->articleBody,
            (string)$postToCreate->author,
            $postToCreate->datePublished->getDatetimeImmutable()->format('Y-m-d H:m:s')
        ])->then(
            function (MysqlResult $mysqlResult): bool {
                $affectedRows = $mysqlResult->affectedRows;
                return $affectedRows > 0;
            },
            function (\Exception $e) {
                //Si en vez the throw hacemos return, en vez de
                //irse al onRejected , se va al onFullFilled
                //ya  que de hacerlo se entiende que la estamos solucionando
                throw $e;
            }
        );

    }
}
