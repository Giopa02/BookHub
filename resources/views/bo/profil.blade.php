{{-- =========================================================================
	bo/profil.blade.php — Back-Office : profil détaillé d'un usager
	=========================================================================
	Cette vue est réservée aux bibliothécaires (role_id = 1).
	Elle affiche toutes les informations d'un usager spécifique :
	Le bibliothécaire peut aussi enregistrer le retour d'un emprunt depuis cette page.

	Données reçues depuis le Controller (UserController@profil) :
	- $user : l'usager avec ses relations (role, borrows.copies.book)
--}}
@extends('template')

@section('title', 'BookHub - Profil de ' . ($user->prenom ?? ''))

@section('content')

<section class="py-5 my-5">
	<div class="container">
        {{-- Bouton retour vers la liste des usagers --}}
		<a href="/bo/profils" class="btn btn-outline-dark mb-3">&larr; Retour à la liste</a>

		<div class="row">

            {{-- =====================================================================
				Colonne gauche : carte d'identité de l'usager
            ====================================================================== --}}
			<div class="col-md-4">
				<div class="card p-4">
					<div class="card-body text-center">
						<i class="icon icon-user" style="font-size: 4rem;"></i>
						<h3 class="mt-3">{{ $user->prenom }} {{ $user->name }}</h3>
						<p class="text-muted">{{ $user->email }}</p>
                        {{-- Rôle de l'utilisateur (usager ou bibliothécaire) --}}
						<p><span class="badge bg-dark">{{ $user->role->role ?? 'Usager' }}</span></p>
                        {{-- Date d'inscription --}}
						<p>Inscrit le {{ $user->created_at->format('d/m/Y') }}</p>
					</div>
				</div>
			</div>

            {{-- =====================================================================
				Colonne droite : emprunts de l'usager
            ====================================================================== --}}
			<div class="col-md-8">

                {{-- Emprunt en cours --}}
				<div class="card p-4 mb-4">
					<h4>Emprunt en cours</h4>

                    {{-- On calcule l'emprunt en cours directement dans la vue --}}
					@php
                        // whereNull('return_date') filtre les emprunts sans date de retour = en cours
						$currentBorrow = $user->borrows->whereNull('return_date')->first();
					@endphp

					@if($currentBorrow)
						<p><strong>Date d'emprunt :</strong> {{ \Carbon\Carbon::parse($currentBorrow->borrowing_date)->format('d/m/Y') }}</p>
						<p><strong>Retour prévu :</strong> {{ \Carbon\Carbon::parse($currentBorrow->borrowing_date)->addDays(30)->format('d/m/Y') }}</p>

						<table class="table">
							<thead>
								<tr>
									<th>Exemplaire</th>
									<th>Livre</th>
								</tr>
							</thead>
							<tbody>
								@foreach($currentBorrow->copies as $copy)
								<tr>
									<td>#{{ $copy->id }}</td>
									<td>{{ $copy->book->title ?? 'N/A' }}</td>
								</tr>
								@endforeach
							</tbody>
						</table>

                        {{-- Bouton permettant au bibliothécaire d'enregistrer le retour --}}
                        {{-- Même fonctionnalité que depuis la page personnelle de l'usager --}}
						<form method="POST" action="/return/{{ $currentBorrow->id }}">
							@csrf
							@method('PATCH') {{-- Requête PATCH simulée pour mettre à jour la date de retour --}}
							<button type="submit" class="btn btn-dark">Enregistrer le retour</button>
						</form>
					@else
						<p class="text-muted">Aucun emprunt en cours.</p>
					@endif
				</div>

                {{-- Historique complet des emprunts passés --}}
				<div class="card p-4">
                    {{-- On compte les emprunts retournés pour l'afficher dans le titre --}}
					<h4>Historique ({{ $user->borrows->whereNotNull('return_date')->count() }} emprunts)</h4>

					@forelse($user->borrows->whereNotNull('return_date') as $borrow)
						<div class="border-bottom pb-2 mb-2">
                            {{-- Dates d'emprunt et de retour --}}
							<strong>{{ \Carbon\Carbon::parse($borrow->borrowing_date)->format('d/m/Y') }}</strong>
							&rarr; {{-- Flèche → --}}
                            {{ \Carbon\Carbon::parse($borrow->return_date)->format('d/m/Y') }}
							<br>
                            {{-- Titres des livres empruntés, séparés par des virgules --}}
							<small class="text-muted">
								@foreach($borrow->copies as $copy)
									{{ $copy->book->title ?? 'N/A' }}@if(!$loop->last), @endif
								@endforeach
							</small>
						</div>
					@empty
						<p class="text-muted">Aucun historique.</p>
					@endforelse
				</div>

			</div>
		</div>
	</div>
</section>

@endsection
