<?php

namespace Pascualmg\Rx\ddd\Domain\Entity;

use DateTimeInterface;
use Stringable;

class Post implements \JsonSerializable, Stringable
{
    public function __construct(
        public int $id,
        public string $body,
        public DateTimeInterface $creationDate
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'creationDate' => $this->creationDate->format(DateTimeInterface::ATOM),
        ];
    }

    public function __toString()
    {
        $serialized = $this->jsonSerialize();
        return json_encode($serialized);
    }
}
