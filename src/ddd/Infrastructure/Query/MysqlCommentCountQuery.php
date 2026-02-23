<?php

namespace pascualmg\cohete\ddd\Infrastructure\Query;

use pascualmg\cohete\ddd\Domain\Query\CommentCountQuery;
use React\Mysql\MysqlClient;
use React\Mysql\MysqlResult;
use React\Promise\PromiseInterface;
use Rx\Observable;

class MysqlCommentCountQuery implements CommentCountQuery
{
    public function __construct(
        private readonly MysqlClient $mysqlClient,
    ) {
    }

    public function countGroupedByPost(): PromiseInterface
    {
        return Observable::fromPromise(
            $this->mysqlClient->query(
                'SELECT post_id, COUNT(id) as cnt FROM comment GROUP BY post_id'
            )
        )->map(
            fn (MysqlResult $result) => array_column($result->resultRows, 'cnt', 'post_id')
        )->toPromise();
    }
}
