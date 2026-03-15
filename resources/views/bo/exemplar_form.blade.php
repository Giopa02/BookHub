{{-- =========================================================================
	bo/exemplar_form.blade.php — Formulaire d'ajout ou modification d'exemplaire
	=========================================================================
	Cette vue sert pour deux cas selon si la variable $copy existe ou non :
	- Si $copy N'existe PAS → formulaire d'AJOUT d'un nouvel exemplaire
	- Si $copy EXISTE → formulaire de MODIFICATION d'un exemplaire existant

	Données reçues depuis le Controller :
	- $books    : liste de tous les livres (pour la liste déroulante)
	- $statuses : liste de tous les statuts (disponible, emprunté)
	- $copy     : l'exemplaire à modifier (uniquement en cas de modification)
--}}
@extends('template')

{{-- Le titre change selon si c'est un ajout ou une modification --}}
@section('title', isset($copy) ? 'BookHub - Modifier exemplaire' : 'BookHub - Ajouter exemplaire')

@section('content')

<section class="py-5 my-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-8">

                {{-- Titre de la page : différent selon ajout ou modification --}}
                {{-- isset($copy) = true si la variable $copy existe (modification), false sinon (ajout) --}}
				<h2>{{ isset($copy) ? 'Modifier un exemplaire' : 'Ajouter un exemplaire' }}</h2>

                {{-- Affichage des erreurs de validation --}}
				@if($errors->any())
					<div class="alert alert-danger">
						<ul class="mb-0">
							@foreach($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<div class="card p-4 mt-3">
                    {{-- L'action du formulaire change selon le cas :
						- Ajout      : POST vers /bo/exemplar/add
						- Modification : PUT vers /bo/exemplar/update/{id} --}}
					<form method="POST" action="{{ isset($copy) ? '/bo/exemplar/update/' . $copy->id : '/bo/exemplar/add' }}">
						@csrf
                        {{-- En modification, on simule une requête PUT (HTML ne supporte que POST) --}}
						@if(isset($copy))
							@method('PUT')
						@endif

                        {{-- ---------------------------------------------------------------
							Champ : sélection du livre
							Liste déroulante avec tous les livres disponibles
                        --------------------------------------------------------------- --}}
						<div class="mb-3">
							<label for="book_id" class="form-label">Livre</label>
							<select class="form-select" id="book_id" name="book_id" required>
								<option value="">-- Sélectionner un livre --</option>
								@foreach($books ?? [] as $book)
                                    {{-- old('book_id', $copy->book_id ?? '') :
										En cas d'erreur, on conserve la valeur sélectionnée
										En modification, on pré-sélectionne le livre actuel --}}
									<option value="{{ $book->id }}" {{ (old('book_id', $copy->book_id ?? '') == $book->id) ? 'selected' : '' }}>
										{{ $book->title }} — {{ $book->author->name ?? '' }}
									</option>
								@endforeach
							</select>
						</div>

                        {{-- ---------------------------------------------------------------
							Champ : date de mise en service
							Date à laquelle cet exemplaire a été intégré à la bibliothèque
                        --------------------------------------------------------------- --}}
						<div class="mb-3">
							<label for="commission_date" class="form-label">Date de mise en service</label>
							<input type="date" class="form-control" id="commission_date" name="commission_date"
								value="{{ old('commission_date', $copy->commission_date ?? date('Y-m-d')) }}" required>
                            {{-- date('Y-m-d') = aujourd'hui par défaut pour un ajout --}}
						</div>

                        {{-- ---------------------------------------------------------------
							Champ : statut de l'exemplaire (disponible ou emprunté)
                        --------------------------------------------------------------- --}}
						<div class="mb-3">
							<label for="status_id" class="form-label">Statut</label>
							<select class="form-select" id="status_id" name="status_id" required>
								@foreach($statuses ?? [] as $status)
									<option value="{{ $status->id }}" {{ (old('status_id', $copy->status_id ?? '') == $status->id) ? 'selected' : '' }}>
										{{ $status->status }}
									</option>
								@endforeach
							</select>
						</div>

                        {{-- ---------------------------------------------------------------
							Champ : état physique de l'exemplaire
							3 valeurs possibles : excellent, bon, moyen
                        --------------------------------------------------------------- --}}
						<div class="mb-3">
							<label for="etat" class="form-label">État physique</label>
							<select class="form-select" id="etat" name="etat" required>
                                {{-- Pour chaque option, on vérifie si elle doit être pré-sélectionnée --}}
                                {{-- old('etat', $copy->etat ?? 'bon') : valeur actuelle, ou 'bon' par défaut --}}
								<option value="excellent" {{ (old('etat', $copy->etat ?? '') === 'excellent') ? 'selected' : '' }}>Excellent</option>
								<option value="bon"       {{ (old('etat', $copy->etat ?? 'bon') === 'bon') ? 'selected' : '' }}>Bon</option>
								<option value="moyen"     {{ (old('etat', $copy->etat ?? '') === 'moyen') ? 'selected' : '' }}>Moyen</option>
							</select>
						</div>

                        {{-- Boutons : valider ou annuler --}}
						<div class="d-flex gap-2">
                            {{-- Le texte du bouton change selon ajout ou modification --}}
							<button type="submit" class="btn btn-dark">{{ isset($copy) ? 'Mettre à jour' : 'Ajouter' }}</button>
							<a href="/bo/copies" class="btn btn-outline-secondary">Annuler</a>
						</div>
					</form>
				</div>

			</div>
		</div>
	</div>
</section>

@endsection
