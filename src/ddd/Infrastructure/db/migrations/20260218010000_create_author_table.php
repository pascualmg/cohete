<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAuthorTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('author', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'char', ['limit' => 36])
            ->addColumn('name', 'string', ['limit' => 100])
            ->addColumn('key_hash', 'string', ['limit' => 255])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['name'], ['unique' => true, 'name' => 'idx_author_name'])
            ->create();
    }
}
