<?php

// "Controller" : c'est lui qui reçoit les actions de l'utilisateur, fait le lien entre ce que voit l'utilisateur (la vue) et la base de données (les modèles).

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Auth permet de savoir qui est connecté
use App\Models\Borrow;               // Le modèle Borrow représente un emprunt en base de données
use App\Models\Copy;                 // Le modèle Copy représente un exemplaire physique d'un livre

class BorrowController extends Controller
{
    // -----------------------------------------------------------------------
    // Affiche la page "Mes emprunts" de l'utilisateur connecté
    // URL : GET /borrowing
    // -----------------------------------------------------------------------
    public function borrowing()
    {
        // récupère l'utilisateur actuellement connecté
        $user = Auth::user();

        // On charge ses emprunts avec toutes les infos liées :
        // "eager loading" : on charge tout d'un coup pour éviter des dizaines de requêtes
        $user->load('borrows.copies.book.author');

        // On sépare l'emprunt en cours (sans date de retour) des emprunts passés (avec date de retour)
        $currentBorrow = $user->borrows->whereNull('return_date')->first(); 
        $borrowHistory = $user->borrows->whereNotNull('return_date')->sortByDesc('borrowing_date'); 
        // On envoie ces données à la vue "borrowing.blade.php" pour les afficher
        return view('borrowing', compact('currentBorrow', 'borrowHistory'));
    }

    // -----------------------------------------------------------------------
    // Crée un emprunt ou ajoute un exemplaire à un emprunt existant
    // URL : POST /borrowing/{id}   (id = identifiant de l'exemplaire à emprunter)
    // -----------------------------------------------------------------------
    public function borrow($id)
    {
        // récupère l'utilisateur connecté
        $user = Auth::user();

        // On cherche si l'utilisateur a déjà un emprunt en cours (sans date de retour)
        $existingBorrow = Borrow::where('user_id', $user->id)
            ->whereNull('return_date')
            ->first();

        if ($existingBorrow) {
            // Si emprunt en cours a moins de 5 exemplaires, on ajoute
            // Règle métier : pas  plus de 5 exemplaires en même temps
            if ($existingBorrow->copies()->count() >= 5) {
                return back()->with('error', 'Vous avez atteint le maximum de 5 exemplaires par emprunt.');
            }

            // vérifie que l'exemplaire demandé est bien dispo (status_id = 1 = disponible)
            $copy = Copy::findOrFail($id); // findOrFail : si l'exemplaire n'existe pas → erreur 404
            if ($copy->status_id !== 1) {
                return back()->with('error', 'Cet exemplaire n\'est pas disponible.');
            }

            // attache l'exemplaire à l'emprunt existant (table pivot borrow_copy)
            $existingBorrow->copies()->attach($id);

            // change le statut de l'exemplaire à "emprunté" (status_id = 2)
            $copy->update(['status_id' => 2]); // emprunté

            return redirect('/borrowing')->with('success', 'Exemplaire ajouté à votre emprunt.');
        }

        // pas d'emprunt en cours : on en crée un nouveau
        $copy = Copy::findOrFail($id);

        // vérification de disponibilité avant d'emprunter
        if ($copy->status_id !== 1) {
            return back()->with('error', 'Cet exemplaire n\'est pas disponible.');
        }

        
        // now() = la date et l'heure actuelles, return_date est null car pas encore rendu
        $borrow = Borrow::create([
            'borrowing_date' => now()->format('Y-m-d'), // Date du jour
            'return_date' => null,                      
            'user_id' => $user->id,   // lie l'emprunt à cet utilisateur
        ]);

        $borrow->copies()->attach($id);

        // marque l'exemplaire comme "emprunté" dans la base de données
        $copy->update(['status_id' => 2]); // emprunté

        return redirect('/borrowing')->with('success', 'Emprunt créé avec succès.');
    }

    // -----------------------------------------------------------------------
    // Enregistre le retour d'un emprunt (tous les exemplaires sont rendus)
    // URL : PATCH /return/{id}   (id = identifiant de l'emprunt)
    // -----------------------------------------------------------------------
    public function return($id)
    {
        $borrow = Borrow::with('copies')->findOrFail($id);

        // Pour chaque exemplaire de l'emprunt, on remet son statut à "disponible"
        foreach ($borrow->copies as $copy) {
            $copy->update(['status_id' => 1]); 
        }

        // enregistre la date de retour = aujourd'hui
        // marque l'emprunt comme "terminé"
        $borrow->update(['return_date' => now()->format('Y-m-d')]);

        return redirect('/borrowing')->with('success', 'Retour enregistré avec succès.');
    }
}
