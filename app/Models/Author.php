<?php

// un "Modèle" correspond à une table en base de données.
// Ce modèle permet de :
//   - Lire, créer, modifier, supprimer des auteurs
//   - Accéder aux livres d'un auteur via une "relation"

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 

class Author extends Model
{
    // $fillable liste les champs qu'on a le droit de remplir en masse (via create() ou update())
    // C'est une protection de sécurité : on ne peut pas modifier des champs non listés ici
    protected $fillable = ['name']; // Un auteur n'a qu'un seul champ : son nom

    public function books()
    {
        // On dit à Laravel : "un auteur possède plusieurs livres"
        // Laravel sait automatiquement chercher les livres via le champ "author_id"
        return $this->hasMany(Book::class);
    }
}
