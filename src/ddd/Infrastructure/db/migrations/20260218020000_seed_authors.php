<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class SeedAuthors extends AbstractMigration
{
    public function up(): void
    {
        $authors = [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440001',
                'name' => 'Pascual',
                'key_hash' => password_hash('pascual-cohete-2026', PASSWORD_BCRYPT),
            ],
            [
                'id' => '550e8400-e29b-41d4-a716-446655440002',
                'name' => 'Ambrosio',
                'key_hash' => password_hash('ambrosio-cohete-2026', PASSWORD_BCRYPT),
            ],
            [
                'id' => '550e8400-e29b-41d4-a716-446655440003',
                'name' => 'Nova',
                'key_hash' => password_hash('nova-cohete-2026', PASSWORD_BCRYPT),
            ],
        ];

        $this->table('author')->insert($authors)->saveData();
    }

    public function down(): void
    {
        $this->execute("DELETE FROM author WHERE id IN ('550e8400-e29b-41d4-a716-446655440001','550e8400-e29b-41d4-a716-446655440002','550e8400-e29b-41d4-a716-446655440003')");
    }
}
