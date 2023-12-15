<?php

namespace Pascualmg\Rx\ddd\Domain\Entity;

use React\Promise\PromiseInterface;

interface PostRepository
{
    public function findAll(): PromiseInterface;
}
