<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCommentTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('comment', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'char', ['limit' => 36, 'null' => false])
            ->addColumn('post_id', 'char', ['limit' => 36])
            ->addColumn('author_name', 'string', ['limit' => 100])
            ->addColumn('body', 'text')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('post_id', 'post', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_comment_post',
            ])
            ->addIndex(['post_id', 'created_at'], ['name' => 'idx_comment_post_date'])
            ->create();
    }
}
