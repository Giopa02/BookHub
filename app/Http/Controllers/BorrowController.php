<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Borrow;
use App\Models\Copy;

class BorrowController extends Controller
{
    // Liste des emprunts de l'usager connecté
    public function borrowing()
    {
        $user = Auth::user();
        $user->load('borrows.copies.book.author');

        $currentBorrow = $user->borrows->whereNull('return_date')->first();
        $borrowHistory = $user->borrows->whereNotNull('return_date')->sortByDesc('borrowing_date');

        return view('borrowing', compact('currentBorrow', 'borrowHistory'));
    }

    // Emprunter un exemplaire
    public function borrow($id)
    {
        $user = Auth::user();

        // Vérifier si l'usager a déjà un emprunt en cours
        $existingBorrow = Borrow::where('user_id', $user->id)
            ->whereNull('return_date')
            ->first();

        if ($existingBorrow) {
            // Si l'emprunt en cours a moins de 5 exemplaires, on ajoute
            if ($existingBorrow->copies()->count() >= 5) {
                return back()->with('error', 'Vous avez atteint le maximum de 5 exemplaires par emprunt.');
            }

            // Vérifier que l'exemplaire est disponible
            $copy = Copy::findOrFail($id);
            if ($copy->status_id !== 1) {
                return back()->with('error', 'Cet exemplaire n\'est pas disponible.');
            }

            // Ajouter l'exemplaire à l'emprunt en cours
            $existingBorrow->copies()->attach($id);
            $copy->update(['status_id' => 2]); // emprunté

            return redirect('/borrowing')->with('success', 'Exemplaire ajouté à votre emprunt.');
        }

        // Pas d'emprunt en cours : en créer un nouveau
        $copy = Copy::findOrFail($id);
        if ($copy->status_id !== 1) {
            return back()->with('error', 'Cet exemplaire n\'est pas disponible.');
        }

        $borrow = Borrow::create([
            'borrowing_date' => now()->format('Y-m-d'),
            'return_date' => null,
            'user_id' => $user->id,
        ]);

        $borrow->copies()->attach($id);
        $copy->update(['status_id' => 2]); // emprunté

        return redirect('/borrowing')->with('success', 'Emprunt créé avec succès.');
    }

    // Retourner un emprunt
    public function return($id)
    {
        $borrow = Borrow::with('copies')->findOrFail($id);

        // Remettre tous les exemplaires en disponible
        foreach ($borrow->copies as $copy) {
            $copy->update(['status_id' => 1]); // disponible
        }

        // Enregistrer la date de retour
        $borrow->update(['return_date' => now()->format('Y-m-d')]);

        return redirect('/borrowing')->with('success', 'Retour enregistré avec succès.');
    }
}