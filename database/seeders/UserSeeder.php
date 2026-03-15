<?php

// Ce Seeder crée des utilisateurs de test dans la base de données :
//   - 3 bibliothécaires (qui auront accès au Back-Office)
//   - 25 usagers (qui pourront emprunter des livres)
//
// Tous les mots de passe sont "password".

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Pour chiffrer les mots de passe

class UserSeeder extends Seeder
{
    /**
     * Crée les bibliothécaires et les usagers
     */
    public function run(): void
    {
        // ---------------------------------------------------------------
        // 3 bibliothécaires (role_id = 1)
        // Ces comptes ont accès au Back-Office de l'application
        // ---------------------------------------------------------------
        $librarians = [
            ['name' => 'Dupont',   'prenom' => 'Marie',  'email' => 'marie.dupont@bookhub.fr'],
            ['name' => 'Bernard',  'prenom' => 'Jean',   'email' => 'jean.bernard@bookhub.fr'],
            ['name' => 'Lefebvre', 'prenom' => 'Claire', 'email' => 'claire.lefebvre@bookhub.fr'],
        ];

        foreach ($librarians as $l) {
            DB::table('users')->insert([
                'name'              => $l['name'],
                'prenom'            => $l['prenom'],
                'email'             => $l['email'],
                'email_verified_at' => now(),
                'password'          => Hash::make('password'), // Mot de passe chiffré
                'role_id'           => 1,   // 1 = bibliothécaire
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }

        // ---------------------------------------------------------------
        // 25 usagers (role_id = 2)
        // Ces comptes peuvent parcourir le catalogue et emprunter des livres
        // ---------------------------------------------------------------
        $users = [
            ['name' => 'Martin',    'prenom' => 'Lucas'],
            ['name' => 'Petit',     'prenom' => 'Emma'],
            ['name' => 'Moreau',    'prenom' => 'Hugo'],
            ['name' => 'Garcia',    'prenom' => 'Léa'],
            ['name' => 'Roux',      'prenom' => 'Nathan'],
            ['name' => 'Fournier',  'prenom' => 'Chloé'],
            ['name' => 'Girard',    'prenom' => 'Louis'],
            ['name' => 'Bonnet',    'prenom' => 'Manon'],
            ['name' => 'Lambert',   'prenom' => 'Jules'],
            ['name' => 'Fontaine',  'prenom' => 'Camille'],
            ['name' => 'Rousseau',  'prenom' => 'Théo'],
            ['name' => 'Vincent',   'prenom' => 'Inès'],
            ['name' => 'Mercier',   'prenom' => 'Raphaël'],
            ['name' => 'Faure',     'prenom' => 'Jade'],
            ['name' => 'André',     'prenom' => 'Arthur'],
            ['name' => 'Blanc',     'prenom' => 'Louise'],
            ['name' => 'Chevalier', 'prenom' => 'Gabriel'],
            ['name' => 'Gautier',   'prenom' => 'Alice'],
            ['name' => 'Duval',     'prenom' => 'Adam'],
            ['name' => 'Lemoine',   'prenom' => 'Lina'],
            ['name' => 'Picard',    'prenom' => 'Ethan'],
            ['name' => 'Renard',    'prenom' => 'Sarah'],
            ['name' => 'Colin',     'prenom' => 'Noah'],
            ['name' => 'Mathieu',   'prenom' => 'Eva'],
            ['name' => 'Perrin',    'prenom' => 'Tom'],
        ];

        foreach ($users as $u) {
            DB::table('users')->insert([
                'name'              => $u['name'],
                'prenom'            => $u['prenom'],
                // L'email est généré automatiquement à partir du prénom et du nom
                'email'             => strtolower($u['prenom'] . '.' . $u['name']) . '@email.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role_id'           => 2,   // 2 = usager
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}
