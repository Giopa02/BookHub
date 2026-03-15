{{-- =========================================================================
	search.blade.php — Page de recherche et catalogue
	=========================================================================
	Cette vue affiche les résultats de recherche de livres.
	Elle est utilisée pour deux cas :
	1. /search/all       → affiche tout le catalogue (sans filtre)
	2. /search?params=xx → affiche les livres correspondant au mot-clé saisi

	Les données reçues depuis le Controller (SearchController) :
	- $books  : collection paginée de livres (objet LengthAwarePaginator)
	- $params : le mot-clé de recherche (ou null si pas de recherche)
--}}
@extends('template')

@section('title', 'BookHub - Recherche')

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<div class="section-header align-center">
			<h2 class="section-title">Recherche</h2>
		</div>

        {{-- =====================================================================
			Formulaire de recherche
			L'utilisateur peut saisir un titre, un auteur ou une catégorie
        ====================================================================== --}}
		{{-- Barre de recherche --}}
		<div class="row justify-content-center mb-5">
			<div class="col-md-8">
				<form method="GET" action="/search" class="d-flex gap-2">
                    {{-- Champ texte : l'utilisateur saisit son mot-clé --}}
                    {{-- value="{{ $params ?? '' }}" : pré-remplit avec la recherche actuelle --}}
					<input type="text" class="form-control" name="params" placeholder="Titre, auteur, catégorie..." value="{{ $params ?? '' }}">

                    {{-- Menu déroulant pour filtrer par disponibilité (non implémenté côté Controller pour l'instant) --}}
					<select name="availability" class="form-select" style="max-width: 200px;">
						<option value="">Tous</option>
						<option value="available">Disponibles</option>
					</select>

					<button type="submit" class="btn btn-dark">Rechercher</button>
				</form>
			</div>
		</div>

        {{-- =====================================================================
			Grille des résultats
        ====================================================================== --}}
		{{-- Résultats --}}
		<div class="row">
			@forelse($books ?? [] as $book)
			<div class="col-md-3 mb-4">
				<div class="product-item">
					<figure class="product-style">
                        {{-- Image de couverture ou image par défaut si aucune couverture --}}
						<img src="{{ $book->cover_image ? asset('images/' . $book->cover_image) : asset('images/product-item1.jpg') }}" alt="{{ $book->title }}" class="product-item">
						<a href="/exemplar/{{ $book->id }}" class="add-to-cart">Voir détails</a>
					</figure>
					<figcaption>
						<h3>{{ $book->title }}</h3>
						<span>{{ $book->author->name ?? 'Auteur inconnu' }}</span>

                        {{-- Badges de catégories : on boucle sur toutes les catégories du livre --}}
						<div class="mt-1">
							@foreach($book->categories as $cat)
								<span class="badge bg-secondary">{{ $cat->libelle }}</span>
							@endforeach
						</div>

                        {{-- Nombre d'exemplaires disponibles
							On filtre les copies dont status_id = 1 (disponible) --}}
						<div class="mt-1">
							<small class="text-muted">{{ $book->copies->where('status_id', 1)->count() }} exemplaire(s) disponible(s)</small>
						</div>
					</figcaption>
				</div>
			</div>
			@empty
            {{-- Message affiché si aucun livre ne correspond à la recherche --}}
			<div class="col-md-12">
				<p class="text-center">Aucun résultat trouvé.</p>
			</div>
			@endforelse
		</div>

        {{-- =====================================================================
			Pagination
			$books->links() génère automatiquement les boutons "Page précédente / suivante"
        ====================================================================== --}}
		<div class="d-flex justify-content-center mt-4">
			{{ $books->links() }}
		</div>
	</div>
</section>
@endsection
