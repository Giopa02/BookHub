<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class SearchController extends Controller
{
    public function search($params = null){
        $params = $params ?? request('params');

        $query = Book::with('author', 'categories', 'copies.status');

        if ($params !== 'all') {
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

        $books = $query->get();

        return view('search', compact('books', 'params'));
    }

    public function searchForm(Request $request){
        $params = $request->input('params', 'all');
        return $this->search($params);
    }
}