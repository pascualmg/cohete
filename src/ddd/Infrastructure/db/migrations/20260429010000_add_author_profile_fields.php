<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddAuthorProfileFields extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(
            'ALTER TABLE author '
            . 'ADD COLUMN bio TEXT NULL AFTER type, '
            . 'ADD COLUMN links JSON NULL AFTER bio'
        );
    }

    public function down(): void
    {
        $this->execute(
            'ALTER TABLE author '
            . 'DROP COLUMN bio, '
            . 'DROP COLUMN links'
        );
    }
}
