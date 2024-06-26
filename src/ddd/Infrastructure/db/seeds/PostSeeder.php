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

        for($i = 0; $i < 100; $i++) {
            $postFakeData[] = [
                'id' => $faker->uuid(),
                'headline' => $faker->sentence(),
                'articleBody' => $faker->paragraph(),
                'author' => $faker->name(),
                'datePublished' => $faker->date(),
            ];
        }

        $this->insert('post', $postFakeData);
    }
}
