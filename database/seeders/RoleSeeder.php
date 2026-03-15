<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // DB permet d'exécuter des requêtes SQL directement

class RoleSeeder extends Seeder
{
    /**
     * Insère les deux rôles dans la table "roles"
     */
    public function run(): void
    {
        // DB::table('roles')->insert() insère des lignes directement en base de données
        DB::table('roles')->insert([
            // Rôle 1 : bibliothécaire (aura accès au Back-Office)
            ['role' => 'bibliothécaire', 'created_at' => now(), 'updated_at' => now()],

            // Rôle 2 : usager (peut emprunter des livres)
            ['role' => 'usager', 'created_at' => now(), 'updated_at' => now()],
        ]);
        // Après ce seeder : role_id=1 = bibliothécaire, role_id=2 = usager
    }
}
