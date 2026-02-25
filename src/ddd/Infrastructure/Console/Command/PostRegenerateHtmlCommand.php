<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\Console\Command;

use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use pascualmg\cohete\ddd\Infrastructure\Console\ConsoleCommand;
use pascualmg\cohete\ddd\Infrastructure\Service\OrgToHtmlConverter;

use function React\Async\await;

#[ConsoleCommand(
    name: 'post:regenerate-html',
    description: 'Re-run Pandoc on posts with orgSource to regenerate HTML (fixes highlighting)'
)]
class PostRegenerateHtmlCommand
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly OrgToHtmlConverter $orgToHtmlConverter,
    ) {
    }

    public function __invoke(array $args): int
    {
        $dryRun = $args['dry-run'] ?? false;
        $targetId = $args['post-id'] ?? null;

        /** @var Post[] $posts */
        $posts = await($this->postRepository->findAll());

        $candidates = array_filter($posts, function (Post $post) use ($targetId): bool {
            if ($post->orgSource === null) {
                return false;
            }
            if ($targetId !== null && (string)$post->id !== $targetId) {
                return false;
            }
            return true;
        });

        if (empty($candidates)) {
            fwrite(STDOUT, "No posts with orgSource found" .
                ($targetId ? " matching --post-id=$targetId" : "") . ".\n");
            return 0;
        }

        fprintf(STDOUT, "\n  %s %d post(s)...\n\n",
            $dryRun ? '[DRY RUN] Would regenerate' : 'Regenerating',
            count($candidates)
        );

        $ok = 0;
        $errors = 0;

        foreach ($candidates as $post) {
            $label = mb_substr((string)$post->headline, 0, 50);

            if ($dryRun) {
                fprintf(STDOUT, "  [SKIP] %s  %s\n", (string)$post->id, $label);
                $ok++;
                continue;
            }

            try {
                $newHtml = $this->orgToHtmlConverter->convert($post->orgSource);

                $updated = Post::fromPrimitives(
                    (string)$post->id,
                    (string)$post->headline,
                    $newHtml,
                    (string)$post->author,
                    $post->datePublished->getDatetimeImmutable()->format(\DateTimeInterface::ATOM),
                    $post->orgSource,
                );

                await($this->postRepository->update($updated));
                fprintf(STDOUT, "  [OK]   %s  %s\n", (string)$post->id, $label);
                $ok++;
            } catch (\Throwable $e) {
                fprintf(STDERR, "  [FAIL] %s  %s: %s\n",
                    (string)$post->id, $label, $e->getMessage());
                $errors++;
            }
        }

        fprintf(STDOUT, "\n  Done. %d ok, %d errors.\n\n", $ok, $errors);
        return $errors > 0 ? 1 : 0;
    }
}
