<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 3 bibliothécaires
        $librarians = [
            ['name' => 'Dupont', 'prenom' => 'Marie', 'email' => 'marie.dupont@bookhub.fr'],
            ['name' => 'Bernard', 'prenom' => 'Jean', 'email' => 'jean.bernard@bookhub.fr'],
            ['name' => 'Lefebvre', 'prenom' => 'Claire', 'email' => 'claire.lefebvre@bookhub.fr'],
        ];

        foreach ($librarians as $l) {
            DB::table('users')->insert([
                'name' => $l['name'],
                'prenom' => $l['prenom'],
                'email' => $l['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 25 usagers
        $users = [
            ['name' => 'Martin', 'prenom' => 'Lucas'],
            ['name' => 'Petit', 'prenom' => 'Emma'],
            ['name' => 'Moreau', 'prenom' => 'Hugo'],
            ['name' => 'Garcia', 'prenom' => 'Léa'],
            ['name' => 'Roux', 'prenom' => 'Nathan'],
            ['name' => 'Fournier', 'prenom' => 'Chloé'],
            ['name' => 'Girard', 'prenom' => 'Louis'],
            ['name' => 'Bonnet', 'prenom' => 'Manon'],
            ['name' => 'Lambert', 'prenom' => 'Jules'],
            ['name' => 'Fontaine', 'prenom' => 'Camille'],
            ['name' => 'Rousseau', 'prenom' => 'Théo'],
            ['name' => 'Vincent', 'prenom' => 'Inès'],
            ['name' => 'Mercier', 'prenom' => 'Raphaël'],
            ['name' => 'Faure', 'prenom' => 'Jade'],
            ['name' => 'André', 'prenom' => 'Arthur'],
            ['name' => 'Blanc', 'prenom' => 'Louise'],
            ['name' => 'Chevalier', 'prenom' => 'Gabriel'],
            ['name' => 'Gautier', 'prenom' => 'Alice'],
            ['name' => 'Duval', 'prenom' => 'Adam'],
            ['name' => 'Lemoine', 'prenom' => 'Lina'],
            ['name' => 'Picard', 'prenom' => 'Ethan'],
            ['name' => 'Renard', 'prenom' => 'Sarah'],
            ['name' => 'Colin', 'prenom' => 'Noah'],
            ['name' => 'Mathieu', 'prenom' => 'Eva'],
            ['name' => 'Perrin', 'prenom' => 'Tom'],
        ];

        foreach ($users as $u) {
            DB::table('users')->insert([
                'name' => $u['name'],
                'prenom' => $u['prenom'],
                'email' => strtolower($u['prenom'] . '.' . $u['name']) . '@email.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}