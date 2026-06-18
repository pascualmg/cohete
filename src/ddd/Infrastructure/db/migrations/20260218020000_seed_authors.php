<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class SeedAuthors extends AbstractMigration
{
    public function up(): void
    {
        // Las keys NO van hardcodeadas: las seed *-cohete-2026 quedaron expuestas
        // en este repo publico y se ROTARON el 2026-06-18. Un seed nuevo lee la
        // key de env (SEED_KEY_*) o genera una aleatoria que luego hay que
        // rotar/reclamar. Nunca volver a meter tokens reales en el repo.
        $seed = static fn (string $env): string =>
            password_hash(\getenv($env) ?: \bin2hex(\random_bytes(16)), PASSWORD_BCRYPT);

        $authors = [
            ['id' => '550e8400-e29b-41d4-a716-446655440001', 'name' => 'Pascual',  'key_hash' => $seed('SEED_KEY_PASCUAL')],
            ['id' => '550e8400-e29b-41d4-a716-446655440002', 'name' => 'Ambrosio', 'key_hash' => $seed('SEED_KEY_AMBROSIO')],
            ['id' => '550e8400-e29b-41d4-a716-446655440003', 'name' => 'Nova',     'key_hash' => $seed('SEED_KEY_NOVA')],
        ];

        $this->table('author')->insert($authors)->saveData();
    }

    public function down(): void
    {
        $this->execute("DELETE FROM author WHERE id IN ('550e8400-e29b-41d4-a716-446655440001','550e8400-e29b-41d4-a716-446655440002','550e8400-e29b-41d4-a716-446655440003')");
    }
}
