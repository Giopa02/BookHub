<?php

// Un "exemplaire" = un livre physique précis dans la bibliothèque.
// Chaque exemplaire est lié à un livre, possède un statut et un état physique.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Copy extends Model
{
    // Champs autorisés à être remplis automatiquement
    protected $fillable = [
        'commission_date', // Date de mise en service de cet exemplaire dans la bibliothèque
        'book_id',         // Clé étrangère : quel livre représente cet exemplaire
        'status_id',       // Clé étrangère : disponible (1) ou emprunté (2)
        'etat',            // État physique : 'excellent', 'bon', ou 'moyen'
    ];

    // RELATION : Un exemplaire appartient à UN livre
    public function book()
    {
        return $this->belongsTo(Book::class);
        // Utilisation : $copy->book->title  → donne le titre du livre de cet exemplaire
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
        // Utilisation : $copy->status->status  → donne "disponible" ou "emprunté"
    }

    // RELATION : Un exemplaire peut avoir été dans PLUSIEURS emprunts (à des dates différentes)
    public function borrows()
    {
        return $this->belongsToMany(Borrow::class, 'borrow_copy');
        // Utilisation : $copy->borrows  → donne tous les emprunts de cet exemplaire
    }
}
