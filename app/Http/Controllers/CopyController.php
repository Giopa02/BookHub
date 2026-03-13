<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Copy;
use App\Models\Book;
use App\Models\Status;

class CopyController extends Controller
{
    // Détail d'un livre avec ses exemplaires (côté public)
    public function exemplar($id)
    {
        $book = Book::with('author', 'categories', 'copies.status')->findOrFail($id);

        return view('exemplar', compact('book'));
    }

    // BO : liste de tous les exemplaires
    public function copies(Request $request)
{
    if (!Auth::check() || Auth::user()->role_id !== 1) {
        abort(403);
    }

    $query = Copy::with('book.author', 'status');

    if ($request->input('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->whereHas('book', function ($q2) use ($search) {
                $q2->where('title', 'LIKE', "%{$search}%")
                    ->orWhereHas('author', function ($q3) use ($search) {
                        $q3->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->orWhereHas('status', function ($q2) use ($search) {
                $q2->where('status', 'LIKE', "%{$search}%");
            })
            ->orWhere('etat', 'LIKE', "%{$search}%");
        });
    }

    $copies = $query->paginate(20);

    return view('bo.copies', compact('copies'));
}

    // BO : voir un exemplaire
    public function show($id)
    {
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }

        $copy = Copy::with('book.author', 'status')->findOrFail($id);
        $book = $copy->book;

        return view('exemplar', compact('book'));
    }

    // BO : formulaire d'ajout
    public function add()
    {
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }

        $books = Book::with('author')->get();
        $statuses = Status::all();

        return view('bo.exemplar_form', compact('books', 'statuses'));
    }

    // BO : traiter l'ajout
    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }

        $request->validate([
            'book_id' => 'required|exists:books,id',
            'commission_date' => 'required|date',
            'status_id' => 'required|exists:statuses,id',
            'etat' => 'required|in:excellent,bon,moyen',
        ]);

        Copy::create([
            'book_id' => $request->book_id,
            'commission_date' => $request->commission_date,
            'status_id' => $request->status_id,
            'etat' => $request->etat,
        ]);

        return redirect('/bo/copies')->with('success', 'Exemplaire ajouté avec succès.');
    }

    // BO : formulaire de modification
    public function edit($id)
    {
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }

        $copy = Copy::findOrFail($id);
        $books = Book::with('author')->get();
        $statuses = Status::all();

        return view('bo.exemplar_form', compact('copy', 'books', 'statuses'));
    }

    // BO : traiter la modification
    public function update(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }

        $request->validate([
            'book_id' => 'required|exists:books,id',
            'commission_date' => 'required|date',
            'status_id' => 'required|exists:statuses,id',
            'etat' => 'required|in:excellent,bon,moyen',
        ]);

        $copy = Copy::findOrFail($id);
        $copy->update([
            'book_id' => $request->book_id,
            'commission_date' => $request->commission_date,
            'status_id' => $request->status_id,
            'etat' => $request->etat,
        ]);

        return redirect('/bo/copies')->with('success', 'Exemplaire modifié avec succès.');
    }

    // BO : supprimer un exemplaire
    public function delete($id)
    {
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }

        $copy = Copy::findOrFail($id);
        $copy->delete();

        return redirect('/bo/copies')->with('success', 'Exemplaire supprimé avec succès.');
    }
}