<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // Créer 50 auteurs
        $authorIds = [];
        for ($i = 0; $i < 50; $i++) {
            $authorIds[] = DB::table('authors')->insertGetId([
                'name' => $faker->name(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Créer 10 catégories
        $categoryIds = [];
        $categories = ['Roman', 'Science-Fiction', 'Histoire', 'Policier', 'Fantasy',
                        'Biographie', 'Science', 'Philosophie', 'Poésie', 'Technologie'];
        foreach ($categories as $cat) {
            $categoryIds[] = DB::table('categories')->insertGetId([
                'libelle' => $cat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Créer 400 livres (entre 300 et 500)
        $bookIds = [];
        for ($i = 0; $i < 400; $i++) {
            $bookIds[] = DB::table('books')->insertGetId([
                'title' => ucfirst($faker->words(rand(2, 5), true)),
                'description' => $faker->paragraph(),
                'publication_date' => $faker->dateTimeBetween('-50 years', 'now')->format('Y-m-d'),
                'cover_image' => null,
                'author_id' => $faker->randomElement($authorIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Associer chaque livre à 1-3 catégories (table pivot book_category)
        foreach ($bookIds as $bookId) {
            $nbCategories = rand(1, 3);
            $selectedCategories = $faker->randomElements($categoryIds, $nbCategories);

            foreach ($selectedCategories as $catId) {
                DB::table('book_category')->insert([
                    'book_id' => $bookId,
                    'category_id' => $catId,
                ]);
            }
        }
    }
}