<?php

// Ce fichier est le "Seeder principal" : c'est lui qui orchestre le remplissage
// initial de la base de données avec des données de test.
//
// Un "Seeder" sert à insérer des données fictives (ou de référence) dans la base
// pour pouvoir tester l'application sans avoir à tout saisir manuellement.
//
// Pour lancer tous les seeders : php artisan db:seed
// Pour tout réinitialiser + re-remplir : php artisan migrate:fresh --seed

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * La méthode run() est appelée quand on lance "php artisan db:seed"
     * Elle appelle chaque seeder dans le bon ordre :
     * les tables "parentes" doivent être remplies avant les tables qui dépendent d'elles
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,    // 1. D'abord les rôles (bibliothécaire, usager)
                                  //    → car les utilisateurs ont besoin d'un role_id
            UserSeeder::class,    // 2. Ensuite les utilisateurs (3 bibliothécaires + 25 usagers)
            StatusSeeder::class,  // 3. Les statuts (disponible, emprunté)
                                  //    → car les exemplaires ont besoin d'un status_id
            BookSeeder::class,    // 4. Les livres (avec auteurs et catégories)
                                  //    → car les exemplaires ont besoin d'un book_id
            CopySeeder::class,    // 5. Les exemplaires (1500 exemplaires répartis sur les livres)
            BorrowSeeder::class,  // 6. Enfin les emprunts (250 emprunts simulés)
                                  //    → en dernier car ils dépendent de tout le reste
        ]);
    }
}
