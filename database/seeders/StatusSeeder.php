<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('statuses')->insert([
            ['status' => 'disponible', 'created_at' => now(), 'updated_at' => now()],
            ['status' => 'emprunté', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}