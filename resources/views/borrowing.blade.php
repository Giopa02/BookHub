{{-- =========================================================================
	borrowing.blade.php — Page "Mes emprunts" de l'utilisateur connecté
	=========================================================================
	Cette vue affiche deux sections :
	1. L'emprunt en cours (s'il existe) avec le bouton "Déposer le retour"
	2. L'historique des emprunts passés (déjà retournés)

	Données reçues depuis le Controller (BorrowController@borrowing) :
	- $currentBorrow : l'emprunt en cours (sans date de retour), ou null
	- $borrowHistory : collection des emprunts passés (avec date de retour)
--}}
@extends('template')

@section('title', 'BookHub - Mes emprunts')

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<div class="section-header align-center">
			<h2 class="section-title">Mes emprunts</h2>
		</div>

        {{-- Messages flash de succès ou d'erreur --}}
        {{-- stockés en session et affichés une seule fois après une action --}}
		@if(session('success'))
			<div class="alert alert-success">{{ session('success') }}</div>
		@endif

		@if(session('error'))
			<div class="alert alert-danger">{{ session('error') }}</div>
		@endif

        {{-- =====================================================================
			SECTION 1 : Emprunt en cours
			Affiche les exemplaires actuellement empruntés et la date de retour prévue
        ====================================================================== --}}
		{{-- Emprunt en cours --}}
		<div class="card p-4 mb-4">
			<h4>Emprunt en cours</h4>
			@if($currentBorrow ?? false)
                {{-- Date d'emprunt formatée --}}
				<p><strong>Date d'emprunt :</strong> {{ \Carbon\Carbon::parse($currentBorrow->borrowing_date)->format('d/m/Y') }}</p>

                {{-- Date de retour prévue = date d'emprunt + 30 jours (règle de la bibliothèque) --}}
				<p><strong>Retour prévu avant le :</strong> {{ \Carbon\Carbon::parse($currentBorrow->borrowing_date)->addDays(30)->format('d/m/Y') }}</p>

                {{-- Tableau listant les exemplaires de l'emprunt en cours --}}
				<table class="table">
					<thead>
						<tr>
							<th>Exemplaire</th>
							<th>Livre</th>
							<th>Auteur</th>
						</tr>
					</thead>
					<tbody>
                        {{-- On boucle sur chaque exemplaire de l'emprunt --}}
						@foreach($currentBorrow->copies as $copy)
						<tr>
							<td>#{{ $copy->id }}</td>
							<td>{{ $copy->book->title ?? 'N/A' }}</td>   {{-- N/A si le livre n'est pas trouvé --}}
							<td>{{ $copy->book->author->name ?? 'N/A' }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>

                {{-- Formulaire de retour --}}
                {{-- PATCH = méthode HTTP pour une modification partielle (on n'enregistre que la date de retour) --}}
				<form method="POST" action="/return/{{ $currentBorrow->id }}">
					@csrf
					@method('PATCH') {{-- Blade helper pour simuler une requête PATCH (les navigateurs ne supportent que GET et POST) --}}
					<button type="submit" class="btn btn-dark">Déposer le retour</button>
				</form>
			@else
                {{-- Aucun emprunt en cours --}}
				<p class="text-muted">Vous n'avez aucun emprunt en cours. <a href="/search/all">Parcourir le catalogue</a></p>
			@endif
		</div>

        {{-- =====================================================================
			SECTION 2 : Historique des emprunts passés
			Affiche tous les emprunts déjà retournés, du plus récent au plus ancien
        ====================================================================== --}}
		{{-- Historique --}}
		<div class="card p-4">
			<h4>Historique</h4>
			@forelse($borrowHistory ?? [] as $borrow)
				<div class="border-bottom pb-2 mb-2">
                    {{-- Date d'emprunt et date de retour --}}
					<strong>Emprunt du {{ \Carbon\Carbon::parse($borrow->borrowing_date)->format('d/m/Y') }}</strong>
					— Retourné le {{ \Carbon\Carbon::parse($borrow->return_date)->format('d/m/Y') }}
					<br>
                    {{-- Liste des titres des livres empruntés, séparés par des virgules --}}
					<small class="text-muted">
						@foreach($borrow->copies as $copy)
							{{ $copy->book->title ?? 'N/A' }}@if(!$loop->last), @endif
                            {{-- $loop->last = true si c'est le dernier élément de la boucle --}}
                            {{-- Cela évite d'afficher une virgule après le dernier titre --}}
						@endforeach
					</small>
				</div>
			@empty
                {{-- Aucun emprunt passé --}}
				<p class="text-muted">Aucun historique.</p>
			@endforelse
		</div>
	</div>
</section>

@endsection
