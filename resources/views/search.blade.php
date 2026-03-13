@extends('template')

@section('title', 'BookHub - Recherche')

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<div class="section-header align-center">
			<h2 class="section-title">Recherche</h2>
		</div>

		{{-- Barre de recherche --}}
		<div class="row justify-content-center mb-5">
			<div class="col-md-8">
				<form method="GET" action="/search" class="d-flex gap-2">
					<input type="text" class="form-control" name="params" placeholder="Titre, auteur, catégorie..." value="{{ $params ?? '' }}">
					<select name="availability" class="form-select" style="max-width: 200px;">
						<option value="">Tous</option>
						<option value="available">Disponibles</option>
					</select>
					<button type="submit" class="btn btn-dark">Rechercher</button>
				</form>
			</div>
		</div>

		{{-- Résultats --}}
		<div class="row">
			@forelse($books ?? [] as $book)
			<div class="col-md-3 mb-4">
				<div class="product-item">
					<figure class="product-style">
						<img src="{{ $book->cover_image ? asset('images/' . $book->cover_image) : asset('images/product-item1.jpg') }}" alt="{{ $book->title }}" class="product-item">
						<a href="/exemplar/{{ $book->id }}" class="add-to-cart">Voir détails</a>
					</figure>
					<figcaption>
						<h3>{{ $book->title }}</h3>
						<span>{{ $book->author->name ?? 'Auteur inconnu' }}</span>
						<div class="mt-1">
							@foreach($book->categories as $cat)
								<span class="badge bg-secondary">{{ $cat->libelle }}</span>
							@endforeach
						</div>
						<div class="mt-1">
							<small class="text-muted">{{ $book->copies->where('status_id', 1)->count() }} exemplaire(s) disponible(s)</small>
						</div>
					</figcaption>
				</div>
			</div>
			@empty
			<div class="col-md-12">
				<p class="text-center">Aucun résultat trouvé.</p>
			</div>
			@endforelse
		</div>
	</div>
</section>

@endsection