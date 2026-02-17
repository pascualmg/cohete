<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddAuthorIdToPost extends AbstractMigration
{
    public function up(): void
    {
        // Add author_id column
        $this->table('post')
            ->addColumn('author_id', 'char', ['limit' => 36, 'null' => true, 'after' => 'author'])
            ->update();

        // Data migration: match existing posts to authors by name
        $this->execute("
            UPDATE post p
            INNER JOIN author a ON LOWER(p.author) = LOWER(a.name)
            SET p.author_id = a.id
        ");

        // Add FK constraint
        $this->table('post')
            ->addForeignKey('author_id', 'author', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
                'constraint' => 'fk_post_author',
            ])
            ->addIndex(['author_id'], ['name' => 'idx_post_author_id'])
            ->update();
    }

    public function down(): void
    {
        $this->table('post')
            ->dropForeignKey('author_id')
            ->removeIndex(['author_id'])
            ->removeColumn('author_id')
            ->update();
    }
}
