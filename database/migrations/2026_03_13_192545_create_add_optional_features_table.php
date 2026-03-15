<?php

// Ce fichier de migration ajoute les fonctionnalités optionnelles au projet.
// Au lieu de créer de nouvelles tables depuis zéro, elle MODIFIE des tables existantes
// et en CRÉE une nouvelle.
//
// Les trois fonctionnalités ajoutées :
//   1. Double Authentification (2FA) : ajout de colonnes dans "users"
//   2. État physique des exemplaires : ajout de la colonne "etat" dans "copies" → Un exemplaire peut être en état "excellent", "bon" ou "moyen"
//   3. Historique des mots de passe : création de la table "password_histories" → On stocke les derniers mots de passe pour éviter leur réutilisation (RGPD)

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les colonnes et la table des fonctionnalités optionnelles
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // FONCTIONNALITÉ 1 : Double Authentification (2FA)
        // On ajoute 3 nouvelles colonnes à la table "users"
        // ---------------------------------------------------------------
        // 2FA : ajouter les champs sur users
        Schema::table('users', function (Blueprint $table) {
            // Code à 6 chiffres envoyé par email lors de la connexion
            // nullable = peut être vide (null quand l'utilisateur n'est pas en train de se connecter)
            $table->string('two_factor_code', 6)->nullable()->after('password');

            // Date et heure d'expiration du code 2FA (le code expire après 10 minutes)
            $table->timestamp('two_factor_expires_at')->nullable()->after('two_factor_code');

            // Date du dernier changement de mot de passe (utile pour la sécurité RGPD)
            $table->timestamp('password_changed_at')->nullable()->after('two_factor_expires_at');
        });

        // ---------------------------------------------------------------
        // FONCTIONNALITÉ 2 : État physique des exemplaires
        // ---------------------------------------------------------------
        // État des exemplaires (excellent, bon, moyen)
        Schema::table('copies', function (Blueprint $table) {
            // enum = valeurs prédéfinies (l'utilisateur ne peut pas mettre n'importe quoi)
            // default('bon') = si aucune valeur n'est précisée, l'état est "bon" par défaut
            $table->enum('etat', ['excellent', 'bon', 'moyen'])->default('bon')->after('status_id');
        });

        // ---------------------------------------------------------------
        // FONCTIONNALITÉ 3 : Historique des mots de passe 
        // On crée une nouvelle table pour stocker les anciens mots de passe
        // ---------------------------------------------------------------
        // Historique des mots de passe (5 derniers)
        Schema::create('password_histories', function (Blueprint $table) {
            $table->id();

            // Lien vers utilisateur concerné
            // onDelete('cascade') = si l'utilisateur est supprimé, son historique l'est aussi
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Le mot de passe stocké, toujours chiffré 
            $table->string('password');

            $table->timestamps(); // created_at permet de savoir quand le mot de passe a été changé
        });
    }

    /**
     * Annule toutes les modifications faites dans up()
     */
    public function down(): void
    {
        // On supprime les 3 colonnes ajoutées dans "users"
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['two_factor_code', 'two_factor_expires_at', 'password_changed_at']);
        });

        // On supprime la colonne "etat" ajoutée dans "copies"
        Schema::table('copies', function (Blueprint $table) {
            $table->dropColumn('etat');
        });

        // On supprime la table d'historique des mots de passe
        Schema::dropIfExists('password_histories');
    }
};
