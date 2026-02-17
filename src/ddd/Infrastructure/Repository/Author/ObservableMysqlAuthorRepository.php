<?php

namespace pascualmg\cohete\ddd\Infrastructure\Repository\Author;

use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorId;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorName;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use React\Mysql\MysqlClient;
use React\Mysql\MysqlResult;
use React\Promise\PromiseInterface;
use Rx\Observable;

class ObservableMysqlAuthorRepository implements AuthorRepository
{
    public function __construct(
        private readonly MysqlClient $mysqlClient,
    ) {
    }

    public function findAll(): PromiseInterface
    {
        return Observable::fromPromise(
            $this->mysqlClient->query('SELECT * FROM author ORDER BY name ASC')
        )->map(
            fn (MysqlResult $result) => array_map([self::class, 'hydrate'], $result->resultRows)
        )->toPromise();
    }

    public function findById(AuthorId $id): PromiseInterface
    {
        return Observable::fromPromise(
            $this->mysqlClient->query('SELECT * FROM author WHERE id = ?', [$id->value])
        )->map(
            fn (MysqlResult $result) => self::hydrateOrNull($result->resultRows[0] ?? null)
        )->toPromise();
    }

    public function findByName(AuthorName $name): PromiseInterface
    {
        return Observable::fromPromise(
            $this->mysqlClient->query('SELECT * FROM author WHERE name = ?', [$name->value])
        )->map(
            fn (MysqlResult $result) => self::hydrateOrNull($result->resultRows[0] ?? null)
        )->toPromise();
    }

    public function save(Author $author): PromiseInterface
    {
        return $this->mysqlClient->query(
            'INSERT INTO author (id, name, key_hash) VALUES (?, ?, ?)',
            [(string)$author->id, (string)$author->name, (string)$author->keyHash]
        )->then(
            fn (MysqlResult $result): bool => $result->affectedRows > 0,
            function (\Exception $e) { throw $e; }
        );
    }

    public function updateType(string $authorId, string $type): PromiseInterface
    {
        return $this->mysqlClient->query(
            'UPDATE author SET type = ? WHERE id = ?',
            [$type, $authorId]
        );
    }

    private static function hydrate(array $row): Author
    {
        return Author::fromPrimitives($row['id'], $row['name'], $row['key_hash'], $row['type'] ?? null);
    }

    private static function hydrateOrNull(?array $row): ?Author
    {
        return $row === null ? null : self::hydrate($row);
    }
}
