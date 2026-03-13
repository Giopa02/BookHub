<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // 3 bibliothécaires (role_id = 1)
        for ($i = 0; $i < 3; $i++) {
            DB::table('users')->insert([
                'name' => $faker->lastName(),
                'prenom' => $faker->firstName(),
                'email' => $faker->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 25 usagers (role_id = 2)
        for ($i = 0; $i < 25; $i++) {
            DB::table('users')->insert([
                'name' => $faker->lastName(),
                'prenom' => $faker->firstName(),
                'email' => $faker->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}