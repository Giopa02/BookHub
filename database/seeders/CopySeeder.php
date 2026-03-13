<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CopySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        $bookIds = DB::table('books')->pluck('id')->toArray();

        for ($i = 0; $i < 1500; $i++) {
            DB::table('copies')->insert([
                'commission_date' => $faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
                'book_id' => $faker->randomElement($bookIds),
                'status_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}