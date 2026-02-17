<?php

namespace pascualmg\cohete\ddd\Domain\Service;

use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use React\Promise\PromiseInterface;

use function React\Promise\resolve;

class AuthorAuthenticator
{
    public function __construct(
        private readonly AuthorRepository $authorRepository,
    ) {
    }

    /** @return PromiseInterface of Author|null */
    public function authenticate(string $bearerToken): PromiseInterface
    {
        return $this->authorRepository->findAll()->then(
            function (array $authors) use ($bearerToken): ?Author {
                foreach ($authors as $author) {
                    if ($author->verifyKey($bearerToken)) {
                        return $author;
                    }
                }
                return null;
            }
        );
    }
}
