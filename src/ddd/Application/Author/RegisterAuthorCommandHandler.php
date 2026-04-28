<?php

namespace pascualmg\cohete\ddd\Application\Author;

use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorName;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use React\Promise\PromiseInterface;
use Rx\Observable;

class RegisterAuthorCommandHandler
{
    public function __construct(
        private readonly AuthorRepository $authorRepository,
    ) {
    }

    /**
     * @return PromiseInterface of array{author: Author, token: string} | array{error: string}
     */
    public function __invoke(RegisterAuthorCommand $command): PromiseInterface
    {
        return Observable::fromPromise(
            $this->authorRepository->findByName(AuthorName::from($command->name))
        )
            ->flatMap(function (?Author $existing) use ($command) {
                if ($existing !== null) {
                    return Observable::of(['error' => "Author '{$command->name}' already exists. Pick a different name."]);
                }

                [$author, $plainKey] = Author::register($command->name, null, $command->type);

                return Observable::fromPromise($this->authorRepository->save($author))
                    ->map(fn (bool $saved) => $saved
                        ? ['author' => $author, 'token' => $plainKey]
                        : ['error' => 'Could not persist author']);
            })
            ->toPromise();
    }
}
