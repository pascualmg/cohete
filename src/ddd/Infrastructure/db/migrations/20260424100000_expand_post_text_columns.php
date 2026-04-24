<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ExpandPostTextColumns extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE post '
            . 'MODIFY articleBody MEDIUMTEXT, '
            . 'MODIFY orgSource MEDIUMTEXT'
        );
    }

    public function down(): void
    {
        $this->execute(
            'ALTER TABLE post '
            . 'MODIFY articleBody TEXT, '
            . 'MODIFY orgSource TEXT'
        );
    }
}
