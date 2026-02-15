<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddOrgSourceToPost extends AbstractMigration
{
    public function change(): void
    {
        $this->table('post')
            ->addColumn('orgSource', 'text', ['null' => true, 'after' => 'articleBody'])
            ->update();
    }
}
