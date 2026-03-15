<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Insère les deux statuts dans la table "statuses"
     */
    public function run(): void
    {
        DB::table('statuses')->insert([
            // Statut 1 : disponible → l'exemplaire peut être emprunté
            ['status' => 'disponible', 'created_at' => now(), 'updated_at' => now()],

            // Statut 2 : emprunté → l'exemplaire est actuellement pris par un usager
            ['status' => 'emprunté', 'created_at' => now(), 'updated_at' => now()],
        ]);
        // Après ce seeder : status_id=1 = disponible, status_id=2 = emprunté
    }
}
