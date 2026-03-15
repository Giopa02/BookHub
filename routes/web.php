<?php

// Ce fichier est le "routeur" de l'application.
// C'est ici qu'on définit toutes les URLs (adresses web) que l'application accepte, et quelle fonction (dans quel Controller) doit être appelée pour chaque URL.
//
// Les méthodes HTTP :
//   - GET  : afficher une page (lecture)
//   - POST : envoyer des données d'un formulaire (création)
//   - PUT  : modifier entièrement une ressource
//   - PATCH: modifier partiellement une ressource
//   - DELETE: supprimer une ressource

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;   // Gère les utilisateurs (connexion, inscription...)
use App\Http\Controllers\BorrowController; // Gère les emprunts
use App\Http\Controllers\SearchController; // Gère la recherche de livres
use App\Http\Controllers\CopyController;   // Gère les exemplaires


// ===========================================================================
// PAGE D'ACCUEIL
// ===========================================================================
// On récupère 8 livres au hasard pour les afficher en page d'accueil (la racine du site (/))
Route::get('/', function () {
    $books = \App\Models\Book::with('author')->inRandomOrder()->limit(8)->get();
    // inRandomOrder() = ordre aléatoire à chaque visite
    // limit(8) = on n'en affiche que 8
    return view('index', compact('books'));
});


// ===========================================================================
// AUTHENTIFICATION (Inscription / Connexion / Déconnexion)
// ===========================================================================

// Affiche le formulaire d'inscription
Route::get('/subscription', [UserController::class, 'subscription']);

// Traite le formulaire d'inscription (crée le compte)
Route::post('/subscription', [UserController::class, 'register']);

// Affiche le formulaire de connexion
Route::get('/connect', [UserController::class, 'connect']);

// Traite le formulaire de connexion (vérifie email + mdp, envoie code 2FA)
Route::post('/connect', [UserController::class, 'login']);

// Déconnecte l'utilisateur et redirige vers l'accueil
Route::get('/logout', [UserController::class, 'logout']);


// ===========================================================================
// DOUBLE AUTHENTIFICATION (2FA)
// Étape 2 de la connexion : l'utilisateur doit saisir le code reçu par email
// ===========================================================================

// Affiche le formulaire de saisie du code 2FA
Route::get('/verify-2fa', [UserController::class, 'showTwoFactorForm']);

// Vérifie le code 2FA saisi et connecte l'utilisateur si valide
Route::post('/verify-2fa', [UserController::class, 'verifyTwoFactor']);

// Renvoie un nouveau code 2FA par email (si l'ancien a expiré)
Route::get('/resend-2fa', [UserController::class, 'resendTwoFactorCode']);


// ===========================================================================
// PROFIL & MOT DE PASSE
// ===========================================================================

// Affiche la page de profil de l'utilisateur connecté
Route::get('/profil', [UserController::class, 'personnalProfil']);

// Affiche le formulaire de changement de mot de passe
Route::get('/change-password', [UserController::class, 'showChangePassword']);

// Traite le formulaire de changement de mot de passe (avec vérifications de sécurité)
Route::post('/change-password', [UserController::class, 'changePassword']);


// ===========================================================================
// CATALOGUE & RECHERCHE
// ===========================================================================

// Recherche depuis la barre de recherche du header (formulaire GET avec ?params=...)
Route::get('/search', [SearchController::class, 'searchForm']);

// Recherche avec le mot-clé directement dans l'URL (/search/Hugo, /search/all)
Route::get('/search/{params}', [SearchController::class, 'search']);

// Affiche le détail d'un livre et ses exemplaires
// {id} = l'identifiant du livre dans la base de données
Route::get('/exemplar/{id}', [CopyController::class, 'exemplar']);


// ===========================================================================
// EMPRUNTS
// ===========================================================================

// Affiche la page "Mes emprunts" de l'utilisateur connecté
Route::get('/borrowing', [BorrowController::class, 'borrowing']);

// Emprunte un exemplaire (crée ou met à jour un emprunt)
// {id} = l'identifiant de l'exemplaire à emprunter
Route::post('/borrowing/{id}', [BorrowController::class, 'borrow']);

// Enregistre le retour d'un emprunt (marque la date de retour)
// PATCH = modification partielle (on ne modifie que la date de retour)
// {id} = l'identifiant de l'emprunt à clôturer
Route::patch('/return/{id}', [BorrowController::class, 'return']);


// ===========================================================================
// BACK-OFFICE (réservé aux bibliothécaires, role_id = 1)
// Ces pages sont protégées dans les Controllers (vérification du rôle)
// ===========================================================================

// Liste de tous les usagers
Route::get('/bo/profils', [UserController::class, 'profils']);

// Profil détaillé d'un usager spécifique
Route::get('/bo/profil/{id}', [UserController::class, 'profil']);

// Liste de tous les exemplaires avec recherche et pagination
Route::get('/bo/copies', [CopyController::class, 'copies']);

// Formulaire d'ajout d'un nouvel exemplaire
Route::get('/bo/exemplar/add', [CopyController::class, 'add']);

// Traite l'ajout d'un exemplaire (soumission du formulaire)
Route::post('/bo/exemplar/add', [CopyController::class, 'store']);

// Formulaire de modification d'un exemplaire existant
Route::get('/bo/exemplar/update/{id}', [CopyController::class, 'edit']);

// Traite la modification d'un exemplaire (PUT = mise à jour complète)
Route::put('/bo/exemplar/update/{id}', [CopyController::class, 'update']);

// Supprime un exemplaire définitivement
Route::delete('/bo/exemplar/delete/{id}', [CopyController::class, 'delete']);

// Affiche le détail d'un exemplaire dans le Back-Office
Route::get('/bo/exemplar/{id}', [CopyController::class, 'show']);
