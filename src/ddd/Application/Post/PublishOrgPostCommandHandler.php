<?php

namespace pascualmg\cohete\ddd\Application\Post;

use Cohete\DDD\ValueObject\UuidValueObject;
use pascualmg\cohete\ddd\Domain\Entity\Author\Author;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use pascualmg\cohete\ddd\Domain\Entity\Post\ValueObject\Slug;
use pascualmg\cohete\ddd\Domain\Entity\PostRepository;
use pascualmg\cohete\ddd\Domain\Service\AuthorAuthenticator;
use pascualmg\cohete\ddd\Domain\Service\PostCreator;
use pascualmg\cohete\ddd\Domain\Service\PostUpdater;
use pascualmg\cohete\ddd\Infrastructure\Service\OrgToHtmlConverter;
use React\Promise\PromiseInterface;
use Rx\Observable;

/**
 * Caso de uso UNICO para publicar un post desde .org. IDEMPOTENTE por
 * (autor, slug): si el autor ya tiene un post con ese #+SLUG -> ACTUALIZA
 * preservando el UUID; si no -> CREA. El cliente no decide crear-vs-actualizar:
 * publica el .org y el dominio hace lo correcto por el slug (su identidad).
 *
 * Punto unico que comparten el controller HTTP (POST /post/org) y el MCP
 * (publish_org). Antes esta logica vivia duplicada en dos controllers casi
 * calcados (UploadOrgController + UpdatePostFromOrgController) + el handler MCP.
 *
 * El flujo es una cadena Rx plana en vez de .then anidados:
 *   authenticate -> validar autor -> preparar borrador -> buscar existente -> upsert
 */
readonly class PublishOrgPostCommandHandler
{
    public function __construct(
        private AuthorAuthenticator $authorAuthenticator,
        private OrgToHtmlConverter $orgToHtmlConverter,
        private PostRepository $postRepository,
        private PostCreator $postCreator,
        private PostUpdater $postUpdater,
    ) {
    }

    /**
     * @return PromiseInterface<PublishOrgPostResult>
     * @throws InvalidBearerException si el token no es de ningun autor (-> 403)
     * @throws MissingSlugException   si el org no trae #+SLUG (-> 400)
     */
    public function __invoke(string $bearerToken, string $orgContent): PromiseInterface
    {
        return Observable::fromPromise($this->authorAuthenticator->authenticate($bearerToken))
            ->map(fn (?Author $author): Author => $author
                ?? throw new InvalidBearerException('Invalid bearer key'))
            ->map(fn (Author $author): array => $this->draftFrom($author, $orgContent))
            ->flatMap(fn (array $draft): Observable => Observable::fromPromise(
                $this->postRepository->findByAuthorAndSlug($draft['authorName'], $draft['slug'])
            )->map(fn (?Post $existing): PublishOrgPostResult => $this->upsert($draft, $existing)))
            ->toPromise();
    }

    /**
     * Prepara (sincronamente) todo lo derivable del .org + autor: valida el
     * #+SLUG obligatorio (clave de idempotencia) y convierte el org a HTML.
     *
     * @return array{authorName: string, slug: Slug, slugRaw: string, headline: string, html: string, orgSource: string}
     */
    private function draftFrom(Author $author, string $orgContent): array
    {
        $metadata = $this->orgToHtmlConverter->extractMetadata($orgContent);

        // El #+SLUG es obligatorio: es la clave de idempotencia. Sin el no
        // distinguimos "republicar este post" de "crear otro".
        $slugRaw = $metadata['slug'] ?? null;
        if ($slugRaw === null || trim($slugRaw) === '') {
            throw new MissingSlugException('#+SLUG is required to publish (it is the post identity)');
        }

        return [
            'authorName' => (string) $author->name,
            'slug' => Slug::from($slugRaw),
            'slugRaw' => $slugRaw,
            'headline' => $metadata['title'],
            'html' => $this->orgToHtmlConverter->convert($orgContent),
            'orgSource' => $orgContent,
        ];
    }

    /**
     * Idempotencia por (autor, slug): si ya existe un post con ese slug lo
     * ACTUALIZA preservando el UUID (no rompe permalinks); si no, lo CREA con
     * un UUID nuevo. Un autor solo pisa SU propio post.
     *
     * @param array{authorName: string, slug: Slug, slugRaw: string, headline: string, html: string, orgSource: string} $draft
     */
    private function upsert(array $draft, ?Post $existing): PublishOrgPostResult
    {
        $datePublished = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);

        if ($existing !== null) {
            $postId = (string) $existing->id;
            ($this->postUpdater)($postId, $draft['headline'], $draft['html'], $draft['authorName'], $datePublished, $draft['orgSource'], $draft['slugRaw']);

            return new PublishOrgPostResult(false, $postId, $draft['headline'], $draft['authorName'], $datePublished, (string) $existing->slug);
        }

        $postId = (string) UuidValueObject::v4();
        ($this->postCreator)($postId, $draft['headline'], $draft['html'], $draft['authorName'], $datePublished, $draft['orgSource'], $draft['slugRaw']);

        return new PublishOrgPostResult(true, $postId, $draft['headline'], $draft['authorName'], $datePublished, (string) $draft['slug']);
    }
}
