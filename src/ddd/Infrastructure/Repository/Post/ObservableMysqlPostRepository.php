<?php

namespace pascualmg\cohete\ddd\Infrastructure\Repository\Post;

use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorSlug;
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
        $promiseOfQuery = $this->mysqlClient->query('SELECT p.*, a.type as author_type FROM post p LEFT JOIN author a ON p.author_id = a.id ORDER BY p.datePublished DESC, p.id DESC');

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

    public function findByAuthorAndSlug(AuthorSlug $authorSlug, Slug $slug): PromiseInterface //of Post or Null
    {
        // El slug de post es unico en toda la tabla (idx_post_slug), asi que
        // basta con buscarlo por slug. El autor no se filtra en SQL (nada de
        // mangling ad-hoc de strings): se compara en PHP contra el
        // AuthorSlug canonico, la misma unica fuente de verdad que usan el
        // resto de sitios que construyen /blog/{author}/{slug}.
        $promiseOfQuery = $this->mysqlClient->query(
            'SELECT p.*, a.type as author_type FROM post p LEFT JOIN author a ON p.author_id = a.id WHERE p.slug = ?',
            [(string)$slug]
        );

        return Observable::fromPromise($promiseOfQuery)
            ->map(fn (MysqlResult $mysqlResult) => self::hydrateOrNull($mysqlResult->resultRows[0] ?? null))
            ->map(function (?Post $post) use ($authorSlug): ?Post {
                if ($post === null || !AuthorSlug::from((string)$post->author)->equals($authorSlug)) {
                    return null;
                }
                return $post;
            })
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
            $rawPost['slug'] ?? null,
            $rawPost['author_type'] ?? null,
        );
    }

    public function save(Post $postToCreate): PromiseInterface
    {
        // author_id se resuelve por el nombre COMPLETO del autor, no por su
        // primera palabra: con SUBSTRING_INDEX(?, ' ', 1), "Ambrosio del
        // Santuario" quedaba enlazado al autor homonimo "Ambrosio" y sus
        // URLs /blog/{author}/{slug} daban 404 (el join devolvia el nombre
        // corto, que no casaba con el slug real del autor).
        $insertPostQuery = "
INSERT INTO post
(id, headline, slug, articleBody, author, datePublished, orgSource, author_id)
VALUES (?,?,?,?,?,?,?, (SELECT id FROM author WHERE LOWER(name) = LOWER(?) LIMIT 1))
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
UPDATE post SET headline=?, slug=?, articleBody=?, author=?, datePublished=?, orgSource=?, author_id=(SELECT id FROM author WHERE LOWER(name) = LOWER(?) LIMIT 1) WHERE id=?
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
