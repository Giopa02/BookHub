<?php

// Un emprunt peut contenir jusqu'à 5 exemplaires (règle métier).
// La liste des exemplaires d'un emprunt est stockée dans la table "borrow_copy".

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    // Champs autorisés à être remplis automatiquement
    protected $fillable = [
        'borrowing_date', // Date de l'emprunt (ex: 2026-03-15)
        'return_date',    // Date de retour, NULL si l'emprunt est encore en cours
        'user_id',        // Identifiant de l'usager qui a fait l'emprunt
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
        // Utilisation : $borrow->user->name  → donne le nom de l'usager
    }

    public function copies()
    {
        return $this->belongsToMany(Copy::class, 'borrow_copy');
        // Utilisation : $borrow->copies  → donne tous les exemplaires de cet emprunt
    }
}
