<?php

// Ce Seeder crée 1500 exemplaires physiques et les répartit aléatoirement sur les livres
// Tous les exemplaires sont initialement créés avec status_id = 1 (disponible).
// Le BorrowSeeder viendra ensuite en marquer certains comme "emprunté".

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker; 

class CopySeeder extends Seeder
{
    /**
     * Crée 1500 exemplaires répartis aléatoirement sur tous les livres
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // On récupère tous les identifiants de livres existants
        // pluck('id') retourne juste un tableau d'identifiants [1, 2, 3, ...]
        $bookIds = DB::table('books')->pluck('id')->toArray();

        // On crée 1500 exemplaires
        for ($i = 0; $i < 1500; $i++) {
            DB::table('copies')->insert([
                // Date de mise en service entre il y a 10 ans et aujourd'hui
                'commission_date' => $faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),

                // On associe cet exemplaire à un livre aléatoire parmi tous les livres
                'book_id'         => $faker->randomElement($bookIds),

                // Au départ, tous les exemplaires sont disponibles (status_id = 1)
                // Le BorrowSeeder en marquera certains comme "emprunté"
                'status_id'       => 1,

                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }
}
