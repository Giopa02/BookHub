<?php

// Ce fichier de migration crée deux tables :
//   1. "statuses" : les statuts possibles d'un exemplaire (disponible, emprunté)
//   2. "copies"   : les exemplaires physiques de chaque livre

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée les tables "statuses" et "copies"
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : statuses
        // Contient les statuts possibles des exemplaires
        //   id=1 → "disponible"
        //   id=2 → "emprunté"
        // ---------------------------------------------------------------
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status'); // Libellé du statut (ex: "disponible", "emprunté")
            $table->timestamps();
        });

        // ---------------------------------------------------------------
        // TABLE : copies
        // Contient les exemplaires physiques de chaque livre
        // ---------------------------------------------------------------
        Schema::create('copies', function (Blueprint $table) {
            $table->id();
            $table->date('commission_date');                       // Date à laquelle cet exemplaire a été intégré à la bibliothèque
            $table->foreignId('book_id')->constrained('books');    // Quel livre représente cet exemplaire
            $table->foreignId('status_id')->constrained('statuses'); 
            $table->timestamps();
        });
    }

    /**
     * Annule la migration : supprime "copies" avant "statuses"
     * car "copies" dépend de "statuses"
     */
    public function down(): void
    {
        Schema::dropIfExists('copies');
        Schema::dropIfExists('statuses');
    }
};
