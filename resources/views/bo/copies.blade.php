{{-- =========================================================================
	bo/copies.blade.php — Back-Office : gestion des exemplaires
	=========================================================================
	Cette vue est réservée aux bibliothécaires (role_id = 1).
	Elle affiche la liste de tous les exemplaires de la bibliothèque
	avec des fonctionnalités de recherche, pagination et actions CRUD :
	- Voir le détail d'un exemplaire
	- Modifier un exemplaire
	- Supprimer un exemplaire
	- Ajouter un nouvel exemplaire

	Données reçues depuis le Controller (CopyController@copies) :
	- $copies : collection paginée d'exemplaires avec leurs relations chargées
--}}
@extends('template')

@section('title', 'BookHub - BO Exemplaires')

@section('content')

<section class="py-5 my-5">
	<div class="container">

        {{-- En-tête avec titre et bouton d'ajout --}}
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h2>Gestion des exemplaires</h2>
            {{-- Bouton qui mène au formulaire d'ajout d'un nouvel exemplaire --}}
			<a href="/bo/exemplar/add" class="btn btn-dark">+ Ajouter un exemplaire</a>
		</div>

        {{-- Message de succès après une action (ajout, modification, suppression) --}}
		@if(session('success'))
			<div class="alert alert-success">{{ session('success') }}</div>
		@endif

        {{-- =====================================================================
			Formulaire de recherche
			Filtre les exemplaires par livre, auteur, statut ou état physique
        ====================================================================== --}}
		<div class="row mb-4">
			<div class="col-md-6">
				<form method="GET" action="/bo/copies" class="d-flex gap-2">
                    {{-- request('search') pré-remplit le champ avec la recherche actuelle --}}
					<input type="text" name="search" class="form-control" placeholder="Rechercher (livre, auteur, statut, état)..." value="{{ request('search') }}">
					<button type="submit" class="btn btn-dark">Rechercher</button>
                    {{-- Bouton "Effacer" : visible seulement si une recherche est en cours --}}
					@if(request('search'))
						<a href="/bo/copies" class="btn btn-outline-secondary">Effacer</a>
					@endif
				</form>
			</div>
		</div>

        {{-- =====================================================================
			Tableau des exemplaires
			Chaque ligne représente un exemplaire avec ses informations et les boutons d'action (Voir / Modifier / Supprimer)
        ====================================================================== --}}
		<table class="table table-striped">
            {{-- table-striped = les lignes alternent entre blanc et gris clair --}}
			<thead>
				<tr>
					<th>ID</th>              
					<th>Livre</th>         
					<th>Auteur</th>        
					<th>Mise en service</th>
					<th>Statut</th>       
					<th>État</th>           
					<th>Actions</th>        
				</tr>
			</thead>
			<tbody>
				@php /** @var \App\Models\Copy $copy */ @endphp <!-- evite avertissements de l'IDE "Trying to get property of non-object of type void" -->
				@forelse($copies ?? [] as $copy)
				<tr>
					<td>{{ $copy->id }}</td>
					<td>{{ $copy->book->title ?? 'N/A' }}</td>
					<td>{{ $copy->book->author->name ?? 'N/A' }}</td>
					<td>{{ \Carbon\Carbon::parse($copy->commission_date)->format('d/m/Y') }}</td>

                    {{-- Statut avec badge coloré selon la valeur --}}
					<td>
						@if($copy->status->status === 'disponible')
							<span class="badge bg-success">{{ $copy->status->status }}</span>
						@else
							<span class="badge bg-warning">{{ $copy->status->status }}</span>
						@endif
					</td>

                    {{-- État physique avec badge coloré selon la valeur --}}
					<td>
						@if($copy->etat === 'excellent')
							<span class="badge bg-success">{{ $copy->etat }}</span>
						@elseif($copy->etat === 'bon')
							<span class="badge bg-info">{{ $copy->etat }}</span>
						@else
							<span class="badge bg-secondary">{{ $copy->etat }}</span>
						@endif
					</td>

                    {{-- Boutons d'action pour chaque exemplaire --}}
					<td>
                        {{-- Voir le détail --}}
						<a href="/bo/exemplar/{{ $copy->id }}" class="btn btn-sm btn-outline-dark">Voir</a>

                        {{-- Modifier : mène au formulaire pré-rempli --}}
						<a href="/bo/exemplar/update/{{ $copy->id }}" class="btn btn-sm btn-outline-primary">Modifier</a>

                        {{-- Supprimer : formulaire DELETE avec confirmation --}}
                        {{-- Les navigateurs ne supportent pas DELETE nativement,
                            on utilise @method('DELETE') pour le simuler --}}
						<form method="POST" action="/bo/exemplar/delete/{{ $copy->id }}" class="d-inline" onsubmit="return confirm('Confirmer la suppression ?')">
                            {{-- onsubmit="return confirm(...)" affiche une boîte de dialogue de confirmation --}}
							@csrf
							@method('DELETE')
							<button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
						</form>
					</td>
				</tr>
				@empty
                {{-- Si aucun exemplaire trouvé (liste vide ou recherche sans résultat) --}}
				<tr>
					<td colspan="7">Aucun exemplaire enregistré.</td>
				</tr>
				@endforelse
			</tbody>
		</table>

        {{-- Boutons de pagination générés automatiquement par Laravel --}}
		<div class="d-flex justify-content-center mt-4">
			{{ $copies->links() }}
		</div>
	</div>
</section>

@endsection
