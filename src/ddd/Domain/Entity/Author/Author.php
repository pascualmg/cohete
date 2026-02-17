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
        public readonly ?string $type = null,
    ) {
    }

    public static function fromPrimitives(string $id, string $name, string $keyHash, ?string $type = null): self
    {
        return new self(
            AuthorId::from($id),
            AuthorName::from($name),
            AuthorKeyHash::from($keyHash),
            $type,
        );
    }

    /** @return array{0: self, 1: string} [Author, plainKey] */
    public static function register(string $name, ?string $chosenKey = null): array
    {
        $plainKey = $chosenKey ?? bin2hex(random_bytes(32));
        $hash = password_hash($plainKey, PASSWORD_BCRYPT);

        return [
            new self(
                AuthorId::v4(),
                AuthorName::from($name),
                AuthorKeyHash::from($hash),
            ),
            $plainKey,
        ];
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
            'type' => $this->type,
        ];
    }
}
