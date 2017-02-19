<?php

use Illuminate\Database\Seeder;
use App\Delivers;

class DeliversTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        foreach(range(1,30) as $index)
        {
            Delivers::create([
                // 'id' => $faker->numberBetween($min = 1, $max = 5),
                'name' => $faker->paragraph($nbSentences = 3)
            ]);
        }
    }
}
