<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTablePost extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('post', ['id' => false, 'primary_key' => ['id']]);
        $table
            ->addColumn('id', 'integer', ['identity' => true])
            ->addColumn('headline', 'string', ['limit' => 255])
            ->addColumn('articleBody', 'text')
            ->addColumn('image', 'string', ['limit' => 255])
            ->addColumn('author', 'string', ['limit' => 255])
            ->addColumn('datePublished', 'datetime')
            ->create();
    }
}