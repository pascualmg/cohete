<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\Parser;

use InvalidArgumentException;

class OrgFileParser implements FileParser
{
    public function parse(string $filePath): ParsedPostData
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File not found: {$filePath}");
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new InvalidArgumentException("Cannot read file: {$filePath}");
        }

        $lines = explode("\n", $content);

        $headline = null;
        $author = null;
        $datePublished = null;
        $bodyLines = [];

        foreach ($lines as $line) {
            // Parse org-mode metadata
            if (preg_match('/^#\+TITLE:\s*(.+)$/i', $line, $matches)) {
                $headline = trim($matches[1]);
                continue;
            }
            if (preg_match('/^#\+AUTHOR:\s*(.+)$/i', $line, $matches)) {
                $author = trim($matches[1]);
                continue;
            }
            if (preg_match('/^#\+DATE:\s*(.+)$/i', $line, $matches)) {
                $datePublished = $this->parseDate(trim($matches[1]));
                continue;
            }

            // Skip other metadata lines (#+OPTIONS, #+HTML_HEAD, etc.)
            if (str_starts_with($line, '#+')) {
                continue;
            }

            // Rest is body content
            $bodyLines[] = $line;
        }

        if ($headline === null) {
            throw new InvalidArgumentException("Missing #+TITLE in org file: {$filePath}");
        }

        return new ParsedPostData(
            headline: $headline,
            articleBody: trim(implode("\n", $bodyLines)),
            author: $author,
            datePublished: $datePublished,
        );
    }

    private function parseDate(string $dateStr): ?\DateTimeImmutable
    {
        // org-mode dates can be: <2024-01-15 Mon> or [2024-01-15] or 2024-01-15 or ISO format
        $dateStr = trim($dateStr, '<>[] ');
        // Remove day names (Mon, Tue, Wed, etc.)
        $dateStr = preg_replace('/\s+(Mon|Tue|Wed|Thu|Fri|Sat|Sun)\s*/i', '', $dateStr);
        $dateStr = trim($dateStr);

        if (empty($dateStr)) {
            return null;
        }

        try {
            return new \DateTimeImmutable($dateStr);
        } catch (\Exception) {
            return null;
        }
    }
}
