<?php

namespace pascualmg\cohete\ddd\Infrastructure\HttpServer\RequestHandler;

use Cohete\HttpServer\HttpRequestHandler;
use pascualmg\cohete\ddd\Application\Post\FindAllPosts;
use pascualmg\cohete\ddd\Application\Post\FindAllPostsQuery;
use pascualmg\cohete\ddd\Domain\Entity\Post\Post;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

class RssFeedController implements HttpRequestHandler
{
    private const MAX_ITEMS = 30;

    public function __construct(
        private readonly FindAllPosts $findAllPosts,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ?array $routeParams
    ): ResponseInterface|PromiseInterface {
        $scheme = $request->getUri()->getScheme() ?: 'https';
        $host = $request->getUri()->getHost() ?: 'pascualmg.dev';
        $baseUrl = $scheme . '://' . $host;

        return ($this->findAllPosts)(new FindAllPostsQuery())->then(
            function (array $posts) use ($baseUrl): ResponseInterface {
                $items = array_slice($posts, 0, self::MAX_ITEMS);
                $xml = $this->buildRss($items, $baseUrl);

                return new Response(
                    200,
                    ['Content-Type' => 'application/rss+xml; charset=utf-8'],
                    $xml,
                );
            },
            fn (\Throwable $e) => new Response(
                500,
                ['Content-Type' => 'text/plain'],
                'RSS generation failed: ' . $e->getMessage(),
            ),
        );
    }

    /** @param Post[] $posts */
    private function buildRss(array $posts, string $baseUrl): string
    {
        $lastBuildDate = !empty($posts)
            ? (new \DateTimeImmutable((string)$posts[0]->datePublished))->format(\DATE_RSS)
            : (new \DateTimeImmutable())->format(\DATE_RSS);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        $xml .= '<channel>' . "\n";
        $xml .= '  <title>Cohete Blog - pascualmg.dev</title>' . "\n";
        $xml .= '  <link>' . $this->esc($baseUrl . '/blog') . '</link>' . "\n";
        $xml .= '  <description>Blog asincrono construido con PHP, ReactPHP y DDD</description>' . "\n";
        $xml .= '  <language>es</language>' . "\n";
        $xml .= '  <lastBuildDate>' . $lastBuildDate . '</lastBuildDate>' . "\n";
        $xml .= '  <atom:link href="' . $this->esc($baseUrl . '/rss') . '" rel="self" type="application/rss+xml"/>' . "\n";

        foreach ($posts as $post) {
            $authorSlug = strtolower(explode(' ', (string)$post->author)[0]);
            $link = $baseUrl . '/blog/' . $authorSlug . '/' . (string)$post->slug;
            $pubDate = (new \DateTimeImmutable((string)$post->datePublished))->format(\DATE_RSS);
            $description = mb_substr(strip_tags((string)$post->articleBody), 0, 500);

            $xml .= '  <item>' . "\n";
            $xml .= '    <title>' . $this->esc((string)$post->headline) . '</title>' . "\n";
            $xml .= '    <link>' . $this->esc($link) . '</link>' . "\n";
            $xml .= '    <guid isPermaLink="true">' . $this->esc($link) . '</guid>' . "\n";
            $xml .= '    <pubDate>' . $pubDate . '</pubDate>' . "\n";
            $xml .= '    <author>' . $this->esc((string)$post->author) . '</author>' . "\n";
            $xml .= '    <description>' . $this->esc($description) . '</description>' . "\n";
            $xml .= '    <content:encoded><![CDATA[' . (string)$post->articleBody . ']]></content:encoded>' . "\n";
            $xml .= '  </item>' . "\n";
        }

        $xml .= '</channel>' . "\n";
        $xml .= '</rss>';

        return $xml;
    }

    private function esc(string $s): string
    {
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
