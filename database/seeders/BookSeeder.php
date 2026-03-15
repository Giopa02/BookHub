<?php

// Ce Seeder remplit la base de données avec les livres, auteurs et catégories.
// Il crée environ 400 livres répartis en deux groupes :
//   1. ~250 livres avec des titres et auteurs réels (Victor Hugo, Agatha Christie...)
//   2. ~150 livres supplémentaires avec des titres générés pour atteindre 400 au total
//
// Chaque livre est lié à un auteur et à une ou plusieurs catégories.

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker; // Faker génère des données fictives réalistes (descriptions, dates...)

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // On crée une instance de Faker en français pour les descriptions
        $faker = Faker::create('fr_FR');

        // ---------------------------------------------------------------
        // ÉTAPE 1 : Créer les 10 catégories
        // ---------------------------------------------------------------
        // 10 catégories réalistes
        $categories = [
            'Roman', 'Science-Fiction', 'Policier', 'Fantasy', 'Histoire',
            'Biographie', 'Philosophie', 'Science', 'Poésie', 'Technologie',
        ];

        $categoryIds = []; // Tableau pour stocker les IDs générés par la base de données
        foreach ($categories as $cat) {
            // insertGetId() insère une ligne et retourne son ID auto-incrémenté
            $categoryIds[] = DB::table('categories')->insertGetId([
                'libelle'    => $cat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        

        // ---------------------------------------------------------------
        // ÉTAPE 2 : Créer les 60 auteurs
        // ---------------------------------------------------------------
        // 60 auteurs réalistes
        $authors = [
            'Victor Hugo', 'Albert Camus', 'Émile Zola', 'Marcel Proust', 'Gustave Flaubert',
            'Alexandre Dumas', 'Jules Verne', 'Molière', 'Jean-Paul Sartre', 'Simone de Beauvoir',
            'Antoine de Saint-Exupéry', 'Voltaire', 'Guy de Maupassant', 'Honoré de Balzac', 'Stendhal',
            'Charles Baudelaire', 'Arthur Rimbaud', 'Marguerite Duras', 'Patrick Modiano', 'Annie Ernaux',
            'Michel Houellebecq', 'Amélie Nothomb', 'Fred Vargas', 'Marc Levy', 'Guillaume Musso',
            'J.K. Rowling', 'Stephen King', 'Agatha Christie', 'George Orwell', 'Isaac Asimov',
            'Philip K. Dick', 'Frank Herbert', 'J.R.R. Tolkien', 'Ernest Hemingway', 'Gabriel García Márquez',
            'Haruki Murakami', 'Fyodor Dostoïevski', 'Léon Tolstoï', 'Franz Kafka', 'Jorge Luis Borges',
            'Virginia Woolf', 'Jane Austen', 'Mary Shelley', 'Edgar Allan Poe', 'H.P. Lovecraft',
            'Ray Bradbury', 'Arthur C. Clarke', 'Dan Brown', 'Umberto Eco', 'Italo Calvino',
            'Milan Kundera', 'Paulo Coelho', 'Khaled Hosseini', 'Chimamanda Ngozi Adichie', 'Toni Morrison',
            'Françoise Sagan', 'Romain Gary', 'Boris Vian', 'Albert Cohen', 'Marguerite Yourcenar',
        ];

        $authorIds = []; // Tableau pour stocker les IDs des auteurs
        foreach ($authors as $name) {
            $authorIds[] = DB::table('authors')->insertGetId([
                'name'       => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // Après cette étape, $authorIds[0] = ID de Victor Hugo, $authorIds[1] = ID de Camus, etc.

        // ---------------------------------------------------------------
        // ÉTAPE 3 : Créer ~250 livres avec des titres réels
        // L'index_auteur correspond à la position dans le tableau $authors ci-dessus
        // Les index de catégories correspondent aux positions dans $categories
        // ---------------------------------------------------------------
        // ~400 livres avec titres réalistes regroupés par auteur/genre
        $books = [
            // Victor Hugo (index 0 dans $authors)
            ['Les Misérables', 0, [0]], ['Notre-Dame de Paris', 0, [0]], ['Les Contemplations', 0, [8]],
            ['Les Travailleurs de la mer', 0, [0]], ['Quatrevingt-treize', 0, [0, 4]],
            // Albert Camus (index 1)
            ['L\'Étranger', 1, [0, 6]], ['La Peste', 1, [0]], ['Le Mythe de Sisyphe', 1, [6]],
            ['La Chute', 1, [0]], ['L\'Homme révolté', 1, [6]],
            // Émile Zola (index 2)
            ['Germinal', 2, [0]], ['L\'Assommoir', 2, [0]], ['Nana', 2, [0]],
            ['Au Bonheur des Dames', 2, [0]], ['La Bête humaine', 2, [0, 2]],
            // Marcel Proust (index 3)
            ['Du côté de chez Swann', 3, [0]], ['À l\'ombre des jeunes filles en fleurs', 3, [0]],
            ['Le Temps retrouvé', 3, [0]],
            // Gustave Flaubert (index 4)
            ['Madame Bovary', 4, [0]], ['L\'Éducation sentimentale', 4, [0]], ['Salammbô', 4, [0, 4]],
            // Alexandre Dumas (index 5)
            ['Les Trois Mousquetaires', 5, [0, 4]], ['Le Comte de Monte-Cristo', 5, [0]],
            ['Vingt ans après', 5, [0, 4]], ['La Reine Margot', 5, [0, 4]],
            // Jules Verne (index 6)
            ['Vingt Mille Lieues sous les mers', 6, [1, 7]], ['Le Tour du monde en 80 jours', 6, [0]],
            ['Voyage au centre de la Terre', 6, [1, 7]], ['De la Terre à la Lune', 6, [1]],
            ['L\'Île mystérieuse', 6, [0, 1]], ['Michel Strogoff', 6, [0]],
            // Molière (index 7)
            ['Le Misanthrope', 7, [0]], ['Tartuffe', 7, [0]], ['L\'Avare', 7, [0]],
            ['Le Malade imaginaire', 7, [0]], ['Les Femmes savantes', 7, [0]],
            // Jean-Paul Sartre (index 8)
            ['La Nausée', 8, [0, 6]], ['Huis clos', 8, [0, 6]], ['Les Mots', 8, [5, 6]],
            // Simone de Beauvoir (index 9)
            ['Le Deuxième Sexe', 9, [6]], ['Mémoires d\'une jeune fille rangée', 9, [5]],
            ['Les Mandarins', 9, [0]],
            // Saint-Exupéry (index 10)
            ['Le Petit Prince', 10, [0, 3]], ['Vol de nuit', 10, [0]], ['Terre des hommes', 10, [0]],
            // Voltaire (index 11)
            ['Candide', 11, [0, 6]], ['Zadig', 11, [0, 6]], ['Micromégas', 11, [0, 1]],
            // Maupassant (index 12)
            ['Bel-Ami', 12, [0]], ['Une vie', 12, [0]], ['Pierre et Jean', 12, [0]],
            ['Boule de Suif', 12, [0]], ['Le Horla', 12, [0, 3]],
            // Balzac (index 13)
            ['Le Père Goriot', 13, [0]], ['Eugénie Grandet', 13, [0]], ['Illusions perdues', 13, [0]],
            ['La Peau de chagrin', 13, [0, 3]],
            // Stendhal (index 14)
            ['Le Rouge et le Noir', 14, [0]], ['La Chartreuse de Parme', 14, [0, 4]],
            // Baudelaire (index 15)
            ['Les Fleurs du mal', 15, [8]], ['Le Spleen de Paris', 15, [8]],
            // Rimbaud (index 16)
            ['Une saison en enfer', 16, [8]], ['Illuminations', 16, [8]],
            // Marguerite Duras (index 17)
            ['L\'Amant', 17, [0]], ['Moderato cantabile', 17, [0]], ['Un barrage contre le Pacifique', 17, [0]],
            // Patrick Modiano (index 18)
            ['Rue des Boutiques Obscures', 18, [0]], ['Dora Bruder', 18, [0, 4]],
            ['Dans le café de la jeunesse perdue', 18, [0]],
            // Annie Ernaux (index 19)
            ['Les Années', 19, [0, 5]], ['La Place', 19, [0, 5]], ['Une femme', 19, [0, 5]],
            // Houellebecq (index 20)
            ['Les Particules élémentaires', 20, [0]], ['Soumission', 20, [0]],
            ['La Carte et le Territoire', 20, [0]], ['Extension du domaine de la lutte', 20, [0]],
            // Amélie Nothomb (index 21)
            ['Stupeur et Tremblements', 21, [0]], ['Hygiène de l\'assassin', 21, [0, 2]],
            ['Métaphysique des tubes', 21, [0]],
            // Fred Vargas (index 22)
            ['Pars vite et reviens tard', 22, [2]], ['L\'Homme aux cercles bleus', 22, [2]],
            ['Un lieu incertain', 22, [2]], ['Sous les vents de Neptune', 22, [2]],
            // Marc Levy (index 23)
            ['Et si c\'était vrai...', 23, [0]], ['Où es-tu ?', 23, [0]],
            ['Toutes ces choses qu\'on ne s\'est pas dites', 23, [0]], ['Le Voleur d\'ombres', 23, [0]],
            // Guillaume Musso (index 24)
            ['Et après...', 24, [0]], ['Sauve-moi', 24, [0]], ['Parce que je t\'aime', 24, [0]],
            ['La Fille de Brooklyn', 24, [0, 2]], ['L\'Instant présent', 24, [0]],
            // J.K. Rowling (index 25)
            ['Harry Potter à l\'école des sorciers', 25, [3]], ['Harry Potter et la Chambre des secrets', 25, [3]],
            ['Harry Potter et le Prisonnier d\'Azkaban', 25, [3]], ['Harry Potter et la Coupe de feu', 25, [3]],
            ['Harry Potter et l\'Ordre du Phénix', 25, [3]], ['Harry Potter et le Prince de sang-mêlé', 25, [3]],
            ['Harry Potter et les Reliques de la Mort', 25, [3]],
            // Stephen King (index 26)
            ['Ça', 26, [0, 3]], ['Shining', 26, [0, 3]], ['Misery', 26, [0, 2]],
            ['La Ligne verte', 26, [0, 3]], ['Le Fléau', 26, [1, 3]], ['Simetierre', 26, [0, 3]],
            // Agatha Christie (index 27)
            ['Le Meurtre de Roger Ackroyd', 27, [2]], ['Dix Petits Nègres', 27, [2]],
            ['Le Crime de l\'Orient-Express', 27, [2]], ['Mort sur le Nil', 27, [2]],
            ['ABC contre Poirot', 27, [2]], ['Les Vacances d\'Hercule Poirot', 27, [2]],
            // George Orwell (index 28)
            ['1984', 28, [1, 0]], ['La Ferme des animaux', 28, [0, 6]],
            // Isaac Asimov (index 29)
            ['Fondation', 29, [1]], ['Les Robots', 29, [1]], ['Fondation et Empire', 29, [1]],
            ['Le Cycle des robots', 29, [1]], ['La Fin de l\'éternité', 29, [1]],
            // Philip K. Dick (index 30)
            ['Ubik', 30, [1]], ['Le Maître du Haut Château', 30, [1]],
            ['Les androïdes rêvent-ils de moutons électriques ?', 30, [1]],
            ['Minority Report', 30, [1]],
            // Frank Herbert (index 31)
            ['Dune', 31, [1, 3]], ['Le Messie de Dune', 31, [1, 3]],
            ['Les Enfants de Dune', 31, [1, 3]],
            // Tolkien (index 32)
            ['Le Seigneur des anneaux : La Communauté de l\'anneau', 32, [3]],
            ['Le Seigneur des anneaux : Les Deux Tours', 32, [3]],
            ['Le Seigneur des anneaux : Le Retour du roi', 32, [3]],
            ['Le Hobbit', 32, [3]], ['Le Silmarillion', 32, [3]],
            // Hemingway (index 33)
            ['Le Vieil Homme et la Mer', 33, [0]], ['Pour qui sonne le glas', 33, [0, 4]],
            ['L\'Adieu aux armes', 33, [0]], ['Paris est une fête', 33, [0, 5]],
            // García Márquez (index 34)
            ['Cent ans de solitude', 34, [0, 3]], ['L\'Amour aux temps du choléra', 34, [0]],
            ['Chronique d\'une mort annoncée', 34, [0]],
            // Murakami (index 35)
            ['Kafka sur le rivage', 35, [0, 3]], ['1Q84', 35, [0, 1]],
            ['La Ballade de l\'impossible', 35, [0]], ['Les Chroniques de l\'oiseau à ressort', 35, [0]],
            // Dostoïevski (index 36)
            ['Crime et Châtiment', 36, [0, 6]], ['Les Frères Karamazov', 36, [0, 6]],
            ['L\'Idiot', 36, [0]], ['Les Démons', 36, [0, 6]],
            // Tolstoï (index 37)
            ['Guerre et Paix', 37, [0, 4]], ['Anna Karénine', 37, [0]],
            ['La Mort d\'Ivan Ilitch', 37, [0, 6]], ['Résurrection', 37, [0]],
            // Kafka (index 38)
            ['La Métamorphose', 38, [0, 6]], ['Le Procès', 38, [0, 6]], ['Le Château', 38, [0]],
            // Borges (index 39)
            ['Fictions', 39, [0, 3]], ['L\'Aleph', 39, [0, 3]],
            // Virginia Woolf (index 40)
            ['Mrs Dalloway', 40, [0]], ['La Promenade au phare', 40, [0]], ['Orlando', 40, [0, 3]],
            // Jane Austen (index 41)
            ['Orgueil et Préjugés', 41, [0]], ['Raison et Sentiments', 41, [0]],
            ['Emma', 41, [0]], ['Persuasion', 41, [0]],
            // Mary Shelley (index 42)
            ['Frankenstein', 42, [1, 3]],
            // Edgar Allan Poe (index 43)
            ['Les Aventures d\'Arthur Gordon Pym', 43, [0, 3]], ['Histoires extraordinaires', 43, [0, 3]],
            // H.P. Lovecraft (index 44)
            ['L\'Appel de Cthulhu', 44, [0, 3]], ['Les Montagnes hallucinées', 44, [1, 3]],
            // Ray Bradbury (index 45)
            ['Fahrenheit 451', 45, [1]], ['Chroniques martiennes', 45, [1]],
            // Arthur C. Clarke (index 46)
            ['2001 : L\'Odyssée de l\'espace', 46, [1]], ['Rendez-vous avec Rama', 46, [1]],
            // Dan Brown (index 47)
            ['Da Vinci Code', 47, [0, 2]], ['Anges et Démons', 47, [0, 2]],
            ['Inferno', 47, [0, 2]], ['Origine', 47, [0, 2]],
            // Umberto Eco (index 48)
            ['Le Nom de la rose', 48, [0, 2, 4]], ['Le Pendule de Foucault', 48, [0, 2]],
            // Italo Calvino (index 49)
            ['Le Baron perché', 49, [0, 3]], ['Si par une nuit d\'hiver un voyageur', 49, [0]],
            // Milan Kundera (index 50)
            ['L\'Insoutenable Légèreté de l\'être', 50, [0, 6]], ['La Plaisanterie', 50, [0]],
            ['L\'Immortalité', 50, [0, 6]],
            // Paulo Coelho (index 51)
            ['L\'Alchimiste', 51, [0, 6]], ['Brida', 51, [0]],
            // Khaled Hosseini (index 52)
            ['Les Cerfs-volants de Kaboul', 52, [0]], ['Mille soleils splendides', 52, [0]],
            // Chimamanda Ngozi Adichie (index 53)
            ['Americanah', 53, [0]], ['L\'Hibiscus pourpre', 53, [0]],
            // Toni Morrison (index 54)
            ['Beloved', 54, [0, 4]], ['L\'Œil le plus bleu', 54, [0]],
            // Françoise Sagan (index 55)
            ['Bonjour tristesse', 55, [0]], ['Aimez-vous Brahms...', 55, [0]],
            // Romain Gary (index 56)
            ['La Vie devant soi', 56, [0]], ['Les Racines du ciel', 56, [0]],
            ['La Promesse de l\'aube', 56, [0, 5]],
            // Boris Vian (index 57)
            ['L\'Écume des jours', 57, [0, 3]], ['L\'Automne à Pékin', 57, [0]],
            ['J\'irai cracher sur vos tombes', 57, [0, 2]],
            // Albert Cohen (index 58)
            ['Belle du Seigneur', 58, [0]],
            // Marguerite Yourcenar (index 59)
            ['Mémoires d\'Hadrien', 59, [0, 4]], ['L\'Œuvre au noir', 59, [0, 4]],
        ];

        // ---------------------------------------------------------------
        // ÉTAPE 4 : Insérer les livres et leurs catégories en base
        // ---------------------------------------------------------------
        // Insérer les livres et leurs catégories
        $bookIds = []; 
        foreach ($books as $b) {
            // $b[0] = titre du livre
            // $b[1] = index de l'auteur dans $authorIds
            // $b[2] = tableau des index de catégories dans $categoryIds

            $bookId = DB::table('books')->insertGetId([
                'title'            => $b[0],
                'description'      => $faker->paragraph(3), // Description générée aléatoirement
                'publication_date' => $faker->dateTimeBetween('-80 years', '-1 year')->format('Y-m-d'),
                'cover_image'      => null, // Pas d'image de couverture (utilise l'image par défaut)
                'author_id'        => $authorIds[$b[1]], // On utilise l'ID réel de l'auteur
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
            $bookIds[] = $bookId;

            // On lie le livre à ses catégories dans la table pivot "book_category"
            foreach ($b[2] as $catIndex) {
                DB::table('book_category')->insert([
                    'book_id'     => $bookId,
                    'category_id' => $categoryIds[$catIndex], // On utilise l'ID réel de la catégorie
                ]);
            }
        }

        // ---------------------------------------------------------------
        // ÉTAPE 5 : Générer des livres supplémentaires pour atteindre ~400
        // Ces titres sont générés aléatoirement en combinant préfixes et suffixes
        // ---------------------------------------------------------------
        // Compléter jusqu'à ~400 livres avec des titres générés mais crédibles
        $prefixes = ['Le Secret de', 'Les Ombres de', 'La Dernière', 'Le Mystère de', 'Les Chroniques de',
            'Le Chemin de', 'La Nuit de', 'Le Jardin de', 'Les Portes de', 'Le Silence de',
            'La Mémoire de', 'Le Destin de', 'Les Étoiles de', 'La Promesse de', 'Le Voyage de',
            'L\'Héritage de', 'Les Sentiers de', 'La Couleur de', 'Le Serment de', 'Les Rêves de'];
        $suffixes = ['l\'aube', 'minuit', 'la mer', 'l\'oubli', 'la terre', 'l\'horizon',
            'la montagne', 'la forêt', 'l\'éternité', 'la lumière', 'la rivière',
            'l\'espoir', 'la tempête', 'l\'automne', 'la brume', 'l\'exil',
            'la mémoire', 'la nuit', 'l\'ombre', 'la source'];

        $existingCount = count($books);    // Nombre de livres déjà créés
        $remaining     = 400 - $existingCount; // Combien il en reste à créer

        for ($i = 0; $i < $remaining; $i++) {
            // On génère un titre en combinant un préfixe et un suffixe aléatoires
            // Exemple : "Le Secret de l'aube", "Les Ombres de minuit"...
            $title    = $faker->randomElement($prefixes) . ' ' . $faker->randomElement($suffixes);
            $authorId = $faker->randomElement($authorIds); // Auteur aléatoire parmi les 60

            // Entre 1 et 2 catégories aléatoires
            $nbCats       = rand(1, 2);
            $selectedCats = $faker->randomElements($categoryIds, $nbCats);

            $bookId = DB::table('books')->insertGetId([
                'title'            => $title,
                'description'      => $faker->paragraph(3),
                'publication_date' => $faker->dateTimeBetween('-30 years', '-1 year')->format('Y-m-d'),
                'cover_image'      => null,
                'author_id'        => $authorId,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
            $bookIds[] = $bookId;

            // On lie le livre à ses catégories
            foreach ($selectedCats as $catId) {
                DB::table('book_category')->insert([
                    'book_id'     => $bookId,
                    'category_id' => $catId,
                ]);
            }
        }
        // Résultat final : ~400 livres dans la base, répartis sur 60 auteurs et 10 catégories
    }
}
