<?php

// Ce Controller gère la recherche de livres dans le catalogue.
// L'utilisateur peut chercher par titre, auteur ou catégorie.

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book; // modèle représente un livre en base de données

class SearchController extends Controller
{
    // -----------------------------------------------------------------------
    // Affiche les résultats de recherche selon un mot-clé passé dans l'URL
    // URL : GET /search/{params}
    // -----------------------------------------------------------------------
    public function search($params = null)
    {
        // On commence une requête sur la table "books" en chargeant aussi l'auteur, les catégories, et les exemplaires avec leur statut (nécessaire pour afficher le nombre d'exemplaires disponibles)
        $query = Book::with('author', 'categories', 'copies.status');

        // Si un mot-clé est fourni et qu'il ne vaut pas 'all' (= tout afficher),
        // on applique un filtre de recherche
        if ($params && $params !== 'all') {
            $query->where(function ($q) use ($params) {
                $q->where('title', 'LIKE', "%{$params}%") // Recherche dans le titre du livre
                    ->orWhereHas('author', function ($q2) use ($params) {
                        $q2->where('name', 'LIKE', "%{$params}%"); // dans le nom de l'auteur
                    })
                    ->orWhereHas('categories', function ($q2) use ($params) {
                        $q2->where('libelle', 'LIKE', "%{$params}%"); // dans le nom de la catégorie
                    });
            });
            // 
        }

        // pagination des résultat
        $books = $query->paginate(12);

        // On affiche la vue search.blade.php avec les livres trouvés et le mot-clé utilisé
        return view('search', compact('books', 'params'));
    }

    // -----------------------------------------------------------------------
    // Reçoit la recherche depuis le formulaire (barre de recherche du header)
    // URL : GET /search  (avec le champ "params" dans l'URL)
    // -----------------------------------------------------------------------
    public function searchForm(Request $request)
    {
        // On récupère le mot saisi par l'utilisateur dans le champ de recherche
        // Si rien n'est saisi, on prend "all" par défaut (= tout afficher)
        $params = $request->input('params', 'all');

        // éviter de dupliquer le code
        return $this->search($params);
    }
}
