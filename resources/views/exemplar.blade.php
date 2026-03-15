{{-- =========================================================================
	exemplar.blade.php — Page de détail d'un livre
	=========================================================================
	Cette vue affiche toutes les informations d'un livre :
	- Image de couverture, titre, auteur, date de publication, catégories, description
	- Tableau de tous ses exemplaires avec leur statut et état physique
	- Bouton "Emprunter" (visible seulement si connecté et s'il y a des exemplaires disponibles)

	Données reçues depuis le Controller (CopyController@exemplar) :
	- $book : le livre avec ses relations (author, categories, copies.status)
--}}
@extends('template')

{{-- Le titre de l'onglet inclut le titre du livre --}}
@section('title', 'BookHub - ' . ($book->title ?? 'Détail'))

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<div class="row">

            {{-- =====================================================================
				Colonne gauche : image de couverture du livre
            ====================================================================== --}}
			<div class="col-md-4">
				<figure>
                    {{-- Si le livre a une image de couverture on l'affiche, sinon image par défaut --}}
					<img src="{{ $book->cover_image ? asset('images/' . $book->cover_image) : asset('images/single-image.jpg') }}" alt="{{ $book->title }}" class="single-image img-fluid">
				</figure>
			</div>

            {{-- =====================================================================
				Colonne droite : informations du livre + tableau des exemplaires
            ====================================================================== --}}
			<div class="col-md-8">
				<h2>{{ $book->title }}</h2>
				<p class="text-muted">Par {{ $book->author->name ?? 'Auteur inconnu' }}</p>

                {{-- Date de publication, formatée en jour/mois/année (ex: 15/03/2024) --}}
                {{-- \Carbon\Carbon::parse() convertit la date en objet manipulable --}}
				@if($book->publication_date)
					<p><strong>Date de publication :</strong> {{ \Carbon\Carbon::parse($book->publication_date)->format('d/m/Y') }}</p>
				@endif

                {{-- Badges des catégories du livre --}}
				<div class="mb-3">
					@foreach($book->categories as $cat)
						<span class="badge bg-secondary">{{ $cat->libelle }}</span>
					@endforeach
				</div>

                {{-- Description / résumé du livre --}}
				@if($book->description)
					<p>{{ $book->description }}</p>
				@endif

				<hr>

                {{-- =====================================================================
					Tableau des exemplaires
					Affiche chaque exemplaire avec son statut et son état physique
					Les badges changent de couleur selon le statut et l'état
                ====================================================================== --}}
				<h4>Exemplaires</h4>
				<table class="table">
					<thead>
						<tr>
							<th>#</th>              
							<th>Mise en service</th> 
							<th>Statut</th>          
							<th>État</th>      
						</tr>
					</thead>
					<tbody>
						@forelse($book->copies as $copy)
						<tr>
							<td>{{ $copy->id }}</td>
							<td>{{ \Carbon\Carbon::parse($copy->commission_date)->format('d/m/Y') }}</td>

                            {{-- Statut : badge vert si disponible, orange si emprunté --}}
							<td>
								@if($copy->status->status === 'disponible')
									<span class="badge bg-success">{{ $copy->status->status }}</span>
								@else
									<span class="badge bg-warning">{{ $copy->status->status }}</span>
								@endif
							</td>

                            {{-- État physique : badge vert (excellent), bleu (bon), gris (moyen) --}}
							<td>
								@if($copy->etat === 'excellent')
									<span class="badge bg-success">{{ $copy->etat }}</span>
								@elseif($copy->etat === 'bon')
									<span class="badge bg-info">{{ $copy->etat }}</span>
								@else
									<span class="badge bg-secondary">{{ $copy->etat }}</span>
								@endif
							</td>
						</tr>
						@empty
						<tr>
							<td colspan="4">Aucun exemplaire enregistré.</td>
						</tr>
						@endforelse
					</tbody>
				</table>

                {{-- =====================================================================
					Bouton d'emprunt
					3 cas possibles :
					1. Utilisateur connecté + exemplaires disponibles → bouton "Emprunter"
					2. Utilisateur connecté + aucun exemplaire disponible → bouton grisé
					3. Utilisateur non connecté → lien vers la page de connexion
                ====================================================================== --}}
				@auth
                    {{-- On vérifie s'il existe au moins un exemplaire avec status_id = 1 (disponible) --}}
					@if($book->copies->where('status_id', 1)->count() > 0)
                        {{-- Formulaire POST vers /borrowing/{id de l'exemplaire disponible} --}}
						<form method="POST" action="/borrowing/{{ $book->copies->where('status_id', 1)->first()->id }}">
							@csrf <!--contre les attaques CSRF (falsification de requête inter-site) -->
                            {{-- @csrf génère un token de sécurité caché dans le formulaire --}}
							<button type="submit" class="btn btn-dark">Emprunter un exemplaire</button>
						</form>
					@else
                        {{-- Aucun exemplaire disponible : bouton désactivé --}}
						<button class="btn btn-secondary" disabled>Aucun exemplaire disponible</button>
					@endif
				@else
                    {{-- L'utilisateur n'est pas connecté : on l'invite à se connecter --}}
					<a href="/connect" class="btn btn-outline-dark">Connectez-vous pour emprunter</a>
				@endauth
			</div>

		</div>
	</div>
</section>

@endsection
