<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Domain\Entity\Media\ValueObject;

final readonly class ContentType
{
    private function __construct(public string $value)
    {
    }

    public static function from(string $value): self
    {
        if (!preg_match('#^[a-z]+/[a-z0-9.+\-]+$#i', $value)) {
            throw new \InvalidArgumentException("invalid content type: '$value'");
        }
        return new self(strtolower($value));
    }

    public static function detectFromExtension(string $filename): self
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return self::from(match ($ext) {
            'png'  => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'svg'  => 'image/svg+xml',
            'pdf'  => 'application/pdf',
            'mp4'  => 'video/mp4',
            'webm' => 'video/webm',
            'wav'  => 'audio/wav',
            'mp3'  => 'audio/mpeg',
            default => 'application/octet-stream',
        });
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
