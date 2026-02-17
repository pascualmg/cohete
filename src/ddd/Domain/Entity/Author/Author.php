<?php

namespace pascualmg\cohete\ddd\Domain\Entity\Author;

use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorId;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorKeyHash;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorName;

class Author implements \JsonSerializable
{
    public function __construct(
        public readonly AuthorId $id,
        public readonly AuthorName $name,
        public readonly AuthorKeyHash $keyHash,
    ) {
    }

    public static function fromPrimitives(string $id, string $name, string $keyHash): self
    {
        return new self(
            AuthorId::from($id),
            AuthorName::from($name),
            AuthorKeyHash::from($keyHash),
        );
    }

    public function verifyKey(string $plainKey): bool
    {
        return password_verify($plainKey, $this->keyHash->value);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => (string)$this->id,
            'name' => (string)$this->name,
        ];
    }
}
