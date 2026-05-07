<?php

// "Controller" : c'est lui qui reçoit les actions de l'utilisateur, fait le lien entre ce que voit l'utilisateur (la vue) et la base de données (les modèles).

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Borrow;
use App\Models\Copy;

class BorrowController extends Controller
{
    // -----------------------------------------------------------------------
    // Affiche la page "Mes emprunts" de l'utilisateur connecté
    // URL : GET /borrowing
    // -----------------------------------------------------------------------
    public function borrowing()
    {
        $user = Auth::user();

        //  charge ses emprunts avec toutes les infos liées :
        $user->load('borrows.copies.book.author');

        // On sépare l'emprunt en cours (sans date de retour) des emprunts passés (avec date de retour)
        $currentBorrow = $user->borrows->whereNull('return_date')->first();
        $borrowHistory = $user->borrows->whereNotNull('return_date')->sortByDesc('borrowing_date');
        return view('borrowing', compact('currentBorrow', 'borrowHistory'));
    }

    // -----------------------------------------------------------------------
    // Crée un emprunt ou ajoute un exemplaire à un emprunt existant
    // URL : POST /borrowing/{id}
    // -----------------------------------------------------------------------
    public function borrow($id)
    {
        $user = Auth::user();

        // cherche si l'utilisateur a déjà un emprunt en cours
        $existingBorrow = Borrow::where('user_id', $user->id)
            ->whereNull('return_date')
            ->first();

        if ($existingBorrow) {
            // Si emprunt en cours a moins de 5 exemplaires, on ajoute
            if ($existingBorrow->copies()->count() >= 5) {
                return back()->with('error', 'Vous avez atteint le maximum de 5 exemplaires par emprunt.');
            }

            $copy = Copy::findOrFail($id); // findOrFail : si l'exemplaire n'existe pas → erreur 404
            if ($copy->status_id !== 1) {
                return back()->with('error', 'Cet exemplaire n\'est pas disponible.');
            }

            // attache l'exemplaire à l'emprunt existant (table pivot borrow_copy)
            $existingBorrow->copies()->attach($id);

            $copy->update(['status_id' => 2]); // emprunté

            return redirect('/borrowing')->with('success', 'Exemplaire ajouté à votre emprunt.');
        }

        // pas d'emprunt en cours : on en crée un nouveau
        $copy = Copy::findOrFail($id);

        if ($copy->status_id !== 1) {
            return back()->with('error', 'Cet exemplaire n\'est pas disponible.');
        }

        
        // return_date est null car pas encore rendu
        $borrow = Borrow::create([
            'borrowing_date' => now()->format('Y-m-d'),
            'return_date' => null,
            'user_id' => $user->id,
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

        // on remet statut d'emprunt à "disponible"
        foreach ($borrow->copies as $copy) {
            $copy->update(['status_id' => 1]);
        }

        // marque l'emprunt comme "terminé"
        $borrow->update(['return_date' => now()->format('Y-m-d')]);

        return redirect('/borrowing')->with('success', 'Retour enregistré avec succès.');
    }
}
