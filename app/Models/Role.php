<?php

// il existe deux rôles :
//   - role_id = 1 → bibliothécaire (accès au Back-Office)
//   - role_id = 2 → usager (accès aux fonctions d'emprunt)
// Chaque utilisateur a exactement un rôle.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // Champs autorisés à être remplis automatiquement
    protected $fillable = ['role']; 

    // RELATION : Un rôle peut être attribué à PLUSIEURS utilisateurs
    
    public function users()
    {
        return $this->hasMany(User::class);
        // Utilisation : $role->users  → donne tous les utilisateurs ayant ce rôle
    }
}
