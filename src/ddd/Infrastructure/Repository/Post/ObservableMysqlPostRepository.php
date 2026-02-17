<?php

namespace pascualmg\cohete\ddd\Infrastructure\Repository\Post;

use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\PostId;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\Slug;
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

    public function findById(PostId $postId): PromiseInterface //of Post or Null
    {
        $promiseOfQuery = $this->mysqlClient->query(
            "SELECT * FROM post where post.id = ?",
            [$postId->value]
        );

        return Observable::fromPromise($promiseOfQuery)
            ->map(fn (MysqlResult $mysqlResult) => self::hydrateOrNull($mysqlResult->resultRows[0] ?? null))
            ->toPromise();
    }

    public function findBySlug(Slug $slug): PromiseInterface //of Post or Null
    {
        $promiseOfQuery = $this->mysqlClient->query(
            "SELECT * FROM post WHERE slug = ?",
            [(string)$slug]
        );

        return Observable::fromPromise($promiseOfQuery)
            ->map(fn (MysqlResult $mysqlResult) => self::hydrateOrNull($mysqlResult->resultRows[0] ?? null))
            ->toPromise();
    }

    public function findByAuthorAndSlug(string $authorName, Slug $slug): PromiseInterface //of Post or Null
    {
        $promiseOfQuery = $this->mysqlClient->query(
            "SELECT p.* FROM post p INNER JOIN author a ON p.author_id = a.id WHERE LOWER(a.name) = LOWER(?) AND p.slug = ?",
            [$authorName, (string)$slug]
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
            $rawPost['orgSource'] ?? null,
        );
    }

    public function save(Post $postToCreate): PromiseInterface
    {
        $insertPostQuery = "
INSERT INTO post
(id, headline, slug, articleBody, author, datePublished, orgSource, author_id)
VALUES (?,?,?,?,?,?,?, (SELECT id FROM author WHERE LOWER(name) = LOWER(SUBSTRING_INDEX(?, ' ', 1)) LIMIT 1))
";
        $authorStr = (string)$postToCreate->author;
        return $this->mysqlClient->query($insertPostQuery, [
            (string)$postToCreate->id,
            (string)$postToCreate->headline,
            (string)$postToCreate->slug,
            (string)$postToCreate->articleBody,
            $authorStr,
            $postToCreate->datePublished->getDatetimeImmutable()->format('Y-m-d H:i:s'),
            $postToCreate->orgSource,
            $authorStr,
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

    public function update(Post $post): PromiseInterface
    {
        $updateQuery = "
UPDATE post SET headline=?, slug=?, articleBody=?, author=?, datePublished=?, orgSource=?, author_id=(SELECT id FROM author WHERE LOWER(name) = LOWER(SUBSTRING_INDEX(?, ' ', 1)) LIMIT 1) WHERE id=?
";
        return $this->mysqlClient->query($updateQuery, [
            (string)$post->headline,
            (string)$post->slug,
            (string)$post->articleBody,
            (string)$post->author,
            $post->datePublished->getDatetimeImmutable()->format('Y-m-d H:i:s'),
            $post->orgSource,
            (string)$post->author,
            (string)$post->id,
        ])->then(
            fn (MysqlResult $mysqlResult): bool => $mysqlResult->affectedRows > 0,
            function (\Exception $e) {
                throw $e;
            }
        );
    }

    public function delete(PostId $postId): PromiseInterface
    {
        return $this->mysqlClient->query(
            "DELETE FROM post WHERE id=?",
            [$postId->value]
        )->then(
            fn (MysqlResult $mysqlResult): bool => $mysqlResult->affectedRows > 0,
            function (\Exception $e) {
                throw $e;
            }
        );
    }
}
