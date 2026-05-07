<?php

// Un "exemplaire" (Copy) = un livre physique précis dans la bibliothèque.

//   - Côté public : afficher les exemplaires d'un livre
//   - Côté Back-Office (BO) : permettre au bibliothécaire de gérer les exemplaires

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Copy;
use App\Models\Book;
use App\Models\Status;

class CopyController extends Controller
{
    
    public function exemplar($id)
    {
        // On récupère le livre par son id et ses relations :
        $book = Book::with('author', 'categories', 'copies.status')->findOrFail($id);

        return view('exemplar', compact('book'));
    }

    // -----------------------------------------------------------------------
    // [BACK-OFFICE] Affiche la liste de tous les exemplaires
    // URL : GET /bo/copies
    // -----------------------------------------------------------------------
    public function copies(Request $request)
    {
        // abort(403) = affiche une erreur "Accès refusé"
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }

        $query = Copy::with('book.author', 'status');

        // si un mot-clé de recherche est fourni dans l'URL (?search=...)
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

        // compteur disponible/emprunté
        $bookStatsBorrow = Copy::selectRaw('book_id,
                SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as available_count,
                SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END) as borrowed_count')
            ->groupBy('book_id')
            ->get()
            ->keyBy('book_id');


        //compteur bon/ moyen/mauvais
        $bookStatsState = Copy::selectRaw('book_id,
                SUM(CASE WHEN etat = "bon" THEN 1 ELSE 0 END) as good_count,
                SUM(CASE WHEN etat = "moyen" THEN 1 ELSE 0 END) as medium_count,
                SUM(CASE WHEN etat = "excellent" THEN 1 ELSE 0 END) as excellent_count')
            ->groupBy('book_id')
            ->get()
            ->keyBy('book_id');

        // résultats au Back-Office
        return view('bo.copies', compact('copies', 'bookStatsBorrow', 'bookStatsState'));


        
    }

    // -----------------------------------------------------------------------
    // [BACK-OFFICE] Affiche le détail d'un exemplaire spécifique
    // URL : GET /bo/exemplar/{id}   (id = identifiant de l'exemplaire)
    // -----------------------------------------------------------------------
    public function show($id)
    {
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }

        // récupère l'exemplaire avec son livre + l'auteur + statut
        $copy = Copy::with('book.author', 'status')->findOrFail($id);
        $book = $copy->book;

        return view('exemplar', compact('book'));
    }

    // -----------------------------------------------------------------------
    // [BACK-OFFICE] Affiche le formulaire pour ajouter un nouvel exemplaire
    // URL : GET /bo/exemplar/add
    // -----------------------------------------------------------------------
    public function add()
    {
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }

        $books = Book::with('author')->get();

        // récupère tous les statuts possibles
        $statuses = Status::all();

        // compteurs disponible / emprunté par livre
        $bookStatsBorrow = Book::withCount([
            'copies as available_count' => fn($q) => $q->where('status_id', 1),
            'copies as borrowed_count'  => fn($q) => $q->where('status_id', 2),
        ])->get()->keyBy('id');

        //compteurs bon / moyen / mauvais par livre
        $bookStatsState = Book::withCount([
            'copies as good_count' => fn($q) => $q->where('etat',"good"),
            'copies as medium_count'  => fn($q) => $q->where('etat',"medium"),
            'copies as excellent_count'  => fn($q) => $q->where('etat',"bad"),
        ])->get()->keyBy('id');

        // affiche le formulaire d'ajout
        return view('bo.exemplar_form', compact('books', 'statuses', 'bookStatsBorrow', 'bookStatsState'));
    }

    // -----------------------------------------------------------------------
    // [BACK-OFFICE] Traite la soumission du formulaire d'ajout d'un exemplaire
    // URL : POST /bo/exemplar/add
    // -----------------------------------------------------------------------
    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }
        
        $request->validate([
            'book_id'         => 'required|exists:books,id',
            'commission_date' => 'required|date',
            'status_id'       => 'required|exists:statuses,id',
            'etat'            => 'required|in:excellent,bon,moyen',
        ]);

        //crée l'exemplaire en base de données avec les données du formulaire
        Copy::create([
            'book_id'         => $request->book_id,
            'commission_date' => $request->commission_date,
            'status_id'       => $request->status_id,
            'etat'            => $request->etat,
        ]);

        
        return redirect('/bo/copies')->with('success', 'Exemplaire ajouté avec succès.');
    }

    // -----------------------------------------------------------------------
    // [BACK-OFFICE] Affiche le formulaire de modification d'un exemplaire existant
    // URL : GET /bo/exemplar/update/{id}
    // -----------------------------------------------------------------------
    public function edit($id)
    {
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }

        $copy = Copy::findOrFail($id);

        // pour les menus déroulants du formulaire
        $books    = Book::with('author')->get();
        $statuses = Status::all();

        // compteur disponible/emprunté
        $bookStatsBorrow = Book::withCount([
            'copies as available_count' => fn($q) => $q->where('status_id', 1),
            'copies as borrowed_count'  => fn($q) => $q->where('status_id', 2),
        ])->get()->keyBy('id');

        return view('bo.exemplar_form', compact('copy', 'books', 'statuses', 'bookStatsBorrow'));
    }

    // -----------------------------------------------------------------------
    // [BACK-OFFICE] Traite la soumission du formulaire de modification
    // URL : PUT /bo/exemplar/update/{id}
    // -----------------------------------------------------------------------
    public function update(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }

        // validation des données
        $request->validate([
            'book_id'         => 'required|exists:books,id',
            'commission_date' => 'required|date',
            'status_id'       => 'required|exists:statuses,id',
            'etat'            => 'required|in:excellent,bon,moyen',
        ]);

        
        $copy = Copy::findOrFail($id);
        $copy->update([
            'book_id'         => $request->book_id,
            'commission_date' => $request->commission_date,
            'status_id'       => $request->status_id,
            'etat'            => $request->etat,
        ]);

        return redirect('/bo/copies')->with('success', 'Exemplaire modifié avec succès.');
    }

    // -----------------------------------------------------------------------
    // [BACK-OFFICE] Supprime un exemplaire de la base de données
    // URL : DELETE /bo/exemplar/delete/{id}
    // -----------------------------------------------------------------------
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
