<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\Console\Command;

use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use pascualmg\cohete\ddd\Infrastructure\Console\ConsoleCommand;

use function React\Async\await;

#[ConsoleCommand(name: 'post:list', description: 'List all posts with id, date, author and headline')]
class PostListCommand
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(array $args): int
    {
        /** @var Post[] $posts */
        $posts = await($this->postRepository->findAll());

        if (empty($posts)) {
            fwrite(STDOUT, "No posts found.\n");
            return 0;
        }

        fprintf(STDOUT, "\n  %-36s  %-12s  %-15s  %s\n", 'ID', 'DATE', 'AUTHOR', 'HEADLINE');
        fprintf(STDOUT, "  %s\n", str_repeat('-', 100));

        foreach ($posts as $post) {
            $date = $post->datePublished->getDatetimeImmutable()->format('Y-m-d');
            $hasOrg = $post->orgSource !== null ? '*' : ' ';

            fprintf(STDOUT, " %s%-36s  %-12s  %-15s  %s\n",
                $hasOrg,
                (string)$post->id,
                $date,
                mb_substr((string)$post->author, 0, 15),
                mb_substr((string)$post->headline, 0, 60),
            );
        }

        fprintf(STDOUT, "\n  Total: %d posts (* = has orgSource)\n\n", count($posts));
        return 0;
    }
}
