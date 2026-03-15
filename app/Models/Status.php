<?php


// Un statut indique si un exemplaire est disponible ou non :
//   - status_id = 1 → "disponible" (peut être emprunté)
//   - status_id = 2 → "emprunté"   (déjà pris par quelqu'un)
// Ce statut est mis à jour automatiquement quand un emprunt est créé ou retourné.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    // Champs autorisés à être remplis automatiquement
    protected $fillable = ['status']; // "status" = "disponible", "emprunté"

    public function copies()
    {
        return $this->hasMany(Copy::class);
        // Utilisation : $status->copies  → donne tous les exemplaires ayant ce statut
    }
}
