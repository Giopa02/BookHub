<?php

// Ce fichier représente le modèle "PasswordHistory" (Historique des mots de passe).

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordHistory extends Model
{
    // Champs autorisés à être remplis automatiquement
    protected $fillable = [
        'user_id',  // Identifiant de l'utilisateur concerné
        'password', // Mot de passe chiffré (jamais stocké en clair !)
    ];

    // RELATION : Un historique de mot de passe appartient à UN utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
        // Utilisation : $history->user  → donne l'utilisateur associé à cet historique
    }
}
