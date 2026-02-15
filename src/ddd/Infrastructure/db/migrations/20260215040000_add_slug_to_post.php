<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSlugToPost extends AbstractMigration
{
    public function change(): void
    {
        $this->table('post')
            ->addColumn('slug', 'string', ['limit' => 255, 'null' => true, 'after' => 'headline'])
            ->addIndex(['slug'], ['unique' => true, 'name' => 'idx_post_slug'])
            ->update();
    }
}
