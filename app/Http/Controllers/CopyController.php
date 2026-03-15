<?php


// Un "exemplaire" (Copy) = un livre physique précis dans la bibliothèque.
// Un même livre peut avoir plusieurs exemplaires (ex : 3 exemplaires des Misérables).
//
// Ce controller a deux rôles :
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
    // -----------------------------------------------------------------------
    // Affiche la page de détail d'un livre avec tous ses exemplaires (côté public)
    // URL : GET /exemplar/{id}   (id = identifiant du livre)
    // -----------------------------------------------------------------------
    public function exemplar($id)
    {
        // On récupère le livre par son id, ainsi que ses relations :
        // - author : le nom de l'auteur
        // - categories : les catégories du livre
        // - copies.status : chaque exemplaire avec son statut (disponible ou emprunté)
        // findOrFail = si le livre n'existe pas, affiche une erreur 404
        $book = Book::with('author', 'categories', 'copies.status')->findOrFail($id);

        // On envoie les données du livre à la vue pour l'afficher
        return view('exemplar', compact('book'));
    }

    // -----------------------------------------------------------------------
    // [BACK-OFFICE] Affiche la liste de tous les exemplaires avec recherche et pagination
    // URL : GET /bo/copies
    // Réservé aux bibliothécaires (role_id = 1)
    // -----------------------------------------------------------------------
    public function copies(Request $request)
    {
        // Sécurité : seul un bibliothécaire (role_id = 1) peut accéder à cette page
        // abort(403) = affiche une erreur "Accès refusé"
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            abort(403);
        }

        // On prépare la requête pour récupérer tous les exemplaires
        $query = Copy::with('book.author', 'status');

        // si un mot-clé de recherche est fourni dans l'URL (?search=...)
        if ($request->input('search')) {
            $search = $request->input('search');

            // On filtre les exemplaires selon plusieurs critères en même temps
            $query->where(function ($q) use ($search) {
                $q->whereHas('book', function ($q2) use ($search) {
                    $q2->where('title', 'LIKE', "%{$search}%")        // titre de livre
                        ->orWhereHas('author', function ($q3) use ($search) {
                            $q3->where('name', 'LIKE', "%{$search}%"); // nom d'auteur
                        });
                })
                ->orWhereHas('status', function ($q2) use ($search) {
                    $q2->where('status', 'LIKE', "%{$search}%");       // statut (disponible/emprunté)
                })
                ->orWhere('etat', 'LIKE', "%{$search}%");              // état physique (excellent/bon/moyen)
            });
        }

        // Pagination
        $copies = $query->paginate(20);

        // envoie les résultats au Back-Office
        return view('bo.copies', compact('copies'));
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

        // récupère l'exemplaire avec son livre et l'auteur du livre, et son statut
        $copy = Copy::with('book.author', 'status')->findOrFail($id);
        $book = $copy->book; // récupère le livre associé à cet exemplaire

        // réutilise la vue publique "exemplar" pour afficher le détail
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

        // récupère tous les livres pour alimenter la liste déroulante du formulaire
        $books = Book::with('author')->get();

        // récupère tous les statuts possibles 
        $statuses = Status::all();

        // affiche le formulaire d'ajout
        return view('bo.exemplar_form', compact('books', 'statuses'));
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

        // Validation des données envoyées par le formulaire
        // Si une règle n'est pas respectée, Laravel retourne automatiquement une erreur
        $request->validate([
            'book_id'         => 'required|exists:books,id',           // livre doit exister dans la base
            'commission_date' => 'required|date',                       // doit être une date valide
            'status_id'       => 'required|exists:statuses,id',        // statut doit exister
            'etat'            => 'required|in:excellent,bon,moyen',    // doit être une de ces 3 valeurs
        ]);

        // On crée l'exemplaire en base de données avec les données du formulaire
        Copy::create([
            'book_id'         => $request->book_id,
            'commission_date' => $request->commission_date,
            'status_id'       => $request->status_id,
            'etat'            => $request->etat,
        ]);

        // On redirige vers la liste des exemplaires avec un message de confirmation
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

        // récupère l'exemplaire à modifier (ou erreur 404 s'il n'existe pas)
        $copy = Copy::findOrFail($id);

        // récupère les listes nécessaires pour les menus déroulants du formulaire
        $books    = Book::with('author')->get();
        $statuses = Status::all();

        // affiche le formulaire pré-rempli avec les données actuelles de l'exemplaire
        return view('bo.exemplar_form', compact('copy', 'books', 'statuses'));
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

        // validation des données (mêmes règles qu'à l'ajout)
        $request->validate([
            'book_id'         => 'required|exists:books,id',
            'commission_date' => 'required|date',
            'status_id'       => 'required|exists:statuses,id',
            'etat'            => 'required|in:excellent,bon,moyen',
        ]);

        // récupère l'exemplaire existant et on met à jour ses données
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

        // récupère l'exemplaire (ou erreur 404) et on le supprime définitivement
        $copy = Copy::findOrFail($id);
        $copy->delete();

        return redirect('/bo/copies')->with('success', 'Exemplaire supprimé avec succès.');
    }
}
