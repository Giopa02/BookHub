<?php

//modèle central de l'application : gère les comptes utilisateurs.
// Un utilisateur peut être :
//   - Un usager (role_id = 2) : peut emprunter des livres
//   - Un bibliothécaire (role_id = 1) : peut gérer les exemplaires et voir les usagers
//
// contient aussi les fonctionnalités de sécurité avancées :
//   - Double authentification (2FA) : code à 6 chiffres envoyé par email
//   - Historique des mots de passe

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Permet de créer des utilisateurs de test
use Illuminate\Notifications\Notifiable;   

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Liste des champs qu'on peut remplir automatiquement (protection contre les attaques)
    protected $fillable = [
        'name',                   // Nom de famille
        'prenom',                 // Prénom
        'email',                  // Adresse email (unique, sert d'identifiant)
        'password',               // Mot de passe (toujours stocké chiffré)
        'role_id',                // 1 = bibliothécaire, 2 = usager
        'two_factor_code',        // Code 2FA à 6 chiffres 
        'two_factor_expires_at',  // Date/heure d'expiration du code 2FA
        'password_changed_at',    // Date du dernier changement de mot de passe
    ];

    // Liste des champs qui ne sont JAMAIS inclus dans les réponses JSON (protection : ces données ne doivent pas fuiter)
    protected $hidden = [
        'password',         // Le mot de passe ne doit jamais être visible
        'remember_token',   // Token de "se souvenir de moi"
        'two_factor_code',  // Le code 2FA ne doit pas être exposé
    ];

    // "Casts" = indique à Laravel comment convertir automatiquement certains champs
    // Par exemple, "two_factor_expires_at" sera automatiquement converti en objet DateTime
    // ce qui permet d'utiliser des méthodes comme ->isPast() 
    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',        
            'two_factor_expires_at' => 'datetime',      
            'password_changed_at' => 'datetime',        
            'password'            => 'hashed',          
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
        // Utilisation : $user->role->role  → donne "bibliothécaire" ou "usager"
    }

    // RELATION : Un utilisateur peut avoir PLUSIEURS emprunts
    public function borrows()
    {
        return $this->hasMany(Borrow::class);
        // Utilisation : $user->borrows  → donne tous les emprunts de cet utilisateur
    }

    // RELATION : Un utilisateur a PLUSIEURS entrées dans l'historique de mots de passe
    public function passwordHistories()
    {
        return $this->hasMany(PasswordHistory::class);
        // Utilisation : $user->passwordHistories  → donne l'historique de ses mots de passe
    }

    /**
     * Génère un code 2FA à 6 chiffres et l'enregistre en base de données
     * str_pad complète avec des zéros devant si nécessaire (ex: 42 → "000042")
     */
    public function generateTwoFactorCode(): string
    {
        // On génère un code aléatoire à 6 chiffres
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // sauvegarde ce code en base avec sa date d'expiration (dans 10 minutes)
        $this->update([
            'two_factor_code'       => $code,
            'two_factor_expires_at' => now()->addMinutes(10), // now() = maintenant, +10 minutes
        ]);

        return $code; // retourne le code pour pouvoir l'envoyer par email
    }

    /**
     * Réinitialise le code 2FA après utilisation
     * Le code est remis à null pour qu'il ne puisse pas être réutilisé
     */
    public function resetTwoFactorCode(): void
    {
        $this->update([
            'two_factor_code'       => null, // efface le code
            'two_factor_expires_at' => null, // efface la date d'expiration
        ]);
    }
}
