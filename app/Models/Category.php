<?php

// Les catégories permettent de classer les livres par genre :
// Roman, Science-Fiction, Policier, Fantasy, Histoire, etc.
//
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Champs autorisés à être remplis automatiquement
    protected $fillable = ['libelle']; // "libelle" = le nom de la catégorie (ex: "Roman", "Policier")

    // RELATION : Une catégorie peut contenir PLUSIEURS livres et un livre peut appartenir à PLUSIEURS catégories
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_category');
        // Utilisation : $category->books  → donne tous les livres de cette catégorie
    }
}
