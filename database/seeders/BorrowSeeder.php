<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class BorrowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // Récupérer uniquement les usagers (role_id = 2)
        $userIds = DB::table('users')->where('role_id', 2)->pluck('id')->toArray();
        $allCopyIds = DB::table('copies')->pluck('id')->toArray();

        // Pour suivre les exemplaires déjà utilisés dans des emprunts en cours
        $borrowedCopyIds = [];

        for ($i = 0; $i < 250; $i++) {
            $borrowingDate = $faker->dateTimeBetween('-2 years', 'now');
            $borrowingCarbon = Carbon::parse($borrowingDate);

            // 90% des emprunts sont retournés, 10% en cours
            $isReturned = $faker->boolean(90);
            $returnDate = $isReturned
                ? $borrowingCarbon->copy()->addDays(rand(1, 30))->format('Y-m-d')
                : null;

            // Créer l'emprunt
            $borrowId = DB::table('borrows')->insertGetId([
                'borrowing_date' => $borrowingCarbon->format('Y-m-d'),
                'return_date' => $returnDate,
                'user_id' => $faker->randomElement($userIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Associer 1-5 exemplaires à cet emprunt
            $nbCopies = rand(1, 5);

            // Choisir des exemplaires pas encore empruntés (pour les emprunts en cours)
            $availableCopies = array_diff($allCopyIds, $borrowedCopyIds);
            if (count($availableCopies) < $nbCopies) {
                $availableCopies = $allCopyIds;
            }

            $selectedCopies = $faker->randomElements(array_values($availableCopies), min($nbCopies, count($availableCopies)));

            foreach ($selectedCopies as $copyId) {
                DB::table('borrow_copy')->insert([
                    'borrow_id' => $borrowId,
                    'copy_id' => $copyId,
                ]);

                // Si emprunt en cours, marquer l'exemplaire comme emprunté
                if (!$isReturned) {
                    $borrowedCopyIds[] = $copyId;
                    DB::table('copies')
                        ->where('id', $copyId)
                        ->update(['status_id' => 2]); // emprunté
                }
            }
        }
    }
}