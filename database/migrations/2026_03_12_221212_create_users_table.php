<?php

// Ce fichier de migration crée deux tables :
//   1. La table "users" : contient les comptes des utilisateurs (usagers et bibliothécaires)
//   2. La table "sessions" : stocke les sessions de navigation (qui est connecté, depuis quel IP...)
//
// Note : la table "roles" doit être créée AVANT "users" car "users" contient un champ "role_id" qui pointe vers "roles". C'est pourquoi ce fichier a un numéro de date/heure plus grand que celui de roles.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée les tables "users" et "sessions"
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                      // Identifiant unique auto-incrémenté
            $table->string('name');                            // Nom de famille
            $table->string('prenom');                          // Prénom
            $table->string('email')->unique();                 // Email : doit être unique 
            $table->timestamp('email_verified_at')->nullable(); // Date de vérification de l'email (peut être vide)
            $table->string('password');                        // Mot de passe chiffré 
            $table->foreignId('role_id')->constrained('roles'); // Lien vers la table "roles" (1=bibliothécaire, 2=usager)
                                                               // constrained() = contrainte de clé étrangère (l'ID doit exister dans roles)
            $table->timestamps();                              // created_at et updated_at
        });

        // ---------------------------------------------------------------
        // TABLE : sessions
        // Stocke les données de session de chaque visiteur connecté
        // ---------------------------------------------------------------
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();              // Identifiant unique de la session (clé primaire)
            $table->foreignId('user_id')->nullable()->index(); // Lien vers l'utilisateur (null si non connecté)
            $table->string('ip_address', 45)->nullable(); // Adresse IP du visiteur (max 45 car IPv6)
            $table->text('user_agent')->nullable();       // Navigateur utilisé (Chrome, Firefox...)
            $table->longText('payload');                  // Données de session (sérialisées)
            $table->integer('last_activity')->index();    // Timestamp de la dernière activité
        });
    }

    /**
     * Annule la migration : supprime les tables dans l'ordre inverse
     * (sessions d'abord car elle dépend de users)
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
