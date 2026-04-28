<?php

namespace pascualmg\cohete\ddd\Application\Author;

use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\Author\ValueObject\AuthorId;
use pascualmg\cohete\ddd\Domain\Entity\AuthorRepository;
use React\Promise\PromiseInterface;
use Rx\Observable;

class UpdateAuthorProfileCommandHandler
{
    public function __construct(
        private readonly AuthorRepository $authorRepository,
    ) {
    }

    /**
     * @return PromiseInterface of Author|null
     */
    public function __invoke(UpdateAuthorProfileCommand $command): PromiseInterface
    {
        return Observable::fromPromise(
            $this->authorRepository->findById(AuthorId::from($command->authorId))
        )
            ->flatMap(function (?Author $author) use ($command) {
                if ($author === null) {
                    return Observable::of(null);
                }

                $updated = $author->withProfile($command->bio, $command->links);

                return Observable::fromPromise($this->authorRepository->update($updated))
                    ->map(fn (bool $ok) => $ok ? $updated : null);
            })
            ->toPromise();
    }
}
