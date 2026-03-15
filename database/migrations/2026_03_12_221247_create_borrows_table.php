<?php

// Ce fichier de migration crée deux tables :
//   1. "borrows"     : les emprunts (qui a emprunté, quand, retourné quand)
//   2. "borrow_copy" : table pivot reliant emprunts et exemplaires
//
// Un emprunt peut concerner jusqu'à 5 exemplaires 
// La table "borrow_copy" fait le lien entre un emprunt et ses exemplaires,
//   - Un emprunt peut avoir plusieurs exemplaires
//   - Un exemplaire peut avoir été dans plusieurs emprunts (à des dates différentes)

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée les tables "borrows" et "borrow_copy"
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : borrows
        // Chaque ligne représente un emprunt fait par un usager
        // ---------------------------------------------------------------
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();
            $table->date('borrowing_date');                      // Date à laquelle l'emprunt a commencé
            $table->date('return_date')->nullable();             // Date de retour (NULL = l'emprunt est encore en cours !)
            $table->foreignId('user_id')->constrained('users'); // Quel usager a fait cet emprunt
            $table->timestamps();
        });

        // ---------------------------------------------------------------
        // TABLE : borrow_copy (table pivot)
        // Relie chaque emprunt à ses exemplaires
        // ---------------------------------------------------------------
        Schema::create('borrow_copy', function (Blueprint $table) {
            $table->foreignId('borrow_id')->constrained('borrows')->onDelete('cascade');
            // Si un emprunt est supprimé, ses liens vers les exemplaires sont aussi supprimés
            $table->foreignId('copy_id')->constrained('copies')->onDelete('cascade');
            // Si un exemplaire est supprimé, ses liens vers les emprunts sont aussi supprimés

            // La combinaison (borrow_id + copy_id) est unique : un exemplaire ne peut être dans le même emprunt qu'une seule fois
            $table->primary(['borrow_id', 'copy_id']);
        });
    }

    /**
     * Annule la migration : on supprime d'abord "borrow_copy" (table pivot)
     * puis "borrows"
     */
    public function down(): void
    {
        Schema::dropIfExists('borrow_copy');
        Schema::dropIfExists('borrows');
    }
};
