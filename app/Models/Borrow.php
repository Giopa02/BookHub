<?php

// Un emprunt peut contenir jusqu'à 5 exemplaires

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    // Champs autorisés à être remplis automatiquement
    protected $fillable = [
        'borrowing_date',
        'return_date',    // NULL si l'emprunt est encore en cours
        'user_id',
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
