<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    // Liste des champs autorisés à être remplis automatiquement
    protected $fillable = [
        'title',            // Titre du livre
        'description',      // Résumé / description
        'publication_date', // Date de publication
        'cover_image',      // Chemin vers l'image de couverture
        'author_id',        // Clé étrangère vers la table "authors"
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
        // Utilisation : $book->author->name  → donne le nom de l'auteur du livre
    }

    // RELATION : Un livre peut avoir PLUSIEURS catégories
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category');
        // Utilisation : $book->categories  → donne toutes les catégories du livre
    }

    // RELATION : Un livre peut avoir PLUSIEURS exemplaires physiques
    public function copies()
    {
        return $this->hasMany(Copy::class);
        // Utilisation : $book->copies  → donne tous les exemplaires du livre
        // On peut ensuite filtrer : $book->copies->where('status_id', 1) → que les disponibles
    }
}
