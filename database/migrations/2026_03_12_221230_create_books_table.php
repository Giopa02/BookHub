<?php

// Ce fichier de migration crée quatre tables liées aux livres :
//   1. "authors"       : les auteurs
//   2. "categories"    : les catégories de livres (Roman, SF, Policier...)
//   3. "books"         : les livres eux-mêmes
//   4. "book_category" : table intermédiaire pour lier livres et catégories
//
// La table "book_category" est une "table pivot" ou "table de jointure". Elle existe parce qu'un livre peut avoir plusieurs catégories

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : authors
        // Contient les auteurs des livres
        // ---------------------------------------------------------------
        Schema::create('authors', function (Blueprint $table) {
            $table->id();           
            $table->string('name'); 
            $table->timestamps();
        });

        // ---------------------------------------------------------------
        // TABLE : categories
        // Contient les genres/catégories de livres
        // ---------------------------------------------------------------
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('libelle'); 
            $table->timestamps();
        });

        // ---------------------------------------------------------------
        // TABLE : books
        // Contient les livres avec leurs métadonnées
        // ---------------------------------------------------------------
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');                              // Titre du livre
            $table->text('description')->nullable();              // Description/résumé (peut être vide)
            $table->date('publication_date')->nullable();         // Date de publication (peut être vide)
            $table->string('cover_image')->nullable();            // Image de couverture (chemin vers le fichier)
            $table->foreignId('author_id')->constrained('authors'); // Lien vers l'auteur (doit exister dans "authors")
            $table->timestamps();
        });

        // ---------------------------------------------------------------
        // TABLE : book_category (table pivot / de jointure)
        // Relie les livres à leurs catégories
        // "Les Misérables" → "Roman" et "Histoire"
        // ---------------------------------------------------------------
        Schema::create('book_category', function (Blueprint $table) {
            $table->foreignId('book_id')    ->constrained('books')     ->onDelete('cascade');
            // Si un livre est supprimé, ses liens vers les catégories sont aussi supprimés
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            // Si une catégorie est supprimée, ses liens vers les livres sont aussi supprimés

            // La clé primaire est la combinaison des deux colonnes → un livre ne peut pas être lié deux fois à la même catégorie
            $table->primary(['book_id', 'category_id']);
        });
    }

    /**
     * Annule la migration dans l'ordre inverse
     * (on supprime d'abord la table pivot, puis les tables qu'elle référence)
     */
    public function down(): void
    {
        Schema::dropIfExists('book_category'); // En premier car elle dépend de books et categories
        Schema::dropIfExists('books');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('authors');
    }
};
