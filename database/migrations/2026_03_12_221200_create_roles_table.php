<?php

// "migration" : c'est un script qui crée ou modifie des tables en base de données. permettent de versionner la structure de la base de données, comme on versionne du code avec Git.
//
// exécuter les migrations : php artisan migrate
// annuler la dernière migration : php artisan migrate:rollback
//
// Cette migration crée la table "roles" qui contient les rôles des utilisateurs :
//   - bibliothécaire (id = 1)
//   - usager (id = 2)

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;   // Permet de définir les colonnes de la table
use Illuminate\Support\Facades\Schema;      // Permet d'interagir avec la base de données

return new class extends Migration
{
    /**
     * La méthode up() crée la table dans la base de données
     */
    public function up(): void
    {
        // Schema::create() crée une nouvelle table nommée "roles"
        Schema::create('roles', function (Blueprint $table) {
            $table->id();           // Colonne "id" : identifiant auto-incrémenté (1, 2, 3...)
            $table->string('role'); // Colonne "role" : le nom du rôle (ex: "bibliothécaire")
            $table->timestamps();   // Ajoute automatiquement "created_at" et "updated_at"
                                    // Ces colonnes enregistrent quand un enregistrement a été créé/modifié
        });
    }

    /**
     * La méthode down() est appelée lors du "php artisan migrate:rollback"
     * Elle annule ce que up() a fait → supprime la table
     */
    public function down(): void
    {
        Schema::dropIfExists('roles'); // Supprime la table "roles" si elle existe
    }
};
