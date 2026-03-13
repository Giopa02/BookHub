<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['role' => 'bibliothécaire', 'created_at' => now(), 'updated_at' => now()],
            ['role' => 'usager', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}