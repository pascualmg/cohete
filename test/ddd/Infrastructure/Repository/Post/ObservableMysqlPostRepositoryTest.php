<?php

namespace pascualmg\cohete\ddd\Infrastructure\Repository\Post;

use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorSlug;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\Slug;
use PHPUnit\Framework\TestCase;
use React\Mysql\MysqlClient;
use React\Mysql\MysqlResult;
use React\Promise\Deferred;

/**
 * @covers \pascualmg\cohete\ddd\Infrastructure\Repository\Post\ObservableMysqlPostRepository
 * @covers \pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorSlug
 */
class ObservableMysqlPostRepositoryTest extends TestCase
{
    /**
     * "Ambrosio del Santuario" es un autor de mas de una palabra: antes del
     * fix, el WHERE del SQL comparaba mangling ad-hoc y la busqueda por
     * /blog/ambrosio-del-santuario/{slug} nunca casaba. Ahora se busca solo
     * por slug (unico en toda la tabla) y se compara el AuthorSlug en PHP.
     */
    public function test_given_multi_word_author_slug_when_it_matches_the_post_author_then_returns_the_post(): void
    {
        $mysqlClient = $this->createMock(MysqlClient::class);
        $mysqlClient->method('query')->willReturn($this->resolvedResult([
            $this->rawPostRow('Ambrosio del Santuario', 'un-post-cualquiera'),
        ]));

        $repository = new ObservableMysqlPostRepository($mysqlClient);

        $post = $this->await($repository->findByAuthorAndSlug(
            AuthorSlug::from('ambrosio-del-santuario'),
            Slug::from('un-post-cualquiera'),
        ));

        $this->assertInstanceOf(Post::class, $post);
        $this->assertSame('Ambrosio del Santuario', (string)$post->author);
    }

    public function test_given_slug_exists_but_author_does_not_match_then_returns_null(): void
    {
        $mysqlClient = $this->createMock(MysqlClient::class);
        $mysqlClient->method('query')->willReturn($this->resolvedResult([
            $this->rawPostRow('Ambrosio del Santuario', 'un-post-cualquiera'),
        ]));

        $repository = new ObservableMysqlPostRepository($mysqlClient);

        $post = $this->await($repository->findByAuthorAndSlug(
            AuthorSlug::from('ambrosio'),
            Slug::from('un-post-cualquiera'),
        ));

        $this->assertNull($post);
    }

    public function test_given_no_post_with_that_slug_then_returns_null(): void
    {
        $mysqlClient = $this->createMock(MysqlClient::class);
        $mysqlClient->method('query')->willReturn($this->resolvedResult([]));

        $repository = new ObservableMysqlPostRepository($mysqlClient);

        $post = $this->await($repository->findByAuthorAndSlug(
            AuthorSlug::from('ambrosio-del-santuario'),
            Slug::from('un-post-cualquiera'),
        ));

        $this->assertNull($post);
    }

    private function rawPostRow(string $author, string $slug): array
    {
        return [
            'id' => '550e8400-e29b-41d4-a716-446655440099',
            'headline' => 'headline',
            'slug' => $slug,
            'articleBody' => 'body',
            'author' => $author,
            'datePublished' => '2026-01-01 00:00:00',
            'orgSource' => null,
            'author_type' => null,
        ];
    }

    private function resolvedResult(array $rows): \React\Promise\PromiseInterface
    {
        $result = new MysqlResult();
        $result->resultRows = $rows;

        $deferred = new Deferred();
        $deferred->resolve($result);

        return $deferred->promise();
    }

    private function await(\React\Promise\PromiseInterface $promise): mixed
    {
        $value = null;
        $promise->then(function ($resolved) use (&$value) {
            $value = $resolved;
        });

        return $value;
    }
}
