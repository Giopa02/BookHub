<?php

// Ce Seeder crée 250 emprunts fictifs pour simuler l'historique d'une bibliothèque.
// Il simule des emprunts passés (retournés) et quelques emprunts en cours (pas encore rendus).


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon; // Carbon est une bibliothèque pour manipuler les dates facilement

class BorrowSeeder extends Seeder
{
    /**
     * Crée 250 emprunts simulés avec leurs exemplaires associés
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // On récupère tous les identifiants des usagers (role_id = 2)
        // Les bibliothécaires n'empruntent pas de livres
        $userIds = DB::table('users')->where('role_id', 2)->pluck('id')->toArray();

        // On récupère tous les identifiants d'exemplaires disponibles
        $allCopyIds = DB::table('copies')->pluck('id')->toArray();

        // On garde trace des exemplaires déjà "en cours d'emprunt"
        // pour éviter qu'un même exemplaire soit dans deux emprunts actifs simultanément
        $borrowedCopyIds = [];

        // On crée 250 emprunts
        for ($i = 0; $i < 250; $i++) {
            // Date d'emprunt aléatoire dans les 2 dernières années
            $borrowingDate   = $faker->dateTimeBetween('-2 years', 'now');
            $borrowingCarbon = Carbon::parse($borrowingDate); // Conversion en objet Carbon pour manipuler la date

            // 90% de chance que l'emprunt soit déjà retourné
            $isReturned = $faker->boolean(90); // boolean(90) = true 90% du temps

            // Si retourné, la date de retour est entre 1 et 30 jours après l'emprunt
            // Sinon, return_date reste NULL (emprunt en cours)
            $returnDate = $isReturned
                ? $borrowingCarbon->copy()->addDays(rand(1, 30))->format('Y-m-d')
                : null;

            // On crée l'emprunt en base de données
            $borrowId = DB::table('borrows')->insertGetId([
                'borrowing_date' => $borrowingCarbon->format('Y-m-d'),
                'return_date'    => $returnDate,
                'user_id'        => $faker->randomElement($userIds), // Usager aléatoire
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // On choisit entre 1 et 5 exemplaires pour cet emprunt
            $nbCopies = rand(1, 5);

            // On s'assure de ne pas reprendre des exemplaires déjà en cours d'emprunt
            $availableCopies = array_diff($allCopyIds, $borrowedCopyIds);

            // Si plus assez d'exemplaires disponibles, on reprend tous les exemplaires
            // (c'est acceptable pour les emprunts déjà retournés)
            if (count($availableCopies) < $nbCopies) {
                $availableCopies = $allCopyIds;
            }

            // On sélectionne aléatoirement les exemplaires pour cet emprunt
            $selectedCopies = $faker->randomElements(
                array_values($availableCopies),
                min($nbCopies, count($availableCopies))
            );

            // On associe chaque exemplaire à cet emprunt via la table pivot "borrow_copy"
            foreach ($selectedCopies as $copyId) {
                DB::table('borrow_copy')->insert([
                    'borrow_id' => $borrowId,
                    'copy_id'   => $copyId,
                ]);

                // Si l'emprunt est encore en cours (pas retourné),
                // on marque l'exemplaire comme "emprunté" (status_id = 2) en base
                if (!$isReturned) {
                    $borrowedCopyIds[] = $copyId; // On le note pour ne pas le réutiliser

                    DB::table('copies')
                        ->where('id', $copyId)
                        ->update(['status_id' => 2]); // 2 = emprunté
                }
            }
        }
    }
}
