<?php

namespace pascualmg\cohete\ddd\Infrastructure\Service;

class OrgToHtmlConverter
{
    private string $pandocPath;

    public function __construct(?string $pandocPath = null)
    {
        $this->pandocPath = $pandocPath ?? ($_ENV['PANDOC_PATH'] ?? 'pandoc');
    }

    public function convert(string $orgContent): string
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open(
            [$this->pandocPath, '-f', 'org', '-t', 'html'],
            $descriptors,
            $pipes
        );

        if (!is_resource($process)) {
            throw new \RuntimeException('Failed to start pandoc');
        }

        fwrite($pipes[0], $orgContent);
        fclose($pipes[0]);

        $html = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new \RuntimeException("pandoc failed (exit $exitCode): $stderr");
        }

        return $html;
    }

    public function extractMetadata(string $orgContent): array
    {
        $title = '';
        $author = '';
        $date = '';

        foreach (explode("\n", $orgContent) as $line) {
            if (preg_match('/^#\+TITLE:\s*(.+)$/i', $line, $m)) {
                $title = trim($m[1]);
            } elseif (preg_match('/^#\+AUTHOR:\s*(.+)$/i', $line, $m)) {
                $author = trim($m[1]);
            } elseif (preg_match('/^#\+DATE:\s*(.+)$/i', $line, $m)) {
                $date = trim($m[1]);
            }
        }

        return [
            'title' => $title ?: 'Untitled',
            'author' => $author ?: 'pascualmg',
            'date' => $date ?: (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ];
    }
}
