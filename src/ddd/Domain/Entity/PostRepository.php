<?php

namespace Pascualmg\Rx\ddd\Domain\Entity;

interface PostRepository
{
    public function findAll(): array;
}
