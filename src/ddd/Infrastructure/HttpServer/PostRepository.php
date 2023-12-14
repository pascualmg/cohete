<?php

namespace Pascualmg\Rx\ddd\Infrastructure\HttpServer;

interface PostRepository
{
    public function findAll(): array;
}
