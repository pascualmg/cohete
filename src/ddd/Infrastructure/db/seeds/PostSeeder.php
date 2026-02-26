<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        //todo:meter esto en el readme
        $faker = Faker\Factory::create();
        $postFakeData = [];

        for ($i = 0; $i < 100; $i++) {
            $headline = $faker->sentence();
            $author = $faker->randomElement(['Pascual', 'Ambrosio', 'Nova']);
            $postFakeData[] = [
                'id' => $faker->uuid(),
                'headline' => $headline,
                'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $headline), '-')),
                'articleBody' => $faker->paragraph(),
                'author' => $author,
                'author_id' => null, // Let the db handle or we can just ignore it since it's a seed, wait, we can just leave it to null unless strict, but we can set author as Pascual.
                'datePublished' => $faker->date(),
            ];
        }

        $this->insert('post', $postFakeData);
    }
}
