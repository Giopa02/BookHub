<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class SearchController extends Controller
{
    // -----------------------------------------------------------------------
    // URL : GET /search/{params}
    // -----------------------------------------------------------------------
    public function search($params = null)
    {
        $query = Book::with('author', 'categories', 'copies.status');

        // Si un mot-clé est fourni et qu'il ne vaut pas 'all' (= tout afficher),
        // on applique un filtre de recherche
        if ($params && $params !== 'all') {
            $query->where(function ($q) use ($params) {
                $q->where('title', 'LIKE', "%{$params}%")
                    ->orWhereHas('author', function ($q2) use ($params) {
                        $q2->where('name', 'LIKE', "%{$params}%");
                    })
                    ->orWhereHas('categories', function ($q2) use ($params) {
                        $q2->where('libelle', 'LIKE', "%{$params}%");
                    });
            });
            
        }

        $books = $query->paginate(12);

        return view('search', compact('books', 'params'));
    }

    // -----------------------------------------------------------------------
    // Reçoit la recherche depuis le formulaire (barre de recherche du header)
    // URL : GET /search  (avec le champ "params" dans l'URL)
    // -----------------------------------------------------------------------
    public function searchForm(Request $request)
    {
        // Si rien n'est saisi, on prend "all" par défaut (= tout afficher)
        $params = $request->input('params', 'all');

        return $this->search($params);
    }
}
