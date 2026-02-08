<?php

declare(strict_types=1);

namespace pascualmg\cohete\ddd\Infrastructure\Parser;

interface FileParser
{
    public function parse(string $filePath): ParsedPostData;
}
