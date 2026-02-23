<?php

namespace pascualmg\cohete\ddd\Domain\Query;

use React\Promise\PromiseInterface;

interface CommentCountQuery
{
    /** @return PromiseInterface<array<string, int>> postId => count */
    public function countGroupedByPost(): PromiseInterface;
}
